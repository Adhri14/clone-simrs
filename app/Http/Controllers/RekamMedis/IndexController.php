<?php

namespace App\Http\Controllers\RekamMedis;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\KunjunganDB;
use App\Models\UnitDB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index_penyakit_rajal(Request $request)
    {
        if ($request->tanggal == null) {
            $tanggal = null;
            $diagnosa = null;
        } else {
            $tanggal = explode(' - ', $request->tanggal);
            $tanggal_awal = Carbon::parse($tanggal[0])->format('Y-m-d H:m:s');
            $tanggal_akhir = Carbon::parse($tanggal[1])->format('Y-m-d H:m:s');
            $diagnosa =  Diagnosa::whereBetween('input_date', [$tanggal_awal, $tanggal_akhir])
                ->where('diag_utama', $request->diagnosa)
                ->get();
        }
        return view('simrs.rekammedis.index_penyakit_rajal', compact([
            'request',
            'diagnosa',
            'tanggal'
        ]));
    }
    public function index_dokter(Request $request)
    {
        if ($request->tanggal == null) {
            $tanggal = null;
            $kunjungans = null;
        } else {
            $tanggal = explode(' - ', $request->tanggal);
            $tanggal_awal = Carbon::parse($tanggal[0])->format('Y-m-d H:m:s');
            $tanggal_akhir = Carbon::parse($tanggal[1])->format('Y-m-d H:m:s');
            $kunjungans = KunjunganDB::whereBetween('tgl_masuk', [$tanggal_awal, $tanggal_akhir])
                ->where('kode_unit', $request->unit)
                ->get();

            // dd($kunjungans);
            // $kunjungans =  Diagnosa::whereBetween('input_date', [$tanggal_awal, $tanggal_akhir])
            //     ->where('diag_utama', $request->diagnosa)
            //     ->get();
        }
        $units = UnitDB::pluck('nama_unit', 'kode_unit');
        return view('simrs.rekammedis.index_dokter', compact([
            'request',
            'kunjungans',
            'tanggal',
            'units',
        ]));
    }
}
