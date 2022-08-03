<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public $baseUrl = 'https://rsudwaled.ruangwa.id/api/';

    public function index(Request $request)
    {
        $request['message'] = 'cek cek';
        $request['number'] = '089529909036';
        return $this->send_message($request);
    }
    public function send_message(Request $request)
    {
        $url = $this->baseUrl . "send_message";
        $response = Http::asForm()->post($url, [
            'token' => env('TOKEN_SERVICE_WA'),
            'number' => $request->number,
            'message' => $request->message,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_button(Request $request)
    {
        $url = $this->baseUrl . "send_button";
        $response = Http::asForm()->post($url, [
            'token' => env('TOKEN_SERVICE_WA'),
            'number' => $request->number,
            'contenttext' => $request->contenttext,
            'footertext' => $request->footertext,
            'buttonid' => $request->buttonid, #'id1,id2',
            'buttontext' => $request->buttontext, #'UMUM,BPJS'
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_button_link(Request $request)
    {
        $url = $this->baseUrl . "send_buttonurl";
        $response = Http::asForm()->post($url, [
            'token' => env('TOKEN_SERVICE_WA'),
            'number' => $request->number,
            'text' => $request->text,
            'buttonlabel' => $request->buttonlabel,
            'buttonurl' => $request->buttonurl,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_image(Request $request)
    {
        $url = $this->baseUrl . "send_image";
        $response = Http::asForm()->post($url, [
            'token' => env('TOKEN_SERVICE_WA'),
            'number' => $request->number,
            'file' => $request->file,
            'caption' => $request->caption,
            // 'date' => Carbon::now()->format('Y-m-d'),
            // 'time' => Carbon::now()->format('H:i:s'),
            // 'file' => asset('vendor/adminlte/dist/img/info poli.jpeg'),
            // 'caption' => "Daftar poliklinik yang tersedia untuk antrian Online WhatsApp ada di RSUD Waled, silahkan menggunakan *Kode Online Poliklinik* untuk menentukan pilihan.",
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
}
