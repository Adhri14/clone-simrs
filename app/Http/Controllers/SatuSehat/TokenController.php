<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class TokenController extends Controller
{
    public function status()
    {
        $token = Token::latest()->first();
        return view('satusehat.status',compact([
            'token'
        ]));
    }
    public function refresh_token()
    {
        $response = $this->token();
        if ($response->status() == 200) {
            Alert::success($response->statusText());
        } else {
            Alert::error($response->statusText().' '. $response->status());
        }
        return redirect()->route('satusehat.status');
    }
    // API SATU SEHAT
    public function token()
    {
        $url =  env('SATUSEHAT_AUTH_URL') . "/accesstoken?grant_type=client_credentials";
        $response = Http::asForm()->post($url, [
            'client_id' => env('SATUSEHAT_CLIENT_ID'),
            'client_secret' => env('SATUSEHAT_SECRET_ID'),
        ]);
        if ($response->successful()) {
            $json = $response->json();
            // dd($json['access_token']);
            Token::create([
                'access_token' => $json['access_token'],
                'application_name'=> $json['application_name'],
                'organization_name'=> $json['organization_name'],
                'token_type'=> $json['token_type'],
                'issued_at'=> $json['issued_at'],
            ]);
            Log::notice('Auth Token Satu Sehat : ' . $json['access_token']);
        }
        return response()->json($response->json(), $response->status());
    }
}
