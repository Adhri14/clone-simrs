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
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            // identifier
            $table->string('norm')->index()->unique();
            $table->string('nokartu')->index()->unique()->nullable();
            $table->string('nik')->index()->unique()->nullable();
            $table->string('ihs')->index()->unique()->nullable();
            // name
            $table->string('nama')->nullable();
            // telecom
            $table->string('nohp')->nullable();
            $table->string('email')->nullable();
            // address
            $table->string('negara')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kota')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kodepos')->nullable();
            // photo
            $table->string('photo_id')->nullable();
            // pasien
            $table->boolean('status')->default(1);
            $table->string('jeniskelamin')->nullable();
            $table->date('tanggallahir')->nullable();
            $table->string('menikah')->nullable();
            $table->dateTime('kematian')->nullable();
            // keterangan tambahan
            $table->string('nokk')->nullable();
            $table->string('agama')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('pic')->nullable();
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
        Schema::dropIfExists('pasiens');
    }
};
