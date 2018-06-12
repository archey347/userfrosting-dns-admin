<?php
/**
  * DNSAdmin
  *
  * @link https://github.com/archey347/userfrosting-dns-admin
  * @license https://github.com/archey347/userfrosting-dns-admin/blob/master/LICENSE (MIT Licence)
  */
namespace UserFrosting\Sprinkle\Dnsadmin\Config;

use UserFrosting\Sprinkle\Dnsadmin\Database\Models\Zone;
use Badcow\DNS\Zone as ConfigZone;
use Badcow\DNS\Rdata\Factory;
use Badcow\DNS\ResourceRecord;
use Badcow\DNS\AlignedBuilder;
use Badcow\DNS\Ip\Toolbox;
use Badcow\DNS\Classes;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
/**
  * Service which allows for DNS zone files to be generated and saved
  *
  * @author Archey Barrell
  * @extends SimpleController
  */
class Generator
{
  /**
    * @var Filesystem Filesystem
    */
  protected $filesystem;

  /**
    * Constructor function
    * @param Filesystem $filesystem
    */
  public function __construct($filesystem)
  {
    $this->filesystem = $filesystem;
  }

  /**
    * Generates the configuration file for a zone
    *
    * @param Zone $zone
    */
  public function getZoneConfig(Zone $zone)
  {
    $entries = [];

    array_push($entries, new ResourceRecord(
      '@',
      Factory::Soa(
        $zone->primary_dns,
        $zone->admin_domain,
        $zone->serial_number,
        $zone->refresh,
        $zone->retry,
        $zone->expiry,
        $zone->minimum
      ),
      null,
      Classes::INTERNET
    ));

    foreach($zone->entries()->get() as $entry) {
        if($entry->type == "NS") {
          array_push($entries, new ResourceRecord($entry->name, Factory::Ns($entry->value), null, Classes::INTERNET));
        } elseif($entry->type == "A") {
          array_push($entries, new ResourceRecord($entry->name, Factory::A($entry->value), null, Classes::INTERNET));
        } elseif($entry->type == "AAAA") {
          array_push($entries, new ResourceRecord($entry->name, Factory::Aaaa($entry->value), null, Classes::INTERNET));
        } elseif($entry->type == "MX") {
          array_push($entries, new ResourceRecord($entry->name, Factory::Mx($entry->value), null, Classes::INTERNET));
        } elseif($entry->type == "PTR") {
          array_push($entries, new ResourceRecord($entry->name, Factory::Ptr($entry->value), null, Classes::INTERNET));
        }
    }

    $origin = $this->getZoneOrigin($zone);

    $zone = new ConfigZone($origin, $zone->ttl, $entries);

    $builder = new AlignedBuilder();

    return $builder->build($zone);

  }

  /**
    * Saves the configuration file for a zone
    *
    * @param Zone $zone
    */
  public function saveZone($zone)
  {
    $config = $this->getZoneConfig($zone);

    $config_dir = "/zones/db." . rtrim($this->getZoneOrigin($zone), ".");

    $this->filesystem->write($config_dir, $config);
  }

  /**
    * Gets the origin of a zone
    *
    * @param Zone $zone
    */
  public function getZoneOrigin($zone)
  {
    if($zone->type == "normal") {
      $origin = $zone->name;
    } elseif ($zone->type == "reverse_ipv4") {
      $origin = Toolbox::reverseIpv4($zone->name);
    } elseif ($zone->type == "reverse_ipv6") {
      $origin = Toolbox::reverseIpv6($zone->name);
    }
    return $origin;
  }

}
