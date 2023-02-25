<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\AlasanMasukDB;
use App\Models\AlasanPulangDB;
use App\Models\KunjunganDB;
use App\Models\StatusKunjunganDB;
use Illuminate\Http\Request;

class KunjunganController extends ApiController
{
    public function index(Request $request)
    {
        if (empty($request->search)) {
            $kunjungans = KunjunganDB::with(['pasien', 'unit', 'penjamin'])
                ->orderByDesc('tgl_masuk')
                ->paginate();
        } else {
            $kunjungans = KunjunganDB::with(['pasien', 'unit', 'penjamin'])
                ->where('no_rm',  $request->search)
                ->orderByDesc('tgl_masuk')
                ->paginate();
        }
        $status_kunjungan = StatusKunjunganDB::pluck('status_kunjungan', 'id');
        $alasan_masuk = AlasanMasukDB::pluck('alasan_masuk', 'id');
        $alasan_pulang = AlasanPulangDB::pluck('alasan_pulang', 'kode');
        return view('simrs.kunjungan_index', [
            'request' => $request,
            'kunjungans' => $kunjungans,
            'status_kunjungan' => $status_kunjungan,
            'alasan_masuk' => $alasan_masuk,
            'alasan_pulang' => $alasan_pulang,
        ]);
    }
    public function show($kodekunjungan)
    {
        $kunjungan = KunjunganDB::firstWhere('kode_kunjungan', $kodekunjungan);
        $data['noSEP'] = $kunjungan->no_sep;
        $data['namaPasien'] = $kunjungan->pasien->nama_px;
        $data['kodePoli'] = $kunjungan->unit->KDPOLI;
        $data['kodeDokter'] = $kunjungan->dokter ? (string) $kunjungan->dokter->kode_dokter_jkn : null;
        return $this->sendResponse('OK', $data, 200);
    }
    public function edit($kodekunjungan)
    {
        $kunjungan = KunjunganDB::firstWhere('kode_kunjungan', $kodekunjungan);
        return response()->json($kunjungan);
    }
    public function kunjungan_tanggal($tanggal)
    {
        $kunjungans = KunjunganDB::whereDate('tgl_masuk',$tanggal)->get(['no_rm','kode_unit','kode_paramedis','tgl_masuk']);
        return response()->json($kunjungans);
    }
}
