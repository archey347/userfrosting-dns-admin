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
use UserFrosting\Sprinkle\Dnsadmin\Database\Models\Zone;

/**
  * Sprunje class for the Zones table
  *
  * @author Archey Barrell
  * @extends Sprunje
  */
class ZonesSprunje extends Sprunje
{
  /**
   * @var array The fieds which are sortable
   */
  protected $sortable = [
      'name',
  ];

  /**
    * Set the initial query used by the sprunje
    */
  protected function baseQuery()
  {
    $instance = new Zone();

    return $instance->newQuery();
  }
}
