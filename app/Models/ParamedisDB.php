<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParamedisDB extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'mt_paramedis';
    protected $fillable = [
        'id_layanan_detail',
        'row_id_header',
        'kode_layanan_header',
        'kode_tarif_detail',
        'total_tarif',
        'tagihan_pribadi',
        'tagihan_penjamin',
        'jumlah_layanan',
        'total_layanan',
        'grantotal_layanan',
        'kode_dokter1',
        'tgl_layanan_detail',
    ];
}
