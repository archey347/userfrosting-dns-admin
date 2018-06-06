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


}
