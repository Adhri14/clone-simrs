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
        Schema::create('jadwal_dokter_antrians', function (Blueprint $table) {
            $table->id();
            $table->string('kodePoli')->nullable();
            $table->string('namaPoli')->nullable();
            $table->string('kodeSubspesialis')->nullable();
            $table->string('namaSubspesialis')->nullable();
            $table->string('kodeDokter')->nullable();
            $table->string('namaDokter')->nullable();
            $table->string('hari')->nullable();
            $table->string('namaHari')->nullable();
            $table->string('jadwal')->nullable();
            $table->string('kapasitasPasien')->nullable();
            $table->string('libur')->nullable();
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
        Schema::dropIfExists('jadwal_dokter_antrians');
    }
};
