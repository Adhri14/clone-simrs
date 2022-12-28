<?php

namespace App\Models\BPJS\Antrian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDokterAntrian extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'jkn_jadwal_dokter';
    protected $fillable = [
        'kodepoli',
        'namapoli',
        'kodesubspesialis',
        'namasubspesialis',
        'namadokter',
        'kodedokter',
        'hari',
        'namahari',
        'jadwal',
        'kapasitaspasien',
        'libur',
    ];
}
