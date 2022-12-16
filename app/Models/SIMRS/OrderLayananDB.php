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
    protected $table = 'erm_order_header';
    protected $primaryKey = 'idx';

    protected $appends = ['nama_pasien', 'nama_dokter_pic', 'nama_dokter_pengirim'];

    public function getNamaPasienAttribute()
    {
        $pasien = PasienDB::firstWhere('no_rm', $this->no_rm)->nama_px;
        return $pasien;
    }
    public function getNamaDokterPicAttribute()
    {
        $pasien = ParamedisDB::firstWhere('kode_paramedis', $this->pic1)->nama_paramedis;
        return $pasien;
    }
    public function getNamaDokterPengirimAttribute()
    {
        $pasien = ParamedisDB::firstWhere('kode_paramedis', $this->Dokter_pengirim)->nama_paramedis;
        return $pasien;
    }
}
