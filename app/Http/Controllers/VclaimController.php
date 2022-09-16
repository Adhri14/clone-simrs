<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\VclaimBPJSController;
use App\Models\Poliklinik;
use App\Models\SuratKontrol;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;

class VclaimController extends Controller
{
    public function monitoring_pelayanan_peserta(Request $request)
    {
        $response = null;
        $api = new VclaimBPJSController();
        if ($request->nomorkartu) {
            $response = $api->peserta_nomorkartu($request);
        }
        if ($request->nik) {
            $response = $api->peserta_nik($request);
            if (isset($response->response->peserta->noKartu)) {
                $request["nomorkartu"] = $response->response->peserta->noKartu;
            }
        }
        $monitoring = $api->monitoring_pelayanan_peserta($request);
        return view('vclaim.monitoring_pelayanan_peserta', [
            'request' => $request,
            'response' => $response,
            'monitoring' => $monitoring,
        ]);
    }
    public function delete_sep($noSep, Request $request)
    {
        $api = new VclaimBPJSController();
        $request['noSep'] = $noSep;
        $response = $api->delete_sep($request);
        if ($response->metaData->code == '200') {
            Alert::success('Success', 'Data berhasil dihapus. ' . $response->metaData->message);
        } else {
            Alert::error('Error', 'Data gagal dihapus. ' .  $response->metaData->message);
        }
        return redirect()->back();
    }
    public function data_surat_kontrol(Request $request)
    {
        $suratkontrols = null;
        $api = new VclaimBPJSController();
        if (isset($request->tanggalsuratkontrol)) {
            $tanggal = explode('-', $request->tanggalsuratkontrol);
            $request['tanggal_awal'] =  Carbon::parse($tanggal[0])->format('Y-m-d');
            $request['tanggal_akhir'] =  Carbon::parse($tanggal[1])->format('Y-m-d');
            // dd();
            try {
                $response = $api->data_surat_kontrol($request);
                $suratkontrols = collect($response->response->list);
            } catch (\Throwable $th) {
                //throw $th;
                Alert::error('Error', 'Error : ' .  $th->getMessage());
                return redirect()->route('vclaim.data_surat_kontrol');
            }
        }
        return view('vclaim.data_surat_kontrol', [
            'request' => $request,
            'suratkontrols' => $suratkontrols,
        ]);
    }
    public function delete_surat_kontrol($noSurat, Request $request)
    {
        $api = new VclaimBPJSController();
        $request['noSurat'] = $noSurat;
        $response = $api->delete_surat_kontrol($request);
        if ($response->metaData->code == '200') {
            Alert::success('Success', 'Data berhasil dihapus. ' . $response->metaData->message);
        } else {
            Alert::error('Error', 'Data gagal dihapus. ' .  $response->metaData->message);
        }
        return redirect()->back();
    }
    public function buat_surat_kontrol(Request $request)
    {
        $request->validate([
            'nomorsep_suratkontrol' => 'required',
            'tanggal_suratkontrol' => 'required',
            'kodepoli_suratkontrol' => 'required',
            'kodedokter_suratkontrol' => 'required',
        ]);
        $request['nomorsep'] = $request->nomorsep_suratkontrol;
        $request['tanggalperiksa'] = $request->tanggal_suratkontrol;
        $request['kodepoli'] = $request->kodepoli_suratkontrol;
        $poli = Poliklinik::where('kodesubspesialis',$request->kodepoli)->first();
        $request['kodedokter'] = $request->kodedokter_suratkontrol;
        $vclaim = new VclaimBPJSController();
        $sk = $vclaim->insert_rencana_kontrol($request);
        if ($sk->metaData->code == 200) {
            SuratKontrol::create([
                "tglTerbitKontrol" => Carbon::now()->format('Y-m-d'),
                "tglRencanaKontrol" => $sk->response->tglRencanaKontrol,
                "kodeDokter" => $request->kodedokter,
                "namaDokter" => $sk->response->namaDokter,
                "noSuratKontrol" => $sk->response->noSuratKontrol,
                "namaJnsKontrol" => "Surat Kontrol",
                "noSepAsalKontrol" => $request->nomorsep,
                "noKartu" => $sk->response->noKartu,
                "nama" => $sk->response->nama,
                "kelamin" => $sk->response->kelamin,
                "tglLahir" => $sk->response->tglLahir,
                "user" => Auth::user()->name,
            ]);
            Alert::success('Success', 'Pembuatan Surat Kontrol Berhasil Silahkan tulis nomor surat ini untuk pasien : ' .  $sk->response->noSuratKontrol);
            return redirect()->back();
        } else {
            Alert::error('Error', 'Pembuatan Surat Kontrol Gagal karena ' .  $sk->metaData->message);
            return redirect()->back();
        }
    }
}
