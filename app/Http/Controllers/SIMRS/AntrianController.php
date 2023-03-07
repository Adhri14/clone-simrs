<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Admin\WhatsappController;
use App\Http\Controllers\API\AntrianBPJSController;
use App\Http\Controllers\BPJS\Antrian\AntrianController as AntrianAntrianController;
use App\Http\Controllers\BPJS\Vclaim\VclaimController;
use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\AntrianDB;
use App\Models\BPJS\Antrian\JadwalDokterAntrian;
use App\Models\JadwalDokter;
use App\Models\KunjunganDB;
use App\Models\ParamedisDB;
use App\Models\PenjaminSimrs;
use App\Models\PoliklinikDB;
use App\Models\SIMRS\JadwalDokter as SIMRSJadwalDokter;
use App\Models\SuratKontrol;
use App\Models\UnitDB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravolt\Indonesia\Models\Provinsi;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use RealRashid\SweetAlert\Facades\Alert;

class AntrianController extends Controller
{
    public function console()
    {
        $poliklinik = PoliklinikDB::with(['antrians', 'jadwals'])->where('status', 1)->get();
        $jadwal = JadwalDokter::where('hari',  now()->dayOfWeek)->get();
        return view('simrs.antrian_console', [
            'poliklinik' => $poliklinik,
            'jadwal' => $jadwal,
        ]);
    }
    public function daftar_pasien_bpjs_offline(Request $request)
    {
        $request['tanggalperiksa'] = now()->format('Y-m-d');
        $request['kodepoli'] = $request->kodesubspesialis;
        $validator = Validator::make(request()->all(), [
            "kodesubspesialis" => "required",
            "kodedokter" => "required",
            "nomorkartu" => "required|numeric",
        ]);
        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->route('antrian.console');
        }

        // cek peserta
        $api = new VclaimController();
        $request['nomorKartu'] = $request->nomorkartu;
        $request['tanggal'] = $request->tanggalperiksa;
        $response =  $api->peserta_nomorkartu($request);
        if ($response->status() == 200) {
            $peserta = $response->getData()->response->peserta;
            // dd($peserta);
            if ($peserta->statusPeserta->kode == 0) {
            } else {
                Alert::error('Error Status Peserta',  "Maaf status peserta " . $peserta->statusPeserta->keterangan);
                return redirect()->route('antrian.console');
            }
        } else {
            Alert::error('Error ' . $response->getData()->metadata->code,  $response->getData()->metadata->message);
            return redirect()->route('antrian.console');
        }

        // get jadwal
        $jadwal = JadwalDokterAntrian::where('kodesubspesialis', $request->kodesubspesialis)
            ->where('kodedokter', $request->kodedokter)
            ->where('hari', now()->dayOfWeek)->first();
        if ($jadwal == null) {
            Alert::error('Error',  "Jadwal tidak ditemukan");
            return redirect()->route('antrian.console');
        }

        $request['nik'] = $peserta->nik;
        $request['nama'] = $peserta->nama;
        $request['nohp'] = $peserta->mr->noTelepon;
        $request['norm'] = $peserta->mr->noMR;
        $request['jampraktek'] = $jadwal->jadwal;
        $request['jenispasien'] = 'JKN';
        $request['method'] = 'Offline';

