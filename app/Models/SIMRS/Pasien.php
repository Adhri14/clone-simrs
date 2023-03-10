<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     // identifier
    //     'norm',
    //     'nokartu',
    //     'nik',
    //     'ihs',
    //     // name
    //     'nama',
    //     // telecom
    //     'nohp',
    //     'email',
    //     // address
    //     'negara',
    //     'provinsi',
    //     'kota',
    //     'kecamatan',
    //     'desa',
    //     'alamat',
    //     'rt',
    //     'rw',
    //     'kodepos',
    //     // photo
    //     'photo_id',
    //     // pasien
    //     'status',
    //     'jeniskelamin',
    //     'tanggallahir',
    //     'menikah',
    //     'kematian',
    //     // keterangan tambahan
    //     'nokk',
    // ];

    protected $connection = 'mysql2';
    protected $table = 'mt_pasien';
    protected $primaryKey = 'no_rm';
    public $timestamps = false;
    const CREATED_AT = 'tgl_entry';
    // const UPDATED_AT = 'last_update';

    protected $fillable = [
        "no_Bpjs",
        "nik_bpjs",
        "no_rm",
        "nama_px",
        "jenis_kelamin",
        "tgl_lahir",
        "no_tlp",
        "no_hp",
        "alamat",
        "kode_propinsi",
        "kode_kabupaten",
        "kode_kecamatan",
        "kode_desa",
    ];
    public function kecamatans()
    {
        return $this->hasOne(Kecamatan::class, 'kode_kecamatan', 'kecamatan');
    }
    public function kabupaten()
    {
        return $this->hasOne(Kecamatan::class, 'kode_kecamatan', 'kecamatan');
    }
}
