<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\BPJS\Antrian\AntrianController;
use App\Http\Controllers\Controller;
use App\Models\BPJS\Antrian\PoliklinikAntrian;
use App\Models\Poliklinik;
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
        $poli_jkn_simrs = Poliklinik::get();
        return view('bpjs.antrian.poliklinik', compact([
            'polikliniks',
            'fingerprint',
            'poli_jkn_simrs',
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
    public function index()
    {
        $polis = Poliklinik::get();
        return view('simrs.poli_index', [
            'polis' => $polis
        ]);
    }
    public function create()
    {
        $api = new AntrianBPJSController();
        $poli = $api->ref_poli()->response;
        $poliDB = UnitDB::where('KDPOLI', '!=', null)->get(['kode_unit', 'nama_unit', 'KDPOLI',]);
        foreach ($poli as $value) {
            if ($value->kdpoli == $value->kdsubspesialis) {
                $subpesialis = 0;
            } else {
                $subpesialis = 1;
            }
            Poliklinik::updateOrCreate(
                [
                    'kodepoli' => $value->kdpoli,
                    'kodesubspesialis' => $value->kdsubspesialis,
                ],
                [
                    'namapoli' => $value->nmpoli,
                    'namasubspesialis' => $value->nmsubspesialis,
                    'subspesialis' => $subpesialis,
                    'lokasi' => 1,
                    'loket' => 1,
                    'status' => 0,
                ]
            );
        }
        // update aktif
        $poli_jkn = Poliklinik::get();
        foreach ($poli_jkn as $poli) {
            foreach ($poliDB as $unit) {
                if ($poli->kodesubspesialis ==  $unit->KDPOLI) {
                    if (isset($unit->lokasi)) {
                        $lokasi = $unit->lokasi->lokasi;
                        $loket = $unit->lokasi->loket;
                    } else {
                        $lokasi = 0;
                        $loket = 0;
                    }
                    $poli->update([
                        'status' => 1,
                        'lokasi' => $lokasi,
                        'loket' => $loket,
                    ]);
                    $user = User::updateOrCreate([
                        'email' => $poli->kodesubspesialis . '@gmail.com',
                        'username' => $poli->kodesubspesialis,
                    ], [
                        'name' => 'ADMIN POLI ' . $poli->namasubspesialis,
                        'phone' => '089529909036',
                        'password' => bcrypt('adminpoli'),
                    ]);
                    $user->assignRole('Poliklinik');
                }
            }
        }
        Alert::success('Success', 'Refresh Poliklinik Berhasil');
        return redirect()->route('poli.index');
    }
    public function edit($id)
    {
        $poli = Poliklinik::find($id);
        if ($poli->status == '0') {
            $status = 1;
            $keterangan = 'Aktifkan';
        } else {
            $status = 0;
            $keterangan = 'Non-Aktifkan';
        }
        $poli->update([
            'status' => $status,
        ]);
        Alert::success('Success', 'Poliklinik ' . $poli->namasubspesialis . ' Telah Di ' . $keterangan);
        return redirect()->route('poli.index');
    }
    public function show($id)
    {
        $poli = Poliklinik::find($id);
        return response()->json($poli);
    }
    public function store(Request $request)
    {
        $poli = Poliklinik::find($request->idpoli);
        if ($request->status == "true") {
            $request['status'] = 1;
        } else {
            $request['status'] = 0;
        }
        $poli->update([
            'lokasi' => $request->lokasi,
            'loket' => $request->lantaipendaftaran,
            'status' => $request->status,
        ]);
        Alert::success('Success', 'Poliklinik ' . $poli->namasubspesialis . ' telah diperbaharui');
        return redirect()->route('poli.index');
    }
}
