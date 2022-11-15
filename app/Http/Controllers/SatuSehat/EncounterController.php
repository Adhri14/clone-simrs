<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\SIMRS\Encounter;
use App\Models\SIMRS\Location;
use App\Models\SIMRS\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class EncounterController extends ApiController
{
    public function index(Request $request)
    {
        return view('satusehat.encounter', compact([
            'request',
        ]));
    }
    public function create(Request $request)
    {
        $location = Location::pluck('name', 'satusehat_uuid');
        return view('satusehat.encounter_create', compact([
            'request',
            'location',
        ]));
    }
    public function store(Request $request)
    {
        $response = $this->encounter_store_api($request);
        if ($response->isSuccessful()) {
            Alert::success($response->statusText(), 'Kunjungan Berhasil Dibuat');
        } else {
            Alert::error($response->statusText(), 'Kunjungan Gagal Dibuat');
        }
        return redirect()->route('satusehat.encounter.index');
    }
    // API SIMRS
    public function encounter_store_api(Request $request)
    {
        $response = $this->encounter_craete($request);
        if ($response->isSuccessful()) {
            $json = $response->getData();
            $data = [
                // identifier
                'satusehat_uuid' => $json->id, #satusehat uuid
                'identifier_id' => $json->identifier[0]->value,
                // location
                'location_id' => $request->location_id,
                'location_name' => $request->location_name,
                // participant
                'practitioner_id' => $request->practitioner_id,
                'practitioner_name' => $request->practitioner_name,
                // patient
                'patient_id' => $request->patient_id,
                'patient_name' => $request->patient_name,
                // periode
                'period_start' =>  $json->period->start,
                // service provider / rumahsakit
                'provider_id' => env('SATUSEHAT_ORGANIZATION_ID'),
                'status' => $json->status,
            ];
            Encounter::create($data);
        }
        return response()->json($response, $response->status());
    }
    // API SATU SEHAT
    public function encounter_craete(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "patient_id" => "required",
            "patient_name" => "required",
            "practitioner_id" => "required",
            "practitioner_name" => "required",
            "location_id" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Data Belum Lengkap', $validator->errors()->first(), 400);
        }
        $request['location_name'] = Location::firstWhere('satusehat_uuid', $request->location_id)->name;
        $request['encounter_id'] = strtoupper(uniqid());
        $token = Token::latest()->first()->access_token;
        $url =  env('SATUSEHAT_BASE_URL') . "/Encounter";
        $data = [
            "resourceType" => "Encounter",
            "status" => "arrived",
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory"
            ],
            "subject" => [
                "reference" => "Patient/" . $request->patient_id,
                "display" => $request->patient_name,
            ],
            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code" => "ATND",
                                    "display" => "attender"
                                ]
                            ]
                        ]
                    ],
                    "individual" => [
                        "reference" => "Practitioner/" . $request->practitioner_id,
                        "display" => $request->practitioner_name,
                    ]
                ]
            ],
            "period" => [
                "start" => now()
            ],
            "location" => [
                [
                    "location" => [
                        "reference" => "Location/" . $request->location_id,
                        "display" => $request->location_name,
                    ]
                ]
            ],
            "statusHistory" => [
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => now()
                    ]
                ]
            ],
            "serviceProvider" => [
                "reference" => "Organization/" . env('SATUSEHAT_ORGANIZATION_ID')
            ],
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/encounter/" . env('SATUSEHAT_ORGANIZATION_ID'),
                    "value" => $request->encounter_id
                ]
            ]
        ];
        $response = Http::withToken($token)->post($url, $data);
        return response()->json($response->json(), $response->status());
    }
}
