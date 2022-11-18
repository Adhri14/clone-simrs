<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\WhatsappController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function verifikasi_akun(Request $request)
    {
        return view('vendor.adminlte.auth.verify', compact(['request']));
    }
    public function verifikasi_kirim(Request $request)
    {
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)->first();
        $wa = new WhatsappController();
        $request['message'] = "*Verifikasi Akun SIMRS WALED* \nPesan verifikasi akun anda telah dikirim dengan sebagai berikut.\n\nNAMA : " . $user->name . "\nPHONE : " . $user->phone . "\nEMAIL : " . $user->email . "\n\nSilahkan menunggu Administrator atau Kepegawaian untuk memverifikasi anda.";
        $request['number'] = $user->phone;
        $wa->send_message($request);
        $request['notif'] = "*Verifikasi Akun SIMRS WALED* \nTelah registrasi akun baru dengan data sebagai berikut.\n\nNAMA : " . $user->name . "\nPHONE : " . $user->phone . "\nEMAIL : " . $user->email . "\n\nMohon segera lakukan verifikasi registrasi tersebut.\nsim.rsudwaled.id";
        $wa->send_notif($request);
        Alert::success('Succes','Berhasil Kirim Pesan Verifikasi. Silahkan menunggu verifikasi');
        return redirect()->route('login');
    }
}
