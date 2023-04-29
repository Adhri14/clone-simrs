<?php

namespace App\Http\Controllers\Inacbg;

use App\Http\Controllers\BPJS\ApiBPJSController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class InacbgController extends ApiBPJSController
{
    public function search_diagnosis(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "keyword" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "search_diagnosis",
            ],
            "data" => [
                "keyword" => $request->keyword,
            ]
        ];
        $json_request = json_encode($request_data);
        $response =  $this->send_request($json_request);
        $datarray = array();
        if ($response->status() == 200) {
            $data = $response->getData()->response->data;
            $count = $response->getData()->response->count;
            if ($count == 0) {
            } else {
                foreach ($data as  $item) {
                    $datarray[] = array(
                        "id" => $item[1],
                        "text" => $item[1] . ' ' . $item[0]
                    );
                }
            }
            return response()->json($datarray);
        } else {
            return $response;
        }
    }
    public function search_procedures(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "keyword" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "search_procedures",
            ],
            "data" => [
                "keyword" => $request->keyword,
            ]
        ];
        $json_request = json_encode($request_data);
        $response =  $this->send_request($json_request);
        $datarray = array();
        if ($response->status() == 200) {
            $data = $response->getData()->response->data;
            $count = $response->getData()->response->count;
            if ($count == 0) {
            } else {
                foreach ($data as  $item) {
                    $datarray[] = array(
                        "id" => $item[1],
                        "text" => $item[1] . ' ' . $item[0]
                    );
                }
            }
            return response()->json($datarray);
        } else {
            return $response;
        }
    }
    public function search_diagnosis_inagrouper(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "keyword" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "search_diagnosis_inagrouper",
            ],
            "data" => [
                "keyword" => $request->keyword
            ]
        ];
        $json_request = json_encode($request_data);
        return $this->send_request($json_request);
    }
    public function search_procedures_inagrouper(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "keyword" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "search_procedures_inagrouper",
            ],
            "data" => [
                "keyword" => $request->keyword
            ]
        ];
        $json_request = json_encode($request_data);
        return $this->send_request($json_request);
    }
    public function new_claim(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomor_kartu" =>  "required",
            "nomor_sep" =>  "required",
            "nomor_rm" =>  "required",
            "nama_pasien" =>  "required",
            "tgl_lahir" =>  "required",
            "gender" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "new_claim",
            ],
            "data" => [
                "nomor_kartu" => $request->nomor_kartu,
                "nomor_sep" => $request->nomor_sep,
                "nomor_rm" => $request->nomor_rm,
                "nama_pasien" => $request->nama_pasien,
                "tgl_lahir" => $request->tgl_lahir,
                "gender" => $request->gender,
            ]
        ];
        $json_request = json_encode($request_data);
        return $this->send_request($json_request);
    }
    public function set_claim(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomor_sep" =>  "required",
            "nomor_kartu" =>  "required",
            "tgl_masuk" =>  "required|date",
            "diagnosa" =>  "required",
            // "procedure" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "set_claim_data",
                "nomor_sep" => $request->nomor_sep,

            ],
            "data" => [
                "nomor_sep" =>  $request->nomor_sep,
                "nomor_kartu" => $request->nomor_kartu,
                "tgl_masuk" => $request->tgl_masuk,
                "tgl_pulang" => $request->tgl_pulang,
                "cara_masuk" => "gp", #isi
                "jenis_rawat" => "2", #inap, jalan, igd
                "kelas_rawat" => "1", #kelas rawat
                "adl_sub_acute" => "0",
                "adl_chronic" => "0",
                "icu_indikator" => "0",
                "icu_los" => "0",
                "ventilator_hour" => "0",
                // "ventilator" => [
                //     "use_ind" => "1",
                //     "start_dttm" => "2023-01-26 12:55:00",
                //     "stop_dttm" => "2023-01-26 17:50:00"
                // ],
                // "upgrade_class_ind" => "0",
                // "upgrade_class_class" => "0",
                // "upgrade_cla ss_los" => "0",
                // "upgrade_class_payor" => "0",
                // "add_payment_pct" => "0",
                "birth_weight" => "0", #berat bayi
                "sistole" => 120, #detak tensi
                "diastole" => 70, #yg dbawah
                "discharge_status" => "1", #kluar
                "diagnosa" => $request->diagnosa,
                "procedure" => $request->procedure,
                "diagnosa_inagrouper" => $request->diagnosa_inagrouper,
                "procedure_inagrouper" => $request->procedure_inagrouper,
                "tarif_rs" => [
                    "prosedur_non_bedah" => "300000",
                    "prosedur_bedah" => "20000000",
                    "konsultasi" => "300000",
                    "tenaga_ahli" => "200000",
                    "keperawatan" => "80000",
                    "penunjang" => "1000000",
                    "radiologi" => "500000",
                    "laboratorium" => "600000",
                    "pelayanan_darah" => "150000",
                    "rehabilitasi" => "100000",
                    "kamar" => "6000000",
                    "rawat_intensif" => "2500000",
                    "obat" => "100000",
                    "obat_kronis" => "1000000",
                    "obat_kemoterapi" => "5000000",
                    "alkes" => "500000",
                    "bmhp" => "400000",
                    "sewa_alat" => "210000"
                ],
                "pemulasaraan_jenazah" => "0",
                "kantong_jenazah" => "0",
                "peti_jenazah" => "0",
                "plastik_erat" => "0",
                "desinfektan_jenazah" => "0",
                "mobil_jenazah" => "0",
                "desinfektan_mobil_jenazah" => "0",
                "covid19_status_cd" => "0",
                "nomor_kartu_t" => "nik",
                "episodes" => "",
                "covid19_cc_ind" => "0",
                "covid19_rs_darurat_ind" => "0",
                "covid19_co_insidense_ind" => "0",
                // "covid19_penunjang_pengurang" => [
                //     "lab_asam_laktat" => "1",
                //     "lab_procalcitonin" => "1",
                //     "lab_crp" => "1",
                //     "lab_kultur" => "1",
                //     "lab_d_dimer" => "1",
                //     "lab_pt" => "1",
                //     "lab_aptt" => "1",
                //     "lab_waktu_pendarahan" => "1",
                //     "lab_anti_hiv" => "1",
                //     "lab_analisa_gas" => "1",
                //     "lab_albumin" => "1",
                //     "rad_thorax_ap_pa" => "0"
                // ],
                "terapi_konvalesen" => "0",
                "akses_naat" => "C",
                // "isoman_ind" => "0",
                "bayi_lahir_status_cd" => 0,
                "dializer_single_use" => "0", #hd setting multiple
                "kantong_darah" => 0,
                // "apgar" => [
                //     "menit_1" =>
                //     [
                //         "appearance" => 1,
                //         "pulse" => 2,
                //         "grimace" => 1,
                //         "activity" => 1,
                //         "respiration" => 1
                //     ],
                //     "menit_5" => [
                //         "appearance" => 2,
                //         "pulse" => 2,
                //         "grimace" => 2,
                //         "activity" => 2,
                //         "respiration" => 2
                //     ],
                // ],
                // "persalinan" => [
                //     "usia_kehamilan" => "22",
                //     "gravida" => "2",
                //     "partus" => "4",
                //     "abortus" => "2",
                //     "onset_kontraksi" => "induksi",
                //     "delivery" => [
                //         [
                //             "delivery_sequence" => "1",
                //             "delivery_method" => "vaginal",
                //             "delivery_dttm" => "2023-01-21 17:01:33",
                //             "letak_janin" => "kepala",
                //             "kondisi" => "livebirth",
                //             "use_manual" => "1",
                //             "use_forcep" => "0",
                //             "use_vacuum" => "1"
                //         ],
                //         [
                //             "delivery_sequence" => "2",
                //             "delivery_method" => "vaginal",
                //             "delivery_dttm" => "2023-01-21 17:03:49",
                //             "letak_janin" => "lintang",
                //             "kondisi" => "livebirth",
                //             "use_manual" => "1",
                //             "use_forcep" => "0",
                //             "use_vacuum" => "0"
                //         ]
                //     ]
                // ],
                "tarif_poli_eks" => "0",
                "nama_dokter" => "RUDY, DR",
                "kode_tarif" => "BP",
                "payor_id" => "3",
                "payor_cd" => "JKN",
                // "cob_cd" => "0001",
                "coder_nik" => "123123123123",

            ]
        ];
        $json_request = json_encode($request_data);
        return $this->send_request($json_request);
    }
    public function grouper(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomor_sep" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "grouper",
                "stage" => "1",
            ],
            "data" => [
                "nomor_sep" => $request->nomor_sep,
            ]
        ];
        $json_request = json_encode($request_data);
        return $this->send_request($json_request);
    }
    public function get_claim_data(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomor_sep" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "get_claim_data",
            ],
            "data" => [
                "nomor_sep" => $request->nomor_sep,
            ]
        ];
        $json_request = json_encode($request_data);
        return $this->send_request($json_request);
    }
    public function get_claim_status(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomor_sep" =>  "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $request_data = [
            "metadata" => [
                "method" => "get_claim_status",
            ],
            "data" => [
                "nomor_sep" => $request->nomor_sep,

            ]
        ];
        $json_request = json_encode($request_data);
        return $this->send_request($json_request);
    }


    public function send_request($json_request)
    {
        // data yang akan dikirimkan dengan method POST adalah encrypted:
        $key = env('EKLAIM_KEY');
        $payload = $this->inacbg_encrypt($json_request, $key);
        // tentukan Content-Type pada http header
        $header = array("Content-Type: application/x-www-form-urlencoded");
        // url server aplikasi E-Klaim,
        // silakan disesuaikan instalasi masing-masing
        $url = "http://192.168.2.210/E-Klaim/ws.php";
        // setup curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // request dengan curl
        $response = curl_exec($ch);
        // terlebih dahulu hilangkan "----BEGIN ENCRYPTED DATA----\r\n"
        // dan hilangkan "----END ENCRYPTED DATA----\r\n" dari response
        $first = strpos($response, "\n") + 1;
        $last = strrpos($response, "\n") - 1;
        $response = substr(
            $response,
            $first,
            strlen($response) - $first - $last
        );
        // decrypt dengan fungsi inacbg_decrypt
        $response = $this->inacbg_decrypt($response, $key);
        // hasil decrypt adalah format json, ditranslate kedalam array
        $msg = json_decode($response);
        return response()->json($msg);
    }
    // Encryption Function
    function inacbg_encrypt($data, $key)
    {

        /// make binary representasion of $key
        $key = hex2bin($key);
        /// check key length, must be 256 bit or 32 bytes
        if (mb_strlen($key, "8bit") !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        /// create initialization vector
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($iv_size); // dengan catatan dibawah
        /// encrypt
        $encrypted = openssl_encrypt(
            $data,
            "aes-256-cbc",
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        /// create signature, against padding oracle attacks
        $signature = mb_substr(hash_hmac(
            "sha256",
            $encrypted,
            $key,
            true
        ), 0, 10, "8bit");
        /// combine all, encode, and format
        $encoded = chunk_split(base64_encode($signature . $iv . $encrypted));
        return $encoded;
    }
    // Decryption Function
    function inacbg_decrypt($str, $strkey)
    {
        /// make binary representation of $key
        $key = hex2bin($strkey);
        /// check key length, must be 256 bit or 32 bytes
        if (mb_strlen($key, "8bit") !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        /// calculate iv size
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        /// breakdown parts
        $decoded = base64_decode($str);
        $signature = mb_substr($decoded, 0, 10, "8bit");
        $iv = mb_substr($decoded, 10, $iv_size, "8bit");
        $encrypted = mb_substr($decoded, $iv_size + 10, NULL, "8bit");
        /// check signature, against padding oracle attack
        $calc_signature = mb_substr(hash_hmac(
            "sha256",
            $encrypted,
            $key,
            true
        ), 0, 10, "8bit");
        if (!$this->inacbg_compare($signature, $calc_signature)) {
            return "SIGNATURE_NOT_MATCH"; /// signature doesn't match
        }
        $decrypted = openssl_decrypt(
            $encrypted,
            "aes-256-cbc",
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        return $decrypted;
    }
    // Compare Function
    function inacbg_compare($a, $b)
    {
        /// compare individually to prevent timing attacks
        /// compare length
        if (strlen($a) !== strlen($b)) return false;

        /// compare individual
        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }

        return $result == 0;
    }
}
