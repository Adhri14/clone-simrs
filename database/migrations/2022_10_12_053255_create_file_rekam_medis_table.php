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
        Schema::create('file_rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal');
            $table->string('nama');
            $table->string('norm');
            $table->string('kode')->nullable();
            $table->string('tipekunjungan')->nullable();
            $table->string('counter')->nullable();
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
        Schema::dropIfExists('file_rekam_medis');
    }
};
