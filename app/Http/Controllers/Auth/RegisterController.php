<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\WhatsappController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string',  'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data,)
    {
        $user =  User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $request = new  Request();
        $wa = new WhatsappController();
        $request['message'] = "*Registrasi Akun SIMRS WALED* \nAnda telah registrasi akun SIMRS Waled dengan data sebagai berikut.\n\nNAMA : " . $user->name . "\nPHONE : " . $user->phone . "\nEMAIL : " . $user->email . "\n\nSilahkan menunggu Administrator atau Kepegawaian untuk memverifikasi anda.";
        $request['number'] = $user->phone;
        $wa->send_message($request);
        $request['notif'] = "*Registrasi Akun SIMRS WALED* \nTelah registrasi akun baru dengan data sebagai berikut.\n\nNAMA : " . $user->name . "\nPHONE : " . $user->phone . "\nEMAIL : " . $user->email . "\n\nMohon segera lakukan verifikasi registrasi tersebut.\nsim.rsudwaled.id";
        $wa->send_notif($request);
        return  $user->assignRole($data['role']);
    }
}
