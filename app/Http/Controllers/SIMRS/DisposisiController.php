<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\SuratMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisposisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('simrs.bagum.disposisi_blanko_print');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $surat = SuratMasuk::find($id);
        $pernyataan_direktur = null;
        $pernyataan_penerima = null;
        $nomor = str_pad($surat->no_urut, 3, '0', STR_PAD_LEFT) . '/' . $surat->kode . '/' . Carbon::parse($surat->tgl_disposisi)->translatedFormat('m/Y');

        if (isset($surat->ttd_direktur)) {
            $pernyataan_direktur = "E-Disposisi dengan nomor " . $nomor . " telah ditandatangani oleh Direktur RSUD Waled pada tanggal ";
        }
        if ($surat->tgl_terima_surat && $surat->tanda_terima) {
            $pernyataan_penerima = "E-Disposisi dengan nomor " . $nomor . " telah diterima dan ditandatangani oleh " . $surat->tanda_terima . " pada tanggal " . Carbon::parse($surat->tgl_terima_surat)->translatedFormat('l, d F Y');
        }



        return view('simrs.bagum.disposisi_print', compact(['surat', 'pernyataan_direktur', 'pernyataan_penerima']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
