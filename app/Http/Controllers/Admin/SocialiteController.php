<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;


class SocialiteController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }
    public function callback()
    {
        try {
            $user_google    = Socialite::driver('google')->user();
            $user           = User::firstWhere('email', $user_google->email);
            //jika user ada maka langsung di redirect ke halaman home
            if ($user) {
                $user->update([
                    'email' => $user_google->email,
                    'name' => $user_google->name,
                    'google_id' => $user_google->id,
                    'avatar' => $user_google->avatar,
                    'avatar_original' => $user_google->avatar_original,
                ]);
                auth()->login($user, true);
            }
            //jika user tidak ada maka simpan ke database
            else {
                //$user_google menyimpan data google account seperti email, foto, dsb
                $create = User::Create([
                    'email' => $user_google->email,
                    'name' => $user_google->name,
                    'password' => Hash::make('password'),
                    'google_id' => $user_google->id,
                    'avatar' => $user_google->avatar,
                    'avatar_original' => $user_google->avatar_original,
                ]);
                $create->assignRole('Pasien');
                auth()->login($create, true);
            }
            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->route('login');
        }
    }
    public function logout(Request $request)
    {
        auth('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
