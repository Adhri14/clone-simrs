<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\AntrianBPJSController;
use App\Http\Controllers\API\VclaimBPJSController;
use App\Models\Antrian;
use App\Models\AntrianDB;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\PasienDB;
use App\Models\Poliklinik;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;


class AntrianController extends Controller
{
    // console antrian
    public function console()
    {
        $poliklinik = Poliklinik::with(['antrians', 'jadwals'])->where('status', 1)->get();
        return view('simrs.antrian_console', [
            'poliklinik' => $poliklinik,
        ]);
    }
    public function console_jadwaldokter($poli, $tanggal)
    {
        $poli = Poliklinik::with(['jadwals'])->firstWhere('kodesubspesialis', $poli);
        $jadwals = $poli->jadwals->where('hari', Carbon::parse($tanggal)->dayOfWeek)
            ->where('kodesubspesialis', $poli->kodesubspesialis);
        return response()->json($jadwals);
    }
    public function tambah_offline($poli, $dokter, $jadwal)
    {
        $tanggal = Carbon::now()->format('Y-m-d');
        $antrian_poli = Antrian::where('tanggalperiksa', $tanggal)
            ->where('kodepoli', $poli)
            ->count();
        $antrian_tgl = Antrian::where('tanggalperiksa', $tanggal)
            ->count();
        $antrian_dokter = Antrian::where('tanggalperiksa', $tanggal)
            ->where('kodepoli', $poli)
            ->where('kodedokter', $dokter)
            ->count();
        $nomorantrean = $poli . '-' .    str_pad($antrian_poli + 1, 3, '0', STR_PAD_LEFT);
        $angkaantrean = $antrian_tgl + 1;
        $kodebooking = strtoupper(uniqid());
        $poli = Poliklinik::where('kodesubspesialis', $poli)->first();
        $jadwal = $poli->jadwals->where('hari', Carbon::parse($tanggal)->dayOfWeek)->where('kodedokter', $dokter)->first();
        $dokter = Dokter::where('kodedokter', $dokter)->first();
        if ($antrian_dokter >= $jadwal->kapasitaspasien) {
            Alert::error('Error', 'Antrian poliklinik jadwal dokter tersebut telah penuh');
            return redirect()->route('antrian.console');
        }
        $antrian = Antrian::create([
            "kodebooking" => $kodebooking,
            "nik" => 'Offline',
            "nohp" => 'Offline',
            "kodepoli" => $poli->kodesubspesialis,
            "norm" => 'Offline',
            "pasienbaru" => 2,
            "tanggalperiksa" => Carbon::now()->format('Y-m-d'),
            "kodedokter" => $dokter->kodedokter,
            "jampraktek" => $jadwal->jadwal,
            "jeniskunjungan" => 'Offline',
            "jenispasien" => 'Offline',
            "namapoli" =>  $poli->namasubspesialis,
            "namadokter" => $dokter->namadokter,
            "nomorantrean" =>  $nomorantrean,
            "angkaantrean" =>  $angkaantrean,
            "estimasidilayani" => 0,
            "taskid" => 1,
            "taskid1" => Carbon::now(),
            "user" => 'System',
            "keterangan" => 'Ambil antrian offline',
        ]);
        // print antrian
        try {
            // $connector = new WindowsPrintConnector('Printer Receipt');
            $connector = new WindowsPrintConnector("smb://PRINTER:qweqwe@192.168.2.133/Printer Receipt");
            // $connector = new WindowsPrintConnector("smb://PRINTER:qweqwe@ANTRIAN/Printer Receipt");
            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("RSUD Waled\n");
            $printer->setEmphasis(false);
            $printer->text("Melayani Dengan Sepenuh Hati\n");
            $printer->text("------------------------------------------------\n");
            $printer->text("Karcis Antrian Pendaftaran Offline\n");
            $printer->text("Antrian Pendaftaran / Antrian Poliklinik :\n");
            $printer->setTextSize(2, 2);
            $printer->text($antrian->angkaantrean . " / " .  $antrian->nomorantrean . "\n");
            $printer->setTextSize(1, 1);
            $printer->text("Kode Booking : " . $antrian->kodebooking . "\n\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Silahkan menunggu di Loket Pendaftaran\n");
            $printer->cut();
            $printer->close();
        } catch (Exception $e) {
            Alert::error('Error', 'Error Message : ' . $e->getMessage());
            return redirect()->route('antrian.console');
        }
        Alert::success('Success', 'Antrian Berhasil Ditambahkan');
        return redirect()->route('antrian.console');
    }
    public function taskid(Request $request)
    {
        $response = null;
        $api = new AntrianBPJSController();
        if ($request->kodebooking) {
            $response = $api->list_waktu_task($request);
        }
        return view('simrs.antrian_task_id', [
            'request' => $request,
            'response' => $response,
        ]);
    }
    public function checkin_update(Request $request)
    {
        // checking request
        $validator = Validator::make(request()->all(), [
            "kodebooking" => "required",
            "waktu" => "required|numeric",
        ]);
        if ($validator->fails()) {
            $response = [
                'metadata' => [
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ],
            ];
            return $response;
        }
        // cari antrian
        $antrian = Antrian::firstWhere('kodebooking', $request->kodebooking);
        if (isset($antrian)) {
            $api = new AntrianBPJSController();
            $response = json_decode(json_encode($api->checkin_antrian($request)));
            return $response;
        }
        // jika antrian tidak ditemukan
        else {
            return $response = [
                'metadata' => [
                    'code' => 400,
                    'message' => "Antrian tidak ditemukan",
                ],
            ];
        }
    }
    // pendaftaran
    public function pendaftaran(Request $request)
    {
        $antrians = [];
        if ($request->tanggal) {
            $antrians = Antrian::where('tanggalperiksa', $request->tanggal)
                ->get();
        }
        $provinsis = Provinsi::get();
        return view('simrs.antrian_pendaftaran', [
            'antrians' => $antrians,
            'request' => $request,
            'provinsis' => $provinsis,
        ]);
    }
    public function panggil_pendaftaran($kodebooking, $loket, $lantai, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        if ($antrian) {
            $request['kodebooking'] = $antrian->kodebooking;
            $request['taskid'] = 2;
            $now = Carbon::now();
            $request['waktu'] = Carbon::now()->timestamp * 1000;
            $vclaim = new AntrianBPJSController();
            $response = $vclaim->update_antrian($request);
            $antrian->update([
                'taskid' => 2,
                'status_api' => 1,
                'keterangan' => "Panggilan ke loket pendaftaran",
                'taskid2' => $now,
                // 'user' => Auth::user()->name,
            ]);

            //panggil urusan mesin antrian
            try {
                            // notif wa
                            $wa = new WhatsappController();
                            $request['message'] = "Panggilan kepada Antrian dengan kode booking " . $antrian->kodebooking . " untuk melakukan pendaftaran di Loket pendaftaran.";
                            $request['number'] = $antrian->nohp;
                            $wa->send_message($request);

                $tanggal = Carbon::now()->format('Y-m-d');
                $urutan = $antrian->angkaantrean;
                $mesin_antrian = DB::connection('mysql3')->table('tb_counter')
                    ->where('tgl', $tanggal)
                    ->where('kategori', 'WA')
                    ->where('loket', $loket)
                    ->where('lantai', $lantai)
                    ->get();
                if ($mesin_antrian->count() < 1) {
                    $mesin_antrian = DB::connection('mysql3')->table('tb_counter')->insert([
                        'tgl' => $tanggal,
                        'kategori' => 'WA',
                        'loket' => $loket,
                        'counterloket' => $urutan,
                        'lantai' => $lantai,
                        'mastercount' => $urutan,
                        'sound' => 'PLAY',
                    ]);
                } else {
                    DB::connection('mysql3')->table('tb_counter')
                        ->where('tgl', $tanggal)
                        ->where('kategori', 'WA')
                        ->where('loket', $loket)
                        ->where('lantai', $lantai)
                        ->limit(1)
                        ->update([
                            // 'counterloket' => $antrian->first()->mastercount + 1,
                            'counterloket' => $urutan,
                            // 'mastercount' => $antrian->first()->mastercount + 1,
                            'mastercount' => $urutan,
                            'sound' => 'PLAY',
                        ]);
                }


            } catch (\Throwable $th) {
                Alert::error('Error', $th->getMessage);
                return redirect()->back();
            }
            Alert::success('Success', 'Panggilan Berhasil ' . $response->metadata->message);
            return redirect()->back();
        } else {
            Alert::error('Error', 'Kode Booking tidak ditemukan');
            return redirect()->back();
        }
    }
    public function cari_pasien($nik)
    {
        $pasien = PasienDB::where('nik_bpjs', $nik)->first();
        if ($pasien == null) {
            $code = 201;
            $message = "Pasien Tidak Ditemukan. Silahkan daftarkan pasien.";
        } else {
            $message = "Pasien Ditemukan";
            $code = 200;
        }
        $response = [
            "response" => $pasien,
            "metadata" => [
                "message" => $message,
                "code" => $code,
            ]
        ];
        return $response;
    }
    public function update_pendaftaran_offline(Request $request)
    {
        // validation
        $request->validate([
            'antrianid' => 'required',
            'statuspasien' => 'required',
            'nik' => 'required|digits:16',
            'nama' => 'required',
            'nohp' => 'required',
            'jeniskunjungan' => 'required',
            'tanggalperiksa' => 'required',
            'kodepoli' => 'required',
            'kodedokter' => 'required',
        ]);
        if ($request->statuspasien == "BARU") {
            $request->validate([
                'jeniskelamin' => 'required',
                'tanggallahir' => 'required',
                'alamat' => 'required',
                'kodeprop' => 'required',
            ]);
        }
        // init
        $poli = Poliklinik::where('kodesubspesialis', $request->kodepoli)->first();
        $api = new AntrianBPJSController();
        // jika pasien jkn
        if (isset($request->nomorreferensi)) {
            $jenispasien = 'JKN';
            $request['keterangan'] = "Silahkan menunggu diruang tunggu poliklinik";
            $request['status_api'] = 1;
            // insert sep
            // $vclaim = new VclaimBPJSController();
            // $request['noKartu'] = $request->nomorkartu;
            // $request['tglSep'] = Carbon::now()->format('Y-m-d');
            // $request['noMR'] = $request->norm;
            // $request['nik'] = $request->nik;
            // $request['nohp'] = $request->nohp;
            // $request['kodedokter'] = $request->kodedokter;
            // $request['nomorreferensi'] = $request->nomorreferensi;
            // $request['ppkPelayanan'] = "1018R001";
            // $request['jnsPelayanan'] = "2";
            // $data = $vclaim->rujukan_nomor($request);
            // if ($data->metaData->code == 200) {
            //     $rujukan = $data->response->rujukan;
            //     $peserta = $rujukan->peserta;
            //     $diganosa = $rujukan->diagnosa;
            //     $tujuan = $rujukan->poliRujukan;
            //     // tujuan rujukan
            //     $request['ppkPelayanan'] = "1018R001";
            //     $request['jnsPelayanan'] = "2";
            //     // peserta
            //     // $request['klsRawatHak'] = $peserta->hakKelas->kode;
            //     // $request['klsRawatNaik'] = "";
            //     // $request['pembiayaan'] = $peserta->jenisPeserta->kode;
            //     // $request['penanggungJawab'] =  $peserta->jenisPeserta->keterangan;
            //     // asal rujukan
            //     $request['asalRujukan'] = $data->response->asalFaskes;
            //     $request['tglRujukan'] = $rujukan->tglKunjungan;
            //     $request['noRujukan'] =   $request->nomorreferensi;
            //     $request['ppkRujukan'] = $rujukan->provPerujuk->kode;
            //     // diagnosa
            //     $request['catatan'] =  $diganosa->nama;
            //     $request['diagAwal'] =  $diganosa->kode;
            //     // poli tujuan
            //     $request['tujuan'] =  "INT";
            //     $request['eksekutif'] =  0;
            //     // dpjp
            //     // $request['dpjpLayan'] =  $request->kodedokter;
            // }
            // if ($request->nomorsuratkontrol) {
            //     $request['tujuanKunj'] = "1";
            //     $request['flagProcedure'] = "";
            //     $request['kdPenunjang'] = "";
            //     $request['assesmentPel'] = "5";
            //     $request['noSurat'] = $request->nomorsuratkontrol;
            //     $request['kodeDPJP'] = $request->kodedokter;
            //     $request['dpjpLayan'] = $request->kodedokter;
            // } else {
            //     $request['tujuanKunj'] = "2";
            //     $request['flagProcedure'] = "";
            //     $request['kdPenunjang'] = "";
            //     $request['assesmentPel'] = "";
            //     $request['noSurat'] = $request->nomorsuratkontrol;
            //     $request['kodeDPJP'] = $request->kodedokter;
            //     $request['dpjpLayan'] = $request->kodedokter;
            // }
            // $sep = $vclaim->insert_sep($request);
            // dd($sep);
        }
        // jika pasien non-jkn
        else {
            $jenispasien = 'NON JKN';
            $request['keterangan'] = "Silahkan untuk membayar biaya pendaftaran diloket pembayaran";
            $request['status_api'] = 0;
        }
        $antrian = Antrian::find($request->antrianid);
        $waktu1 = Carbon::parse($antrian->taskid1)->timestamp * 1000;
        $waktu2 = Carbon::parse($antrian->taskid2)->timestamp * 1000;
        $waktu3 =  Carbon::now()->timestamp * 1000;
        $request['kodebooking'] = $antrian->kodebooking;
        $request['nomorantrean'] = $antrian->nomorantrean;
        $request['angkaantrean'] = $antrian->angkaantrean;
        $request['jenispasien'] = $jenispasien;
        $request['estimasidilayani'] = 0;
        $request['sisakuotajkn'] = 5;
        $request['sisakuotanonjkn'] = 5;
        $request['kuotajkn'] = 20;
        $request['kuotanonjkn'] = 20;
        $request['namapoli'] = $poli->namapoli;
        $request['kodepoli'] = $poli->kodepoli;
        // update pasien baru
        if ($request->statuspasien == "BARU") {
            $request['pasienbaru'] = 1;
            $pasien_terakhir = PasienDB::latest()->first()->no_rm;
            $request['status'] = 1;
            $request['norm'] = $pasien_terakhir + 1;
            $pasien = PasienDB::updateOrCreate(
                [
                    "no_Bpjs" => $request->nomorkartu,
                    "nik_bpjs" => $request->nik,
                    "no_rm" => $request->norm,
                ],
                [
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
        }
        // update pasien lama
        else {
            $pasien = PasienDB::firstWhere('no_rm', $request->norm);
            $pasien->update([
                "no_Bpjs" => $request->nomorkartu,
                "no_tlp" => $request->nohp,
            ]);
            $request['pasienbaru'] = 0;
        }
        $res_antrian = $api->tambah_antrian($request);
        if ($res_antrian->metadata->code == 200) {
            if ($request->statuspasien == "BARU") {
                $request['taskid'] = 1;
                $request['waktu'] = $waktu1;
                $taskid1 = $api->update_antrian($request);
                $request['taskid'] = 2;
                $request['waktu'] = $waktu2;
                $taskid2 = $api->update_antrian($request);
            }
            $request['taskid'] = 3;
            $request['waktu'] = $waktu3;
            $taskid3 = $api->update_antrian($request);
            $antrian->update([
                "nomorkartu" => $request->nomorkartu,
                "nik" => $request->nik,
                "nohp" => $request->nohp,
                "nama" => $pasien->nama_px,
                "norm" => $pasien->no_rm,
                "jampraktek" => $request->jampraktek,
                "jeniskunjungan" => $request->jeniskunjungan,
                "nomorreferensi" => $request->nomorreferensi,
                "jenispasien" => $jenispasien,
                "pasienbaru" => $request->pasienbaru,
                "namapoli" => $request->namapoli,
                "namadokter" => $request->namadokter,
                "taskid" => $request->taskid,
                "keterangan" => $request->keterangan,
                // "user" => Auth::user()->name,
                "status_api" => $request->status_api,
            ]);
            Alert::success('Success', 'Success Message : ' . $request->keterangan);
            return redirect()->back();
        } else {
            Alert::error('Error', 'Error Message : ' . $res_antrian->metadata->message);
            return redirect()->back();
        }
    }
    public function update_pendaftaran_online(Request $request)
    {
        // validation
        $request->validate([
            'antrianidOn' => 'required',
            'statuspasienOn' => 'required',
            'nikOn' => 'required',
            'namaOn' => 'required',
            'nohpOn' => 'required',
            'jeniskelaminOn' => 'required',
            'tanggallahirOn' => 'required',
            // 'alamatOn' => 'required',
            // 'kodepropOn' => 'required',
        ]);
        // init
        $antrian = Antrian::firstWhere('id', $request->antrianidOn);
        // update antrian bpjs
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 3;
        if ($antrian->jenispasien == "JKN") {
            $request['status_api'] = 1;
            $request['keterangan'] = "Silahkan melakukan menunggu di poliklinik untuk dilayani";
        } else {
            $request['status_api'] = 0;
            $request['keterangan'] = "Silahkan melakukan pembayaran pendaftaran ke loket pembayaran";
        }
        $request['waktu'] = Carbon::now()->timestamp * 1000;;
        $vclaim = new AntrianBPJSController();
        $response = $vclaim->update_antrian($request);
        if ($response->metadata->code == 200) {
            // update pasien
            $pasien = PasienDB::firstWhere('nik_bpjs', $request->nikOn);
            $pasien->update(
                [
                    "no_Bpjs" => $request->nomorkartuOn,
                    "nik_bpjs" => $request->nikOn,
                    "no_rm" => $request->normOn,
                    // "nomorkk" => $request->nomorkk,
                    "nama_px" => $request->namaOn,
                    "jenis_kelamin" => $request->jeniskelaminOn,
                    "tgl_lahir" => $request->tanggallahirOn,
                    "no_tlp" => $request->nohpOn,
                    "alamat" => $request->alamatOn,
                    "kode_propinsi" => $request->kodepropOn,
                    // "namaprop" => $request->namaprop,
                    "kode_kabupaten" => $request->kodedati2On,
                    // "namadati2" => $request->namadati2,
                    "kode_kecamatan" => $request->kodekecOn,
                    // "namakec" => $request->namakec,
                    "kode_desa" => $request->kodekelOn,
                    // "namakel" => $request->namakel,
                    // "rw" => $request->rw,
                    // "rt" => $request->rt,
                    // "status" => $request->status,
                ]
            );
            // update antrian simrs
            $antrian->update([
                'taskid' => 3,
                'status_api' => $request->status_api,
                'keterangan' => $request->keterangan,
                // 'user' => Auth::user()->name,
            ]);
            Alert::success('Success', "Pendaftaran Berhasil. " . $request->keterangan . " " . $response->metadata->message);
            return redirect()->back();
        }
        // jika gagal update antrian bpjs
        else {
            Alert::error('Error', "Pendaftaran Gagal.\n" . $response->metadata->message);
            return redirect()->back();
        }
    }
    public function batal_antrian($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        $request['kodebooking'] = $antrian->kodebooking;
        $request['keterangan'] = "Dibatalkan oleh sistem admin";
        $vclaim = new AntrianBPJSController();
        $response = $vclaim->batal_antrian($request);
        Alert::success('Success', "Antrian berhasil dibatalkan. " . $response->metadata->message);
        return redirect()->back();
    }
    // kasir
    public function pembayaran(Request $request)
    {
        $antrians = [];
        if ($request->tanggal) {
            $antrians = Antrian::where('taskid', '>=', 3)
                ->where('jenispasien', "NON JKN")
                ->whereDate('tanggalperiksa', $request->tanggal)
                ->get();
        }
        return view('simrs.antrian_pembayaran', [
            'antrians' => $antrians,
            'request' => $request,
        ]);
    }
    public function update_pembayaran(Request $request)
    {
        $antrian = Antrian::find($request->antrianid);
        $antrian->update([
            "taskid" => 3,
            // "user" => Auth::user()->name,
            "status_api" => 1,
        ]);
        Alert::success('Success', 'Pembayaran berhasil diupdate');
        return redirect()->back();
    }
    // poliklinik
    public function poli(Request $request)
    {
        $antrians = null;
        if ($request->tanggal) {
            $antrians = Antrian::whereDate('tanggalperiksa', $request->tanggal)
                ->get();
            if ($request->kodepoli != null) {
                $antrians = $antrians->where('kodepoli', $request->kodepoli);
            }
            if ($request->kodedokter != null) {
                $antrians = $antrians->where('kodedokter', $request->kodedokter);
            }
        }
        $polis = Poliklinik::where('status', 1)->get();
        $dokters = Dokter::get();
        return view('simrs.antrian_poli', [
            'antrians' => $antrians,
            'request' => $request,
            'polis' => $polis,
            'dokters' => $dokters,
        ]);
    }
    public function panggil_poli($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        if ($antrian) {
            $request['kodebooking'] = $antrian->kodebooking;
            $request['taskid'] = 4;
            $request['keterangan'] = "Panggilan ke poliklinik yang anda pilih";
            $request['waktu'] = Carbon::now()->timestamp * 1000;
            $vclaim = new AntrianBPJSController();
            $response = $vclaim->update_antrian($request);
            $antrian->update([
                'taskid' => $request->taskid,
                'status_api' => 1,
                'keterangan' => $request->keterangan,
                // 'user' => Auth::user()->name,
            ]);
            Alert::success('Success', 'Panggilan Berhasil ' . $response->metadata->message);
            return redirect()->back();
        } else {
            Alert::error('Error', 'Kodebooking tidak ditemukan');
            return redirect()->back();
        }
    }
    public function edit($id)
    {
        $antrian = Antrian::find($id);
        return response()->json($antrian);
    }
    public function lanjut_farmasi($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 5;
        $request['keterangan'] = "Silahkan tunggu di farmasi";
        $request['waktu'] = Carbon::now()->timestamp * 1000;
        $vclaim = new AntrianBPJSController();
        $response = $vclaim->update_antrian($request);
        $antrian->update([
            'taskid' => $request->taskid,
            'status_api' => 0,
            'keterangan' => $request->keterangan,
            // 'user' => Auth::user()->name,
        ]);
        Alert::success('Success', "Antrian Berhasil Dilanjutkan ke Farmasi.\n" . $response->metadata->message);
        return redirect()->back();
    }
    public function selesai($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 5;
        $request['keterangan'] = "Antrian selesai, semoga cepat sembuh";
        $request['waktu'] = Carbon::now()->timestamp * 1000;
        $vclaim = new AntrianBPJSController();
        $response = $vclaim->update_antrian($request);
        $antrian->update([
            'taskid' => $request->taskid,
            'status_api' => 1,
            'keterangan' => $request->keterangan,
            // 'user' => Auth::user()->name,
        ]);
        Alert::success('Success', "Antrian Selesai. Semoga cepat sembuh.\n" . $response->metadata->message);
        return redirect()->back();
    }
    // farmasi
    public function farmasi(Request $request)
    {
        $antrians = null;
        if ($request->tanggal) {
            $request['tanggal'] = Carbon::now()->format('Y-m-d');
            $antrians = Antrian::whereDate('tanggalperiksa', $request->tanggal)
                ->where('taskid', '>=', 3)->get();
        }
        // $polis = Poliklinik::where('status', 1)->get();
        // $dokters = Dokter::get();
        return view('simrs.antrian_farmasi', [
            'antrians' => $antrians,
            'request' => $request,
            // 'polis' => $polis,
            // 'dokters' => $dokters,
        ]);
    }
    public function racik_farmasi($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        if ($antrian) {
            $request['kodebooking'] = $antrian->kodebooking;
            $request['taskid'] = 6;
            $request['keterangan'] = "Proses peracikan obat";
            $request['waktu'] = Carbon::now()->timestamp * 1000;
            $vclaim = new AntrianBPJSController();
            $response = $vclaim->update_antrian($request);
            $antrian->update([
                'taskid' => $request->taskid,
                'status_api' => 1,
                'keterangan' => $request->keterangan,
                // 'user' => Auth::user()->name,
            ]);
            Alert::success('Proses', 'Proses Peracikan Obat ' . $response->metadata->message);
            return redirect()->back();
        } else {
            Alert::error('Error', 'Kodebooking tidak ditemukan');
            return redirect()->back();
        }
    }
    public function selesai_farmasi($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 7;
        $request['keterangan'] = "Selesai peracikan obat";
        $request['waktu'] = Carbon::now()->timestamp * 1000;
        $vclaim = new AntrianBPJSController();
        $response = $vclaim->update_antrian($request);
        $antrian->update([
            'taskid' => $request->taskid,
            'status_api' => 1,
            'keterangan' => $request->keterangan,
            // 'user' => Auth::user()->name,
        ]);
        Alert::success('Success', 'Selesai Peracikan Obat ' . $response->metadata->message);
        return redirect()->back();
    }
    // farmasi
    public function show($kodebooking, Request $request)
    {
        dd($request->all());
        $antrian = Antrian::firstWhere('kodebooking', $kodebooking);
        $poli = Poliklinik::get();
        return view('simrs.antrian_baru_offline', [
            'poli' => $poli,
            'antrian' => $antrian,
        ]);
    }
    public function display_pendaftaran(Request $request)
    {
        $poliklinik = Poliklinik::with(['antrians'])->where('status', 1)->get();
        return view('simrs.display_pendaftaran', [
            'poliklinik' => $poliklinik,
            'request' => $request,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nohp' => 'required',
            'jeniskunjungan' => 'required',
            'tanggalperiksa' => 'required',
            'kodepoli' => 'required',
            'kodedokter' => 'required',
        ]);
        $api = new AntrianBPJSController();
        $response = $api->ambil_antrian($request);
        $response = json_decode(json_encode($response, true));
        if ($response->metadata->code == 200) {
            Alert::success('Success Title', 'Success Message');
            return redirect()->route('antrian.tambah');
        } else {
            Alert::error('Error Title', "Error Message " . $response->metadata->message);
            return redirect()->route('antrian.tambah');
        }
    }
    // admin pendaftaran
    public function laporan(Request $request)
    {
        if ($request->tanggal == null) {
            $tanggal_awal = Carbon::now()->startOfMonth()->format('Y-m-d');
            $tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        } else {
            $tanggal = explode(' - ', $request->tanggal);
            $tanggal_awal = Carbon::parse($tanggal[0])->format('Y-m-d');
            $tanggal_akhir = Carbon::parse($tanggal[1])->format('Y-m-d');
        }
        $antrians = Antrian::with(['pasien'])
            ->whereBetween('tanggalperiksa', [$tanggal_awal, $tanggal_akhir])
            ->get();
        return view('simrs.antrian_laporan', [
            'antrians' => $antrians,
            'request' => $request,
        ]);
    }
    public function laporan_tanggal(Request $request)
    {
        if ($request['tanggal'] == null) {
            $request['tanggal'] = Carbon::now()->format('Y-m-d');
            $request['waktu'] = 'rs';
            $antrians = null;
            return view('simrs.antrian_laporan_tanggal', [
                'antrians' => $antrians,
                'request' => $request,
            ]);
        } else {
            $api = new AntrianBPJSController();
            $response = $api->dashboard_tanggal($request);
            if ($response->metadata->code == 200) {
                $antrians = $response->response->list;
                Alert::success('Success', "Success Message " . $response->metadata->message);
                return view('simrs.antrian_laporan_tanggal', [
                    'antrians' => $antrians,
                    'request' => $request,
                ]);
            } else {
                Alert::error('Error Title', "Error Message " . $response->metadata->message);
                return redirect()->route('antrian.laporan_tanggal');
            }
        }
    }
    public function laporan_bulan(Request $request)
    {
        if ($request['tanggal'] == null) {
            $request['tanggal'] = Carbon::now()->format('Y-m');
            $request['tahun'] = Carbon::now()->format('Y');
            $request['bulan'] = Carbon::now()->format('m');
            $request['waktu'] = 'rs';
            $antrians = null;
            return view('simrs.antrian_laporan_bulan', [
                'antrians' => $antrians,
                'request' => $request,
            ]);
        } else {
            $tanggal = explode('-', $request->tanggal);
            $request['tahun'] = $tanggal[0];
            $request['bulan'] = $tanggal[1];
            $api = new AntrianBPJSController();
            $response = $api->dashboard_bulan($request);
            if ($response->metadata->code == 200) {
                Alert::success('Success', "Success Message " . $response->metadata->message);
                $antrians = $response->response->list;
                return view('simrs.antrian_laporan_bulan', [
                    'antrians' => $antrians,
                    'request' => $request,
                ]);
            } else {
                Alert::error('Error Title', "Error Message " . $response->metadata->message);
                return redirect()->route('antrian.laporan_bulan');
            }
        }
    }


    // public function baru_online($kodebooking)
    // {
    //     $antrian = Antrian::firstWhere('kodebooking', $kodebooking);
    //     $poli = Poliklinik::get();
    //     $api = new VclaimBPJSController();
    //     $provinsis = $api->ref_provinsi()->response->list;
    //     return view('simrs.antrian_baru_online', [
    //         'poli' => $poli,
    //         'antrian' => $antrian,
    //         'provinsis' => $provinsis,
    //     ]);
    // }
    // public function simpan_baru_online($kodebooking, Request $request)
    // {
    //     $request->validate([
    //         'nomorkartu' => 'required',
    //         'nik' => 'required',
    //         'nomorkk' => 'required',
    //         'nama' => 'required',
    //         'jeniskelamin' => 'required',
    //         'tanggallahir' => 'required',
    //         'nohp' => 'required',
    //         'alamat' => 'required',
    //         'kodeprop' => 'required',
    //     ]);

    //     $api = new AntrianBPJSController();
    //     $request['taskid'] = 3;
    //     $request['waktu'] = Carbon::now()->timestamp * 1000;
    //     $request['kodebooking'] = $kodebooking;
    //     $response = $api->update_antrian($request);
    //     if ($response->metadata->code == 200) {
    //         $pasien = Pasien::count();
    //         $request['norm'] =  Carbon::now()->format('Y') . str_pad($pasien + 1, 4, '0', STR_PAD_LEFT);
    //         Pasien::create($request->except('_token'));
    //         $antrian = Antrian::firstWhere('kodebooking', $kodebooking);
    //         $antrian->update([
    //             'taskid' => 3,
    //             'norm' => $pasien->norm,
    //             'nama' => $pasien->nama,
    //             // 'user' => Auth::user()->name,
    //         ]);
    //     } else {
    //         Alert::error('Error', "Error Message " . $response->metadata->message);
    //     }
    //     return redirect()->route('antrian.pendaftaran');
    // }
    // public function baru_offline($kodebooking)
    // {
    //     $antrian = Antrian::firstWhere('kodebooking', $kodebooking);
    //     $poli = Poliklinik::get();
    //     return view('simrs.antrian_baru_offline', [
    //         'poli' => $poli,
    //         'antrian' => $antrian,
    //     ]);
    // }
    // public function tambah()
    // {
    //     $poli = Poliklinik::get();
    //     return view('simrs.antrian_tambah', [
    //         'poli' => $poli,
    //     ]);
    // }
}
