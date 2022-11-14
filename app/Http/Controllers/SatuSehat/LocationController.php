<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\SIMRS\Location;
use App\Models\SIMRS\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class LocationController extends ApiController
{
    public function index(Request $request)
    {
        $location = null;
        if (isset($request->partOf)) {
            $response = $this->location_part_of($request->partOf);
            if ($response->status() == 200) {
                $location = json_decode($response->content());
                Alert::success('Success', 'location Ditemukan');
            } else {
                Alert::error('Error', $response->statusText());
            }
        }
        $provinsi = Province::pluck('name', 'code');
        $location_simrs = Location::simplePaginate();
        return view('satusehat.location', compact([
            'request',
            'location',
            'provinsi',
            'location_simrs',
        ]));
    }
    // API SIMRS
    public function location_store_api(Request $request)
    {
        $response = $this->location_crate($request);
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
                // position
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                // resource
                'name' => $request->name,
                'description' => $request->description,
            ];
            Location::create($data);
        }
        return response()->json($response, $response->status());
    }
    public function location_update_api($id, Request $request)
    {
        $response = $this->location_update($id, $request);
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
                // position
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                // resource
                'name' => $request->name,
                'description' => $request->description,
            ];
            $organization =  Location::firstWhere('satusehat_uuid', $id);
            $organization->update($data);
        }
        return response()->json($response, $response->status());
    }
    // API SATU SEHAT
    public function location_crate(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "organization_id" => "required",
            "identifier" => "required",
            "name" => "required",
            "description" => "required",
            "phone" => "required",
            "email" => "required|email",
            "url" => "required",
            "address" => "required",
            "postalCode" => "required",
            "province" => "required",
            "city" => "required",
            "district" => "required",
            "village" => "required",
            "longitude" => "required",
            "latitude" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Data Belum Lengkap', $validator->errors()->first(), 400);
        }
        $request['cityText'] = City::firstWhere('code', $request->city)->name;
        $organization_id = explode('/', $request->organization_id);

        $token = Token::latest()->first()->access_token;
        $url =  env('SATUSEHAT_BASE_URL') . "/Location";
        $data = [
            "resourceType" => "Location",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/location/" . $organization_id[1],
                    "value" => $request->identifier,
                ]
            ],
            "status" => $request->status,
            "name" => $request->name,
            "description" => $request->description,
            "mode" => "instance",
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => $request->phone,
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" =>  $request->email,
                ],
                [
                    "system" => "url",
                    "value" =>  $request->url,
                    "use" => "work"
                ]
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    $request->address,
                ],
                "city" =>  $request->cityText,
                "postalCode" =>  $request->postalCode,
                "country" => "ID",
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" =>  $request->province,
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
                            ],
                        ]
                    ]
                ]
            ],
            "physicalType" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                        "code" => "ro",
                        "display" => "Room"
                    ]
                ]
            ],
            "position" => [
                "longitude" =>  floatval($request->longitude),
                "latitude" => floatval($request->latitude),
                "altitude" => 0
            ],
            "managingOrganization" => [
                "reference" => $request->organization_id,
            ]
        ];
        $response = Http::withToken($token)->post($url, $data);
        return response()->json($response->json(), $response->status());
    }
    public function edit($id)
    {
        $token = Token::latest()->first()->access_token;
        $url =  env('SATUSEHAT_BASE_URL') . "/Location/" . $id;
        $response = Http::withToken($token)->get($url);
        return response()->json($response->json(), $response->status());
    }
    public function location_update($id, Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "organization_id" => "required",
            "identifier" => "required",
            "name" => "required",
            "description" => "required",
            "phone" => "required",
            "email" => "required|email",
            "url" => "required",
            "address" => "required",
            "postalCode" => "required",
            "province" => "required",
            "city" => "required",
            "district" => "required",
            "village" => "required",
            "longitude" => "required",
            "latitude" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Data Belum Lengkap', $validator->errors()->first(), 400);
        }
        $request['cityText'] = City::firstWhere('code', $request->city)->name;
        $organization_id = explode('/', $request->organization_id);

        $token = Token::latest()->first()->access_token;
        $url =  env('SATUSEHAT_BASE_URL') . "/Location/" . $id;
        $data = [
            "resourceType" => "Location",
            "id" => $id,
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/location/" . $organization_id[1],
                    "value" => $request->identifier,
                ]
            ],
            "status" => $request->status,
            "name" => $request->name,
            "description" => $request->description,
            "mode" => "instance",
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => $request->phone,
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" =>  $request->email,
                ],
                [
                    "system" => "url",
                    "value" =>  $request->url,
                    "use" => "work"
                ]
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    $request->address,
                ],
                "city" =>  $request->cityText,
                "postalCode" =>  $request->postalCode,
                "country" => "ID",
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" =>  $request->province,
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
                            ],
                        ]
                    ]
                ]
            ],
            "physicalType" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                        "code" => "ro",
                        "display" => "Room"
                    ]
                ]
            ],
            "position" => [
                "longitude" =>  floatval($request->longitude),
                "latitude" => floatval($request->latitude),
                "altitude" => 0
            ],
            "managingOrganization" => [
                "reference" => $request->organization_id,
            ]
        ];
        $response = Http::withToken($token)->put($url, $data);
        return response()->json($response->json(), $response->status());
    }
}
