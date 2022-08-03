<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\AntrianBPJSController;
use App\Models\Dokter;
use App\Models\JadwalPoli;
use App\Models\Poliklinik;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PoliklinikController extends Controller
{
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
                ]
            );
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
}
