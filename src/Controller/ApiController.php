<?php
/**
  * DNSAdmin
  *
  * @link https://github.com/archey347/userfrosting-dns-admin
  * @license https://github.com/archey347/userfrosting-dns-admin/blob/master/LICENSE (MIT Licence)
  */
namespace UserFrosting\Sprinkle\Dnsadmin\Controller;

use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use Slim\Http\Request;
use Slim\Http\Response;
use UserFrosting\Sprinkle\Dnsadmin\Sprunje\ZonesSprunje;
use UserFrosting\Sprinkle\Dnsadmin\Sprunje\ZoneEntriesSprunje;
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Fortress\ServerSideValidator;
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\Zone;
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\ZoneEntry;
use Illuminate\Database\Capsule\Manager as Capsule;


/**
  * Controller class which handles all of the API calls for the dns config
  *
  * @author Archey Barrell
  * @extends Controller
  */
class ApiController extends SimpleController
{
  /**
    * Adds a "." to a domain name if there isn't one already present
    */
  public function domainTransformer($domain)
  {
      return substr($domain, -1) == "." ? $domain : $domain . ".";
  }

  /**
    * Gets a list of all of the zones through the Zones sprunje
    * Method Type: GET
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return void
    */
  public function getZones(Request $request, Response $response, $args)
  {
    // Get the GET parameters
    $params = $request->getQueryParams();

    /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
    $classMapper = $this->ci->classMapper;

    $sprunje = new ZonesSprunje($classMapper, $params);

    return $sprunje->toResponse($response);
  }

  /**
   * Creates a zone
   *
   * @param Request $request
   * @param Response $response
   * @param array $args
   * @return void
   */
  public function createZone(Request $request, Response $response, $args)
  {
      $params = $request->getParsedBody();

      // Start off by checking to make sure that the zone type is correct
      $schema = new RequestSchema('schema://requests/zone-create.yaml');

      $transformer = new RequestDataTransformer($schema);
      $data = $transformer->transform($params);

      $ms = $this->ci->alerts;

      $validator = new ServerSideValidator($schema, $this->ci->translator);

      if(!$validator->validate($data)) {
        $ms->addValidationErrors($validator);
        return $response->withStatus(400);
      }

      if($data['type'] == "normal") {
        $data['name'] = $this->domainTransformer($data['name']);
      }

      // Make sure that this zone doesn't already exist
      $zone = Zone::where('name', $data['name'])->first();

      if($zone) {
        $ms->addMessage('danger', 'A zone with that name already exists.');
        return $response->withStatus(400);
      }

      // Then add the zone to the Database

      Capsule::transaction(function() use($data) {
        $zone = new Zone();

        $zone->name = $data['name'];
        $zone->type = $data['type'];
        $zone->ttl = $data['ttl'];

        $zone->primary_dns = $this->domainTransformer($data['primary_dns']);
        $zone->admin_domain = $this->domainTransformer($data['admin_domain']);

        $zone->serial_number_mode = $data['serial_number_mode'];
        $zone->serial_number = $data['serial_number'];

        $zone->refresh = $data['refresh'];
        $zone->retry = $data['retry'];
        $zone->expiry = $data['expire'];

        $zone->save();
      });

      $ms->addMessage('success', "Successfully added zone '{$data['name']}'");

  }

  /**
   * Edits a zone
   *
   * @param Request $request
   * @param Response $response
   * @param array $args
   * @return void
   */
  public function editZone(Request $request, Response $response, $args)
  {
      if(!isset($args['id'])) {
        $ms->addMessage('danger', 'Zone Not Found.');
        return $response->withStatus(404);
      }

      $zone = Zone::find($args['id']);

      if(!$zone) {
        $ms->addMessage('danger', 'Zone Not Found.');
        return $response->withStatus(404);
      }

      $params = $request->getParsedBody();

      // Start off by checking to make sure that the zone type is correct
      $schema = new RequestSchema('schema://requests/zone-edit.yaml');

      $transformer = new RequestDataTransformer($schema);
      $data = $transformer->transform($params);

      $ms = $this->ci->alerts;

      $validator = new ServerSideValidator($schema, $this->ci->translator);

      if(!$validator->validate($data)) {
        $ms->addValidationErrors($validator);
        return $response->withStatus(400);
      }

      if($zone->type == "normal") {
        $data['name'] = $this->domainTransformer($data['name']);
      }

      Capsule::transaction(function() use($data, $zone) {

        $zone->name = $data['name'];

        $zone->ttl = $data['ttl'];

        $zone->primary_dns = $this->domainTransformer($data['primary_dns']);
        $zone->admin_domain = $this->domainTransformer($data['admin_domain']);

        $zone->serial_number_mode = $data['serial_number_mode'];
        $zone->serial_number = $data['serial_number'];

        $zone->refresh = $data['refresh'];
        $zone->retry = $data['retry'];
        $zone->expiry = $data['expire'];

        $zone->save();
      });

      $ms->addMessage('success', "Successfully modified zone '{$data['name']}'");

  }

