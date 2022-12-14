<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenunjangController extends Controller
{
    public function get_tarif_laboratorium(Request $request)
    {
        $query = DB::connection('mysql2')->select("CALL sp_panggil_tarif_laboratorium('3','')");
        return $query;
    }
    public function insert_layanan(Request $request)
    {
        // buat kode transaksi
        // insert layanan
    }
    public function print_nota(Request $request)
    {
        // buat kode transaksi
        // insert layanan
    }

}
