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
        Schema::create('pratitioners', function (Blueprint $table) {
            $table->id();
            $table->uuid('satusehat_uuid')->nullable();
            // identifier
            $table->string('rm_id')->nullable();
            $table->string('nik_id')->nullable();
            $table->string('ihs_id')->nullable();
            $table->string('bpjs_id')->nullable();
            // name
            $table->string('name')->nullable();
            // telecom
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            // address
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('province_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('village')->nullable();
            $table->string('line')->nullable();
            $table->string('postalCode')->nullable();
            // photo
            $table->string('photo_id')->nullable();
            // qualification
            $table->string('str_kki_id')->nullable();
            $table->string('str_kki_organization')->nullable();
            $table->string('period_end')->nullable();
            $table->string('period_start')->nullable();
            // resource
            $table->boolean('active')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthDate')->nullable();
            $table->string('communication')->nullable(); #bahasa
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
        Schema::dropIfExists('pratitioners');
    }
};
