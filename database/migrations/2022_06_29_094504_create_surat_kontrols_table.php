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
            $table->date('tglTerbitKontrol');
            $table->date('tglRencanaKontrol');
            $table->string('poliTujuan');
            $table->string('namaPoliAsal');
            $table->string('kodeDokter');
            $table->string('namaDokter');
            $table->string('noSuratKontrol')->unique()->index();
            $table->string('noSepAsalKontrol');
            $table->string('namaJnsKontrol');
            $table->string('noKartu');
            $table->string('nama');
            $table->string('kelamin');
            $table->string('tglLahir');
            $table->string('user');
            $table->string('namaDiagnosa')->nullable();;
            $table->string('noRujukan')->nullable();
            $table->string('jnsPelayanan')->nullable();
            $table->string('jnsKontrol')->nullable();
            $table->string('poliAsal')->nullable();
            $table->string('namaPoliTujuan')->nullable();
            $table->string('tglSEP')->nullable();
            $table->string('terbitSEP')->nullable();
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
