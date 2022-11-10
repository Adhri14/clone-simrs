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
            $table->uuid('satusehat_uuid')->nullable();
            // identifier
            $table->string('identifier_id')->nullable();
            $table->string('part_of_id')->nullable();
            // type
            $table->string('type_code')->nullable();
            $table->string('type_display')->nullable();
            // telecom
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            // address
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('province_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('village')->nullable();
            $table->string('line')->nullable();
            $table->string('postalCode')->nullable();
            // resource
            $table->boolean('active')->nullable();
            $table->string('name')->nullable();
            $table->string('alias')->nullable();
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
