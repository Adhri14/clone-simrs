<?php

namespace App\Http\Controllers;

use App\Models\PasienDB;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $pasiens = PasienDB::latest()
            ->where('no_rm', 'LIKE', "%{$request->search}%")
            ->orWhere('nama_px', 'LIKE', "%{$request->search}%")
            ->orWhere('nik_bpjs', 'LIKE', "%{$request->search}%")
            ->simplePaginate(20);
        $total_pasien = PasienDB::count();
        $pasien_jkn = PasienDB::where('no_Bpjs', '!=', '')->count();
        $pasien_nik = PasienDB::where('nik_bpjs', '!=', '')->count();
        // dd($total_pasien);
        return view('simrs.pasien_index', compact([
            'pasiens',
            'request',
            'total_pasien',
            'pasien_jkn',
            'pasien_nik',
        ]));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:users,nik,' . $request->user_id,
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'no_rm' => 'required',
        ]);
        $request['username'] = $request->nik;
        $request['tanggal_lahir'] = date('Y-m-d', strtotime($request->tanggal_lahir));
        // $user = User::updateOrCreate(['id' => $request->user_id], $request->except(['_token', 'role']));
        // $user->assignRole('Pasien');
        $pasien = PasienDB::updateOrCreate(['no_rm' => $request->no_rm], [
            'nik_bpjs' => $request->nik,
            'nama_px' => $request->nama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tanggal_lahir,
        ]);
        Alert::success('Success', 'Data Pasien Telah Disimpan');
        return redirect()->route('pasien.index');
    }
    public function edit($no_rm)
    {
        $pasien = PasienDB::firstWhere('no_rm', $no_rm);
        return view('simrs.pasien_edit', compact('pasien'));
    }
    public function destroy($no_rm)
    {
        $pasien = PasienDB::firstWhere('no_rm', $no_rm);
        $pasien->delete();
        Alert::success('Success', 'Data Pasien Telah Dihapus');
        return redirect()->route('pasien.index');
    }
}
