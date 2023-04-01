<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\SuratLampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class SuratLampiranController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:pdf,xlx,csv,png,jpg,jpeg',
        ]);
        $filename = time() . '-' . $request->file->getClientOriginalName();
        $fileurl = asset("storage/file_upload/" . $filename);
        $extension = $request->file->extension();
        SuratLampiran::create([
            'surat_id' => $request->id_surat,
            'jenis_surat' => $request->jenis_surat,
            'filename' => $filename,
            'fileurl' => $fileurl,
            'extension' => $extension,
            'user' => $request->id_surat,
        ]);
        $request->file->move(public_path('storage/file_upload/'), $filename);
        Alert::success('Success', 'Surat Masuk Berhasil Diinputkan');
        return redirect()->back();
    }
}
