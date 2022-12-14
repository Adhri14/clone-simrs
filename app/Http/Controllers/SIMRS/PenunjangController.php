<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenunjangController extends Controller
{
    public function get_tarif_laboratorium(Request $request)
    {
        $query = DB::connection('mysql2')->select("CALL sp_panggil_tarif_laboratorium('3','')");
        return response($query);
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
        return response()->json('OK',200);
    }
    public function print_nota(Request $request)
    {
        // buat kode transaksi
        // insert layanan
    }
}
