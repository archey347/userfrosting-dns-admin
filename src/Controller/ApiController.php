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

/**
  * Controller class which handles all of the API calls for the dns config
  *
  * @author Archey Barrell
  * @extends Controller
  */
class ApiController extends SimpleController
{
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
}
