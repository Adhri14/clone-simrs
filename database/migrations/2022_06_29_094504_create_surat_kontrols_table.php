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
        Schema::create('surat_kontrols', function (Blueprint $table) {
            $table->id();
            $table->string('noSuratKontrol')->unique()->index();
            $table->string('noRujukan');
            $table->string('jnsPelayanan')->nullable();
            $table->string('jnsKontrol')->nullable();
            $table->string('namaJnsKontrol')->nullable();
            $table->date('tglRencanaKontrol')->nullable();
            $table->date('tglTerbitKontrol')->nullable();
            $table->string('noSepAsalKontrol');
            $table->string('poliAsal')->nullable();
            $table->string('namaPoliAsal')->nullable();
            $table->string('poliTujuan')->nullable();
            $table->string('namaPoliTujuan')->nullable();
            $table->string('tglSEP')->nullable();
            $table->string('kodeDokter')->nullable();
            $table->string('namaDokter')->nullable();
            $table->string('noKartu')->nullable();
            $table->string('nama')->nullable();
            $table->string('kelamin')->nullable();
            $table->string('tglLahir')->nullable();
            $table->string('namaDiagnosa')->nullable();
            $table->string('terbitSEP')->nullable();
            $table->string('user')->nullable();
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
        Schema::dropIfExists('surat_kontrols');
    }
};
