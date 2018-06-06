<?php
/**
  * DNSAdmin
  *
  * @link https://github.com/archey347/userfrosting-dns-admin
  * @license https://github.com/archey347/userfrosting-dns-admin/blob/master/LICENSE (MIT Licence)
  */
namespace UserFrosting\Sprinkle\Dnsadmin\Sprunje;

use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;
use UserFrosting\Sprinkle\Core\Util\ClassMapper;
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\Zone;
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\ZoneEntries;


/**
  * Sprunje class for the Zones table
  *
  * @author Archey Barrell
  * @extends Sprunje
  */
class ZoneEntriesSprunje extends Sprunje
{
  /**
   * @var array The fieds which are sortable
   */
  protected $sortable = [
      'name',
      'type'
  ];

  /**
    * @var integer The id of the zone
    */
  protected $zone_id;

  /*
   *  Allows the zone id to be set on initialisation
   */
  public function __construct(ClassMapper $classMapper, array $options, $zone_id)
  {
    $this->zone_id = $zone_id;
    parent::__construct($classMapper, $options);
  }

  /**
    * Set the initial query used by the sprunje
    */
  protected function baseQuery()
  {
    $zone = Zone::find($this->zone_id);

    $instance = $zone->entries();

    return $instance->newQuery();
  }
}
