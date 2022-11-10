<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            // identifier
            $table->string('part_of_id')->index()->nullable();
            $table->uuid('satusehat_uuid')->index()->nullable()->unique();
            $table->string('identifier_id')->index()->nullable();
            // telecom
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            // address
            $table->string('province_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('village_id')->nullable();
            $table->string('city')->nullable();
            $table->string('line')->nullable();
            $table->string('postalCode')->nullable();
            // resource
            $table->boolean('active')->default(true);
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
};
