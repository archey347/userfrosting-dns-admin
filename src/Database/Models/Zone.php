<?php

namespace UserFrosting\Sprinkle\Dnsadmin\Database\Models;

use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
  * Zones Table Model
  * Version 0.0.0
  *
  * @extends Model
  * @author Archey Barrell
  */
class Zone extends Model
{
    public $timestamps = true;

    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'zones';

    /**
     * @var array The fieds which are writeable?
     */
    protected $fillable = [
        'type',
        'ttl',
        'primary_dns',
        'admin_domain',
        'serial_number',
        'serial_number_mode',
        'refresh',
        'retry',
        'expiry'
    ];

    /**
     * @method enteries() Gets all of the zone entries
     */
    public function entries() {
      return $this->hasMany('UserFrosting\Sprinkle\Dnsadmin\Database\Models\ZoneEntries');
    }
}
