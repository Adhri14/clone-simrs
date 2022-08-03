<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKontrol extends Model
{
    use HasFactory;

    protected $fillable = [
        "noSuratKontrol",
        "noRujukan",
        "jnsPelayanan",
        "jnsKontrol",
        "namaJnsKontrol",
        "tglRencanaKontrol",
        "tglTerbitKontrol",
        "noSepAsalKontrol",
        "poliAsal",
        "namaPoliAsal",
        "poliTujuan",
        "namaPoliTujuan",
        "tglSEP",
        "kodeDokter",
        "namaDokter",
        "noKartu",
        "nama",
        "kelamin",
        "tglLahir",
        "namaDiagnosa",
        "terbitSEP",
        "user",
    ];
}
