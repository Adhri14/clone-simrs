<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\AntrianBPJSController;
use App\Http\Controllers\API\VclaimBPJSController;
use App\Models\JadwalDokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth()->user()->email_verified_at == null) {
            Auth::logout();
            Alert::success('Success', 'Akun SIMRS Waled anda telah didaftarkan, silahkan login untuk meminta verifikasi.');
            return redirect()->route('login');
        } else {
            return view('home');
        }
    }
    public function landingpage()
    {
        $jadwal = JadwalDokter::get();

        // dd($jadwal->groupBy('hari')->first());

        return view('vendor.medilab.landingpage', compact([
            'jadwal'
        ]));
    }
}
