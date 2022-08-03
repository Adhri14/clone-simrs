<?php

namespace App\Http\Controllers;

use App\Models\AlasanMasukDB;
use App\Models\AlasanPulangDB;
use App\Models\KunjunganDB;
use App\Models\StatusKunjunganDB;
use Carbon\Carbon;
use Illuminate\Http\Request;


class KunjunganController extends Controller
{
    public function index(Request $request)
    {
        if (is_null($request->periode)) {
            $request['periode'] = Carbon::today()->format('d-m-Y') . ' - ' . Carbon::now()->format('d-m-Y');
        }
        $tanggal = explode(' - ', $request->periode);
        $tanggal_awal = Carbon::parse($tanggal[0]);
        $tanggal_akhir = Carbon::parse($tanggal[1]);

        $kunjungans = KunjunganDB::whereDate('tgl_masuk', '>=', $tanggal_awal)
            ->whereDate('tgl_masuk', '<=', $tanggal_akhir)
            ->with(['pasien', 'unit', 'penjamin'])
            ->orderByDesc('tgl_masuk')
            ->paginate();

        $status_kunjungan = StatusKunjunganDB::pluck('status_kunjungan', 'id');
        $alasan_masuk = AlasanMasukDB::pluck('alasan_masuk', 'id');
        $alasan_pulang = AlasanPulangDB::pluck('alasan_pulang', 'kode');

        return view('simrs.kunjungan_index', [
            'request' => $request,
            'kunjungans' => $kunjungans,
            'status_kunjungan' => $status_kunjungan,
            'alasan_masuk' => $alasan_masuk,
            'alasan_pulang' => $alasan_pulang,
            'tanggal' => $tanggal,
        ]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
