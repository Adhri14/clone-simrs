<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\KunjunganDB;
use App\Models\UnitDB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimrsController extends Controller
{
    public function dashboard(Request $request)
    {
        $kunjungan_tahun = KunjunganDB::where('status_kunjungan', '<=', 2)
            ->select(
                DB::raw("(DATE_FORMAT(tgl_masuk, '%Y')) as year"),
                DB::raw("count(no_sep) as total_jkn"),
                DB::raw("count(*) as total"),
            )
            ->groupBy(DB::raw("DATE_FORMAT(tgl_masuk, '%Y')"))->get();

        $kunjungan_bulan = KunjunganDB::whereYear('tgl_masuk', 2022)
            ->where('status_kunjungan', '<=', 2)
            ->select(
                DB::raw("(DATE_FORMAT(tgl_masuk, '%Y-%m')) as month"),
                DB::raw("count(no_sep) as total_jkn"),
                DB::raw("count(*) as total"),
            )
            ->groupBy(DB::raw("DATE_FORMAT(tgl_masuk, '%Y-%m')"))->get();

        $kunjungan_tanggal = KunjunganDB::whereYear('tgl_masuk', 2022)
            ->whereMonth('tgl_masuk', 11)
            ->where('status_kunjungan', '<=', 2)
            ->select(
                DB::raw("(DATE_FORMAT(tgl_masuk, '%d %b')) as date"),
                DB::raw("count(no_sep) as total_jkn"),
                DB::raw("count(*) as total"),
            )
            ->groupBy(DB::raw("DATE_FORMAT(tgl_masuk, '%d %b')"))->get();
        return view('simrs.dashboard', compact([
            'request',
            'kunjungan_bulan',
            'kunjungan_tahun',
            'kunjungan_tanggal',
        ]));
    }
}
