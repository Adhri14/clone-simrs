<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $practitioner = null;
        $token = new TokenController();
        if (isset($request->nik)) {
            $response = $this->practitioner_by_nik($request->nik);
            $data = $response->getData();
            if ($response->status() == 200) {
                if ($data->total) {
                    $practitioner = $data->entry[0]->resource;
                    Alert::success($response->statusText(), 'Practitioner Ditemukan');
                } else {
                    Alert::error('Not Found', 'Practitioner Tidak Ditemukan');
                }
            } else {
                Alert::error($response->statusText() . ' ' . $response->status());
            }
        }
        if (isset($request->id)) {
            $response = $this->practitioner_by_id($request->id);
            $data = $response->getData();
            if ($response->status() == 200) {
                if ($data->resourceType == "Practitioner") {
                    $practitioner = $data;
                    Alert::success($response->statusText(), ' Practitioner Ditemukan');
                } else {
                    Alert::error('Not Found', 'Practitioner Tidak Ditemukan');
                }
            } else {
                Alert::error($response->statusText() . ' ' . $response->status());
            }
        }
        return view('satusehat.practitioner', compact([
            'request',
            'practitioner'
        ]));
    }
    // API SATU SEHAT
    public function practitioner_by_nik($nik)
    {
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|" . $nik;
        $response = Http::withToken($token)->get($url);
        return response()->json($response->json(), $response->status());
    }
    public function practitioner_by_id($id)
    {
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Practitioner/" . $id;
        $response = Http::withToken($token)->get($url);
        return response()->json($response->json(), $response->status());
    }
}
