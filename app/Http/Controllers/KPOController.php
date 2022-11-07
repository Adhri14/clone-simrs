<?php

namespace App\Http\Controllers;

use App\Models\ICD10;
use App\Models\KunjunganDB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KPOController extends Controller
{
    public function index(Request $request)
    {
        $roles = ICD10::limit(10)->get();
        return view('simrs.kpo_create', compact(['roles', 'request']));
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
