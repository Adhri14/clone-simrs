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
    public function index_daerah(Request $request)
    {
        if ($request->tanggal == null) {
            $tanggal = null;
            $kunjungans = null;
        } else {

            $tanggal = explode(' - ', $request->tanggal);
            $tanggal_awal = Carbon::parse($tanggal[0])->format('Y-m-d H:m:s');
            $tanggal_akhir = Carbon::parse($tanggal[1])->format('Y-m-d H:m:s');
            $kunjungans =  DB::connection('mysql2')
                ->table('ts_kunjungan')
                ->where('no_sep', '!=', '')
                ->whereBetween('tgl_masuk', [$tanggal_awal, $tanggal_akhir])
                ->join('mt_pasien', 'ts_kunjungan.no_rm', '=', 'mt_pasien.no_rm')
                ->join('mt_kecamatan', 'mt_pasien.kode_kecamatan', '=', 'mt_kecamatan.kode_kecamatan')
                ->join('mt_kabupaten_kota', 'mt_kecamatan.kode_kabupaten_kota', '=', 'mt_kabupaten_kota.kode_kabupaten_kota')
                ->select(DB::raw(
                    "mt_pasien.kode_kecamatan,
                    mt_kecamatan.nama_kecamatan,
                    mt_kabupaten_kota.nama_kabupaten_kota,
                    ts_kunjungan.no_rm,
                    mt_pasien.nama_px,
                    ts_kunjungan.kode_unit,
                    ts_kunjungan.status_kunjungan,
                    ts_kunjungan.tgl_masuk,
                    month(ts_kunjungan.tgl_masuk) as bulan,
                    year(ts_kunjungan.tgl_masuk) as tahun,
                    ts_kunjungan.no_sep"
                ))
                // ->limit(10)
                ->get();

            dd($kunjungans);
        }
        return view('simrs.rekammedis.index_daerah', compact([
            'request',
            'kunjungans',
            'tanggal',
        ]));
    }
}
