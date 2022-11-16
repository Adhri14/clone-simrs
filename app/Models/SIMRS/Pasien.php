<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;
    protected $fillable = [
        // identifier
        'norm',
        'nokartu',
        'nik',
        'ihs',
        // name
        'nama',
        // telecom
        'nohp',
        'email',
        // address
        'negara',
        'provinsi',
        'kota',
        'kecamatan',
        'desa',
        'alamat',
        'rt',
        'rw',
        'kodepos',
        // photo
        'photo_id',
        // pasien
        'status',
        'jeniskelamin',
        'tanggallahir',
        'menikah',
        'kematian',
        // keterangan tambahan
        'nokk',
    ];
}
