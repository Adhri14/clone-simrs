<?php

namespace App\Http\Controllers\BPJS\Vclaim;

use App\Http\Controllers\BPJS\ApiBPJSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class VclaimController extends ApiBPJSController
{
    // public $baseUrl = 'https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/';
    public $baseUrl = 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/';

    public static function signature()
    {
        $cons_id =  env('VCLAIM_CONS_ID');
        $secretKey = env('VCLAIM_SECRET_KEY');
        $userkey = env('VCLAIM_USER_KEY');

        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cons_id . "&" . $tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);

        $response = array(
            'user_key' => $userkey,
            'x-cons-id' => $cons_id,
            'x-timestamp' => $tStamp,
            'x-signature' => $encodedSignature,
            'decrypt_key' => $cons_id . $secretKey . $tStamp,
        );
        return $response;
    }
    public static function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $output = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $output;
    }
    public function response_decrypt($response, $signature)
    {
        if ($response->failed()) {
            return $this->sendError($response->reason(),  $response->json('response'), $response->status());
        } else {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->json('response'));
            $data = json_decode($decrypt);
            if ($response->json('metaData.code') == 1) {
                $code = 200;
            } else {
                $code = $response->json('metaData.code');
            }
            return $this->sendResponse($response->json('metaData.message'), $data, $code);
        }
    }
    public function response_no_decrypt($response)
    {
        if ($response->failed()) {
            return $this->sendError($response->reason(),  $response->json('response'), $response->status());
        } else {
            return $this->sendResponse($response->json('metaData.message'), $response->json('response'), $response->json('metaData.code'));
        }
    }
    // API VCLAIM

    // Cari Rujukan
    public function rujukan_nomor(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = $this->baseUrl . "Rujukan/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_peserta(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "nomorkartu" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }

        $url = $this->baseUrl . "Rujukan/List/Peserta/" . $request->nomorkartu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_rs_nomor(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }

        $url = $this->baseUrl . "Rujukan/RS/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);

        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_rs_peserta(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "nomorkartu" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = $this->baseUrl . "Rujukan/RS/List/Peserta/" . $request->nomorkartu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_jumlah_sep(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "jenisrujukan" => "required",
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = $this->baseUrl . "Rujukan/JumlahSEP/" . $request->jenisrujukan . "/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function surat_kontrol_nomor(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = $this->baseUrl . "RencanaKontrol/noSuratKontrol/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
}
