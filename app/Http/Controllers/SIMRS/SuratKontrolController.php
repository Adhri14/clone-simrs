<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Admin\WhatsappController;
use App\Http\Controllers\BPJS\Vclaim\VclaimController;
use App\Http\Controllers\Controller;
use App\Models\ParamedisDB;
use App\Models\PasienDB;
use App\Models\PoliklinikDB;
use App\Models\SuratKontrol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratKontrolController extends Controller
{
    public function store(Request $request)
    {
        $request['noSep'] = $request->nomorsep_suratkontrol;
        $request['tglRencanaKontrol'] = $request->tanggal_suratkontrol;
        $request['kodeDokter'] = $request->kodedokter_suratkontrol;
        $request['poliKontrol'] = $request->kodepoli_suratkontrol;
        $poli = PoliklinikDB::where('kodesubspesialis', $request->poliKontrol)->first();
        $request['user'] = Auth::user()->name;
        $vclaim = new VclaimController();
        $response = $vclaim->suratkontrol_insert($request);
        if ($response->status() == 200) {
            $suratkontrol = $response->getData()->response;
            SuratKontrol::create([
                "tglTerbitKontrol" => now()->format('Y-m-d'),
                "tglRencanaKontrol" => $suratkontrol->tglRencanaKontrol,
                "poliTujuan" => $request->poliKontrol,
                "namaPoliTujuan" => $poli->namasubspesialis,
                "kodeDokter" => $request->kodeDokter,
                "namaDokter" => $suratkontrol->namaDokter,
                "noSuratKontrol" => $suratkontrol->noSuratKontrol,
                "namaJnsKontrol" => "Surat Kontrol",
                "noSepAsalKontrol" => $request->noSep,
                "noKartu" => $suratkontrol->noKartu,
                "nama" => $suratkontrol->nama,
                "kelamin" => $suratkontrol->kelamin,
                "tglLahir" => $suratkontrol->tglLahir,
                "user" => Auth::user()->name,
            ]);
            $pasien = PasienDB::firstWhere('no_Bpjs', $suratkontrol->noKartu);
            $wa = new WhatsappController();
            $request['message'] = "*Surat Kontrol Rawat Jalan*\nTelah berhasil pembuatan surat kontrol atas pasien sebagai berikut.\n\nNama : " . $suratkontrol->nama . "\nNo Surat Kontrol : " . $suratkontrol->noSuratKontrol . "\nTanggal Kontrol : " . $suratkontrol->tglRencanaKontrol . "\nPoliklinik : " . $poli->namasubspesialis . "\n\nUntuk surat kontrol online dapat diakses melalui link berikut.\nsim.rsudwaled.id/simrs/bpjs/vclaim/surat_kontrol_print/" . $suratkontrol->noSuratKontrol;
            $request['number'] = $pasien->no_hp;
            $wa->send_message($request);
            $request['notif'] = "*Surat Kontrol Rawat Jalan*\nTelah berhasil pembuatan surat kontrol atas pasien sebagai berikut.\n\nNama : " . $suratkontrol->nama . "\nNo Surat Kontrol : " . $suratkontrol->noSuratKontrol . "\nTanggal Kontrol : " . $suratkontrol->tglRencanaKontrol . "\nPoliklinik : " . $poli->namasubspesialis . "\n\nUntuk surat kontrol online dapat diakses melalui link berikut.\nsim.rsudwaled.id/simrs/bpjs/vclaim/surat_kontrol_print/" . $suratkontrol->noSuratKontrol;
            $wa->send_notif($request);
        }
        return $response;
    }
    public function update(Request $request)
    {
        $request['noSuratKontrol'] = $request->nomor_suratkontrol;
        $request['noSep'] = $request->nomorsep_suratkontrol;
        $request['kodeDokter'] = $request->kodedokter_suratkontrol;
        $request['poliKontrol'] = $request->kodepoli_suratkontrol;
        $request['tglRencanaKontrol'] = $request->tanggal_suratkontrol;
        $poli = PoliklinikDB::where('kodesubspesialis', $request->poliKontrol)->first();
        $request['user'] = Auth::user()->name;
        $vclaim = new VclaimController();
        $response = $vclaim->suratkontrol_update($request);
        if ($response->status() == 200) {
            $suratkontrol = $response->getData()->response;
            $sk = SuratKontrol::firstWhere('noSuratKontrol', $request->nomor_suratkontrol);
            $sk->update([
                "tglTerbitKontrol" => now()->format('Y-m-d'),
                "tglRencanaKontrol" => $suratkontrol->tglRencanaKontrol,
                "poliTujuan" => $request->poliKontrol,
                "namaPoliTujuan" => $poli->namasubspesialis,
                "kodeDokter" => $request->kodeDokter,
                "namaDokter" => $suratkontrol->namaDokter,
                "noSuratKontrol" => $suratkontrol->noSuratKontrol,
                "namaJnsKontrol" => "Surat Kontrol",
                "noSepAsalKontrol" => $request->noSep,
                "noKartu" => $suratkontrol->noKartu,
                "nama" => $suratkontrol->nama,
                "kelamin" => $suratkontrol->kelamin,
                "tglLahir" => $suratkontrol->tglLahir,
                "user" => Auth::user()->name,
            ]);
        }
        return $response;
    }
    public function destroy(Request $request)
    {
        $request['noSuratKontrol'] = $request->nomor_suratkontrol;
        $request['user'] = Auth::user()->name;
        $vclaim = new VclaimController();
        $response = $vclaim->suratkontrol_delete($request);
        if ($response->status() == 200) {
            $sk = SuratKontrol::firstWhere('noSuratKontrol', $request->nomor_suratkontrol);
            $sk->delete();
        }
        return $response;
    }
    public function print($nomorsuratkontrol, Request $request)
    {
        $request['noSuratKontrol'] = $nomorsuratkontrol;
        $vclaim = new VclaimController();
        $response = $vclaim->suratkontrol_nomor($request);
        if ($response->status() == 200) {
            $suratkontrol = $response->getData()->response;
            $sep = $response->getData()->response->sep;
            $peserta = $response->getData()->response->sep->peserta;
            $pasien = PasienDB::firstWhere('no_Bpjs', $peserta->noKartu);
            $dokter = ParamedisDB::firstWhere('kode_dokter_jkn', $suratkontrol->kodeDokter);
            return view('simrs.suratkontrol.suratkontrol_print', compact([
                'suratkontrol',
                'sep',
                'peserta',
                'pasien',
                'dokter',
            ]));
        } else {
            return $response->getData()->metadata->message;
        }
    }
}
