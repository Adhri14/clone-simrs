<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKontrol extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'jkn_surat_kontrol';
    protected $fillable = [
        "noSuratKontrol",
        "namaJnsKontrol",
        "tglRencanaKontrol",
        "tglTerbitKontrol",
        "noSepAsalKontrol",
        "kodeDokter",
        "namaDokter",
        "noKartu",
        "nama",
        "kelamin",
        "tglLahir",
        "user",
    ];
}
