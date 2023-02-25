<?php

namespace App\Http\Controllers;

use App\Models\ICD10;
use App\Models\Kunjungan;
use App\Models\KunjunganDB;
use App\Models\SIMRS\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class KPOController extends Controller
{
    public function index(Request $request)
    {
        $icd10 = ICD10::limit(10)->get();
        $units = Unit::whereIn('kelas_unit', ['1', '2'])->pluck('nama_unit', 'kode_unit');
        $kunjungans = null;
        if (isset($request->unit)) {
            $kunjungans = KunjunganDB::with(['pasien'])
                ->whereDate('tgl_masuk', $request->tanggal)
                ->where('kode_unit', $request->unit)
                ->get();

            Alert::success("Success", "Data kunjungan ditemukan " . $kunjungans->count() . " pasien");
        }
        return view('simrs.kpo_create', compact([
            'icd10',
            'request',
            'units',
            'kunjungans',
        ]));
    }
    public function kunjungan_tanggal($tanggal)
    {
        $kunjungans = KunjunganDB::where('status_kunjungan', 1)
            ->whereBetween('tgl_masuk', [Carbon::parse($tanggal)->startOfDay(), Carbon::parse($tanggal)->endOfDay()])
            ->get();
        return response()->json($kunjungans);
        // dd($kunjungans);
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
