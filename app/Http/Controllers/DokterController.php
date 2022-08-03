<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\AntrianBPJSController;
use App\Models\Dokter;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class DokterController extends Controller
{
    public function index()
    {
        $dokters = Dokter::get();
        return view('simrs.dokter_index', [
            'dokters' => $dokters,
        ]);
    }

    public function create()
    {
        $api = new AntrianBPJSController();
        $poli = $api->ref_dokter()->response;
        foreach ($poli as $value) {
            Dokter::updateOrCreate(
                [
                    'kodedokter' => $value->kodedokter,
                ],
                [
                    'namadokter' => $value->namadokter,
                ]
            );
        }
        Alert::success('Success', 'Refresh Data Dokter Berhasil');
        return redirect()->route('dokter.index');
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
