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
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            // identifier
            $table->uuid('satusehat_uuid')->index()->nullable()->unique(); #satusehat uuid
            $table->string('identifier_id')->index()->nullable();
            // location
            $table->string('location_id')->nullable();
            $table->string('location_name')->nullable();
            // participant
            $table->string('practitioner_id')->nullable();
            $table->string('practitioner_name')->nullable();
            // patient
            $table->string('patient_id')->nullable();
            $table->string('patient_name')->nullable();
            // periode
            $table->string('period_start')->nullable();
            $table->string('period_end')->nullable();
            // service provider / rumahsakit
            $table->string('provider_id')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('encounters');
    }
};
