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
            if ($response->status() == 200) {
                if ($response->json('total')) {
                    $practitioner = json_decode($response)->entry[0]->resource;
                    Alert::success('Success', 'Practitioner Ditemukan (' . $practitioner->id . ')');
                } else {
                    Alert::error('Error', 'Practitioner Tidak Ditemukan');
                }
            } else {
                Alert::error('Error', $response->reason());
            }
        }
        if (isset($request->id)) {
            $response = $this->practitioner_by_id($request->id);
            if ($response->json('resourceType') == "Practitioner") {
                $practitioner = json_decode($response);
                Alert::success('Success', 'Pasien Ditemukan');
            } else {
                Alert::error('Error', $response->reason());
            }
        }
        return view('satusehat.practitioner', compact([
            'request',
            'practitioner'
        ]));
    }
    public function practitioner_by_nik($nik)
    {
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|" . $nik;
        $response = Http::withToken($token)->get($url);
        return $response;
    }
    public function practitioner_by_id($id)
    {
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Practitioner/" . $id;
        $response = Http::withToken($token)->get($url);
        return $response;
    }
}
