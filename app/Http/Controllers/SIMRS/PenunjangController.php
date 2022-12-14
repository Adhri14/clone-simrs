<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\API\ApiController;
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
    public function get_order_layanan(Request $request)
    {
        $data = OrderLayananDB::get();
        return $this->sendResponse('OK', $data);
    }
    public function insert_layanan(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "nomorkartu" =>  "required|digits:13|numeric",
            "nik" =>  "required|digits:16|numeric",
            "nohp" => "required|numeric",
            "kodepoli" =>  "required",
            "norm" =>  "required",
            "pasienbaru" =>  "required",
            "tanggalperiksa" =>  "required|date|date_format:Y-m-d",
            "kodedokter" =>  "required",
            "jampraktek" =>  "required",
            "jeniskunjungan" => "required",
            "jenispasien" =>  "required",
            "namapoli" =>  "required",
            "namadokter" =>  "required",
            "nomorantrean" =>  "required",
            "angkaantrean" =>  "required",
            "estimasidilayani" =>  "required",
            "sisakuotajkn" =>  "required",
            "kuotajkn" => "required",
            "sisakuotanonjkn" => "required",
            "kuotanonjkn" => "required",
            "keterangan" =>  "required",
            "nama" =>  "required",
        ]);
        if ($validator->fails()) {
            return response()->json('OK', 400);
        }
        return response()->json('OK');
    }
    public function print_nota(Request $request)
    {
        // buat kode transaksi
        // insert layanan
    }
}
