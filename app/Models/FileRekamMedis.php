<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRekamMedis extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'jkn_scan_file_rm';

    protected $fillable = [
        'norm',
        'nama',
        'nomorkartu',
        'nik',
        'nohp',
        'tanggallahir',
        'jenisberkas',
        'namafile',
        'tanggalscan',
        'fileurl',
    ];
}
