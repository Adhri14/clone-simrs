<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class OrganizationController extends ApiController
{
    public function index(Request $request)
    {
        $organization = null;
        if (isset($request->partOf)) {
            $response = $this->organization_part_of($request->partOf);
            if ($response->status() == 200) {
                $organization = json_decode($response->content());
                Alert::success('Success', 'Organization Ditemukan');
            } else {
                Alert::error('Error', $response->statusText());
            }
        }
        return view('satusehat.organization', compact([
            'request',
            'organization'
        ]));
    }
    public function edit($id)
    {
        $response = $this->organization_by_id($id);
        if ($response->status() == 200) {
            $organization = json_decode($response->content());
            Alert::success('Success', 'Organization Ditemukan');
        } else {
            Alert::error('Error', $response->statusText());
        }
        return view('satusehat.organization_edit', compact([
            'organization'
        ]));
    }
    public function store(Request $request)
    {

        $response = $this->craete_organization($request);
        dd($response);
        // if ($response->successful()) {
        //     Alert::success('Success', 'Create Organization Berhasil');
        // } else {
        //     Alert::error('Error', 'Create Organization Gagal');
        // }
        // return redirect()->route('satusehat.organization.index');
    }
    public function organization_by_id($id)
    {
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Organization/" . $id;
        $response = Http::withToken($token)->get($url);
        return response()->json($response->json(), $response->status());
    }
    public function organization_part_of($id)
    {
        $token = Session::get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Organization?partof=" . $id;
        $response = Http::withToken($token)->get($url);
        return response()->json($response->json(), $response->status());
    }
    public function craete_organization(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "name" => "required",
            "phone" => "required",
            "email" => "required",
            "url" => "required",
            "address" => "required",
            "postalCode" => "required",
            "cityText" => "required",
            "province" => "required",
            "city" => "required",
            "district" => "required",
            "village" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Data Belum Lengkap', $validator->errors(), 400);
        }
        $token = session()->get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Organization";
        $data = [
            "resourceType" => "Organization",
            "active" => true,
            "identifier" => [
                [
                    "use" => "official",
                    "system" => "http://sys-ids.kemkes.go.id/organization/" . env('SATUSEHAT_ORGANIZATION_ID'),
                    "value" => env('SATUSEHAT_ORGANIZATION_ID')
                ]
            ],
            "type" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/organization-type",
                            "code" => "dept",
                            "display" => "Hospital Department"
                        ]
                    ]
                ]
            ],
            "name" => $request->name,
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => $request->phone,
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" => $request->email,
                    "use" => "work"
                ],
                [
                    "system" => "url",
                    "value" => $request->url,
                    "use" => "work"
                ]
            ],
            "address" => [
                [
                    "use" => "work",
                    "type" => "both",
                    "line" => [
                        $request->address
                    ],
                    "city" => $request->cityText,
                    "postalCode" => $request->posatalCode,
                    "country" => "ID",
                    "extension" => [
                        [
                            "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                            "extension" => [
                                [
                                    "url" => "province",
                                    "valueCode" => $request->province,
                                ],
                                [
                                    "url" => "city",
                                    "valueCode" =>  $request->city,
                                ],
                                [
                                    "url" => "district",
                                    "valueCode" =>  $request->district,
                                ],
                                [
                                    "url" => "village",
                                    "valueCode" =>  $request->village,
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "partOf" => [
                "reference" => "Organization/" . env('SATUSEHAT_ORGANIZATION_ID')
            ]
        ];
        $response = Http::withToken($token)->post($url, $data);
        return response()->json($response, $response->status());
    }
}
