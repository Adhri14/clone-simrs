<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\PasienDB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RISController extends ApiController
{
    public $baseurl = "http://192.168.10.22/ris/public/api/";

    public function pasien_get(Request $request)
    {
        if (isset($request->id)) {
            $pasien = PasienDB::firstWhere('no_urut', $request->id);
            $data['id'] = $pasien->no_urut;
            $data['nama'] = $pasien->nama_px;
            $data['norm'] = $pasien->no_rm;
            $data['jk'] = $pasien->jenis_kelamin;
            $data['tgl_lahir'] = Carbon::parse($pasien->tgl_lahir)->format('Y-m-d');
            $data['kota'] = $pasien->kabupaten;
            $data['alamat'] = "KEC. " . $pasien->kecamatans->nama_kecamatan . ' ' . $pasien->alamat;
        } else {
            $data = PasienDB::where('Kirim_ris_pasien', 1)->get(['no_urut', 'nama_px', 'no_rm', 'jenis_kelamin', 'tgl_lahir', 'kabupaten', 'alamat']);
        }
        return $this->sendResponse('OK', $data);
    }
    public function pasien_add(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "id" => 'required',
            "nama" => 'required',
            "norm" => 'required',
            "jk" => 'required',
            "tgl_lahir" => 'required',
            "kota" => 'required',
            "alamat" => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 201);
        }
        $url = $this->baseurl . "pasien";
        $response = Http::post(
            $url,
            [
                "id" => $request->id,
                "nama" => $request->nama,
                "norm" => $request->norm,
                "jk" => $request->jk,
                "tgl_lahir" => $request->tgl_lahir,
                "kota" => $request->kota,
                "alamat" => $request->alamat,
            ]
        );
        $data = json_decode($response);
        if ($data->status == "success") {
            return $this->sendResponse("OK", null, 200);
        }
        if ($data->status == "error") {
            return $this->sendError($data->error_desc, null, 400);
        } else {
            return $this->sendError($response->reason(), null, $response->status());
        }
    }
}
