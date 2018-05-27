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

    protected $fillable = [
        'type',
        'name',
        'value'
    ];
}
