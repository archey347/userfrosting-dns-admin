<?php
/**
  * DNSAdmin
  *
  * @link https://github.com/archey347/userfrosting-dns-admin
  * @license https://github.com/archey347/userfrosting-dns-admin/blob/master/LICENSE (MIT Licence)
  */
namespace UserFrosting\Sprinkle\Dnsadmin\ServicesProvider;

use UserFrosting\Sprinkle\Dnsadmin\Config\Generator;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
/**
  * ServicesProvider for DNS Admin Sprinkle
  *
  * @author Archey Barrell
  * @extends SimpleController
*/
class ServicesProvider
{
  /**
    * Registers the services
    *
    * @param Container $container
    */
  public function register($container)
  {
    $container['dnsConfigGenerator'] = (function ($container) {
      $directory = $container->config['dnsadmin']['directory'];



      $adapter = new Local($directory);

      $filesystem = new Filesystem($adapter);

      $generator = new Generator($filesystem, $directory);

      return $generator;
  });
  }
}
