<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\BukuTamu;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class BukuTamuController extends Controller
{
    public function bukutamu(Request $request)
    {
        return view('admin.bukutamu');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'organisasi' => 'required',
            'phone' => 'required|numeric',
            'alamat' => 'required',
            'tujuan' => 'required',
        ]);
        BukuTamu::create([
            'name' => $request->name,
            'organisasi' => $request->organisasi,
            'phone' => $request->phone,
            'alamat' => $request->alamat,
            'tujuan' => $request->tujuan,
        ]);
        Alert::success('Success','Selamat Berkunjung di RSUD Waled');
        return redirect()->route('bukutamu');
    }
}
