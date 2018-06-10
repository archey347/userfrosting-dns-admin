<?php

namespace UserFrosting\Sprinkle\Dnsadmin\Database\Models;

use UserFrosting\Sprinkle\Core\Database\Models\Model;

class ZoneEntry extends Model
{
    public $timestamps = true;

    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'zone_entries';

    /**
     * @var array Fields that are fillable
     */
    protected $fillable = [
        'type',
        'name',
        'value'
    ];

    /**
     * @method zone() Gets the parent zone
     */
    public function zone() {
      return $this->belongsTo('UserFrosting\Sprinkle\Dnsadmin\Database\Models\Zone');
    }
}
