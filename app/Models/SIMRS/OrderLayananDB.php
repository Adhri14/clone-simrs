<?php

namespace App\Models\SIMRS;

use App\Models\ParamedisDB;
use App\Models\PasienDB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLayananDB extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'ts_layanan_header_order';
    protected $primaryKey = 'idx';

    protected $appends = ['nama_dokter_pic'];
    // public function getNamaPasienAttribute()
    // {
    //     $pasien = PasienDB::firstWhere('no_rm', $this->no_rm)->nama_px;
    //     return $pasien;
    // }
    public function getNamaDokterPicAttribute()
    {
        $pasien = ParamedisDB::firstWhere('kode_paramedis', $this->pic)->nama_paramedis;
        return $pasien;
    }
}
