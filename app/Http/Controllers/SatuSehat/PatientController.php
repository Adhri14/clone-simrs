<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\API\ApiController;
use App\Models\SIMRS\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class PatientController extends ApiController
{
    public function index(Request $request)
    {
        $patient = null;
        if (isset($request->nik)) {
            $response = $this->patient_by_nik($request->nik);
            $data = $response->getData();
            if ($response->status() == 200) {
                if ($data->total) {
                    $patient = $data->entry[0]->resource;
                    Alert::success($response->statusText(), 'Pasien Ditemukan');
                } else {
                    Alert::error('Not Found', 'Pasien Tidak Ditemukan');
                }
            } else {
                Alert::error($response->statusText() . ' ' . $response->status());
            }
        }
        if (isset($request->id)) {
            $response = $this->patient_by_id($request->id);
            $data = $response->getData();
            if ($response->status() == 200) {
                if ($data->resourceType == "Patient") {
                    $patient = $data;
                    Alert::success($response->statusText(), ' Pasien Ditemukan');
                } else {
                    Alert::error('Not Found', 'Pasien Tidak Ditemukan');
                }
            } else {
                Alert::error($response->statusText() . ' ' . $response->status());
            }
        }
        return view('satusehat.patient', compact([
            'request',
            'patient',
        ]));
    }
    // API SATU SEHAT
    public function patient_by_nik($nik)
    {
        $token = Token::latest()->first()->access_token;
        $url =  env('SATUSEHAT_BASE_URL') . "/Patient?identifier=https://fhir.kemkes.go.id/id/nik|" . $nik;
        $response = Http::withToken($token)->get($url);
        if ($response->status() == 401) {
            $refresh_token = new TokenController();
            $refresh_token->token();
        }
        return response()->json($response->json(), $response->status());
    }
    public function patient_by_name(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "birthdate" => "required",
            "gender" => "required",
            "name" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Data Belum Lengkap', $validator->errors()->first(), 400);
        }
        $token = Token::latest()->first()->access_token;
        $url =  env('SATUSEHAT_BASE_URL') . "/Patient?name=" . $request->name . "&birthdate=" . $request->birthdate . "&gender=" . $request->gender;
        $response = Http::withToken($token)->get($url);
        if ($response->status() == 401) {
            $refresh_token = new TokenController();
            $refresh_token->token();
        }
        return response()->json($response->json(), $response->status());
    }
    public function patient_by_id($id)
    {
        $token = Token::latest()->first()->access_token;
        $url =  env('SATUSEHAT_BASE_URL') . "/Patient/" . $id;
        $response = Http::withToken($token)->get($url);
        if ($response->status() == 401) {
            $refresh_token = new TokenController();
            $refresh_token->token();
        }
        return response()->json($response->json(), $response->status());
    }
}
