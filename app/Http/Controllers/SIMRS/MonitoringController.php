<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\BPJS\Vclaim\VclaimController;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function monitoring_data_kunjungan_index(Request $request)
    {
        $sep = null;
        $vclaim = new VclaimController();
        if ($request->tanggal && $request->jenisPelayanan) {
            $response =  $vclaim->monitoring_data_kunjungan($request);
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
        $vclaim = new VclaimController();
        if ($request->tanggalPulang && $request->jenisPelayanan && $request->statusKlaim) {
            $response =   $vclaim->monitoring_data_klaim($request);
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
        $vclaim = new VclaimController();
        // get  peserta
        if ($request->tanggal) {
            if ($request->nik && $request->tanggal) {
                $response =  $vclaim->peserta_nik($request);
                if ($response->status() == 200) {
                    $peserta = $response->getData()->response->peserta;
                    $request['nomorKartu'] = $peserta->noKartu;
                    Alert::success('OK', 'Peserta Ditemukan');
                } else {
                }
            } else if ($request->nomorKartu && $request->tanggal) {
                $response =  $vclaim->peserta_nomorkartu($request);
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
            $request['tanggalAkhir'] = Carbon::parse($request->tanggal)->format('Y-m-d');
            $request['tanggalMulai'] = Carbon::parse($request->tanggalAkhir)->subDays(90)->format('Y-m-d');
            // history sep
            $response = $vclaim->monitoring_pelayanan_peserta($request);
            if ($response->status() == 200) {
                $sep = $response->getData()->response->histori;
            }
            // rujukan fktp
            $response = $vclaim->rujukan_peserta($request);
            if ($response->status() == 200) {
                $rujukan = $response->getData()->response->rujukan;
            }
            // rujukan antar rs
            $response = $vclaim->rujukan_rs_peserta($request);
            if ($response->status() == 200) {
                $rujukan_rs = $response->getData()->response->rujukan;
            }
            // rujukan antar rs
            $request['tahun'] = Carbon::parse($request->tanggal)->format('Y');
            $request['bulan'] = Carbon::parse($request->tanggal)->format('m');
            $request['formatfilter'] = 2;
            $response = $vclaim->suratkontrol_peserta($request);
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
        $vclaim = new VclaimController();
        if ($request->tanggal && $request->jenisPelayanan) {
            $tanggal = explode('-', $request->tanggal);
            $request['tanggalMulai'] = Carbon::parse($tanggal[0])->format('Y-m-d');
            $request['tanggalAkhir'] = Carbon::parse($tanggal[1])->format('Y-m-d');
            $response =  $vclaim->monitoring_klaim_jasaraharja($request);
            if ($response->status() == 200) {
                if ($response->getData()->response) {
                    $klaim = $response->getData()->response->jaminan;
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
}
