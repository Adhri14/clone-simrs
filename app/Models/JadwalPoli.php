<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPoli extends Model
{
    use HasFactory;
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
        'libur',
        'kapasitaspasien',
    ];
}
