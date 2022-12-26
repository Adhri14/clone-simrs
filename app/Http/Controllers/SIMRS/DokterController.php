<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\BPJS\Antrian\AntrianController;
use App\Http\Controllers\Controller;
use App\Models\BPJS\Antrian\DokterAntrian;
use App\Models\Dokter;
use App\Models\ParamedisDB;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class DokterController extends Controller
{
    public function dokter_antrian_bpjs()
    {
        $controller = new AntrianController();
        $response = $controller->ref_dokter();
        if ($response->status() == 200) {
            $dokters = $response->getData()->response;
            Alert::success($response->statusText(), 'Dokter Antrian BPJS Total : ' . count($dokters));
        } else {
            $dokters = null;
            Alert::error($response->getData()->metadata->message . ' ' . $response->status());
        }
        return view('bpjs.antrian.dokter', compact([
            'dokters'
        ]));
    }
    public function dokter_antrian_refresh(Request $request)
    {
        $controller = new AntrianController();
        $response = $controller->ref_dokter();
        if ($response->status() == 200) {
            $dokters = $response->getData()->response;
            foreach ($dokters as $value) {
                DokterAntrian::firstOrCreate([
                    'kodeDokter' => $value->kodedokter,
                    'namaDokter' => $value->namadokter,
                ]);
            }
            Alert::success($response->statusText(), 'Refresh Dokter Antrian BPJS Total : ' . count($dokters));
        } else {
            Alert::error($response->getData()->metadata->message . ' ' . $response->status());
        }
        return redirect()->route('pelayanan-medis.dokter_antrian');
    }
    public function dokter_antrian_yanmed(Request $request)
    {
        $dokters =  DokterAntrian::get();
        return view('simrs.pelyananmedis.dokter_antrian', compact([
            'dokters'
        ]));
    }
}
