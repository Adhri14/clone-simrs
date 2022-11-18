<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\AntrianBPJSController;
use App\Http\Controllers\API\VclaimBPJSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        if (Auth()->user()->email_verified_at == null) {
            Auth::logout();
            Alert::success('Success','Akun SIMRS Waled anda telah didaftarkan, silahkan login untuk meminta verifikasi.');
            return redirect()->route('login');
        } else {
            return view('home');
        }
    }
}
