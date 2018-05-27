<?php

namespace UserFrosting\Sprinkle\Dnsadmin\Database\Migrations\v000;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\System\Bakery\Migration;

/**
  * Zones Table Migration
  * Version 0.0.0
  *
  * @extends Migration
  * @author Archey Barrell
  */
class ZoneEntriesTable extends Migration
{
    /**
      * {@inheritDoc}
      */
    public function up()
    {
      if(!$this->schema->hasTable('zone_entries')) {
        $this->schema->create('zone_entries', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('zone_id');
          $table->string('type');
          $table->string('name', 1000);
          $table->string('value', 1000);

          $table->index('zone_id');
        });
      }
    }
    /**
      * {@inheritDoc}
      */
    public function down()
    {
      $this->schema->drop('zone_entries');
    }
}