        $antrian_api = new AntrianAntrianController();
        $response = $antrian_api->ambil_antrian_offline($request);
        if ($response->status() == 200) {
            // cek printer
            try {
                $connector = new WindowsPrintConnector(env('PRINTER_CHECKIN'));
                $printer = new Printer($connector);
                $printer->close();
            } catch (\Throwable $th) {
                return $this->sendError('Printer Mesin Antrian Tidak Menyala', null, 201);
            }
            $antrian = $response->getData()->response;
            $this->print_karcis_offline($request, $antrian);
            Alert::success('Success', 'Anda berhasil mendaftar dengan antrian ' . $antrian->angkaantrean . " / " . $antrian->nomorantrean);
            return redirect()->route('antrian.console');
        } else {
            Alert::error('Error ' . $response->getData()->metadata->code,  $response->getData()->metadata->message);
            return redirect()->route('antrian.console');
        }
    }
    public function daftar_pasien_umum_offline(Request $request)
    {
        $request['tanggalperiksa'] = now()->format('Y-m-d');
        $request['kodepoli'] = $request->kodesubspesialis;
        $validator = Validator::make(request()->all(), [
            "kodesubspesialis" => "required",
            "kodedokter" => "required",
            "nik" => "required|numeric",
        ]);
        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->route('antrian.console');
        }

        // cek peserta
        $api = new VclaimController();
        $request['tanggal'] = $request->tanggalperiksa;
        $response =  $api->peserta_nik($request);
        if ($response->status() == 200) {
            $peserta = $response->getData()->response->peserta;
        } else {
            Alert::error('Error ' . $response->getData()->metadata->code,  $response->getData()->metadata->message);
            return redirect()->route('antrian.console');
        }

        // get jadwal
        $jadwal = JadwalDokterAntrian::where('kodesubspesialis', $request->kodesubspesialis)
            ->where('kodedokter', $request->kodedokter)
            ->where('hari', now()->dayOfWeek)->first();
        if ($jadwal == null) {
            Alert::error('Error',  "Jadwal tidak ditemukan");
            return redirect()->route('antrian.console');
        }
        if ($peserta->mr->noTelepon == null) {
            $request['nohp'] = "089529909036";
        } else {
            $request['nohp'] = $peserta->mr->noTelepon;
        }
        if ($peserta->mr->noMR == null) {
            $request['norm'] = null;
        } else {
            $request['norm'] = $peserta->mr->noMR;
        }
        $request['nik'] = $peserta->nik;
        $request['nomorkartu'] = $peserta->noKartu;
        $request['nama'] = $peserta->nama;
        $request['jampraktek'] = $jadwal->jadwal;
        $request['jenispasien'] = 'NON-JKN';
        $request['method'] = 'Offline';

        $antrian_api = new AntrianAntrianController();
        $response = $antrian_api->ambil_antrian_offline($request);
        if ($response->status() == 200) {
            // cek printer
            try {
                $connector = new WindowsPrintConnector(env('PRINTER_CHECKIN'));
                $printer = new Printer($connector);
                $printer->close();
            } catch (\Throwable $th) {
                return $this->sendError('Printer Mesin Antrian Tidak Menyala', null, 201);
            }
            $antrian = $response->getData()->response;
            $this->print_karcis_offline($request, $antrian);
            Alert::success('Success', 'Anda berhasil mendaftar dengan antrian ' . $antrian->angkaantrean . " / " . $antrian->nomorantrean);
            return redirect()->route('antrian.console');
        } else {
            Alert::error('Error ' . $response->getData()->metadata->code,  $response->getData()->metadata->message);
            return redirect()->route('antrian.console');
        }
    }
    function print_karcis_offline(Request $request, $antrian)
    {
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        $now = Carbon::now();
        $connector = new WindowsPrintConnector(env('PRINTER_CHECKIN'));
        $printer = new Printer($connector);
        $printer->setEmphasis(true);
        $printer->text("ANTRIAN RAWAT JALAN\n");
        $printer->text("RSUD WALED KAB. CIREBON\n");
        $printer->setEmphasis(false);
        $printer->text("================================================\n");
        $printer->text("No. RM : " . $request->norm . "\n");
        $printer->text("Nama : " . $request->nama . "\n");
        $printer->text("NIK : " . $request->nik . "\n");
        $printer->text("No. Kartu JKN : " . $request->nomorkartu . "\n");
        $printer->text("No. Telp. : " . $request->nohp . "\n");
        $printer->text("================================================\n");
        $printer->text("Jenis Kunj. : " . $request->method . "\n");
        $printer->text("Poliklinik : " . $antrian->namapoli . "\n");
        $printer->text("Dokter : " . $antrian->namadokter . "\n");
        $printer->text("Jam Praktek : " . $request->jampraktek . "\n");
        $printer->text("Tanggal : " . Carbon::parse($request->tanggalperiksa)->format('d M Y') . "\n");
        $printer->text("================================================\n");
        $printer->text("Keterangan : \n" . $antrian->keterangan . "\n");
        $printer->text("================================================\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Jenis Pasien :\n");
        $printer->setTextSize(2, 2);
        $printer->text("JKN OFFLINE\n");
        $printer->setTextSize(1, 1);
        $printer->text("Kode Booking : " . $antrian->kodebooking . "\n");
        $printer->qrCode($antrian->kodebooking, Printer::QR_ECLEVEL_L, 10, Printer::QR_MODEL_2);
        $printer->text("================================================\n");
        $printer->text("Nomor Antrian Poliklinik :\n");
        $printer->setTextSize(2, 2);
        $printer->text($antrian->nomorantrean . "\n");
        $printer->setTextSize(1, 1);
        $printer->text("Lokasi Poliklinik Lantai " . $request->lokasi . " \n");
        $printer->text("================================================\n");
        $printer->text("Angka Antrian :\n");
        $printer->setTextSize(2, 2);
        $printer->text($antrian->angkaantrean . "\n");
        $printer->setTextSize(1, 1);
        $printer->text("Lokasi Pendaftaran Lantai " . $request->lantaipendaftaran . " \n");
        $printer->text("================================================\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Cetakan 1 : " . $now . "\n");
        $printer->cut();
        $printer->close();
    }
    public function pendaftaran(Request $request)
    {
        $antrians = [];
        if ($request->tanggal) {
            $antrians = Antrian::where('tanggalperiksa', $request->tanggal)->get();
        }
        $provinsis = Provinsi::get();
        return view('simrs.laporan_antrian', [
            'antrians' => $antrians,
            'request' => $request,
            'provinsis' => $provinsis,
        ]);
    }
    // pendaftaran
    public function antrian_pendaftaran(Request $request)
    {
        $antrians = null;
        if ($request->tanggal && $request->loket && $request->lantai) {
            $antrians = Antrian::whereDate('tanggalperiksa', $request->tanggal)
                ->get();
            if ($request->kodepoli != null) {
                $antrians = $antrians->where('kodepoli', $request->kodepoli);
            }
        }
        $polis = PoliklinikDB::where('status', 1)->get();
        $dokters = ParamedisDB::where('kode_dokter_jkn', "!=", null)
            ->where('unit', "!=", null)
            ->get();
        if (isset($request->kodepoli)) {
            $poli = UnitDB::firstWhere('KDPOLI', $request->kodepoli);
            $dokters = ParamedisDB::where('unit', $poli->kode_unit)
                ->where('kode_dokter_jkn', "!=", null)
                ->get();
        }
        return view('simrs.pendaftaran.pendaftaran_antrian', [
            'antrians' => $antrians,
            'request' => $request,
            'polis' => $polis,
            'dokters' => $dokters,
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
            $antrian->update([
                'taskid' => 2,
                'status_api' => 1,
                'keterangan' => "Panggilan ke loket pendaftaran",
                'taskid2' => $now,
                'user' => Auth::user()->name,
            ]);
            //panggil urusan mesin antrian
            try {
                // notif wa
                $wa = new WhatsappController();
                $request['message'] = "Panggilan antrian atas nama pasien " . $antrian->nama . " dengan nomor antrian " . $antrian->angkaantrean . "/" . $antrian->nomorantrean . " untuk melakukan pendaftaran di Loket " . $loket . " Lantai " . $lantai;
                $request['number'] = $antrian->nohp;
                $wa->send_message($request);

                $tanggal = now()->format('Y-m-d');
                $urutan = $antrian->angkaantrean;
                if ($antrian->jenispasien == 'JKN') {
                    $tipeloket = 'BPJS';
                } else {
                    $tipeloket = 'UMUM';
                }
                $mesin_antrian = DB::connection('mysql3')->table('tb_counter')
                    ->where('tgl', $tanggal)
                    ->where('kategori', $tipeloket)
                    ->where('loket', $loket)
                    ->where('lantai', $lantai)
                    ->get();
                if ($mesin_antrian->count() < 1) {
                    $mesin_antrian = DB::connection('mysql3')->table('tb_counter')->insert([
                        'tgl' => $tanggal,
                        'kategori' => $tipeloket,
                        'loket' => $loket,
                        'counterloket' => $urutan,
                        'lantai' => $lantai,
                        'mastercount' => $urutan,
                        'sound' => 'PLAY',
                    ]);
                } else {
                    DB::connection('mysql3')->table('tb_counter')
                        ->where('tgl', $tanggal)
                        ->where('kategori', $tipeloket)
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
                Alert::error('Error', $th->getMessage());
                return redirect()->back();
            }
            Alert::success('Success', 'Panggilan Berhasil');
            return redirect()->back();
        } else {
            Alert::error('Error', 'Kode Booking tidak ditemukan');
            return redirect()->back();
        }
    }
    public function selesai_pendaftaran($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 3;
        $request['waktu'] = Carbon::now()->timestamp * 1000;

        if ($antrian->jenispasien == 'JKN') {
            $request['keterangan'] = "Silahkan menunggu dipoliklinik";
            $request['status_api'] = 1;
        } else {
            $request['keterangan'] = "Silahkan lakukan pembayaran di Loket Pembayaran, setelah itu dapat menunggu dipoliklinik";
            $request['status_api'] = 0;
        }
        // $vclaim = new AntrianAntrianController();
        // $response = $vclaim->update_antrean($request);
        // if ($response->status() == 200) {
        // } else {
        //     Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
        // }
        $antrian->update([
            'taskid' => $request->taskid,
            'status_api' => $request->status_api,
            'keterangan' => $request->keterangan,
            'user' => Auth::user()->name,
        ]);
        try {
            // notif wa
            $wa = new WhatsappController();
            $request['message'] = "Anda berhasil di daftarkan atas nama pasien " . $antrian->nama . " dengan nomor antrean " . $antrian->nomorantrean . " telah selesai. " . $request->keterangan;
            $request['number'] = $antrian->nohp;
            $wa->send_message($request);
        } catch (\Throwable $th) {
            //throw $th;
        }
        Alert::success('Success', 'Pasien diteruskan ke poliklinik');
        return redirect()->back();
    }
    // poliklinik
    public function antrian_poliklinik(Request $request)
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
        $polis = PoliklinikDB::where('status', 1)->get();
        $dokters = ParamedisDB::where('kode_dokter_jkn', "!=", null)
            ->where('unit', "!=", null)
            ->get();
        if (isset($request->kodepoli)) {
            $poli = UnitDB::firstWhere('KDPOLI', $request->kodepoli);
            $dokters = ParamedisDB::where('unit', $poli->kode_unit)
                ->where('kode_dokter_jkn', "!=", null)
                ->get();
        }
        return view('simrs.poliklinik.poliklinik_antrian', [
            'antrians' => $antrians,
            'request' => $request,
            'polis' => $polis,
            'dokters' => $dokters,
        ]);
    }
    public function panggil_poliklinik(Antrian $antrian, Request $request)
    {
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 4;
        $request['keterangan'] = "Panggilan ke poliklinik yang anda pilih";
        $request['waktu'] = Carbon::now()->timestamp * 1000;
        $vclaim = new AntrianAntrianController();
        $response = $vclaim->update_antrean($request);
        if ($response->status() == 200) {
            // try {
            //     // notif wa
            //     $wa = new WhatsappController();
            //     $request['message'] = "Panggilan antrian atas nama pasien " . $antrian->nama . " dengan nomor antrean " . $antrian->nomorantrean . " untuk segera dilayani di POLIKLINIK " . $antrian->namapoli;
            //     $request['number'] = $antrian->nohp;
            //     $wa->send_message($request);
            // } catch (\Throwable $th) {
            //     //throw $th;
            // }
            $antrian->update([
                'taskid' => $request->taskid,
                'status_api' => 1,
                'keterangan' => $request->keterangan,
                'user' => Auth::user()->name,
            ]);
            Alert::success('Success', 'Panggil Pasien Berhasil');
        } else {
            Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
        }
        return redirect()->back();
    }
    public function panggil_ulang_poliklinik(Antrian $antrian, Request $request)
    {
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 4;
        $request['keterangan'] = "Panggilan ke poliklinik yang anda pilih";
        $request['waktu'] = Carbon::now()->timestamp * 1000;
        // try {
        //     // notif wa
        //     $wa = new WhatsappController();
        //     $request['message'] = "Panggilan ulang antrian atas nama pasien " . $antrian->nama . " dengan nomor antrean " . $antrian->nomorantrean . " untuk segera dilayani di POLIKLINIK " . $antrian->namapoli;
        //     $request['number'] = $antrian->nohp;
        //     $wa->send_message($request);
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }
        Alert::success('Success', 'Panggil Pasien Berhasil');
        return redirect()->back();
    }
    public function batal_antrian_poliklinik(Antrian $antrian, Request $request)
    {
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 99;
        $request['keterangan'] = "Antrian dibatalkan di poliklinik oleh " . Auth::user()->name;
        $vclaim = new AntrianAntrianController();
        $response = $vclaim->batal_antrian($request);
        if ($response->status() == 200) {
            Alert::success('Success', "Antrian berhasil dibatalkan");
        } else {
            Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
        }
        return redirect()->back();
    }
    public function lanjut_farmasi($kodebooking, Request $request)
    {
        $antrian = Antrian::firstWhere('kodebooking', $kodebooking);
        $request['kodebooking'] = $antrian->kodebooking;
        $request['jenisresep'] = $antrian->jenisresep ?? 'racikan';
        $request['taskid'] = 5;
        $request['keterangan'] = "Silahkan tunggu di farmasi untuk pengambilan obat.";
        $request['waktu'] = Carbon::now()->timestamp * 1000;
        $api = new AntrianAntrianController();
        $response = $api->update_antrean($request);
        if ($response->status() == 200) {
            $antrian->update([
                'taskid' => $request->taskid,
                'status_api' => 0,
                'keterangan' => $request->keterangan,
                'user' => Auth::user()->name,
            ]);
            // try {
            //     // notif wa
            //     $wa = new WhatsappController();
            //     $request['message'] = "Pelayanan di poliklinik atas nama pasien " . $antrian->nama . " dengan nomor antrean " . $antrian->nomorantrean . " telah selesai. " . $request->keterangan;
            //     $request['number'] = $antrian->nohp;
            //     $wa->send_message($request);
            // } catch (\Throwable $th) {
            //     //throw $th;
            // }
            Alert::success('Success', 'Pasien Dilanjutkan Ke Farmasi');
        } else {
            Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
        }
        $response = $api->ambil_antrian_farmasi($request);
        if ($response->status() == 200) {
            Alert::success('Success', 'Pasien Dilanjutkan Ke Farmasi');
        } else {
            Alert::error('Error Tambah Antrian Farmasi ' . $response->status(), $response->getData()->metadata->message);
        }
        return redirect()->back();
    }
    public function selesai_poliklinik($kodebooking, Request $request)
    {
        $antrian = Antrian::where('kodebooking', $kodebooking)->first();
        $request['kodebooking'] = $antrian->kodebooking;
        $request['taskid'] = 5;
        $request['keterangan'] = "Selesai poliklinik";
        $request['waktu'] = Carbon::now()->timestamp * 1000;
        $vclaim = new AntrianAntrianController();
        $response = $vclaim->update_antrean($request);
        if ($response->status() == 200) {
            $antrian->update([
                'taskid' => $request->taskid,
                'status_api' => 1,
                'keterangan' => $request->keterangan,
                'user' => Auth::user()->name,
            ]);
            // try {
            //     // notif wa
            //     $wa = new WhatsappController();
            //     $request['message'] = "Pelayanan di poliklinik atas nama pasien " . $antrian->nama . " dengan nomor antrean " . $antrian->nomorantrean . " telah selesai. " . $request->keterangan;
            //     $request['number'] = $antrian->nohp;
            //     $wa->send_message($request);
            // } catch (\Throwable $th) {
            //     //throw $th;
            // }
            Alert::success('Success', 'Pasien Selesai Di Poliklinik');
        } else {
            Alert::error('Error ' . $response->status(), $response->getData()->metadata->message);
        }
        return redirect()->back();
    }
    public function suratkontrol_poliklinik(Request $request)
    {
        $kunjungans = null;
        $surat_kontrols = null;
        if ($request->tanggal) {
            $surat_kontrols = SuratKontrol::whereDate('tglTerbitKontrol', $request->tanggal)->get();
            $kunjungans = KunjunganDB::whereDate('tgl_masuk', $request->tanggal)
                ->where('status_kunjungan', "!=", 8)
                ->where('kode_unit', "!=", null)
                ->where('kode_unit', 'LIKE', '10%')
                ->where('kode_unit', "!=", 1002)
                ->where('kode_unit', "!=", 1023)
                ->with(['dokter', 'unit', 'pasien', 'surat_kontrol'])
                ->get();
            if ($request->kodepoli != null) {
                $poli = UnitDB::where('KDPOLI', $request->kodepoli)->first();
                $kunjungans = $kunjungans->where('kode_unit', $poli->kode_unit);
                $surat_kontrols = $surat_kontrols->where('poliTujuan', $request->kodepoli);
            }
            if ($request->kodedokter != null) {
                $dokter = ParamedisDB::where('kode_dokter_jkn', $request->kodedokter)->first();
                $kunjungans = $kunjungans->where('kode_paramedis', $dokter->kode_paramedis);
            }
        }
        if ($request->kodepoli == null) {
            $unit = UnitDB::where('KDPOLI', "!=", null)
                ->where('KDPOLI', "!=", "")
                ->get();
            $dokters = ParamedisDB::where('kode_dokter_jkn', "!=", null)
                ->where('unit', "!=", null)
                ->get();
        } else {
            $unit = UnitDB::where('KDPOLI', "!=", null)
                ->where('KDPOLI', "!=", "")
                ->get();
            $poli =   UnitDB::firstWhere('KDPOLI', $request->kodepoli);
            $dokters = ParamedisDB::where('unit', $poli->kode_unit)
                ->where('kode_dokter_jkn', "!=", null)
                ->get();
        }
        return view('simrs.poliklinik.poliklinik_suratkontrol', [
            'kunjungans' => $kunjungans,
            'request' => $request,
            'unit' => $unit,
            'dokters' => $dokters,
            'surat_kontrols' => $surat_kontrols,
        ]);
    }
    public function laporan_kunjungan_poliklinik(Request $request)
    {
        $response = null;
        $kunjungans = null;
        if (isset($request->tanggal) && isset($request->kodepoli)) {
            $poli = UnitDB::where('KDPOLI', $request->kodepoli)->first();
            $kunjungans = KunjunganDB::whereDate('tgl_masuk', $request->tanggal)
                ->where('kode_unit', $poli->kode_unit)
                ->where('status_kunjungan',  "<=", 2)
                ->with(['dokter', 'unit', 'pasien', 'diagnosapoli', 'pasien.kecamatans', 'penjamin', 'surat_kontrol'])
                ->get();
            $response = DB::connection('mysql2')->select("CALL SP_PANGGIL_PASIEN_RAWAT_JALAN_KUNJUNGAN('" . $poli->kode_unit . "','" . $request->tanggal . "')");
        }
        $unit = UnitDB::where('KDPOLI', "!=", null)->where('KDPOLI', "!=", "")->get();
        $penjaminrs = PenjaminSimrs::get();
        $response = collect($response);
        return view('simrs.poliklinik.poliklinik_laporan_kunjungan', [
            'kunjungans' => $kunjungans,
            'request' => $request,
            'response' => $response,
            'penjaminrs' => $penjaminrs,
            'unit' => $unit,
        ]);
    }
    public function laporan_antrian_poliklinik(Request $request)
    {
        if ($request->tanggal == null) {
            $tanggal_awal = now()->startOfDay()->format('Y-m-d');
            $tanggal_akhir = now()->endOfDay()->format('Y-m-d');
        } else {
            $tanggal = explode(' - ', $request->tanggal);
            $tanggal_awal = Carbon::parse($tanggal[0])->format('Y-m-d');
            $tanggal_akhir = Carbon::parse($tanggal[1])->format('Y-m-d');
        }
        $antrians = Antrian::whereBetween('tanggalperiksa', [$tanggal_awal, $tanggal_akhir])
            ->get();
        $kunjungans = KunjunganDB::whereBetween('tgl_masuk', [Carbon::parse($tanggal_awal)->startOfDay(), Carbon::parse($tanggal_akhir)->endOfDay()])
            ->where('kode_unit', "!=", null)
            ->where('kode_unit', 'LIKE', '10%')
            ->where('kode_unit', '!=', 1002)
            ->where('kode_unit', "!=", 1023)
            ->where('kode_unit', "!=", 1015)
            ->get();
        $units = UnitDB::where('KDPOLI', '!=', null)->get();
        return view('simrs.laporan_kunjungan', [
            'antrians' => $antrians,
            'request' => $request,
            'kunjungans' => $kunjungans,
            'units' => $units,
        ]);
    }
    public function dashboard_antrian_tanggal(Request $request)
    {
        $antrians = null;
        if (isset($request->tanggal) && isset($request->waktu)) {
            $api = new AntrianAntrianController();
            $response = $api->dashboard_tanggal($request);
            if ($response->status() == 200) {
                $antrians = $response->getData()->response->list;
                Alert::success('Success', "Berhasil Dapatkan Data Antrian");
            } else {
                Alert::error('Error ' . $response->status(),  $response->getData()->metadata->message);
                return redirect()->route('antrian.laporan_tanggal');
            }
        }
        return view('simrs.antrian_laporan_tanggal', [
            'antrians' => $antrians,
            'request' => $request,
        ]);
    }
    public function dashboard_antrian_bulan(Request $request)
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
    public function antrian_per_tanggal(Request $request)
    {
        $antrians = null;
        if (isset($request->tanggal)) {
            $api = new AntrianAntrianController();
            $response = $api->antrian_tanggal($request);
            if ($response->status() == 200) {
                $antrians = $response->getData()->response;
                Alert::success('Success', "Berhasil Dapatkan Data Antrian");
            } else {
                Alert::error('Error ' . $response->status(),  $response->getData()->metadata->message);
                return redirect()->route('bpjs.antrian.antrian_per_tanggal');
            }
        }
        return view('simrs.antrian_per_tanggal', [
            'antrians' => $antrians,
            'request' => $request,
        ]);
    }
    public function antrian_per_kodebooking(Request $request)
    {
        $antrian = null;
        if ($request->kodebooking) {
            $request['kodeBooking'] = $request->kodebooking;
            $api = new AntrianAntrianController();
            $response = $api->antrian_kodebooking($request);
            if ($response->status() == 200) {
                $antrian = $response->getData()->response[0];
            }
        } else {
            # code...
        }
        return view('bpjs.antrian.antrian_per_kodebooking', compact([
            'request', 'antrian'
        ]));
    }
    public function antrian_belum_dilayani(Request $request)
    {
        // $antrians = null;
        // if (isset($request->tanggal)) {
        $request['tanggal'] = now()->format('Y-m-d');
        $api = new AntrianAntrianController();
        $response = $api->antrian_belum_dilayani($request);
        if ($response->status() == 200) {
            $antrians = $response->getData()->response;
            Alert::success('Success', "Berhasil Dapatkan Data Antrian");
        } else {
            $antrians = null;
            Alert::error('Error ' . $response->status(),  $response->getData()->metadata->message);
            return redirect()->route('antrian.laporan_tanggal');
        }
        // }
        return view('simrs.antrian_belum_dilayani', [
            'antrians' => $antrians,
            'request' => $request,
        ]);
    }
    public function antrian_per_dokter(Request $request)
    {
        $antrians = null;
        $jadwaldokter = SIMRSJadwalDokter::orderBy('hari', 'ASC')->get();
        if (isset($request->jadwaldokter)) {
            $jadwal = SIMRSJadwalDokter::find($request->jadwaldokter);
            $api = new AntrianAntrianController();
            $request['kodePoli'] = $jadwal->kodesubspesialis;
            $request['kodeDokter'] = $jadwal->kodedokter;
            $request['hari'] = $jadwal->hari;
            $request['jamPraktek'] = $jadwal->jadwal;
            $response = $api->antrian_poliklinik($request);
            if ($response->status() == 200) {
                $antrians = $response->getData()->response;
                Alert::success('Success', "Berhasil Dapatkan Data Antrian");
            } else {
                Alert::error('Error ' . $response->status(),  $response->getData()->metadata->message);
            }
        }
        return view('simrs.antrian_per_dokter', [
            'antrians' => $antrians,
            'jadwaldokter' => $jadwaldokter,
            'request' => $request,
        ]);
    }
}
