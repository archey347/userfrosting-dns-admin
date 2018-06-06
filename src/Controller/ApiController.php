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
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Fortress\ServerSideValidator;
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\Zone;
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
  public function createZone(Request $request, Response $response, $args) {
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
}
