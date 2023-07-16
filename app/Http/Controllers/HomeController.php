<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\AntrianBPJSController;
use App\Http\Controllers\API\VclaimBPJSController;
use App\Models\BlogAdmin;
use App\Models\JadwalDokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth()->user()->email_verified_at == null) {
            Alert::success('Success', 'Akun SIMRS Waled anda telah didaftarkan, silahkan masukan nomor telepon anda untuk meminta verifikasi.');
            $user = Auth::user();
            Auth::logout();
            return view('vendor.adminlte.auth.verify', compact(['request', 'user']));
            // return redirect()->route('login');

        } else {
            return view('home');
        }
    }
    public function landingpage()
    {
        $blogs = BlogAdmin::where('status', 'active')->paginate(3);
        return view('vendor.medilab.landingpage', ['blogs' => $blogs]);
    }
}
