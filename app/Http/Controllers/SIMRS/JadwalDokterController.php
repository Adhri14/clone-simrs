<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\BPJS\Antrian\AntrianController;
use App\Http\Controllers\Controller;
use App\Models\BPJS\Antrian\JadwalDokterAntrian;
use App\Models\BPJS\Antrian\PoliklinikAntrian;
use App\Models\SIMRS\JadwalDokter;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class JadwalDokterController extends Controller
{
    public function jadwal_dokter_bpjs(Request $request)
    {
        $controller = new AntrianController();
        $response = $controller->ref_dokter();
        if ($response->status() == 200) {
            $dokters = $response->getData()->response;
        } else {
            $dokters = null;
        }
        // get poli
        $response = $controller->ref_poli();
        if ($response->status() == 200) {
            $polikliniks = $response->getData()->response;
        } else {
            $polikliniks = null;
        }
        // get jadwal
        $jadwals = null;
        if (isset($request->kodePoli)) {
            $response = $controller->ref_jadwal_dokter($request);
            if ($response->status() == 200) {
                $jadwals = $response->getData()->response;
                Alert::success($response->statusText(), 'Jadwal Dokter Antrian BPJS Total : ' . count($jadwals));
            } else {
                Alert::error($response->getData()->metadata->message . ' ' . $response->status());
            }
        }
        return view('bpjs.antrian.jadwal_dokter', compact([
            'request',
            'dokters',
            'polikliniks',
            'jadwals',
        ]));
    }
    public function jadwaldokter_simrs(Request $request)
    {
        // get jadwal
        $jadwals = null;
        if (isset($request->kodePoli)) {
            $controller = new AntrianController();
            $response = $controller->ref_jadwal_dokter($request);
            if ($response->status() == 200) {
                $jadwals = $response->getData()->response;
                // Alert::success($response->statusText(), 'Jadwal Dokter Antrian BPJS Total : ' . count($jadwals));
            } else {
                Alert::error($response->getData()->metadata->message . ' ' . $response->status());
            }
        }

        $polikliniks = PoliklinikAntrian::get();
        $jadwal_antrian = JadwalDokterAntrian::get();
        return view('simrs.pelyananmedis.jadwal_dokter', compact([
            'request',
            'polikliniks',
            'jadwals',
            'jadwal_antrian',
        ]));
    }
    public function jadwaldokter_add(Request $request)
    {
        $request->validate([
            'kodePoli' => 'required',
            'namaPoli' => 'required',
            'kodeSubspesialis' => 'required',
            'namaSubspesialis' => 'required',
            'kodeDokter' => 'required',
            'namaDokter' => 'required',
            'hari' => 'required',
            'namaHari' => 'required',
            'jadwal' => 'required',
            'kapasitasPasien' => 'required',
            'libur' => 'required',
        ]);
        JadwalDokterAntrian::firstOrCreate([
            'kodepoli' => $request->kodePoli,
            'namapoli' => $request->namaPoli,
            'kodesubspesialis' => $request->kodeSubspesialis,
            'namasubspesialis' => $request->namaSubspesialis,
            'kodedokter' => $request->kodeDokter,
            'namadokter' => $request->namaDokter,
            'hari' => $request->hari,
            'namahari' => $request->namaHari,
        ], [
            'jadwal' => $request->jadwal,
            'kapasitaspasien' => $request->kapasitasPasien,
            'libur' => $request->libur,
        ]);
        Alert::success('Success', 'Jadwal Dokter Telah Ditambahkan');
        return redirect()->back();
    }
    public function jadwaldokter_get($id, Request $request)
    {
        $jadwal = JadwalDokterAntrian::find($id);
        return response()->json($jadwal);
    }
    public function jadwaldokter_update(Request $request)
    {
        $request->validate([
            'jadwal' => 'required',
            'kapasitaspasien' => 'required',
        ]);
        if ($request->libur == "true") {
            $libur = 1;
        } else {
            $libur = 0;
        }
        $jadwal = JadwalDokterAntrian::find($request->idjadwal);
        $jadwal->update([
            'jadwal' => $request->jadwal,
            'kapasitaspasien' => $request->kapasitaspasien,
            'libur' => $libur,
        ]);
        Alert::success('Success', 'Jadwal Dokter Telah Diupdate');
        return redirect()->back();
    }
    public function jadwaldokter_delete(Request $request)
    {
        $request->validate([
            'idjadwal' => 'required',
        ]);
        $jadwal = JadwalDokterAntrian::find($request->idjadwal);
        $jadwal->delete();
        Alert::success('Success', 'Jadwal Dokter Telah Dihapus');
        return redirect()->back();
    }
}
