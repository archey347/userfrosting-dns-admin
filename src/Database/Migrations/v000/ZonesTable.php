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
class ZonesTable extends Migration
{
    /**
      * {@inheritDoc}
      */
    public function up()
    {
      if(!$this->schema->hasTable('zones')) {
        $this->schema->create('zones', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name', 1000);
          $table->string('type', 100);
          $table->integer('ttl');
          $table->string('primary_dns', 500);
          $table->string('admin_domain', 500);
          $table->bigInteger('serial_number');
          $table->string("serial_number_mode", 100);
          $table->integer('refresh');
          $table->integer('retry');
          $table->integer('expiry');
          $table->integer('minimum');
          $table->boolean('enabled')->default(false);

          $table->timestamps();

        });
      }
    }
    /**
      * {@inheritDoc}
      */
    public function down()
    {
      $this->schema->drop('zones');
    }
}
