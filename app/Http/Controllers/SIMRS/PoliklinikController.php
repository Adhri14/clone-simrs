<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\BPJS\Antrian\AntrianController;
use App\Http\Controllers\Controller;
use App\Models\BPJS\Antrian\PoliklinikAntrian;
use App\Models\Poliklinik;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PoliklinikController extends Controller
{
    public function poliklik_antrian_bpjs()
    {
        $controller = new AntrianController();
        $response = $controller->ref_poli();
        if ($response->status() == 200) {
            $polikliniks = $response->getData()->response;
            Alert::success($response->statusText(), 'Poliklinik Antrian BPJS');
        } else {
            $polikliniks = null;
            Alert::error($response->getData()->metadata->message . ' ' . $response->status());
        }
        $response = $controller->ref_poli_fingerprint();
        if ($response->status() == 200) {
            $fingerprint = $response->getData()->response;
            Alert::success($response->statusText(), 'Poliklinik Antrian BPJS');
        } else {
            $fingerprint = null;
            Alert::error($response->getData()->metadata->message . ' ' . $response->status(),  'Poliklinik Fingerprint Antrian BPJS');
        }
        return view('bpjs.antrian.poli', compact([
            'polikliniks',
            'fingerprint'
        ]));
    }
    public function poliklik_antrian_refresh()
    {
        $controller = new AntrianController();
        $response = $controller->ref_poli();
        if ($response->status() == 200) {
            $polikliniks = $response->getData()->response;
            foreach ($polikliniks as $value) {
                PoliklinikAntrian::firstOrCreate([
                    'kodePoli' => $value->kdpoli,
                    'namaPoli' => $value->nmpoli,
                    'kodeSubspesialis' => $value->kdsubspesialis,
                    'namaSubspesialis' => $value->nmsubspesialis,
                ]);
            }
            Alert::success($response->statusText(), 'Refresh Poliklinik Antrian BPJS Total : ' . count($polikliniks));
        } else {
            Alert::error($response->getData()->metadata->message . ' ' . $response->status());
        }
        return redirect()->route('pelayanan-medis.poliklinik_antrian');
    }
    public function poliklik_antrian_yanmed()
    {
        $polikliniks = PoliklinikAntrian::get();
        return view('simrs.pelyananmedis.poliklinik_antrian_bpjs', compact([
            'polikliniks'
        ]));
    }
}