  /**
   * Deletes a zone
   *
   * @param Request $request
   * @param Response $response
   * @param array $args
   * @return void
   */
  public function deleteZone(Request $request, Response $response, $args)
  {
      $ms = $this->ci->alerts;

      $zone = Zone::find($args['id']);

      if(!$zone) {
        $ms->addMessage('danger', 'Zone Not Found.');
        return $response->withStatus(404);
      }

      $zone_name = $zone->name;

      Capsule::transaction(function() use($zone) {
        $zone->entries()->delete();
        $zone->delete();

      });

      $ms->addMessage('success', "Zone '$zone_name' has been deleted.");

  }

  /**
    * Gets a list of all of the zone entries
    * Method Type: GET
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return void
    */
  public function getZoneEntries(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone = Zone::find($args['id']);

    if(!$zone) {
      $ms->addMessage('danger', 'Zone Not Found.');
      return $response->withStatus(404);
    }

    // Get the GET parameters
    $params = $request->getQueryParams();

    /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
    $classMapper = $this->ci->classMapper;

    $sprunje = new ZoneEntriesSprunje($classMapper, $params, $zone->id);

    return $sprunje->toResponse($response);
  }

  /**
    * Creates a Zone Entry and associates it with a zone
    * Method Type: GET
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return void
    */
  public function createZoneEntry(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone = Zone::find($args['id']);

    if(!$zone) {
      $ms->addMessage('danger', 'Zone Not Found.');
      return $response->withStatus(404);
    }

    $params = $request->getParsedBody();

    $zone_type = $zone->type == "normal" ? "normal" : "reverse";

    $schema = new RequestSchema('schema://requests/'.$zone_type.'-zone-entry-create.yaml');

    $transformer = new RequestDataTransformer($schema);
    $data = $transformer->transform($params);

    $ms = $this->ci->alerts;

    $validator = new ServerSideValidator($schema, $this->ci->translator);

    if(!$validator->validate($data)) {
      $ms->addValidationErrors($validator);
      return $response->withStatus(400);
    }

    Capsule::transaction(function () use ($data, $zone) {
      $entry = new ZoneEntry($data);

      $zone->entries()->save($entry);
    });

    $ms->addMessage('success', 'Successfully added the entry.');


  }

  /**
    * Edits a Zone Entry
    * Method Type: GET
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return void
    */
  public function editZoneEntry(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone = Zone::find($args['zone_id']);

    if(!$zone) {
      $ms->addMessage('danger', 'Zone Not Found.');
      return $response->withStatus(404);
    }

    $entry = $zone->entries()->find($args['entry_id']);

    if(!$entry) {
      $ms->addMessage('danger', 'Zone Entry Not Found.');
      return $response->withStatus(404);
    }

    $params = $request->getParsedBody();

    $zone_type = $zone->type == "normal" ? "normal" : "reverse";

    $schema = new RequestSchema('schema://requests/'.$zone_type.'-zone-entry-edit.yaml');

    $transformer = new RequestDataTransformer($schema);
    $data = $transformer->transform($params);

    $ms = $this->ci->alerts;

    $validator = new ServerSideValidator($schema, $this->ci->translator);

    if(!$validator->validate($data)) {
      $ms->addValidationErrors($validator);
      return $response->withStatus(400);
    }

    Capsule::transaction(function () use ($data, $entry) {
      $entry->fill($data);

      $entry->save();
    });

    $ms->addMessage('success', 'Successfully updated the entry.');


  }

  /**
    * Deletes a Zone Entry
    * Method Type: GET
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return void
    */
  public function deleteZoneEntry(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone = Zone::find($args['zone_id']);

    if(!$zone) {
      $ms->addMessage('danger', 'Zone Not Found.');
      return $response->withStatus(404);
    }

    $entry = $zone->entries()->find($args['entry_id']);

    if(!$entry) {
      $ms->addMessage('danger', 'Zone Entry Not Found.');
      return $response->withStatus(404);
    }

    Capsule::transaction(function () use ($data, $entry) {
      $entry->delete();
    });

    $ms->addMessage('success', 'Successfully deleted the entry.');
  }

  /**
    * Saves the Zone Configuration
    * Method Type: GET
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return void
    */
  public function saveConfiguration(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone = Zone::find($args['id']);

    if(!$zone) {
      $ms->addMessage('danger', 'Zone Not Found.');
      return $response->withStatus(404);
    }

    $dnsConfigGenerator = $this->ci->dnsConfigGenerator;

    $dnsConfigGenerator->saveZone($zone);

    $ms->addMessage('Success', "Zone file was successfully saved");
  }

}
