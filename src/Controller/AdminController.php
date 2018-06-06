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
    $schema = new RequestSchema('schema://requests/zone-create.yaml');
    $validator = new JqueryValidationAdapter($schema, $this->ci->translator);

    return $this->ci->view->render($response, 'pages/dnsadmin-zones.html.twig', [
      'page' => [
        'validators' => [
          'createZone' => $validator->rules()
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


}
