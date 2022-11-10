<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class PatientController extends Controller
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
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Patient?identifier=https://fhir.kemkes.go.id/id/nik|" . $nik;
        $response = Http::withToken($token)->get($url);
        return response()->json($response->json(), $response->status());
    }
    public function patient_by_id($id)
    {
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Patient/" . $id;
        $response = Http::withToken($token)->get($url);
        return response()->json($response->json(), $response->status());
    }
}
