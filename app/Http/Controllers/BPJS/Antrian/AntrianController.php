<?php

namespace App\Http\Controllers\BPJS\Antrian;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\SIMRS\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class AntrianController extends ApiController
{
    public function status()
    {
        $token = Token::latest()->first();
        return view('bpjs.antrian.status', compact([
            'token'
        ]));
    }
    public function poli()
    {
        $response = $this->ref_poli();
        if ($response->isSuccessful()) {
            $polikliniks = $response->getData()->data;
            Alert::success($response->statusText(), 'Poliklinik Antrian BPJS');
        } else {
            $polikliniks = null;
            Alert::error($response->statusText() . ' ' . $response->status());
        }
        return view('bpjs.antrian.poli', compact([
            'polikliniks'
        ]));
    }
    public function dokter()
    {
        $response = $this->ref_dokter();
        if ($response->isSuccessful()) {
            $dokters = $response->getData()->data;
            Alert::success($response->statusText(), 'Poliklinik Antrian BPJS');
        } else {
            $dokters = null;
            Alert::error($response->statusText() . ' ' . $response->status());
        }
        return view('bpjs.antrian.dokter', compact([
            'dokters'
        ]));
    }
    public function jadwal_dokter(Request $request)
    {
        // get dokter
        $response = $this->ref_dokter();
        if ($response->isSuccessful()) {
            $dokters = $response->getData()->data;
        } else {
            $dokters = null;
        }
        // get poli
        $response = $this->ref_poli();
        if ($response->isSuccessful()) {
            $polikliniks = $response->getData()->data;
        } else {
            $polikliniks = null;
        }
        // get jadwal
        $jadwals = null;
        if (isset($request->kodepoli)) {
            $response = $this->ref_jadwal_dokter($request);
            if ($response->isSuccessful()) {
                $jadwals = collect($response->getData()->data);
                Alert::success($response->statusText(), 'Jadwal Dokter Antrian BPJS Total : ' . $jadwals->count());
            } else {
                Alert::error($response->statusText() . ' ' . $response->status());
            }
        }
        return view('bpjs.antrian.jadwal_dokter', compact([
            'request',
            'dokters',
            'polikliniks',
            'jadwals',
        ]));
    }
    public function antrian(Request $request)
    {
        // get poli
        $response = $this->ref_poli();
        if ($response->isSuccessful()) {
            $polikliniks = $response->getData()->data;
        } else {
            $polikliniks = null;
        }
        // get antrian
        $antrians = null;
        if (isset($request->tanggal)) {
            $antrians = Antrian::whereDate('tanggalperiksa', $request->tanggal)->get();
            if ($request->kodepoli != '000') {
                $antrians = $antrians->where('kodepoli', $request->kodepoli);
            }
            Alert::success('OK', 'Antrian BPJS Total : ' . $antrians->count());
        }
        return view('bpjs.antrian.antrian', compact([
            'request',
            'polikliniks',
            'antrians',
        ]));
    }
    public function list_task(Request $request)
    {
        // get antrian
        $taskid = null;
        if (isset($request->kodebooking)) {
            $response =  $this->getlisttask($request);
            if ($response->isSuccessful()) {
                $taskid = $response->getData()->data;
            }
            Alert::success($response->statusText() . ' ' . $response->status());
        }
        return view('bpjs.antrian.list_task', compact([
            'request',
            'taskid',
        ]));
    }
    public function dashboard_tanggal_index(Request $request)
    {
        // get antrian
        $antrians = null;
        if (isset($request->tanggal)) {
            $response =  $this->dashboard_tanggal($request);
            dd($response->getData());
            if ($response->isSuccessful()) {
                $antrians = $response->getData()->data;
            }
            Alert::success($response->statusText() . ' ' . $response->status());
        }
        return view('bpjs.antrian.dashboard_tanggal_index', compact([
            'request',
            'antrians',
        ]));
    }
    // API FUNCTION
    public function signature()
    {
        $cons_id =  env('ANTRIAN_CONS_ID');
        $secretKey = env('ANTRIAN_SECRET_KEY');
        $userkey = env('ANTRIAN_USER_KEY');
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cons_id . "&" . $tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        $data['user_key'] =  $userkey;
        $data['x-cons-id'] = $cons_id;
        $data['x-timestamp'] = $tStamp;
        $data['x-signature'] = $encodedSignature;
        $data['decrypt_key'] = $cons_id . $secretKey . $tStamp;
        return $data;
        // return $this->sendResponse('Ok.', $data);
    }
    public function stringDecrypt($key, $string)
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
            if ($response->json('metadata.code') == 1) {
                $code = 200;
            } else {
                $code = $response->json('metadata.code');
            }
            return $this->sendResponse($response->json('metadata.message'), $data, $code);
        }
    }
    public function response_no_decrypt($response)
    {
        if ($response->failed()) {
            return $this->sendError($response->reason(),  $response->json('response'), $response->status());
        } else {
            return $this->sendResponse($response->json('metadata.message'), $response->json('response'), $response->json('metadata.code'));
        }
    }
    // API BPJS
    public function ref_poli()
    {
        $url = env('ANTRIAN_URL') . "ref/poli";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_dokter()
    {
        $url = env('ANTRIAN_URL') . "ref/dokter";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_jadwal_dokter(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodepoli" => "required",
            "tanggal" =>  "required|date|date_format:Y-m-d",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "jadwaldokter/kodepoli/" . $request->kodepoli . "/tanggal/" . $request->tanggal;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function update_jadwal_dokter(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodepoli" =>  "required",
            "kodesubspesialis" =>  "required",
            "kodedokter" =>  "required",
            "jadwal" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "jadwaldokter/updatejadwaldokter";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->post(
            $url,
            [
                "kodepoli" => $request->kodepoli,
                "kodesubspesialis" => $request->kodesubspesialis,
                "kodedokter" => $request->kodedokter,
                "jadwal" => json_decode($request->jadwal),
            ]
        );
        return $this->response_decrypt($response, $signature);
    }
    public function tambah_antrean(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "nomorkartu" =>  "required|digits:13|numeric",
            // "nomorreferensi" =>  "required",
            "nik" =>  "required|digits:16|numeric",
            "nohp" => "required|numeric",
            "kodepoli" =>  "required",
            "norm" =>  "required",
            "pasienbaru" =>  "required",
            "tanggalperiksa" =>  "required|date|date_format:Y-m-d",
            "kodedokter" =>  "required",
            "jampraktek" =>  "required",
            "jeniskunjungan" => "required",
            "jenispasien" =>  "required",
            "namapoli" =>  "required",
            "namadokter" =>  "required",
            "nomorantrean" =>  "required",
            "angkaantrean" =>  "required",
            "estimasidilayani" =>  "required",
            "sisakuotajkn" =>  "required",
            "kuotajkn" => "required",
            "sisakuotanonjkn" => "required",
            "kuotanonjkn" => "required",
            "keterangan" =>  "required",
            "nama" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "antrean/add";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->post(
            $url,
            [
                "kodebooking" => $request->kodebooking,
                "jenispasien" => $request->jenispasien,
                "nomorkartu" => $request->nomorkartu,
                "nik" => $request->nik,
                "nohp" => $request->nohp,
                "kodepoli" => $request->kodepoli,
                "namapoli" => $request->namapoli,
                "pasienbaru" => $request->pasienbaru,
                "norm" => $request->norm,
                "tanggalperiksa" => $request->tanggalperiksa,
                "kodedokter" => $request->kodedokter,
                "namadokter" => $request->namadokter,
                "jampraktek" => $request->jampraktek,
                "jeniskunjungan" => $request->jeniskunjungan,
                "nomorreferensi" => $request->nomorreferensi,
                "nomorantrean" => $request->nomorantrean,
                "angkaantrean" => $request->angkaantrean,
                "estimasidilayani" => $request->estimasidilayani,
                "sisakuotajkn" => $request->sisakuotajkn,
                "kuotajkn" => $request->kuotajkn,
                "sisakuotanonjkn" => $request->sisakuotanonjkn,
                "kuotanonjkn" => $request->kuotanonjkn,
                "keterangan" => $request->keterangan,
            ]
        );
        return $this->response_decrypt($response, $signature);
    }
    public function update_antrean(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "taskid" =>  "required",
            "waktu" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "antrean/updatewaktu";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->post(
            $url,
            [
                "kodebooking" => $request->kodebooking,
                "taskid" => $request->taskid,
                "waktu" => $request->waktu,
            ]
        );
        return $this->response_decrypt($response, $signature);
    }
    public function batal_antrean(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "keterangan" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "antrean/batal";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->post(
            $url,
            [
                "kodebooking" => $request->kodebooking,
                "keterangan" => $request->keterangan,
            ]
        );
        return $this->response_decrypt($response, $signature);
    }
    public function getlisttask(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "antrean/getlisttask";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->post(
            $url,
            [
                "kodebooking" => $request->kodebooking,
            ]
        );
        return $this->response_decrypt($response, $signature);
    }
    public function dashboard_tanggal(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "tanggal" =>  "required|date|date_format:Y-m-d",
            "waktu" => "required|in:rs,server",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "dashboard/waktutunggu/tanggal/" . $request->tanggal . "/waktu/" . $request->waktu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_no_decrypt($response, $signature);
    }
    public function dashboard_bulan(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "bulan" =>  "required|date_format:m",
            "tahun" =>  "required|date_format:Y",
            "waktu" => "required|in:rs,server",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(), 400);
        }
        $url = env('ANTRIAN_URL') . "dashboard/waktutunggu/bulan/" . $request->bulan . "/tahun/" . $request->tahun . "/waktu/" . $request->waktu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_no_decrypt($response);
    }
}
