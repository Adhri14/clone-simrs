<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\JadwalDokter;
use App\Models\JadwalOperasi;
use App\Models\KunjunganDB;
use App\Models\LayananDB;
use App\Models\LayananDetailDB;
use App\Models\ParamedisDB;
use App\Models\PasienDB;
use App\Models\PenjaminDB;
use App\Models\Poliklinik;
// use App\Models\SEP;
use App\Models\TarifLayananDetailDB;
use App\Models\TracerDB;
use App\Models\TransaksiDB;
use App\Models\UnitDB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AntrianBPJSController extends Controller
{
    // function WS BPJSam
    // public $baseUrl = 'https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev/';
    public $baseUrl = 'https://apijkn.bpjs-kesehatan.go.id/antreanrs/';

    public $printer_antrian = 'smb://PRINTER:qweqwe@192.168.2.133/Printer Receipt';
    //  public $printer_antrian = 'smb://PRINTER:qweqwe@192.168.2.129/Printer Receipt';
    // public $printer_antrian = 'Printer Receipt';

    public static function signature()
    {
        $cons_id =  env('ANTRIAN_CONS_ID');
        $secretKey = env('ANTRIAN_SECRET_KEY');
        $userkey = env('ANTRIAN_USER_KEY');

        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cons_id . "&" . $tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);

        $response = array(
            'user_key' => $userkey,
            'x-cons-id' => $cons_id,
            'x-timestamp' => $tStamp,
            'x-signature' => $encodedSignature,
            'decrypt_key' => $cons_id . $secretKey . $tStamp,
        );
        return $response;
    }
    public static function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $output = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $output;
    }
    public function ref_poli()
    {
        $url = $this->baseUrl . "ref/poli";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        $response = json_decode($response);
        $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
        $response->response = json_decode($decrypt);
        return $response;
    }
    public function ref_dokter()
    {
        $url = $this->baseUrl . "ref/dokter";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        $response = json_decode($response);
        $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
        $response->response = json_decode($decrypt);
        return $response;
    }
    public function ref_jadwal_dokter(Request $request)
    {
        $request['kodepoli'] = $request->kodepoli;
        $request['tanggal'] = $request->tanggalperiksa;
        $url = $this->baseUrl . "jadwaldokter/kodepoli/" . $request->kodepoli . "/tanggal/" . $request->tanggal;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        $response = json_decode($response);
        if ($response->metadata->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function update_jadwal_dokter(Request $request)
    {
        $url = $this->baseUrl . "jadwaldokter/updatejadwaldokter";
        $signature = $this->signature();
        $client = new Client();
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => json_encode([
                "kodepoli" => $request->kodepoli,
                "kodesubspesialis" => $request->kodesubspesialis,
                "kodedokter" => $request->kodedokter,
                "jadwal" => [
                    [
                        "hari" => "1",
                        "buka" => "08:00",
                        "tutup" => "10:00"
                    ],
                    [
                        "hari" => "2",
                        "buka" => "15:00",
                        "tutup" => "17:00"
                    ]
                ]
            ]),
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function tambah_antrian(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "nomorkartu" =>  "required",
            // "nomorreferensi" =>  "required",
            "nik" =>  "required",
            "nohp" => "required",
            "kodepoli" =>  "required",
            "norm" =>  "required",
            "pasienbaru" =>  "required",
            "tanggalperiksa" =>  "required",
            "kodedokter" =>  "required",
            "jampraktek" =>  "required",
            "jeniskunjungan" => "required",
            "jenispasien" =>  "required",
            "namapoli" =>  "required",
            "namadokter" =>  "required",
            "nomorantrean" =>  "required",
            "angkaantrean" =>  "required",
            "estimasidilayani" =>  "required",
            "sisakuotajkn" =>  "required",
            "kuotajkn" => "required",
            "sisakuotanonjkn" => "required",
            "kuotanonjkn" => "required",
            "keterangan" =>  "required",
        ]);
        if ($validator->fails()) {
            $response = [
                'metadata' => [
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ],
            ];
            return json_decode(json_encode($response));
        }
        $url = $this->baseUrl . "antrean/add";
        $signature = $this->signature();
        $client = new Client();
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => json_encode([
                "kodebooking" => $request->kodebooking,
                "nomorkartu" => $request->nomorkartu,
                "nik" => $request->nik,
                "nohp" => $request->nohp,
                "kodepoli" => $request->kodepoli,
                "norm" => $request->norm,
                "pasienbaru" => $request->pasienbaru,
                "tanggalperiksa" => $request->tanggalperiksa,
                "kodedokter" => $request->kodedokter,
                "jampraktek" => $request->jampraktek,
                "jeniskunjungan" => $request->jeniskunjungan,
                "nomorreferensi" => $request->nomorreferensi,
                "jenispasien" => $request->jenispasien,
                "namapoli" => $request->namapoli,
                "namadokter" => $request->namadokter,
                "nomorantrean" => $request->nomorantrean,
                "angkaantrean" => $request->angkaantrean,
                "estimasidilayani" => $request->estimasidilayani,
                "sisakuotajkn" => $request->sisakuotajkn,
                "kuotajkn" => $request->kuotajkn,
                "sisakuotanonjkn" => $request->sisakuotanonjkn,
                "kuotanonjkn" => $request->kuotanonjkn,
                "keterangan" => $request->keterangan,
            ]),
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function update_antrian(Request $request)
    {
        // cek request
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "taskid" => "required",
            "waktu" => "required|numeric",
        ]);
        if ($validator->fails()) {
            $response = [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
            return json_decode(json_encode($response));
        }
        $url = $this->baseUrl . "antrean/updatewaktu";
        $signature = $this->signature();
        $client = new Client();
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => json_encode([
                "kodebooking" => $request->kodebooking,
                "taskid" => $request->taskid,
                "waktu" => $request->waktu,
            ]),
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function batal_antrian_bpjs(Request $request)
    {
        // cek request
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "keterangan" => "required",
        ]);
        if ($validator->fails()) {
            return $response = [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
        }
        $url = $this->baseUrl . "antrean/batal";
        $signature = $this->signature();
        $client = new Client();
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => json_encode([
                "kodebooking" => $request->kodebooking,
                "keterangan" => $request->keterangan,
            ]),
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function list_waktu_task(Request $request)
    {
        // cek request
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $url = $this->baseUrl . "antrean/getlisttask";
        $signature = $this->signature();
        $client = new Client();
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => json_encode([
                "kodebooking" => $request->kodebooking,
            ]),
        ]);
        $response = json_decode($response->getBody());
        if ($response->metadata->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function dashboard_tanggal(Request $request)
    {
        // cek request
        $validator = Validator::make(request()->all(), [
            "tanggal" => "required",
            "waktu" => "required",
        ]);
        if ($validator->fails()) {
            $response = [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
            return $response;
        }
        // proses
        $url = $this->baseUrl . "dashboard/waktutunggu/tanggal/" . $request->tanggal . "/waktu/" . $request->waktu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        $response = json_decode($response);
        return $response;
    }
    public function dashboard_bulan(Request $request)
    {
        // cek request
        $validator = Validator::make(request()->all(), [
            "bulan" => "required",
            "tahun" => "required",
            "waktu" => "required",
        ]);
        if ($validator->fails()) {
            $response = [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
            return $response;
        }
        // proses
        $url = $this->baseUrl . "dashboard/waktutunggu/bulan/" . $request->bulan . "/tahun/" . $request->tahun . "/waktu/" . $request->waktu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        $response = json_decode($response);
        return $response;
    }
    // function WS RS
    public function token(Request $request)
    {
        if (Auth::attempt(['username' => $request->header('x-username'), 'password' => $request->header('x-password')])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $response = [
                "response" => [
                    "token" => $success['token'],
                ],
                "metadata" => [
                    "code" => 200,
                    "message" => "OK"
                ]
            ];
            return $response;
        } else {
            $response = [
                "metadata" => [
                    "code" => 201,
                    "message" => "Unauthorized (Username dan Password Salah)"
                ]
            ];
            return $response;
        }
    }
    public function auth_token(Request $request)
    {
        $aktif = Auth::check();
        $tokenexpired = 3600;
        if ($aktif == false) {
            if ($request->hasHeader('x-token')) {
                if ($request->hasHeader('x-username')) {
                    // $user = User::where('username', $request->header('x-username'))->first();
                    $credentials = $request->header('x-token');
                    $token = PersonalAccessToken::findToken($credentials);
                    // token tidak ditemukan
                    if (!$token) {
                        return $response = [
                            "metadata" => [
                                "code" => 201,
                                "message" => "Unauthorized (Token Salah)"
                            ]
                        ];
                    }
                    // token ditemukan
                    else {
                        $user = $token->tokenable;
                        if (Carbon::now() >  $token->created_at->addMinute($tokenexpired)) {
                            $token->delete();
                            $response = [
                                "metadata" => [
                                    "code" => 201,
                                    "message" => "Token Expired"
                                ]
                            ];
                            return $response;
                        }
                        if ($user->username != $request->header('x-username')) {
                            return $response = [
                                "metadata" => [
                                    "code" => 201,
                                    "message" => "Unauthorized (Username tidak sesuai dengan token)"
                                ]
                            ];
                        } else {
                            return $response = [
                                "metadata" => [
                                    "code" => 200,
                                    "message" => "OK"
                                ]
                            ];
                        }
                    }
                } else {
                    return $response = [
                        "metadata" => [
                            "code" => 201,
                            "message" => "Silahkan isi header dengan x-username"
                        ]
                    ];
                }
            } else {
                return $response = [
                    "metadata" => [
                        "code" => 201,
                        "message" => "Silahkan isi header dengan x-token"
                    ]
                ];
            }
        } else {
            return $response = [
                "metadata" => [
                    "code" => 200,
                    "message" => "OK"
                ]
            ];
        }
    }
    public function status_antrian(Request $request)
    {
        // auth token
        // $auth = $this->auth_token($request);
        // if ($auth['metadata']['code'] != 200) {
        //     return $auth;
        // }
        // check tanggal
        $time = Carbon::parse($request->tanggalperiksa)->endOfDay();
        if ($time->isPast()) {
            return [
                "metadata" => [
                    "code" => 201,
                    "message" => "Tanggal periksa sudah terlewat"
                ]
            ];
        } else {
            $jadwals = $this->ref_jadwal_dokter($request);
            if (isset($jadwals->response)) {
                $jadwal = collect($jadwals->response)->where('kodedokter', $request->kodedokter)->first();
                if (empty($jadwal)) {
                    $response = [
                        "metadata" => [
                            "code" => 201,
                            "message" => "Tidak ada jadwal dokter dihari tersebut."
                        ]
                    ];
                    return $response;
                }
                $antrian = Antrian::where('kodepoli', $request->kodepoli)
                    ->where('tanggalperiksa', $request->tanggalperiksa);
                $antrians = $antrian->count();
                $antreanpanggil =  Antrian::where('kodepoli', $request->kodepoli)
                    ->where('tanggalperiksa', $request->tanggalperiksa)
                    ->where('taskid', 4)->first();
                if (isset($antreanpanggil)) {
                    $nomorantean = $antreanpanggil->nomorantrian;
                } else {
                    $nomorantean = 0;
                }
                $antrianjkn = Antrian::where('kodepoli', $request->kodepoli)
                    ->where('tanggalperiksa', $request->tanggalperiksa)
                    ->where('jenispasien', "JKN")->count();
                $antriannonjkn = Antrian::where('kodepoli', $request->kodepoli)
                    ->where('tanggalperiksa', $request->tanggalperiksa)
                    ->where('jenispasien', "NON-JKN")->count();
                $response = [
                    "response" => [
                        "namapoli" => $jadwal->namapoli,
                        "namadokter" => $jadwal->namadokter,
                        "totalantrean" => $antrians,
                        "sisaantrean" => $jadwal->kapasitaspasien - $antrians,
                        "antreanpanggil" => $nomorantean,
                        "sisakuotajkn" => round($jadwal->kapasitaspasien * 80 / 100) -  $antrianjkn,
                        "kuotajkn" => round($jadwal->kapasitaspasien * 80 / 100),
                        "sisakuotanonjkn" => round($jadwal->kapasitaspasien * 20 / 100) - $antriannonjkn,
                        "kuotanonjkn" =>  round($jadwal->kapasitaspasien * 20 / 100),
                        "keterangan" => "Informasi antrian poliklinik",
                    ],
                    "metadata" => [
                        "message" => "Ok",
                        "code" => 200
                    ]
                ];
                return $response;
            } else {
                return  $jadwals;
            }
        }
    }
    public function ambil_antrian(Request $request)
    {
        // auth token
        // $auth = $this->auth_token($request);
        // if ($auth['metadata']['code'] != 200) {
        //     return $auth;
        // }
        // checking request
        if (substr($request->nohp, -5) == "@c.us") {
            $request['nohp'] = substr($request->nohp, 0, -5);
        }
        $validator = Validator::make(request()->all(), [
            "nik" => "required|numeric|digits:16",
            "nohp" => "required",
            "kodepoli" => "required",
            // "norm" => "required",
            "tanggalperiksa" => "required",
            "kodedokter" => "required",
            "jampraktek" => "required",
            "jeniskunjungan" => "required|numeric",
            // "nomorreferensi" => "numeric",
            "nomorkartu" => "required|numeric|digits:13",
        ]);
        if ($validator->fails()) {
            $response = [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
            return $response;
        }
        // check backdate
        if (Carbon::parse($request->tanggalperiksa)->endOfDay()->isPast()) {
            return [
                "metadata" => [
                    "code" => 201,
                    "message" => "Tanggal periksa sudah terlewat"
                ]
            ];
        }
        if (Carbon::parse($request->tanggalperiksa) >  Carbon::now()->addDay(7)) {
            return [
                "metadata" => [
                    "code" => 201,
                    "message" => "Antrian hanya dapat dibuat untuk 7 hari ke kedepan"
                ]
            ];
        }
        // cek duplikasi nik antrian
        // $antrian_nik = Antrian::where('tanggalperiksa', $request->tanggalperiksa)
        //     ->where('nik', $request->nik)
        //     ->where('taskid', '<=', 4)
        //     ->count();
        // if ($antrian_nik) {
        //     return $response = [
        //         "metadata" => [
        //             "message" => "Terdapat antrian dengan nomor NIK yang sama pada tanggal tersebut yang belum selesai.",
        //             "code" => 201,
        //         ],
        //     ];
        // }
        // proses ambil antrian
        $pasien = PasienDB::where('nik_Bpjs',  $request->nik)->first();
        // cek pasien baru hit info pasien baru
        if (empty($pasien)) {
            return $response = [
                "metadata" => [
                    "message" => "Pasien Baru",
                    "code" => 202,
                ],
            ];
        }
        // cek no kartu sesuai tidak
        else if ($pasien->no_Bpjs != $request->nomorkartu || $pasien->nik_bpjs != $request->nik) {
            return $response = [
                "metadata" => [
                    "message" => "NIK atau Nomor Kartu Tidak Sesuai dengan Data RM, (" . $pasien->no_Bpjs . ", " . $pasien->nik_bpjs . ")",
                    "code" => 201,
                ],
            ];
        }
        // cek pasien lama
        else {
            // cek jika jkn
            if (isset($request->nomorreferensi)) {
                $vclaim = new VclaimBPJSController();
                $request['jenispasien'] = 'JKN';
                // kunjungan kontrol
                if ($request->jeniskunjungan == 3) {
                    $request['nomorsuratkontrol'] = $request->nomorreferensi;
                    $response =  $vclaim->surat_kontrol_nomor($request);
                    if ($response->metaData->code == 200) {
                        $request['nomorrujukan'] = $response->response->sep->provPerujuk->noRujukan;
                        // cek surat kontrol orang lain
                        if ($request->nomorkartu != $response->response->sep->peserta->noKartu) {
                            return [
                                "metadata" => [
                                    "code" => 201,
                                    "message" => "Nomor peserta tidak sesuai dengan surat kontrol."
                                ]
                            ];
                        }
                        // if (Carbon::parse($response->response->tglRencanaKontrol) != Carbon::parse($request->tanggalperiksa)) {
                        //     return [
                        //         "metadata" => [
                        //             "code" => 201,
                        //             "message" => "Tanggal periksa tidak sesuai dengan surat kontrol. Silahkan pengajuan perubahan tanggal surat kontrol terlebih dahulu."
                        //         ]
                        //     ];
                        // }
                    } else {
                        return [
                            "metadata" => [
                                "code" => 201,
                                "message" => $response->metaData->message
                            ]
                        ];
                    }
                }
                // kunjungan rujukan
                else {
                    $request['nomorrujukan'] = $request->nomorreferensi;
                    $response =  $vclaim->rujukan_nomor($request);
                    if ($response->metaData->code == 200) {
                        // cek rujukan orang lain
                        if ($request->nomorkartu != $response->response->rujukan->peserta->noKartu) {
                            return [
                                "metadata" => [
                                    "code" => 201,
                                    "message" => "Nomor peserta tidak sesuai dengan rujukan."
                                ]
                            ];
                        }
                    } else {
                        return [
                            "metadata" => [
                                "code" => 201,
                                "message" => $response->metaData->message
                            ]
                        ];
                    }
                }
            }
            // jika non-jkn harus pilih jenis kunjungan kontrol(3)
            else {
                $request['jenispasien'] = 'NON-JKN';
                // error harus harus pilih jenis kunjungan kontrol(3)
                if ($request->jeniskunjungan != 3) {
                    return [
                        "metadata" => [
                            "message" => "Anda mendaftar tanpa surat Rujukan atau NON-JKN silahkan pilih jenis kunjungan Kontrol(3)",
                            "code" => 201,
                        ],
                    ];
                }
            }
            // ambil data pasien
            $request['norm'] = $pasien->no_rm;
            $request['nama'] = $pasien->nama_px;
            $request['pasienbaru'] = 0;
            // cek jadwal
            // $jadwals = $this->ref_jadwal_dokter($request);
            $jadwals = JadwalDokter::where("kodepoli", $request->kodepoli)->where("hari",  Carbon::parse($request->tanggalperiksa)->dayOfWeek)->get();
            if ($jadwals->count() != 0) {
                $jadwal = $jadwals->where('kodedokter', $request->kodedokter)->first();
                // jika ada jadwal
                if (isset($jadwals)) {
                    // ambil data
                    $request['namapoli'] = $jadwal->namapoli;
                    $request['namadokter'] = $jadwal->namadokter;
                }
                // jika dokter tidak ada
                else {
                    $response = [
                        "metadata" => [
                            "code" => 201,
                            "message" => "Tidak ada jadwal dokter poliklinik tersebut ditanggal tersebut",
                        ]
                    ];
                    return $response;
                }
            } else {
                $response = [
                    "metadata" => [
                        "code" => 201,
                        "message" => "Tidak ada jadwal poliklinik tersebut ditanggal tersebut",
                    ]
                ];
                return $response;
            }
            //  cek nik
            $poli = Poliklinik::where('kodepoli', $request->kodepoli)->first();
            $antrians = Antrian::where('tanggalperiksa', $request->tanggalperiksa)
                ->count();
            $antrian_poli = Antrian::where('tanggalperiksa', $request->tanggalperiksa)
                ->where('kodepoli', $request->kodepoli)
                ->count();
            $antrianjkn = Antrian::where('kodepoli', $request->kodepoli)
                ->where('tanggalperiksa', $request->tanggalperiksa)
                ->where('jenispasien', "JKN")->count();
            $antriannonjkn = Antrian::where('kodepoli', $request->kodepoli)
                ->where('tanggalperiksa', $request->tanggalperiksa)
                ->where('jenispasien', "NON-JKN")->count();
            $request['nomorantrean'] = $request->kodepoli . "-" .  str_pad($antrian_poli + 1, 3, '0', STR_PAD_LEFT);
            $request['angkaantrean'] = $antrians + 1;
            $request['kodebooking'] = strtoupper(uniqid());
            // estimasi
            $timestamp = $request->tanggalperiksa . ' ' . explode('-', $request->jampraktek)[0] . ':00';
            $jadwalbuka = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, 'Asia/Jakarta')->addMinutes(10 * ($antrian_poli + 1));
            $request['estimasidilayani'] = $jadwalbuka->timestamp * 1000;
            $request['sisakuotajkn'] = round($jadwal->kapasitaspasien * 80 / 100)  -  $antrianjkn - 1;
            $request['kuotajkn'] = round($jadwal->kapasitaspasien * 80 / 100);
            $request['sisakuotanonjkn'] = round($jadwal->kapasitaspasien * 20 / 100) - $antriannonjkn - 1;
            $request['kuotanonjkn'] = round($jadwal->kapasitaspasien * 20 / 100);
            $request['keterangan'] = "Peserta harap 60 menit lebih awal dari jadwal untuk checkin dekat mesin antrian untuk mencetak tiket antrian.";
            //tambah antrian bpjs
            $qr = QrCode::backgroundColor(255, 255, 51)->format('png')->generate($request->kodebooking, "public/storage/antrian" . $request->kodebooking . ".png");
            $request['filepath'] = public_path("storage/antrian" . $request->kodebooking . ".png");
            $request['caption'] = "Kode booking : " . $request->kodebooking;
            $request['number'] = $request->nohp;
            $wa = new WhatsappController();
            $wa->send_filepath($request);
            $response = $this->tambah_antrian($request);
            if ($response->metadata->code == 200) {
                //tambah antrian database
                if (isset($suratkontrol)) {
                    $request["nomorsuratkontrol"] = $suratkontrol->noSuratKontrol;
                }
                Antrian::create([
                    "kodebooking" => $request->kodebooking,
                    "nomorkartu" => $request->nomorkartu,
                    "nik" => $request->nik,
                    "nohp" => $request->nohp,
                    "kodepoli" => $request->kodepoli,
                    "norm" => $request->norm,
                    "pasienbaru" => $request->pasienbaru,
                    "tanggalperiksa" => $request->tanggalperiksa,
                    "kodedokter" => $request->kodedokter,
                    "jampraktek" => $request->jampraktek,
                    "jeniskunjungan" => $request->jeniskunjungan,
                    "nomorreferensi" => $request->nomorreferensi,
                    // surat kontrol
                    "nomorrujukan" => $request->nomorrujukan,
                    "nomorsuratkontrol" => $request->nomorsuratkontrol,
                    "jenispasien" => $request->jenispasien,
                    "namapoli" => $request->namapoli,
                    "namadokter" => $request->namadokter,
                    "nomorantrean" => $request->nomorantrean,
                    "angkaantrean" => $request->angkaantrean,
                    "estimasidilayani" => $request->estimasidilayani,
                    "lokasi" => $poli->lokasi,
                    "lantaipendaftaran" => $poli->lantaipendaftaran,
                    "sisakuotajkn" => $request->sisakuotajkn,
                    "kuotajkn" => $request->kuotajkn,
                    "sisakuotanonjkn" => $request->sisakuotanonjkn,
                    "kuotanonjkn" => $request->kuotanonjkn,
                    "keterangan" => $request->keterangan,
                    "status_api" => 1,
                    "taskid" => 0,
                    "user" => "System Antrian",
                    "nama" => $request->nama,
                ]);
                // kirim notif wa
                try {
                    $wa = new WhatsappController();
                    $request['message'] = "*Antrian Berhasil di Daftarkan*\nAntrian anda berhasil didaftarkan melalui Layanan Online RSUD Waled dengan data sebagai berikut : \n\n*Kode Antrian :* " . $request->kodebooking .  "\n*Angka Antrian :* " . $request->angkaantrean .  "\n*Nomor Antrian :* " . $request->nomorantrean .  "\n\n*Nama :* " . $request->nama . "\n*Poliklinik :* " . $request->namapoli  . "\n*Dokter :* " . $request->namadokter  .  "\n*Jam Praktek :* " . $request->jampraktek  .  "\n*Tanggal Berobat :* " . $request->tanggalperiksa . "\n\n*Keterangan :* " . $request->keterangan  .  "\nTerima kasih. Semoga sehat selalu.\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                    $request['number'] = $request->nohp;
                    $wa->send_message($request);
                } catch (\Throwable $th) {
                    //throw $th;
                }
                $response = [
                    "response" => [
                        "nomorantrean" => $request->nomorantrean,
                        "angkaantrean" => $request->angkaantrean,
                        "kodebooking" => $request->kodebooking,
                        "norm" => (string)substr($request->norm, 2),
                        "namapoli" => $request->namapoli,
                        "namadokter" => $request->namadokter,
                        "estimasidilayani" => $request->estimasidilayani,
                        "sisakuotajkn" => $request->sisakuotajkn,
                        "kuotajkn" => $request->kuotajkn,
                        "sisakuotanonjkn" => $request->sisakuotanonjkn,
                        "kuotanonjkn" => $request->kuotanonjkn,
                        "keterangan" => $request->keterangan,
                    ],
                    "metadata" => [
                        "message" => "Ok",
                        "code" => 200
                    ]
                ];
                return json_decode(json_encode($response));
            } else {
                return $response;
            }
        }
    }
    public function info_pasien_baru(Request $request)
    {
        // auth token
        $auth = $this->auth_token($request);
        if ($auth['metadata']['code'] != 200) {
            return $auth;
        }
        // checking request
        $validator = Validator::make(request()->all(), [
            "nik" => "required|digits:16",
            "nomorkartu" => "required|digits:13",
            "nomorkk" => "required",
            "nama" => "required",
            "jeniskelamin" => "required",
            "tanggallahir" => "required",
            "nohp" => "required",
            "alamat" => "required",
            "kodeprop" => "required",
            "namaprop" => "required",
            "kodedati2" => "required",
            "namadati2" => "required",
            "kodekec" => "required",
            "namakec" => "required",
            "kodekel" => "required",
            "namakel" => "required",
            "rw" => "required",
            "rt" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
        }
        $pasien = PasienDB::where('nik_bpjs', $request->nik)->first();
        // cek jika pasien baru
        if (empty($pasien)) {
            // proses pendaftaran baru
            try {
                // checking norm terakhir
                $pasien_terakhir = PasienDB::latest()->first()->no_rm;
                $request['status'] = 1;
                $request['norm'] = $pasien_terakhir + 1;
                // insert pasien
                PasienDB::create(
                    [
                        "no_Bpjs" => $request->nomorkartu,
                        "nik_bpjs" => $request->nik,
                        "no_rm" => $request->norm,
                        // "nomorkk" => $request->nomorkk,
                        "nama_px" => $request->nama,
                        "jenis_kelamin" => $request->jeniskelamin,
                        "tgl_lahir" => $request->tanggallahir,
                        "no_tlp" => $request->nohp,
                        "alamat" => $request->alamat,
                        "kode_propinsi" => $request->kodeprop,
                        // "namaprop" => $request->namaprop,
                        "kode_kabupaten" => $request->kodedati2,
                        // "namadati2" => $request->namadati2,
                        "kode_kecamatan" => $request->kodekec,
                        // "namakec" => $request->namakec,
                        "kode_desa" => $request->kodekel,
                        // "namakel" => $request->namakel,
                        // "rw" => $request->rw,
                        // "rt" => $request->rt,
                        // "status" => $request->status,
                    ]
                );
                return  $response = [
                    "response" => [
                        "norm" => $request->norm,
                    ],
                    "metadata" => [
                        "message" => "Ok",
                        "code" => 200,
                    ],
                ];
            } catch (\Throwable $th) {
                $response = [
                    "metadata" => [
                        "message" => "Gagal Error Code " . $th->getMessage(),
                        "code" => 201,
                    ],
                ];
                return $response;
            }
        }
        // cek jika pasien lama
        else {
            $pasien->update([
                "no_Bpjs" => $request->nomorkartu,
                // "nik_bpjs" => $request->nik,
                // "no_rm" => $request->norm,
                "nomorkk" => $request->nomorkk,
                "nama_px" => $request->nama,
                "jenis_kelamin" => $request->jeniskelamin,
                "tgl_lahir" => $request->tanggallahir,
                "no_tlp" => $request->nohp,
                "alamat" => $request->alamat,
                "kode_propinsi" => $request->kodeprop,
                "namaprop" => $request->namaprop,
                "kode_kabupaten" => $request->kodedati2,
                "namadati2" => $request->namadati2,
                "kode_kecamatan" => $request->kodekec,
                "namakec" => $request->namakec,
                "kode_desa" => $request->kodekel,
                "namakel" => $request->namakel,
                "rw" => $request->rw,
                "rt" => $request->rt,
                // "status" => $request->status,
            ]);
            return $response = [
                "response" => [
                    "norm" => $pasien->no_rm,
                ],
                "metadata" => [
                    "message" => "Ok",
                    "code" => 200,
                ],
            ];
        }
    }
    public function sisa_antrian(Request $request)
    {
        // auth token
        // $auth = $this->auth_token($request);
        // if ($auth['metadata']['code'] != 200) {
        //     return $auth;
        // }
        $antrian = Antrian::firstWhere('kodebooking', $request->kodebooking);
        // antrian ditermukan
        if ($antrian) {
            $sisaantrean = Antrian::where('taskid', "<=", 3)
                ->where('tanggalperiksa', $antrian->tanggalperiksa)
                ->where('taskid', ">=", 1)
                ->count();
            $antreanpanggil =  Antrian::where('taskid', "<=", 3)
                ->where('taskid', ">=", 1)
                ->where('tanggalperiksa', $antrian->tanggalperiksa)
                ->first();
            if (empty($antreanpanggil)) {
                $antreanpanggil['nomorantrean'] = '';
            }
            $antrian['waktutunggu'] = "5";
            $antrian['keterangan'] = "Info antrian anda";
            $response = [
                "response" => [
                    "nomorantrean" => $antrian->nomorantrean,
                    "namapoli" => $antrian->namapoli,
                    "namadokter" => $antrian->namadokter,
                    "sisaantrean" => $sisaantrean,
                    "antreanpanggil" => $antreanpanggil['nomorantrean'],
                    "waktutunggu" => $antrian->waktutunggu * 60 * ($sisaantrean),
                    "keterangan" => $antrian->keterangan,
                ],
                "metadata" => [
                    "message" => "Ok",
                    "code" => 200
                ]
            ];
            return $response;
        }
        // antrian tidak ditermukan
        else {
            return $response = [
                "metadata" => [
                    "message" => "Antrian tidak ditemukan",
                    "code" => 201,
                ],
            ];
        }
    }
    public function batal_antrian(Request $request)
    {
        // return $response = [
        //     'metadata' => [
        //         'code' => 200,
        //         'message' => 'Ok',
        //     ],
        // ];
        // auth token
        // $auth = $this->auth_token($request);
        // if ($auth['metadata']['code'] != 200) {
        //     return $auth;
        // }
        // cek request
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "keterangan" => "required",
        ]);
        if ($validator->fails()) {
            return $response = [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
        }
        $antrian = Antrian::firstWhere('kodebooking', $request->kodebooking);
        // cari antrian
        if ($antrian) {
            $response = $this->batal_antrian_bpjs($request);
            // kirim notif wa
            $wa = new WhatsappController();
            $request['message'] = "Kode antrian " . $antrian->kodebooking . " telah dibatakan karena :\n" . $request->keterangan;
            $request['number'] = $antrian->nohp;
            $wa->send_message($request);
            $antrian->update([
                "taskid" => 99,
                "status_api" => 1,
                "keterangan" => $request->keterangan,
            ]);
            return $response;
        }
        // antrian tidak ditemukan
        else {
            return $response = [
                "metadata" => [
                    "message" => "Antrian tidak ditemukan",
                    "code" => 201,
                ],
            ];
        }
    }
    public function checkin_antrian(Request $request)
    {
        // cek printer
        try {
            $connector = new WindowsPrintConnector($this->printer_antrian);
            $printer = new Printer($connector);
            $printer->close();
        } catch (\Throwable $th) {
            $response = [
                "metadata" => [
                    "message" => "Printer mesin antrian mati",
                    "code" => 201,
                ],
            ];
            return $response;
        }
        // checking request
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "waktu" => "required",
        ]);
        if ($validator->fails()) {
            return $response = [
                'metaData' => [
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ],
            ];
        }
        $antrian = Antrian::firstWhere('kodebooking', $request->kodebooking);
        // jika antrian ditemukan
        if (isset($antrian)) {
            $now = Carbon::now();
            $unit = UnitDB::firstWhere('KDPOLI', $antrian->kodepoli);
            $tarifkarcis = TarifLayananDetailDB::firstWhere('KODE_TARIF_DETAIL', $unit->kode_tarif_karcis);
            $tarifadm = TarifLayananDetailDB::firstWhere('KODE_TARIF_DETAIL', $unit->kode_tarif_adm);
            if ($antrian->pasienbaru) {
                $request['pasienbaru_print'] = 'BARU';
            } else {
                $request['pasienbaru_print'] = 'LAMA';
            }
            // jika pasien jkn
            if ($antrian->jenispasien == "JKN") {
                $request['status_api'] = 1;
                $request['taskid'] = 3;
                $request['keterangan'] = "Untuk pasien peserta JKN silahkan dapat langsung menunggu ke POLIKINIK " . $antrian->namapoli;
                $request['noKartu'] = $antrian->nomorkartu;
                $request['tglSep'] = Carbon::createFromTimestamp($request->waktu / 1000)->format('Y-m-d');
                $request['noMR'] = $antrian->norm;
                $request['nik'] = $antrian->nik;
                $request['nohp'] = $antrian->nohp;
                $request['kodedokter'] = $antrian->kodedokter;
                // insert sep
                $vclaim = new VclaimBPJSController();
                // daftar pake surat kontrol
                if ($antrian->jeniskunjungan == 3) {
                    $request['nomorreferensi'] = $antrian->nomorsuratkontrol;
                    $suratkontrol = $vclaim->surat_kontrol_nomor($request);
                    $request['nomorsuratkontrol'] = $antrian->nomorsuratkontrol;
                    $request['nomorrujukan'] = $suratkontrol->response->sep->provPerujuk->noRujukan;
                    $request['jeniskunjungan_print'] = 'KONTROL';
                    $request['nomorreferensi'] = $antrian->nomorrujukan;
                    $data = $vclaim->rujukan_nomor($request);
                    if ($data->metaData->code == 200) {
                        $rujukan = $data->response->rujukan;
                        $peserta = $rujukan->peserta;
                        $diganosa = $rujukan->diagnosa;
                        $tujuan = $rujukan->poliRujukan;
                        $penjamin = PenjaminDB::where('nama_penjamin_bpjs', $peserta->jenisPeserta->keterangan)->first();
                        $request['kodepenjamin'] = $penjamin->kode_penjamin_simrs;
                        // tujuan rujukan
                        $request['ppkPelayanan'] = "1018R001";
                        $request['jnsPelayanan'] = "2";
                        // peserta
                        $request['klsRawatHak'] = $peserta->hakKelas->kode;
                        $request['klsRawatNaik'] = "";
                        // $request['pembiayaan'] = $peserta->jenisPeserta->kode;
                        // $request['penanggungJawab'] =  $peserta->jenisPeserta->keterangan;
                        // asal rujukan
                        $request['asalRujukan'] = $data->response->asalFaskes;
                        $request['tglRujukan'] = $rujukan->tglKunjungan;
                        $request['noRujukan'] =   $rujukan->noKunjungan;
                        $request['ppkRujukan'] = $rujukan->provPerujuk->kode;
                        // diagnosa
                        $request['catatan'] =  $diganosa->nama;
                        $request['diagAwal'] =  $diganosa->kode;
                        // poli tujuan
                        $request['tujuan'] =  $tujuan->kode;
                        $request['eksekutif'] =  0;
                        // dpjp
                        $request['tujuanKunj'] = "2";
                        $request['flagProcedure'] = "";
                        $request['kdPenunjang'] = "";
                        $request['assesmentPel'] = "5";
                        $request['noSurat'] = $request->nomorsuratkontrol;
                        $request['kodeDPJP'] = $suratkontrol->response->kodeDokter;
                        $request['dpjpLayan'] =  $suratkontrol->response->kodeDokter;
                    } else {
                        return [
                            "metadata" => [
                                "message" => $data->metaData->message,
                                "code" => 201,
                            ],
                        ];
                    }
                    $sep = $vclaim->insert_sep($request);
                }
                // daftar pake rujukan
                else {
                    $request['nomorrujukan'] = $antrian->nomorreferensi;
                    $request['nomorreferensi'] = $antrian->nomorreferensi;
                    $request['jeniskunjungan_print'] = 'RUJUKAN';
                    // cek rujukan
                    $data = $vclaim->rujukan_nomor($request);
                    if ($data->metaData->code == 200) {
                        $rujukan = $data->response->rujukan;
                        $peserta = $rujukan->peserta;
                        $diganosa = $rujukan->diagnosa;
                        $tujuan = $rujukan->poliRujukan;
                        $penjamin = PenjaminDB::where('nama_penjamin_bpjs', $peserta->jenisPeserta->keterangan)->first();
                        $request['kodepenjamin'] = $penjamin->kode_penjamin_simrs;
                        // tujuan rujukan
                        $request['ppkPelayanan'] = "1018R001";
                        $request['jnsPelayanan'] = "2";
                        // peserta
                        $request['klsRawatHak'] = $peserta->hakKelas->kode;
                        $request['klsRawatNaik'] = "";
                        // $request['pembiayaan'] = $peserta->jenisPeserta->kode;
                        // $request['penanggungJawab'] =  $peserta->jenisPeserta->keterangan;
                        // asal rujukan
                        $request['asalRujukan'] = $data->response->asalFaskes;
                        $request['tglRujukan'] = $rujukan->tglKunjungan;
                        $request['noRujukan'] =   $request->nomorreferensi;
                        $request['ppkRujukan'] = $rujukan->provPerujuk->kode;
                        // diagnosa
                        $request['catatan'] =  $diganosa->nama;
                        $request['diagAwal'] =  $diganosa->kode;
                        // poli tujuan
                        $request['tujuan'] =  $tujuan->kode;
                        $request['eksekutif'] =  0;
                        // dpjp
                        $request['tujuanKunj'] = "0";
                        $request['flagProcedure'] = "";
                        $request['kdPenunjang'] = "";
                        $request['assesmentPel'] = "";
                        $request['noSurat'] = "";
                        $request['kodeDPJP'] = "";
                        $request['dpjpLayan'] = $request->kodedokter;
                    } else {
                        return [
                            "metadata" => [
                                "message" => $data->metaData->message,
                                "code" => 201,
                            ],
                        ];
                    }
                    // create sep
                    $sep = $vclaim->insert_sep($request);
                }
                // print sep
                if ($sep->metaData->code == 200) {
                    $print_sep = new AntrianController();
                    $request["nomorsep"] = $sep->response->sep->noSep;
                    $print_sep->print_sep($request, $sep);
                }
                // gagal buat sep
                else {
                    // print antrian ulang
                    try {
                        $print_karcis = new AntrianController();
                        $request['tarifkarcis'] = $tarifkarcis->TOTAL_TARIF_NEW;
                        $request['tarifadm'] = $tarifadm->TOTAL_TARIF_NEW;
                        $request['norm'] = $antrian->norm;
                        $request['nama'] = $antrian->nama;
                        $request['nik'] = $antrian->nik;
                        $request['nomorkartu'] = $antrian->nomorkartu;
                        $request['nohp'] = $antrian->nohp;
                        $request['nomorrujukan'] = $antrian->nomorrujukan;
                        $request['nomorsuratkontrol'] = $antrian->nomorsuratkontrol;
                        $request['namapoli'] = $antrian->namapoli;
                        $request['namadokter'] = $antrian->namadokter;
                        $request['jampraktek'] = $antrian->jampraktek;
                        $request['tanggalperiksa'] = $antrian->tanggalperiksa;
                        $request['jenispasien'] = $antrian->jenispasien;
                        $request['nomorantrean'] = $antrian->nomorantrean;
                        $request['lokasi'] = $antrian->lokasi;
                        $request['angkaantrean'] = $antrian->angkaantrean;
                        $request['lantaipendaftaran'] = $antrian->lantaipendaftaran;
                        $kunjungan = KunjunganDB::where('kode_kunjungan', $antrian->kode_kunjungan)->first();
                        $print_karcis->print_karcis($request, $kunjungan);
                        // notif wa
                        $wa = new WhatsappController();
                        $request['message'] = "Antrian dengan kode booking " . $antrian->kodebooking . " telah melakukan checkin.\n\n" . $request->keterangan;
                        $request['number'] = $antrian->nohp;
                        $wa->send_message($request);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    return [
                        "metadata" => [
                            "message" => $sep->metaData->message,
                            "code" => 201,
                        ],
                    ];
                }
                // rj jkn tipe transaki 2 status layanan 2 status layanan detail opn
                $tipetransaksi = 2;
                $statuslayanan = 2;
                // rj jkn masuk ke tagihan penjamin
                $tagihanpenjamin = $tarifkarcis->TOTAL_TARIF_NEW;
                $totalpenjamin =  $tarifkarcis->TOTAL_TARIF_NEW + $tarifadm->TOTAL_TARIF_NEW;
                $tagihanpribadi = 0;
                $totalpribadi =  0;
            }
            // jika pasien non jkn
            else {
                $request['taskid'] = 3;
                $request['status_api'] = 0;
                $request['kodepenjamin'] = "P01";
                $request['jeniskunjungan_print'] = 'KUNJUNGAN UMUM';
                $request['keterangan'] = "Untuk pasien peserta NON-JKN silahkan menunggu panggilan di Loket Pendaftaran";
                // rj umum tipe transaki 1 status layanan 1 status layanan detail opn
                $tipetransaksi = 1;
                $statuslayanan = 1;
                // rj umum masuk ke tagihan pribadi
                $tagihanpenjamin = 0;
                $totalpenjamin =  0;
                $tagihanpribadi = $tarifkarcis->TOTAL_TARIF_NEW;
                $totalpribadi = $tarifkarcis->TOTAL_TARIF_NEW + $tarifadm->TOTAL_TARIF_NEW;
            }
            // update antrian bpjs
            $response = $this->update_antrian($request);
            // jika antrian berhasil diupdate di bpjs
            if ($response->metadata->code == 200) {
                // insert simrs
                try {
                    $paramedis = ParamedisDB::firstWhere('kode_dokter_jkn', $antrian->kodedokter);
                    // hitung counter kunjungan
                    $kunjungan = KunjunganDB::where('no_rm', $antrian->norm)->orderBy('counter', 'DESC')->first();
                    if (empty($kunjungan)) {
                        $counter = 1;
                    } else {
                        $counter = $kunjungan->counter + 1;
                    }
                    // insert ts kunjungan
                    KunjunganDB::create(
                        [
                            'counter' => $counter,
                            'no_rm' => $antrian->norm,
                            'kode_unit' => $unit->kode_unit,
                            'tgl_masuk' => $now,
                            'kode_paramedis' => $paramedis->kode_paramedis,
                            'status_kunjungan' => 1,
                            'prefix_kunjungan' => $unit->prefix_unit,
                            'kode_penjamin' => $request->kodepenjamin,
                            'pic' => 1319,
                            'id_alasan_masuk' => 1,
                            'kelas' => 3,
                            'hak_kelas' => $request->hakkelas,
                            'no_sep' =>  $request->nomorsep,
                            'no_rujukan' => $antrian->nomorrujukan,
                            'diagx' =>   $request->catatan,
                            'created_at' => $now,
                            'keterangan2' => 'MESIN_2',
                        ]
                    );
                    $kunjungan = KunjunganDB::where('no_rm', $antrian->norm)->where('counter', $counter)->first();
                    // get transaksi sebelumnya
                    $trx_lama = TransaksiDB::where('unit', $unit->kode_unit)
                        ->whereBetween('tgl', [Carbon::now()->startOfDay(), [Carbon::now()->endOfDay()]])
                        ->count();
                    // get kode layanan
                    // PDD2209030-kodetransaksi
                    $kodelayanan = $unit->prefix_unit . $now->format('y') . $now->format('m') . $now->format('d')  . str_pad($trx_lama + 1, 6, '0', STR_PAD_LEFT);
                    //  insert transaksi
                    $trx_baru = TransaksiDB::create([
                        'tgl' => $now->format('Y-m-d'),
                        'no_trx_layanan' => $kodelayanan,
                        'unit' => $unit->kode_unit,
                    ]);
                    //  insert layanan header
                    $layananbaru = LayananDB::create(
                        [
                            'kode_layanan_header' => $kodelayanan,
                            'tgl_entry' => $now,
                            'kode_kunjungan' => $kunjungan->kode_kunjungan,
                            'kode_unit' => $unit->kode_unit,
                            'kode_tipe_transaksi' => $tipetransaksi,
                            'status_layanan' => $statuslayanan,
                            'pic' => '1319',
                            'keterangan' => 'Layanan header melalui antrian sistem',
                        ]
                    );
                    //  insert layanan header dan detail karcis admin konsul 25 + 5 = 30
                    //  insert layanan detail karcis
                    $karcis = LayananDetailDB::create(
                        [
                            'id_layanan_detail' => "DET" . $now->yearIso . $now->month . $now->day .  "001",
                            'row_id_header' => $layananbaru->id,
                            'kode_layanan_header' => $layananbaru->kode_layanan_header,
                            'kode_tarif_detail' => $tarifkarcis->KODE_TARIF_DETAIL,
                            'total_tarif' => $tarifkarcis->TOTAL_TARIF_NEW,
                            'jumlah_layanan' => 1,
                            'tagihan_pribadi' => $tagihanpribadi,
                            'tagihan_penjamin' => $tagihanpenjamin,
                            'total_layanan' => $tarifkarcis->TOTAL_TARIF_NEW,
                            'grantotal_layanan' => $tarifkarcis->TOTAL_TARIF_NEW,
                            'kode_dokter1' => $paramedis->kode_paramedis, // ambil dari mt paramdeis
                            'tgl_layanan_detail' =>  $now,
                        ]
                    );
                    //  insert layanan detail admin
                    $adm = LayananDetailDB::create(
                        [
                            'id_layanan_detail' => "DET" . $now->yearIso . $now->month . $now->day .  "01",
                            'row_id_header' => $layananbaru->id,
                            'kode_layanan_header' => $layananbaru->kode_layanan_header,
                            'kode_tarif_detail' => $tarifadm->KODE_TARIF_DETAIL,
                            'total_tarif' => $tarifadm->TOTAL_TARIF_NEW,
                            'jumlah_layanan' => 1,
                            'tagihan_pribadi' => $tagihanpribadi,
                            'tagihan_penjamin' => $tagihanpenjamin,
                            'total_layanan' => $tarifadm->TOTAL_TARIF_NEW,
                            'grantotal_layanan' => $tarifadm->TOTAL_TARIF_NEW,
                            'kode_dokter1' => 0,
                            'tgl_layanan_detail' =>  $now,
                        ]
                    );
                    //  update layanan header total tagihan
                    $layananbaru->update([
                        'total_layanan' => $tarifkarcis->TOTAL_TARIF_NEW + $tarifadm->TOTAL_TARIF_NEW,
                        'tagihan_pribadi' => $totalpribadi,
                        'tagihan_penjamin' => $totalpenjamin,
                    ]);
                    // insert tracer tc_tracer_header
                    $tracerbaru = TracerDB::create([
                        'kode_kunjungan' => $kunjungan->kode_kunjungan,
                        'tgl_tracer' => $now->format('Y-m-d'),
                        'id_status_tracer' => 1,
                        'cek_tracer' => "N",
                    ]);
                    // print antrian
                    $print_karcis = new AntrianController();
                    $request['tarifkarcis'] = $tarifkarcis->TOTAL_TARIF_NEW;
                    $request['tarifadm'] = $tarifadm->TOTAL_TARIF_NEW;
                    $request['norm'] = $antrian->norm;
                    $request['nama'] = $antrian->nama;
                    $request['nik'] = $antrian->nik;
                    $request['nomorkartu'] = $antrian->nomorkartu;
                    $request['nohp'] = $antrian->nohp;
                    $request['nomorrujukan'] = $antrian->nomorrujukan;
                    $request['nomorsuratkontrol'] = $antrian->nomorsuratkontrol;
                    $request['namapoli'] = $antrian->namapoli;
                    $request['namadokter'] = $antrian->namadokter;
                    $request['jampraktek'] = $antrian->jampraktek;
                    $request['tanggalperiksa'] = $antrian->tanggalperiksa;
                    $request['jenispasien'] = $antrian->jenispasien;
                    $request['nomorantrean'] = $antrian->nomorantrean;
                    $request['lokasi'] = $antrian->lokasi;
                    $request['angkaantrean'] = $antrian->angkaantrean;
                    $request['lantaipendaftaran'] = $antrian->lantaipendaftaran;
                    $print_karcis->print_karcis($request, $kunjungan);
                    // notif wa
                    $wa = new WhatsappController();
                    $request['message'] = "Antrian dengan kode booking " . $antrian->kodebooking . " telah melakukan checkin.\n\n" . $request->keterangan;
                    $request['number'] = $antrian->nohp;
                    $wa->send_message($request);
                    // update antrian
                    Antrian::where('kodebooking', $request->kodebooking)->update([
                        "taskid" => $request->taskid,
                        "status_api" => $request->status_api,
                        "nomorsep" => $request->nomorsep,
                        "keterangan" => $request->keterangan,
                        "kode_kunjungan" => $kunjungan->kode_kunjungan,
                        "taskid1" => $now,
                    ]);
                } catch (\Throwable $th) {
                    //throw $th;
                    return [
                        "metadata" => [
                            "message" => $th->getMessage(),
                            "code" => 201,
                        ],
                    ];
                }
                return [
                    "metadata" => [
                        "message" => "Ok",
                        "code" => 200,
                    ],
                ];
            }
            // jika antrian gagal diupdate di bpjs
            else {
                // print antrian ulang
                try {
                    $print_karcis = new AntrianController();
                    $request['tarifkarcis'] = $tarifkarcis->TOTAL_TARIF_NEW;
                    $request['tarifadm'] = $tarifadm->TOTAL_TARIF_NEW;
                    $request['norm'] = $antrian->norm;
                    $request['nama'] = $antrian->nama;
                    $request['nik'] = $antrian->nik;
                    $request['nomorkartu'] = $antrian->nomorkartu;
                    $request['nohp'] = $antrian->nohp;
                    $request['nomorrujukan'] = $antrian->nomorrujukan;
                    $request['nomorsuratkontrol'] = $antrian->nomorsuratkontrol;
                    $request['namapoli'] = $antrian->namapoli;
                    $request['namadokter'] = $antrian->namadokter;
                    $request['jampraktek'] = $antrian->jampraktek;
                    $request['tanggalperiksa'] = $antrian->tanggalperiksa;
                    $request['jenispasien'] = $antrian->jenispasien;
                    $request['nomorantrean'] = $antrian->nomorantrean;
                    $request['lokasi'] = $antrian->lokasi;
                    $request['angkaantrean'] = $antrian->angkaantrean;
                    $request['lantaipendaftaran'] = $antrian->lantaipendaftaran;
                    $kunjungan = KunjunganDB::where('kode_kunjungan', $antrian->kode_kunjungan)->first();
                    $print_karcis->print_karcis($request, $kunjungan);
                    // notif wa
                    $wa = new WhatsappController();
                    $request['message'] = "Antrian dengan kode booking " . $antrian->kodebooking . " telah melakukan checkin.\n\n" . $request->keterangan;
                    $request['number'] = $antrian->nohp;
                    $wa->send_message($request);
                } catch (\Throwable $th) {
                    //throw $th;
                }
                return $response;
            }
        }
        // jika antrian tidak ditemukan
        else {
            return [
                "metadata" => [
                    "message" => "Kode booking tidak ditemukan",
                    "code" => 201,
                ],
            ];
        }
    }
    public function jadwal_operasi_rs(Request $request)
    {
        // auth token
        $auth = $this->auth_token($request);
        if ($auth['metadata']['code'] != 200) {
            return $auth;
        }
        // checking request
        $validator = Validator::make(request()->all(), [
            "tanggalawal" => "required",
            "tanggalakhir" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
        }
        // end auth token
        $jadwalops = JadwalOperasi::whereBetween('tanggaloperasi', [$request->tanggalawal, $request->tanggalakhir])->get();
        $jadwals = [];
        foreach ($jadwalops as  $jadwalop) {
            if ($jadwalop->terlaksana == "0") {
                $terlaksana = "Belum";
            } else {
                $terlaksana = "Sudah";
            }
            $jadwals[] = [
                "kodebooking" => $jadwalop->kodebooking,
                "tanggaloperasi" => $jadwalop->tanggaloperasi,
                "jenistindakan" => $jadwalop->jenistindakan,
                "kodepoli" => $jadwalop->kodepoli,
                "namapoli" => $jadwalop->namapoli,
                "terlaksana" => $terlaksana,
                "nopeserta" => $jadwalop->nopeserta,
                "lastupdate" => Carbon::parse($jadwalop->updated_at)->format('Y-m-d H:i:s'),
            ];
        }
        $response = [
            "response" => [
                "list" => $jadwals
            ],
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ];
        return $response;
    }
    public function jadwal_operasi_pasien(Request $request)
    {
        // auth token
        $auth = $this->auth_token($request);
        if ($auth['metadata']['code'] != 200) {
            return $auth;
        }
        // checking request
        $validator = Validator::make(request()->all(), [
            "nopeserta" => "required|digits:13",
        ]);
        if ($validator->fails()) {
            return [
                'metadata' => [
                    'code' => 201,
                    'message' => $validator->errors()->first(),
                ],
            ];
        }
        // end auth token
        $jadwalops = JadwalOperasi::where('nopeserta', $request->nopeserta)
            ->where('tanggaloperasi', '>=', Carbon::now()->format('Y-m-d'))
            ->get();

        $jadwals = [];
        foreach ($jadwalops as  $jadwalop) {
            if ($jadwalop->terlaksana == "0") {
                $terlaksana = "Belum";
            } else {
                $terlaksana = "Sudah";
            }
            $jadwals[] = [
                "kodebooking" => $jadwalop->kodebooking,
                "tanggaloperasi" => $jadwalop->tanggaloperasi,
                "jenistindakan" => $jadwalop->jenistindakan,
                "kodepoli" => $jadwalop->kodepoli,
                "namapoli" => $jadwalop->namapoli,
                "terlaksana" => $terlaksana,
                "nopeserta" => $jadwalop->nopeserta,
                "lastupdate" => Carbon::parse($jadwalop->updated_at)->format('Y-m-d H:i:s'),
            ];
        }
        $response = [
            "response" => [
                "list" => $jadwals
            ],
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ];
        return $response;
    }
    // function WS RS
    // public function pasien_pendaftaran(Request $request)
    // {
    //     // checking request
    //     $validator = Validator::make(request()->all(), [
    //         "tanggalperiksa" => "required",
    //     ]);
    //     if ($validator->fails()) {
    //         return [
    //             'metadata' => [
    //                 'code' => 201,
    //                 'message' => $validator->errors()->first(),
    //             ],
    //         ];
    //     }
    //     $antrians = Antrian::where('tanggalperiksa', $request->tanggalperiksa)
    //         ->get();
    //     return response()->json($antrians);
    // }
    // public function panggil_pendaftaran(Request $request)
    // {
    //     // checking request
    //     $validator = Validator::make(request()->all(), [
    //         "tanggalperiksa" => "required",
    //         "kodebooking" => "required",
    //         "loket" => "required",
    //         "lantai" => "required",

    //     ]);
    //     if ($validator->fails()) {
    //         return [
    //             'metadata' => [
    //                 'code' => 201,
    //                 'message' => $validator->errors()->first(),
    //             ],
    //         ];
    //     }

    //     $antrian = Antrian::where('kodebooking', $request->kodebooking)->first();
    //     if ($antrian) {
    //         $request['kodebooking'] = $antrian->kodebooking;
    //         $request['taskid'] = 2;
    //         $now = Carbon::now();
    //         $request['waktu'] = Carbon::now()->timestamp * 1000;
    //         $vclaim = new AntrianBPJSController();
    //         $response = $vclaim->update_antrian($request);
    //         $antrian->update([
    //             'taskid' => 2,
    //             'status_api' => 1,
    //             'keterangan' => "Panggilan ke loket pendaftaran",
    //             'taskid2' => $now,
    //             // 'user' => Auth::user()->name,
    //         ]);
    //         //panggil urusan mesin antrian
    //         try {
    //             $tanggal = Carbon::now()->format('Y-m-d');
    //             $urutan = $antrian->angkaantrean;
    //             $mesin_antrian = DB::connection('mysql3')->table('tb_counter')
    //                 ->where('tgl', $tanggal)
    //                 ->where('kategori', 'WA')
    //                 ->where('loket', $request->loket)
    //                 ->where('lantai', $request->lantai)
    //                 ->get();
    //             if ($mesin_antrian->count() < 1) {
    //                 $mesin_antrian = DB::connection('mysql3')->table('tb_counter')->insert([
    //                     'tgl' => $tanggal,
    //                     'kategori' => 'WA',
    //                     'loket' => $request->loket,
    //                     'counterloket' => $urutan,
    //                     'lantai' => $request->lantai,
    //                     'mastercount' => $urutan,
    //                     'sound' => 'PLAY',
    //                 ]);
    //             } else {
    //                 DB::connection('mysql3')->table('tb_counter')
    //                     ->where('tgl', $tanggal)
    //                     ->where('kategori', 'WA')
    //                     ->where('loket', $request->loket)
    //                     ->where('lantai', $request->lantai)
    //                     ->limit(1)
    //                     ->update([
    //                         // 'counterloket' => $antrian->first()->mastercount + 1,
    //                         'counterloket' => $urutan,
    //                         // 'mastercount' => $antrian->first()->mastercount + 1,
    //                         'mastercount' => $urutan,
    //                         'sound' => 'PLAY',
    //                     ]);
    //             }
    //         } catch (\Throwable $th) {
    //             return $response = [
    //                 "metadata" => [
    //                     "message" => 'Mesin Antrian Tidak Menyala',
    //                     "code" => 201
    //                 ]
    //             ];
    //             // return redirect()->back();
    //         }
    //         return $response = [
    //             "metadata" => [
    //                 "message" => 'Panggilan Berhasil ' . $response->metadata->message,
    //                 "code" => 200
    //             ]
    //         ];
    //     } else {
    //         return $response = [
    //             "metadata" => [
    //                 "message" => "Kodebooking tidak ditemukan",
    //                 "code" => 201
    //             ]
    //         ];
    //     }
    // }
    // public function update_pendaftaran_offline(Request $request)
    // {
    //     // checking request
    //     $validator = Validator::make(request()->all(), [
    //         'antrianid' => 'required',
    //         'statuspasien' => 'required',
    //         'nik' => 'required|digits:16',
    //         'nama' => 'required',
    //         'norm' => 'required',
    //         'nohp' => 'required',
    //         'jeniskunjungan' => 'required',
    //         'tanggalperiksa' => 'required',
    //         'kodepoli' => 'required',
    //         'kodedokter' => 'required',

    //     ]);
    //     if ($request->statuspasien == "LAMA") {
    //         $validator = Validator::make(request()->all(), [
    //             'norm' => 'required',
    //         ]);
    //     }
    //     if ($request->statuspasien == "BARU") {
    //         $validator = Validator::make(request()->all(), [
    //             'jeniskelamin' => 'required',
    //             'tanggallahir' => 'required',
    //             'alamat' => 'required',
    //             'kodeprop' => 'required',

    //         ]);
    //     }
    //     if ($validator->fails()) {
    //         return [
    //             'metadata' => [
    //                 'code' => 201,
    //                 'message' => $validator->errors()->first(),
    //             ],
    //         ];
    //     }

    //     // init
    //     $poli = Poliklinik::where('kodesubspesialis', $request->kodepoli)->first();
    //     $api = new AntrianBPJSController();
    //     // jika pasien jkn
    //     if (isset($request->nomorreferensi)) {
    //         $jenispasien = 'JKN';
    //         $request['keterangan'] = "Silahkan menunggu diruang tunggu poliklinik";
    //         $request['status_api'] = 1;
    //         // insert sep
    //         // $vclaim = new VclaimBPJSController();
    //         // $request['noKartu'] = $request->nomorkartu;
    //         // $request['tglSep'] = Carbon::now()->format('Y-m-d');
    //         // $request['noMR'] = $request->norm;
    //         // $request['nik'] = $request->nik;
    //         // $request['nohp'] = $request->nohp;
    //         // $request['kodedokter'] = $request->kodedokter;
    //         // $request['nomorreferensi'] = $request->nomorreferensi;
    //         // $request['ppkPelayanan'] = "1018R001";
    //         // $request['jnsPelayanan'] = "2";
    //         // $data = $vclaim->rujukan_nomor($request);
    //         // if ($data->metaData->code == 200) {
    //         //     $rujukan = $data->response->rujukan;
    //         //     $peserta = $rujukan->peserta;
    //         //     $diganosa = $rujukan->diagnosa;
    //         //     $tujuan = $rujukan->poliRujukan;
    //         //     // tujuan rujukan
    //         //     $request['ppkPelayanan'] = "1018R001";
    //         //     $request['jnsPelayanan'] = "2";
    //         //     // peserta
    //         //     // $request['klsRawatHak'] = $peserta->hakKelas->kode;
    //         //     // $request['klsRawatNaik'] = "";
    //         //     // $request['pembiayaan'] = $peserta->jenisPeserta->kode;
    //         //     // $request['penanggungJawab'] =  $peserta->jenisPeserta->keterangan;
    //         //     // asal rujukan
    //         //     $request['asalRujukan'] = $data->response->asalFaskes;
    //         //     $request['tglRujukan'] = $rujukan->tglKunjungan;
    //         //     $request['noRujukan'] =   $request->nomorreferensi;
    //         //     $request['ppkRujukan'] = $rujukan->provPerujuk->kode;
    //         //     // diagnosa
    //         //     $request['catatan'] =  $diganosa->nama;
    //         //     $request['diagAwal'] =  $diganosa->kode;
    //         //     // poli tujuan
    //         //     $request['tujuan'] =  "INT";
    //         //     $request['eksekutif'] =  0;
    //         //     // dpjp
    //         //     // $request['dpjpLayan'] =  $request->kodedokter;
    //         // }
    //         // if ($request->nomorsuratkontrol) {
    //         //     $request['tujuanKunj'] = "1";
    //         //     $request['flagProcedure'] = "";
    //         //     $request['kdPenunjang'] = "";
    //         //     $request['assesmentPel'] = "5";
    //         //     $request['noSurat'] = $request->nomorsuratkontrol;
    //         //     $request['kodeDPJP'] = $request->kodedokter;
    //         //     $request['dpjpLayan'] = $request->kodedokter;
    //         // } else {
    //         //     $request['tujuanKunj'] = "2";
    //         //     $request['flagProcedure'] = "";
    //         //     $request['kdPenunjang'] = "";
    //         //     $request['assesmentPel'] = "";
    //         //     $request['noSurat'] = $request->nomorsuratkontrol;
    //         //     $request['kodeDPJP'] = $request->kodedokter;
    //         //     $request['dpjpLayan'] = $request->kodedokter;
    //         // }
    //         // $sep = $vclaim->insert_sep($request);
    //         // dd($sep);
    //     }
    //     // jika pasien non-jkn
    //     else {
    //         $jenispasien = 'NON JKN';
    //         $request['keterangan'] = "Silahkan untuk membayar biaya pendaftaran diloket pembayaran";
    //         $request['status_api'] = 0;
    //     }
    //     $antrian = Antrian::find($request->antrianid);
    //     $waktu1 = Carbon::parse($antrian->taskid1)->timestamp * 1000;
    //     $waktu2 = Carbon::parse($antrian->taskid2)->timestamp * 1000;
    //     $waktu3 =  Carbon::now()->timestamp * 1000;
    //     $request['kodebooking'] = $antrian->kodebooking;
    //     $request['nomorantrean'] = $antrian->nomorantrean;
    //     $request['angkaantrean'] = $antrian->angkaantrean;
    //     $request['jenispasien'] = $jenispasien;
    //     $request['estimasidilayani'] = 0;
    //     $request['sisakuotajkn'] = 5;
    //     $request['sisakuotanonjkn'] = 5;
    //     $request['kuotajkn'] = 20;
    //     $request['kuotanonjkn'] = 20;
    //     $request['namapoli'] = $poli->namapoli;
    //     $request['kodepoli'] = $poli->kodepoli;
    //     // update pasien baru
    //     if ($request->statuspasien == "BARU") {
    //         $request['pasienbaru'] = 1;
    //         $pasien_terakhir = PasienDB::latest()->first()->no_rm;
    //         $request['status'] = 1;
    //         $request['norm'] = $pasien_terakhir + 1;
    //         $pasien = PasienDB::updateOrCreate(
    //             [
    //                 "no_Bpjs" => $request->nomorkartu,
    //                 "nik_bpjs" => $request->nik,
    //                 "no_rm" => $request->norm,
    //             ],
    //             [
    //                 // "nomorkk" => $request->nomorkk,
    //                 "nama_px" => $request->nama,
    //                 "jenis_kelamin" => $request->jeniskelamin,
    //                 "tgl_lahir" => $request->tanggallahir,
    //                 "no_tlp" => $request->nohp,
    //                 "alamat" => $request->alamat,
    //                 "kode_propinsi" => $request->kodeprop,
    //                 // "namaprop" => $request->namaprop,
    //                 "kode_kabupaten" => $request->kodedati2,
    //                 // "namadati2" => $request->namadati2,
    //                 "kode_kecamatan" => $request->kodekec,
    //                 // "namakec" => $request->namakec,
    //                 "kode_desa" => $request->kodekel,
    //                 // "namakel" => $request->namakel,
    //                 // "rw" => $request->rw,
    //                 // "rt" => $request->rt,
    //                 // "status" => $request->status,
    //             ]
    //         );
    //     }
    //     // update pasien lama
    //     else {
    //         $pasien = PasienDB::firstWhere('no_rm', $request->norm);
    //         $pasien->update([
    //             "no_Bpjs" => $request->nomorkartu,
    //             "no_tlp" => $request->nohp,
    //         ]);
    //         $request['pasienbaru'] = 0;
    //     }
    //     $res_antrian = $api->tambah_antrian($request);
    //     if ($res_antrian->metadata->code == 200) {
    //         if ($request->statuspasien == "BARU") {
    //             $request['taskid'] = 1;
    //             $request['waktu'] = $waktu1;
    //             $taskid1 = $api->update_antrian($request);
    //             $request['taskid'] = 2;
    //             $request['waktu'] = $waktu2;
    //             $taskid2 = $api->update_antrian($request);
    //         }
    //         $request['taskid'] = 3;
    //         $request['waktu'] = $waktu3;
    //         $taskid3 = $api->update_antrian($request);
    //         $antrian->update([
    //             "nomorkartu" => $request->nomorkartu,
    //             "nik" => $request->nik,
    //             "nohp" => $request->nohp,
    //             "nama" => $pasien->nama_px,
    //             "norm" => $pasien->no_rm,
    //             "jampraktek" => $request->jampraktek,
    //             "jeniskunjungan" => $request->jeniskunjungan,
    //             "nomorreferensi" => $request->nomorreferensi,
    //             "jenispasien" => $jenispasien,
    //             "pasienbaru" => $request->pasienbaru,
    //             "namapoli" => $request->namapoli,
    //             "namadokter" => $request->namadokter,
    //             "taskid" => $request->taskid,
    //             "keterangan" => $request->keterangan,
    //             // "user" => Auth::user()->name,
    //             "status_api" => $request->status_api,
    //         ]);
    //         return $response = [
    //             "metadata" => [
    //                 "message" => 'Success Message : ' . $request->keterangan,
    //                 "code" => 200
    //             ]
    //         ];
    //     } else {
    //         return $response = [
    //             "metadata" => [
    //                 "message" => 'Error Message : ' . $res_antrian->metadata->message,
    //                 "code" => 201
    //             ]
    //         ];
    //     }
    // }
    // public function update_pendaftaran_online(Request $request)
    // {
    //     // checking request
    //     $validator = Validator::make(request()->all(), [
    //         'antrianidOn' => 'required',
    //         'statuspasienOn' => 'required',
    //         'nikOn' => 'required',
    //         'namaOn' => 'required',
    //         'nohpOn' => 'required',
    //         'jeniskelaminOn' => 'required',
    //         'tanggallahirOn' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return [
    //             'metadata' => [
    //                 'code' => 201,
    //                 'message' => $validator->errors()->first(),
    //             ],
    //         ];
    //     }
    //     // init
    //     $antrian = Antrian::firstWhere('id', $request->antrianidOn);
    //     // update antrian bpjs
    //     $request['kodebooking'] = $antrian->kodebooking;
    //     $request['taskid'] = 3;
    //     if ($antrian->jenispasien == "JKN") {
    //         $request['status_api'] = 1;
    //         $request['keterangan'] = "Silahkan melakukan menunggu di poliklinik untuk dilayani";
    //     } else {
    //         $request['status_api'] = 0;
    //         $request['keterangan'] = "Silahkan melakukan pembayaran pendaftaran ke loket pembayaran";
    //     }
    //     $request['waktu'] = Carbon::now()->timestamp * 1000;;
    //     $vclaim = new AntrianBPJSController();
    //     $response = $vclaim->update_antrian($request);
    //     if ($response->metadata->code == 200) {
    //         // update pasien
    //         $pasien = PasienDB::firstWhere('nik_bpjs', $request->nikOn);
    //         $pasien->update(
    //             [
    //                 "no_Bpjs" => $request->nomorkartuOn,
    //                 "nik_bpjs" => $request->nikOn,
    //                 "no_rm" => $request->normOn,
    //                 // "nomorkk" => $request->nomorkk,
    //                 "nama_px" => $request->namaOn,
    //                 "jenis_kelamin" => $request->jeniskelaminOn,
    //                 "tgl_lahir" => $request->tanggallahirOn,
    //                 "no_tlp" => $request->nohpOn,
    //                 "alamat" => $request->alamatOn,
    //                 "kode_propinsi" => $request->kodepropOn,
    //                 // "namaprop" => $request->namaprop,
    //                 "kode_kabupaten" => $request->kodedati2On,
    //                 // "namadati2" => $request->namadati2,
    //                 "kode_kecamatan" => $request->kodekecOn,
    //                 // "namakec" => $request->namakec,
    //                 "kode_desa" => $request->kodekelOn,
    //                 // "namakel" => $request->namakel,
    //                 // "rw" => $request->rw,
    //                 // "rt" => $request->rt,
    //                 // "status" => $request->status,
    //             ]
    //         );
    //         // update antrian simrs
    //         $antrian->update([
    //             'taskid' => 3,
    //             'status_api' => $request->status_api,
    //             'keterangan' => $request->keterangan,
    //             // 'user' => Auth::user()->name,
    //         ]);
    //         Alert::success('Success', "Pendaftaran Berhasil. " . $request->keterangan . " " . $response->metadata->message);
    //         return redirect()->back();
    //     }
    //     // jika gagal update antrian bpjs
    //     else {
    //         Alert::error('Error', "Pendaftaran Gagal.\n" . $response->metadata->message);
    //         return redirect()->back();
    //     }
    // }
}
