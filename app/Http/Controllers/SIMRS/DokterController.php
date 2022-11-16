<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\ParamedisDB;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $dokters = Dokter::get();
        $paramedis = ParamedisDB::where('nama_paramedis', 'LIKE', "%{$request->search}%")
            ->simplePaginate(20);
        $total_paramedis = ParamedisDB::count();
        return view('simrs.dokter_index', compact([
            'request',
            'dokters',
            'paramedis',
            'total_paramedis',
        ]));
    }
    public function create()
    {
        $api = new AntrianBPJSController();
        $dokters = $api->ref_dokter()->response;
        foreach ($dokters as $value) {
            Dokter::updateOrCreate(
                [
                    'kodedokter' => $value->kodedokter,
                ],
                [
                    'namadokter' => $value->namadokter,
                    'status' => 1,
                ]
            );
            $user = User::updateOrCreate([
                'email' => $value->kodedokter . '@gmail.com',
                'username' => $value->kodedokter,
            ], [
                'name' => $value->namadokter,
                'phone' => $value->kodedokter,
                'password' => bcrypt($value->kodedokter),
            ]);
            $user->assignRole('Dokter');
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
