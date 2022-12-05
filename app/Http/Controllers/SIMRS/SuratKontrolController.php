<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\BPJS\Vclaim\VclaimController;
use App\Http\Controllers\Controller;
use App\Models\PoliklinikDB;
use App\Models\SuratKontrol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratKontrolController extends Controller
{
    public function store(Request $request)
    {
        $request['noSep'] = $request->nomorsep_suratkontrol;
        $request['tglRencanaKontrol'] = $request->tanggal_suratkontrol;
        $request['kodeDokter'] = $request->kodedokter_suratkontrol;
        $request['poliKontrol'] = $request->kodepoli_suratkontrol;
        $poli = PoliklinikDB::where('kodesubspesialis', $request->poliKontrol)->first();
        $request['user'] = Auth::user()->name;
        $vclaim = new VclaimController();
        $response = $vclaim->suratkontrol_insert($request);
        if ($response->status() == 200) {
            $suratkontrol = $response->getData()->response;
            SuratKontrol::create([
                "tglTerbitKontrol" => now()->format('Y-m-d'),
                "tglRencanaKontrol" => $suratkontrol->tglRencanaKontrol,
                "poliTujuan" => $request->poliKontrol,
                "namaPoliTujuan" => $poli->namasubspesialis,
                "kodeDokter" => $request->kodeDokter,
                "namaDokter" => $suratkontrol->namaDokter,
                "noSuratKontrol" => $suratkontrol->noSuratKontrol,
                "namaJnsKontrol" => "Surat Kontrol",
                "noSepAsalKontrol" => $request->noSep,
                "noKartu" => $suratkontrol->noKartu,
                "nama" => $suratkontrol->nama,
                "kelamin" => $suratkontrol->kelamin,
                "tglLahir" => $suratkontrol->tglLahir,
                "user" => Auth::user()->name,
            ]);
        }
        return $response;
    }
    public function update(Request $request)
    {
        $request['noSuratKontrol'] = $request->nomor_suratkontrol;
        $request['noSep'] = $request->nomorsep_suratkontrol;
        $request['kodeDokter'] = $request->kodedokter_suratkontrol;
        $request['poliKontrol'] = $request->kodepoli_suratkontrol;
        $request['tglRencanaKontrol'] = $request->tanggal_suratkontrol;
        $poli = PoliklinikDB::where('kodesubspesialis', $request->poliKontrol)->first();
        $request['user'] = Auth::user()->name;
        $vclaim = new VclaimController();
        $response = $vclaim->suratkontrol_update($request);
        if ($response->status() == 200) {
            $suratkontrol = $response->getData()->response;
            $sk = SuratKontrol::firstWhere('noSuratKontrol', $request->nomor_suratkontrol);
            $sk->update([
                "tglTerbitKontrol" => now()->format('Y-m-d'),
                "tglRencanaKontrol" => $suratkontrol->tglRencanaKontrol,
                "poliTujuan" => $request->poliKontrol,
                "namaPoliTujuan" => $poli->namasubspesialis,
                "kodeDokter" => $request->kodeDokter,
                "namaDokter" => $suratkontrol->namaDokter,
                "noSuratKontrol" => $suratkontrol->noSuratKontrol,
                "namaJnsKontrol" => "Surat Kontrol",
                "noSepAsalKontrol" => $request->noSep,
                "noKartu" => $suratkontrol->noKartu,
                "nama" => $suratkontrol->nama,
                "kelamin" => $suratkontrol->kelamin,
                "tglLahir" => $suratkontrol->tglLahir,
                "user" => Auth::user()->name,
            ]);
        }
        return $response;
    }
    public function destroy(Request $request)
    {
        return $request->all();
    }
}
