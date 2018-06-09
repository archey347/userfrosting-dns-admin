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
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Fortress\Adapter\JqueryValidationAdapter;
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\Zone;
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\ZoneEntry;


/**
  * Controller class that manages all of the DNS Admin front end UI
  *
  * @author Archey Barrell
  * @extends SimpleController
  */
class AdminController extends SimpleController
{
  /**
    * Generates the admin page for managing the Zones
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
  public function pageZonesAdmin(Request $request, Response $response, $args)
  {
    // Get the zone create validation rules
    $schema_create = new RequestSchema('schema://requests/zone-create.yaml');
    $validator_create = new JqueryValidationAdapter($schema_create, $this->ci->translator);

    // Get the zone edit validation rules
    $schema_edit = new RequestSchema('schema://requests/zone-edit.yaml');
    $validator_edit = new JqueryValidationAdapter($schema_edit, $this->ci->translator);

    return $this->ci->view->render($response, 'pages/dnsadmin-zones.html.twig', [
      'page' => [
        'validators' => [
          'createZone' => $validator_create->rules(),
          "editZone" => $validator_edit->rules()
        ]
      ]
    ]);
  }

  /**
    * Generates the admin page for managing the zone entries
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
  public function pageZoneEntriesAdmin(Request $request, Response $response, $args)
  {
    $zone = Zone::find($args['id']);

    if(!$zone) {
      return NotFoundException($request, $response);
    }

    $schema_create = new RequestSchema('schema://requests/'.$zone->type.'-zone-entry-create.yaml');
    $validator_create = new JqueryValidationAdapter($schema_create, $this->ci->translator);

    // Get the zone edit validation rules
    $schema_edit = new RequestSchema('schema://requests/'.$zone->type.'-zone-entry-edit.yaml');
    $validator_edit = new JqueryValidationAdapter($schema_edit, $this->ci->translator);



    return $this->ci->view->render($response, 'pages/dnsadmin-zone-entries.html.twig', [
      'page' => [
        'zone' => $zone->toArray(),
        'validators' => [
          'createEntry' => $validator_create->rules(),
          'editEntry' => $validator_edit->rules()
        ]

      ]
    ]);
  }

  /**
    * Generates the modal form for creating a zone
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
  public function modalCreateZone(Request $request, Response $response, $args)
  {
    return $this->ci->view->render($response, 'modals/zone.html.twig', [
      "form" => [
        "id" => "form-zone-create",
        "method" => "POST",
        "action" => "api/dns/zones",
        "submit" => "Create Zone"
      ]
    ]);
  }

  /**
    * Generates the modal form for editing a zone
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
  public function modalEditZone(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone_id = $request->getQueryParam('id');

    if($zone_id == null) {
      $ms->addMessage('danger', "No Zone ID specified.");
      return $response->withStatus(400);
    }

    $zone = Zone::find($zone_id);

    if(!$zone) {
      $ms->addMessage('danger', 'Invalid Zone ID.');
      return $response->withStatus(400);
    }

    return $this->ci->view->render($response, 'modals/zone.html.twig', [
      "form" => [
        "id" => "form-zone-edit",
        "method" => "PUT",
        "action" => "api/dns/zones/z/" . $zone_id,
        "submit" => "Submit Changes"
      ],
      "zone" => $zone->toArray()
    ]);
  }

  /**
    * Generates the modal form for editing a zone
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
  public function modalDeleteZone(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone_id = $request->getQueryParam('id');

    if($zone_id == null) {
      $ms->addMessage('danger', "No Zone ID specified.");
      return $response->withStatus(400);
    }

    $zone = Zone::find($zone_id);

    if(!$zone) {
      $ms->addMessage('danger', 'Invalid Zone ID.');
      return $response->withStatus(400);
    }

    return $this->ci->view->render($response, 'modals/zone-delete.html.twig', [
      "form" => [
        "action" => "api/dns/zones/z/" . $zone_id
      ],
      "zone" => $zone->toArray()
    ]);
  }

  /**
    * Generates the modal form for creating a zone
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
  public function modalCreateZoneEntry(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone_id = $request->getQueryParam('id');
    $zone = Zone::find($zone_id);

    if(!$zone) {
      $ms->addMessage('danger', 'Invalid Zone ID.');
      return $response->withStatus(400);
    }

    $zone = $zone->toArray();

    return $this->ci->view->render($response, 'modals/zone-entry.html.twig', [
      "form" => [
        "id" => "form-zone-entry-create",
        "method" => "POST",
        "action" => "api/dns/zones/z/$zone_id/entries",
        "submit" => "Create Zone Entry"
      ],
      "zone" => $zone,
      "entry_types" => $this->getEntryTypes($zone['type'])
    ]);
  }

  /**
    * Generates the modal form for editing a zone entry
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
  public function modalEditZoneEntry(Request $request, Response $response, $args)
  {
    $ms = $this->ci->alerts;

    $zone_entry_id = $request->getQueryParam('id');
    $zone_entry = ZoneEntry::find($zone_entry_id);

    if(!$zone_entry) {
      $ms->addMessage('danger', 'Invalid Zone Entry ID.');
      return $response->withStatus(400);
    }

    $zone = $zone_entry->zone()->first();

    return $this->ci->view->render($response, 'modals/zone-entry.html.twig', [
      "form" => [
        "id" => "form-zone-entry-edit",
        "method" => "PUT",
        "action" => "api/dns/zones/z/" . $zone['id'] . "/entries/e/" . $zone_entry_id,
        "submit" => "Edit Zone Entry"
      ],
      "zone" => $zone->toArray(),
      "entry" => $zone_entry->toArray(),
      "entry_types" => $this->getEntryTypes($zone->type)
    ]);
  }

  /**
    * Generates all of the Entry Types available for a specific zone.
    *
    * @param string $zone_type
    * @return array
    */
  public function getEntryTypes($zone_type)
  {
    $result = ['NS'];
    if($zone_type == "normal") {
      $result = array_merge($result, ['A', 'AAAA', 'MX', 'CNAME']);
    } else {
      $result = array_merge($result, ['PTR']);
    }
    return $result;
  }


}
