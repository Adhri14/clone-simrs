<?php

namespace App\Http\Controllers\BPJS\Vclaim;

use App\Http\Controllers\BPJS\ApiBPJSController;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class VclaimController extends ApiBPJSController
{

    public function monitoring_data_kunjungan_index(Request $request)
    {
        $sep = null;
        if ($request->tanggal && $request->jenispelayanan) {
            $response =  $this->monitoring_data_kunjungan($request);
            if ($response->status() == 200) {
                $sep = $response->getData()->response->sep;
                Alert::success($response->getData()->metadata->message, 'Total Data Kunjungan BPJS ' . count($sep) . ' Pasien');
            } else {
                Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
            }
        }
        return view('bpjs.vclaim.monitoring_data_kunjungan_index', compact([
            'request', 'sep'
        ]));
    }
    public function monitoring_data_klaim_index(Request $request)
    {
        $klaim = null;
        if ($request->tanggalpulang && $request->jenispelayanan && $request->statusklaim) {
            $response =  $this->monitoring_data_klaim($request);
            if ($response->status() == 200) {
                $klaim = $response->getData()->response->klaim;
                Alert::success($response->getData()->metadata->message, 'Total Data Kunjungan BPJS ' . count($klaim) . ' Pasien');
            } else {
                Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
            }
        }
        return view('bpjs.vclaim.monitoring_data_klaim_index', compact([
            'request', 'klaim'
        ]));
    }
    public function monitoring_pelayanan_peserta_index(Request $request)
    {
        $peserta = null;
        $sep = null;
        $rujukan = null;
        $rujukan_rs = null;
        $surat_kontrol = null;
        // get  peserta
        if ($request->tanggal) {
            if ($request->nik && $request->tanggal) {
                $response =  $this->peserta_nik($request);
                if ($response->status() == 200) {
                    $peserta = $response->getData()->response->peserta;
                    $request['nomorkartu'] = $peserta->noKartu;
                    Alert::success('OK', 'Peserta Ditemukan');
                } else {
                }
            } else if ($request->nomorkartu && $request->tanggal) {
                $response =  $this->peserta_nomorkartu($request);
                if ($response->status() == 200) {
                    $peserta = $response->getData()->response->peserta;
                    $request['nik'] = $peserta->nik;
                    Alert::success('OK', 'Peserta Ditemukan');
                } else {
                    Alert::error('Error', $response->getData()->metadata->message);
                }
            }
        } else {
            $request['tanggal'] = now()->format('Y-m-d');
        }
        // get data
        if (isset($peserta)) {
            $request['tanggalakhir'] = Carbon::parse($request->tanggal)->format('Y-m-d');
            $request['tanggalmulai'] = Carbon::parse($request->tanggalakhir)->subDays(90)->format('Y-m-d');
            // history sep
            $response = $this->monitoring_pelayanan_peserta($request);
            if ($response->status() == 200) {
                $sep = $response->getData()->response->histori;
            }
            // rujukan fktp
            $response = $this->rujukan_peserta($request);
            if ($response->status() == 200) {
                $rujukan = $response->getData()->response->rujukan;
            }
            // rujukan antar rs
            $response = $this->rujukan_rs_peserta($request);
            if ($response->status() == 200) {
                $rujukan_rs = $response->getData()->response->rujukan;
            }
            // rujukan antar rs
            $request['tahun'] = Carbon::parse($request->tanggal)->format('Y');
            $request['bulan'] = Carbon::parse($request->tanggal)->format('m');
            $request['formatfilter'] = 1;
            $response = $this->surat_kontrol_peserta($request);
            if ($response->status() == 200) {
                $surat_kontrol = $response->getData()->response->list;
            }
        }
        return view('bpjs.vclaim.monitoring_pelayanan_peserta_index', compact([
            'request',
            'peserta',
            'sep',
            'rujukan',
            'rujukan_rs',
            'surat_kontrol',
        ]));
    }
    public function monitoring_klaim_jasaraharja_index(Request $request)
    {
        $klaim = null;
        if ($request->tanggal && $request->jenispelayanan) {
            $tanggal = explode('-', $request->tanggal);
            $request['tanggalmulai'] = Carbon::parse($tanggal[0])->format('Y-m-d');
            $request['tanggalakhir'] = Carbon::parse($tanggal[1])->format('Y-m-d');
            $response =  $this->monitoring_klaim_jasaraharja($request);
            if ($response->status() == 200) {
                if ($response->getData()->response) {
                    $klaim = $response->getData()->response;
                    dd($klaim);
                    Alert::success($response->getData()->metadata->message, 'Total Data Kunjungan BPJS ' . count($klaim) . ' Pasien');
                } else {
                    Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
                }
            } else {
                Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
            }
        }
        return view('bpjs.vclaim.monitoring_klaim_jasaraharja_index', compact([
            'request', 'klaim'
        ]));
    }
    public function referensi_index(Request $request)
    {
        return view('bpjs.vclaim.referensi_index', compact([
            'request',
        ]));
    }
    public function ref_diagnosa_api(Request $request)
    {
        $data = array();
        $response = $this->ref_diagnosa($request);
        if ($response->status() == 200) {
            $diagnosa = $response->getData()->response->diagnosa;
            foreach ($diagnosa as $item) {
                $data[] = array(
                    "id" => $item->kode,
                    "text" => $item->nama
                );
            }
        }
        return response()->json($data);
    }
    public function ref_poliklinik_api(Request $request)
    {
        $data = array();
        $response = $this->ref_poliklinik($request);
        if ($response->status() == 200) {
            $poli = $response->getData()->response->poli;
            foreach ($poli as $item) {
                $data[] = array(
                    "id" => $item->kode,
                    "text" => $item->nama . " (" . $item->kode . ")"
                );
            }
        }
        return response()->json($data);
    }
    public function ref_faskes_api(Request $request)
    {
        $data = array();
        $response = $this->ref_faskes($request);
        if ($response->status() == 200) {
            $faskes = $response->getData()->response->faskes;
            foreach ($faskes as $item) {
                $data[] = array(
                    "id" => $item->kode,
                    "text" => $item->nama . " (" . $item->kode . ")"
                );
            }
        }
        return response()->json($data);
    }
    public function ref_dpjp_api(Request $request)
    {
        $data = array();
        $response = $this->ref_dpjp($request);
        if ($response->status() == 200) {
            $dokter = $response->getData()->response->list;
            foreach ($dokter as $item) {
                if ((strpos(strtoupper($item->nama), strtoupper($request->nama)) !== false)) {
                    $data[] = array(
                        "id" => $item->kode,
                        "text" => $item->nama . " (" . $item->kode . ")"
                    );
                }
            }
        }
        return response()->json($data);
    }
    public function ref_provinsi_api(Request $request)
    {
        $data = array();
        $response = $this->ref_provinsi($request);
        if ($response->status() == 200) {
            $provinsi = $response->getData()->response->list;
            foreach ($provinsi as $item) {
                if ((strpos(strtoupper($item->nama), strtoupper($request->nama)) !== false)) {
                    $data[] = array(
                        "id" => $item->kode,
                        "text" => $item->nama . " (" . $item->kode . ")"
                    );
                }
            }
        }
        return response()->json($data);
    }
    public function ref_kabupaten_api(Request $request)
    {
        $data = array();
        $response = $this->ref_kabupaten($request);
        if ($response->status() == 200) {
            $kabupaten = $response->getData()->response->list;
            foreach ($kabupaten as $item) {
                if ((strpos(strtoupper($item->nama), strtoupper($request->nama)) !== false)) {
                    $data[] = array(
                        "id" => $item->kode,
                        "text" => $item->nama . " (" . $item->kode . ")"
                    );
                }
            }
        }
        return response()->json($data);
    }
    public function ref_kecamatan_api(Request $request)
    {
        $data = array();
        $response = $this->ref_kecamatan($request);
        if ($response->status() == 200) {
            $kecamatan = $response->getData()->response->list;
            foreach ($kecamatan as $item) {
                if ((strpos(strtoupper($item->nama), strtoupper($request->nama)) !== false)) {
                    $data[] = array(
                        "id" => $item->kode,
                        "text" => $item->nama . " (" . $item->kode . ")"
                    );
                }
            }
        }
        return response()->json($data);
    }
    // API VCLAIM
    public static function signature()
    {
        $cons_id =  env('VCLAIM_CONS_ID');
        $secretKey = env('VCLAIM_SECRET_KEY');
        $userkey = env('VCLAIM_USER_KEY');

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
    public function response_decrypt($response, $signature)
    {
        if ($response->failed()) {
            return $this->sendError($response->reason(),  $response->json('response'), $response->status());
        } else {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->json('response'));
            $data = json_decode($decrypt);
            if ($response->json('metaData.code') == 1 || $response->json('metaData.code') == 0) {
                $code = 200;
            } else {
                $code = $response->json('metaData.code');
            }
            return $this->sendResponse($response->json('metaData.message'), $data, $code);
        }
    }
    public function response_no_decrypt($response)
    {
        if ($response->failed()) {
            return $this->sendError($response->reason(),  $response->json('response'), $response->status());
        } else {
            return $this->sendResponse($response->json('metaData.message'), $response->json('response'), $response->json('metaData.code'));
        }
    }
    // MONITORING
    public function monitoring_data_kunjungan(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "tanggal" => "required|date",
            "jenispelayanan" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Monitoring/Kunjungan/Tanggal/" . $request->tanggal . "/JnsPelayanan/" . $request->jenispelayanan;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function monitoring_data_klaim(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "tanggalpulang" => "required|date",
            "jenispelayanan" => "required",
            "statusklaim" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Monitoring/Klaim/Tanggal/" . $request->tanggalpulang . "/JnsPelayanan/" . $request->jenispelayanan . "/Status/" . $request->statusklaim;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function monitoring_pelayanan_peserta(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomorkartu" => "required",
            "tanggalmulai" => "required|date",
            "tanggalakhir" => "required|date",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "monitoring/HistoriPelayanan/NoKartu/" . $request->nomorkartu . "/tglMulai/" . $request->tanggalmulai . "/tglAkhir/" . $request->tanggalakhir;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function monitoring_klaim_jasaraharja(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "jenispelayanan" => "required",
            "tanggalmulai" => "required|date",
            "tanggalakhir" => "required|date",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "monitoring/JasaRaharja/JnsPelayanan/" . $request->jenispelayanan . "/tglMulai/" . $request->tanggalmulai . "/tglAkhir/" . $request->tanggalakhir;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    // PESERTA
    public function peserta_nomorkartu(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomorkartu" => "required",
            "tanggal" => "required|date",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Peserta/nokartu/" . $request->nomorkartu . "/tglSEP/" . $request->tanggal;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function peserta_nik(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nik" => "required",
            "tanggal" => "required|date",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Peserta/nik/" . $request->nik . "/tglSEP/" . $request->tanggal;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    // REFERENSI
    public function ref_diagnosa(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "diagnosa" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "referensi/diagnosa/" . $request->diagnosa;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_poliklinik(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "poliklinik" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "referensi/poli/" . $request->poliklinik;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_faskes(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nama" => "required",
            "jenisfaskes" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "referensi/faskes/" . $request->nama . "/" . $request->jenisfaskes;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_dpjp(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "jenispelayanan" => "required",
            "tanggal" => "required|date",
            "kodespesialis" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "referensi/dokter/pelayanan/" . $request->jenispelayanan . "/tglPelayanan/" . $request->tanggal . "/Spesialis/" . $request->kodespesialis;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_provinsi(Request $request)
    {
        $url = env('VCLAIM_URL') . "referensi/propinsi";
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_kabupaten(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodeprovinsi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "referensi/kabupaten/propinsi/" . $request->kodeprovinsi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function ref_kecamatan(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "kodekabupaten" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "referensi/kecamatan/kabupaten/" . $request->kodekabupaten;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    // RUJUKAN
    public function rujukan_nomor(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Rujukan/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_peserta(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "nomorkartu" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Rujukan/List/Peserta/" . $request->nomorkartu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_rs_nomor(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }

        $url = env('VCLAIM_URL') . "Rujukan/RS/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);

        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_rs_peserta(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "nomorkartu" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Rujukan/RS/List/Peserta/" . $request->nomorkartu;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function rujukan_jumlah_sep(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "jenisrujukan" => "required",
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "Rujukan/JumlahSEP/" . $request->jenisrujukan . "/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function surat_kontrol_peserta(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "tahun" => "required",
            "bulan" => "required",
            "nomorkartu" => "required",
            "formatfilter" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "RencanaKontrol/ListRencanaKontrol/Bulan/" . sprintf("%02d", $request->bulan)  . "/Tahun/" . $request->tahun . "/Nokartu/" . $request->nomorkartu . "/filter/" . $request->formatfilter;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function surat_kontrol_nomor(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "nomorreferensi" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "RencanaKontrol/noSuratKontrol/" . $request->nomorreferensi;
        $signature = $this->signature();
        $response = Http::withHeaders($signature)->get($url);
        return $this->response_decrypt($response, $signature);
    }
    public function sep_insert(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "noKartu" => "required",
            "tglSep" => "required",
            "ppkPelayanan" => "required",
            "jnsPelayanan" => "required",
            "klsRawatHak" => "required",
            "asalRujukan" => "required",
            "tglRujukan" => "required",
            "noRujukan" => "required",
            "ppkRujukan" => "required",
            "catatan" => "required",
            "diagAwal" => "required",
            "tujuan" => "required",
            "eksekutif" => "required",
            "tujuanKunj" => "required",
            // "flagProcedure" => "required",
            // "kdPenunjang" => "required",
            // "assesmentPel" => "required",
            // "noSurat" => "required",
            // "kodeDPJP" => "required",
            "dpjpLayan" => "required",
            "noTelp" => "required",
            "user" => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = env('VCLAIM_URL') . "SEP/2.0/insert";
        $signature = $this->signature();
        $signature['Content-Type'] = 'application/x-www-form-urlencoded';
        $data = [
            "request" => [
                "t_sep" => [
                    "noKartu" => $request->noKartu,
                    "tglSep" => $request->tglSep,
                    "ppkPelayanan" => $request->ppkPelayanan,
                    "jnsPelayanan" => $request->jnsPelayanan,
                    "klsRawat" => [
                        "klsRawatHak" => $request->klsRawatHak,
                        "klsRawatNaik" => "",
                        "pembiayaan" => "",
                        "penanggungJawab" => "",
                    ],
                    "noMR" => $request->noMR,
                    "rujukan" => [
                        "asalRujukan" =>  $request->asalRujukan,
                        "tglRujukan" =>  $request->tglRujukan,
                        "noRujukan" =>  $request->noRujukan,
                        "ppkRujukan" =>  $request->ppkRujukan,
                    ],
                    "catatan" => $request->catatan,
                    "diagAwal" => $request->diagAwal,
                    "poli" => [
                        "tujuan" => $request->tujuan,
                        "eksekutif" => $request->eksekutif,
                    ],
                    "cob" => [
                        "cob" => "0"
                    ],
                    "katarak" => [
                        "katarak" => "0"
                    ],
                    "jaminan" => [
                        "lakaLantas" => "0",
                        "noLP" => "",
                        "penjamin" => [
                            "tglKejadian" => "",
                            "keterangan" => "",
                            "suplesi" => [
                                "suplesi" => "0",
                                "noSepSuplesi" => "",
                                "lokasiLaka" => [
                                    "kdPropinsi" => "",
                                    "kdKabupaten" => "",
                                    "kdKecamatan" => "",
                                ]
                            ]
                        ]
                    ],
                    "tujuanKunj" => $request->tujuanKunj,
                    "flagProcedure" => $request->flagProcedure,
                    "kdPenunjang" => $request->kdPenunjang,
                    "assesmentPel" => $request->assesmentPel,
                    "skdp" => [
                        "noSurat" => $request->noSurat,
                        "kodeDPJP" => $request->kodeDPJP,
                    ],
                    "dpjpLayan" => $request->dpjpLayan,
                    "noTelp" => $request->noTelp,
                    "user" =>  $request->user,
                ]
            ]
        ];
        $response = Http::withHeaders($signature)->post($url, $data);
        return $this->response_decrypt($response, $signature);
    }
}
