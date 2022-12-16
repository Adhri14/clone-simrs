<?php

namespace App\Models\SIMRS;

use App\Models\LayananDB;
use App\Models\ParamedisDB;
use App\Models\PasienDB;
use App\Models\PenjaminSimrs;
use App\Models\TarifLayanan;
use App\Models\TarifLayananDB;
use App\Models\UnitDB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLayananDB extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'ts_layanan_header_order';

    protected $appends = ['nama_pasien', 'nama_unit_pengirim', 'nama_dokter_pic', 'nama_layanan', 'nama_penjamin', 'diskon_dokter', 'cyto',];

    public function getNamaPasienAttribute()
    {
        if (isset($this->no_rm)) {
            $pasien = PasienDB::firstWhere('no_rm', $this->no_rm)->nama_px;
        } else {
            $pasien = '';
        }
        return $pasien;
    }
    public function getNamaDokterPicAttribute()
    {
        if (isset($this->pic)) {
            $dokter = ParamedisDB::firstWhere('kode_paramedis', $this->pic)->nama_paramedis;
        } else {
            $dokter = '';
        }
        return $dokter;
    }
    public function getNamaLayananAttribute()
    {
        $layanandetail = OrderLayananDetailDB::where('kode_layanan_header', $this->kode_layanan_header)->first()->kode_tarif_detail;
        $layanan = TarifLayananDB::firstWhere('KODE_TARIF_HEADER', substr($layanandetail, 0, -1));
        return $layanan->NAMA_TARIF ?? null;
    }
    public function getNamaPenjaminAttribute()
    {
        if (isset($this->kode_penjaminx)) {
            $penjamin = PenjaminSimrs::firstWhere('kode_penjamin', $this->kode_penjaminx)->nama_penjamin;
        } else {
            $penjamin = '';
        }
        return $penjamin;
    }
    public function getNamaUnitPengirimAttribute()
    {
        $unit = UnitDB::where('kode_unit', $this->unit_pengirim)->first()->nama_unit;
        return $unit ?? null;
    }
    public function getDiskonDokterAttribute()
    {
        $diskon = OrderLayananDetailDB::where('kode_layanan_header', $this->kode_layanan_header)->first()->diskon_dokter;
        return $diskon ?? null;
    }
    public function getCytoAttribute()
    {
        $cyto = OrderLayananDetailDB::where('kode_layanan_header', $this->kode_layanan_header)->first()->cyto;
        return $cyto ?? null;
    }
}
