<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratLampiranController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:pdf,xlx,csv,png,jpg,jpeg',
        ]);
        dd($request->all());
        $fileName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $fileName);
        return back()
            ->with('success', 'You have successfully upload file.')
            ->with('file', $fileName);


        $request->validate([
            'no_surat' => 'required',
            'tgl_surat' => 'required|date',
            'asal_surat' => 'required',
            'perihal' => 'required',
            'sifat' => 'required',
            'tgl_disposisi' => 'required|date',
        ]);
        // setting no urut disposisi per bulan
        $tgl_disposisi = Carbon::parse($request->tgl_disposisi);
        $no_urut_bulan = SuratMasuk::whereYear('tgl_disposisi', $tgl_disposisi->year)
            ->whereMonth('tgl_disposisi', $tgl_disposisi->month)
            ->count();
        $request['no_urut'] = $no_urut_bulan;
        // insert surat masuk
        SuratMasuk::create([
            'no_urut' => $request->no_urut,
            'kode' => $request->kode,
            'sifat' => $request->sifat,
            'no_surat' => $request->no_surat,
            'tgl_surat' => $request->tgl_surat,
            'asal_surat' => $request->asal_surat,
            'perihal' => $request->perihal,
            'tgl_disposisi' => $request->tgl_disposisi,
        ]);
        Alert::success('Success', 'Surat Masuk Berhasil Diinputkan');
        return redirect()->back();
    }
}
