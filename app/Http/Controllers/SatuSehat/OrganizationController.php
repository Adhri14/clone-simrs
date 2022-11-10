<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\SIMRS\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use RealRashid\SweetAlert\Facades\Alert;

class OrganizationController extends ApiController
{
    public function index(Request $request)
    {
        $organization = null;
        if (isset($request->partOf)) {
            $response = $this->organization_part_of($request->partOf);
            $data = $response->getData();
            if ($response->status() == 200) {
                if ($data->total) {
                    $organization = $data->entry;
                    Alert::success($response->statusText(), 'Part Of Organization Ditemukan ' . $data->total . ' Data');
                } else {
                    Alert::error($response->statusText(), 'Part Of Organization Ditemukan 0 Data');
                }
            } else {
                Alert::error($response->statusText() . ' ' . $response->status());
            }
        }
        $provinsi = Province::pluck('name', 'code');
        return view('satusehat.organization', compact([
            'request',
            'organization',
            'provinsi',
        ]));
    }
    // API SIMRS
    public function organization_store_api(Request $request)
    {
        $response = $this->organization_create($request);
        if ($response->isSuccessful()) {
            $request['cityText'] = City::firstWhere('code', $request->city)->name;
            $data = [
                'part_of_id' => $request->organization_id,
                'satusehat_uuid' => $response->getData()->id,
                'identifier_id' => $request->identifier,
                // telecom
                'phone' => $request->phone,
                'email' => $request->email,
                'url' => $request->url,
                // address
                'province_id' => $request->province,
                'city_id' => $request->city,
                'district_id' => $request->district,
                'village_id' => $request->village,
                'city' => $request->cityText,
                'line' => $request->address,
                'postalCode' => $request->postalCode,
                // resource
                'name' => $request->name,
            ];
            Organization::create($data);
        }
        return response()->json($response, $response->status());
    }
    public function organization_update_api($id, Request $request)
    {
        $response = $this->organization_update($id, $request);
        return response()->json($response, $response->status());
    }
    // API SATU SEHAT
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
    public function organization_create(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "organization_id" => "required",
            "identifier" => "required",
            "name" => "required",
            "phone" => "required",
            "email" => "required|email",
            "url" => "required",
            "address" => "required",
            "postalCode" => "required",
            "province" => "required",
            "city" => "required",
            "district" => "required",
            "village" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Data Belum Lengkap', $validator->errors()->first(), 400);
        }
        $token = session()->get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Organization";
        $data = [
            "resourceType" => "Organization",
            "active" => true,
            "identifier" => [
                [
                    "use" => "official",
                    "system" => "http://sys-ids.kemkes.go.id/" . $request->organization_id,
                    "value" => $request->identifier
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
                    "postalCode" => $request->postalCode,
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
                "reference" => $request->organization_id,
            ]
        ];
        $response = Http::withToken($token)->post($url, $data);
        return response()->json($response->json(), $response->status());
    }
    public function organization_update($id, Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "organization_id" => "required",
            "identifier" => "required",
            "name" => "required",
            "phone" => "required",
            "email" => "required",
            "url" => "required",
            "address" => "required",
            "postalCode" => "required",
            "province" => "required",
            "city" => "required",
            "district" => "required",
            "village" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Data Belum Lengkap', $validator->errors()->first(), 400);
        }
        $request['cityText'] = City::firstWhere('code', $request->city)->name;
        $token = session()->get('tokenSatuSehat');
        $url =  env('SATUSEHAT_BASE_URL') . "/Organization/" . $id;
        $data = [
            "resourceType" => "Organization",
            "id" => $id,
            "active" => true,
            "identifier" => [
                [
                    "use" => "official",
                    "system" => "http://sys-ids.kemkes.go.id/" . $request->organization_id,
                    "value" => $request->identifier,
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
                        $request->address,
                    ],
                    "city" => $request->cityText,
                    "postalCode" => $request->postalCode,
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
                                    "valueCode" => $request->district,
                                ],
                                [
                                    "url" => "village",
                                    "valueCode" => $request->village,
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "partOf" => [
                "reference" =>  $request->organization_id,
            ]
        ];
        $response = Http::withToken($token)->put($url, $data);
        return response()->json($response->json(), $response->status());
    }
}
