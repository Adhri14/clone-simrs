<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\BPJS\Vclaim\VclaimController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratKontrolController extends Controller
{
    public function store(Request $request)
    {
        $request['noSep'] = $request->nomorsep_suratkontrol;
        $request['tglRencanaKontrol'] = $request->tanggal_suratkontrol;
        $request['kodeDokter'] = $request->kodedokter_suratkontrol;
        $request['poliKontrol'] = $request->kodepoli_suratkontrol;
        $request['user'] = Auth::user()->name;
        $vclaim = new VclaimController();
        $response = $vclaim->suratkontrol_insert($request);
        return $response;
    }
}
