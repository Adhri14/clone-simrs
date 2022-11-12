<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EncounterController extends Controller
{
    public function index(Request $request)
    {
        return view('satusehat.encounter', compact([
            'request',
        ]));
    }

    public function encounter_craete(Request $request)
    {
        // $validator = Validator::make(request()->all(), [
        //     "organization_id" => "required",
        //     "identifier" => "required",
        //     "name" => "required",
        //     "phone" => "required",
        //     "email" => "required",
        //     "url" => "required",
        //     "address" => "required",
        //     "postalCode" => "required",
        //     "province" => "required",
        //     "city" => "required",
        //     "district" => "required",
        //     "village" => "required",
        // ]);
        // if ($validator->fails()) {
        //     return $this->sendError('Data Belum Lengkap', $validator->errors()->first(), 400);
        // }
        // $request['cityText'] = City::firstWhere('code', $request->city)->name;
        $token = session()->get('tokenSatuSehat');
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
                "reference" => "Patient/P00076560468",
                "display" => "Budi Santoso"
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
                        "reference" => "Practitioner/N10000001",
                        "display" => "Dokter Bronsig"
                    ]
                ]
            ],
            "period" => [
                "start" => "2022-06-14T07:00:00+07:00"
            ],
            "location" => [
                [
                    "location" => [
                        "reference" => "Location/3399561c-ee8f-4b89-b33b-716c78c78f40",
                        "display" => "Ruang 1A, Poliklinik Bedah Rawat Jalan Terpadu, Lantai 2, Gedung G"
                    ]
                ]
            ],
            "statusHistory" => [
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => "2022-06-14T07:00:00+07:00"
                    ]
                ]
            ],
            "serviceProvider" => [
                "reference" => "Organization/10000004"
            ],
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/encounter/10000004",
                    "value" => "P20240001"
                ]
            ]
        ];

        $jayParsedAry = [
            "resourceType" => "Encounter",
            "status" => "arrived",
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory"
            ],
            "subject" => [
                "reference" => "Patient/P00076560468",
                "display" => "Budi Santoso"
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
                        "reference" => "Practitioner/N10000001",
                        "display" => "Dokter Bronsig"
                    ]
                ]
            ],
            "period" => [
                "start" => "2022-06-14T07:00:00+07:00"
            ],
            "location" => [
                [
                    "location" => [
                        "reference" => "Location/3399561c-ee8f-4b89-b33b-716c78c78f40",
                        "display" => "Ruang 1A, Poliklinik Bedah Rawat Jalan Terpadu, Lantai 2, Gedung G"
                    ]
                ]
            ],
            "statusHistory" => [
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => "2022-06-14T07:00:00+07:00"
                    ]
                ]
            ],
            "serviceProvider" => [
                "reference" => "Organization/10000004"
            ],
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/encounter/10000004",
                    "value" => "P20240001"
                ]
            ]
        ];


        $response = Http::withToken($token)->post($url, $data);
        return response()->json($response->json(), $response->status());
    }
}
