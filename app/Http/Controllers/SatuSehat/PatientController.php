<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patient = null;
        $token = new TokenController();
        if (isset($request->nik)) {
            $response = $this->patient_by_nik($request->nik);
            $data = $response->getData();
            if ($response->status() == 200) {
                if ($data->total) {
                    $patient = $data->entry[0]->resource;
                    Alert::success($response->statusText(),  $data->total . ' Pasien Ditemukan');
                } else {
                    Alert::error('Not Found', 'Pasien Tidak Ditemukan');
                }
            } else {
                Alert::error($response->reason().' '.$response->status());
            }
        }
        if (isset($request->id)) {
            $response = $this->patient_by_id($request->id);
            if ($response->json('resourceType') == "Patient") {
                $patient = json_decode($response);
                Alert::success('Success', 'Pasien Ditemukan');
            } else {
                Alert::error($response->reason().' '.$response->status());
            }
        }
        return view('satusehat.patient', compact([
            'request',
            'patient'
        ]));
    }
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
        return $response;
    }
}
