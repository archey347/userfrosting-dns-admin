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
    * @return void
    */
  public function pageZonesAdmin(Request $request, Response $response, $args)
  {
    return $this->ci->view->render($response, 'pages/dnsadmin-zones.html.twig');
  }
}
