<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\API\ApiController;
use App\Models\KunjunganDB;
use App\Models\ParamedisDB;
use App\Models\PasienDB;
use App\Models\SIMRS\OrderLayananDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenunjangController extends ApiController
{
    public function get_tarif_laboratorium(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kelas" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        if ($request->nama == null) {
            $sql = "CALL sp_panggil_tarif_laboratorium(" . $request->kelas . ",'')";
        } else {
            $sql = "CALL sp_panggil_tarif_laboratorium(" . $request->kelas . ",'" . $request->nama . "')";
        }
        $query = DB::connection('mysql2')->select($sql);
        return $this->sendResponse('OK', $query);
    }
    public function cari_pasien(Request $request)
    {
        if ($request->norm) {
            $pasien = PasienDB::where('no_rm', $request->norm)->first();
            if ($pasien == null) {
                return $this->sendError('No RM tidak ditemukan', null, 404);
            }
        } else if ($request->nama) {
            $pasien = PasienDB::where('nama_px', 'LIKE', '%' . $request->nama . '%')
                ->get();
            if ($pasien == null) {
                return $this->sendError("Nama Pasien " . $request->nama . " Tidak Ditemukan.", null, 404);
            }
        } else {
            return $this->sendError("Silahkan cari berdasarkan Nama atau No RM", null, 400);
        }
        return $this->sendResponse('OK', $pasien);
    }
    public function cari_dokter(Request $request)
    {
        if ($request->nama) {
            $dokters = ParamedisDB::where('act', 1)->where('nama_paramedis', 'LIKE', "%" . $request->nama . "%")->get();
        } else {
            $dokters = ParamedisDB::where('act', 1)->get();
        }
        return $this->sendResponse('OK', $dokters);
    }
    public function get_kunjungan_pasien(Request $request)
    {
        $request['tanggal'] = '2022-12-16';
        $kunjungan = KunjunganDB::whereDate('tgl_masuk', $request->tanggal)
            ->whereIn('kode_unit',  ['3002', '3003'])
            ->get();
        return $this->sendResponse('OK', $kunjungan);
    }
    public function get_order_layanan(Request $request)
    {
        $data = OrderLayananDB::get();
        return $this->sendResponse('OK', $data);
    }
    public function insert_layanan(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",

        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        return response()->json('OK', 200);
    }
    public function get_ris_order(Request $request)
    {
        // $query = DB::connection('mysql2')->select("CALL RIS_order_save('','','')");
        return $this->sendResponse('OK', 'query');
    }
    public function print_nota(Request $request)
    {
        // buat kode transaksi
        // insert layanan
    }
}
