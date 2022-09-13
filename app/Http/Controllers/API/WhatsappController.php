<?php

namespace App\Http\Controllers\API;

use App\Models\AntrianDB;
use App\Models\JadwalDokter;
use App\Models\JadwalLiburPoliDB;
use App\Models\PasienDB;
use App\Models\Poliklinik;
use App\Models\UnitDB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;
use Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WhatsappController extends Controller
{
    public $baseUrl = "192.168.2.30:3000/";
    public $hari = ["MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"];
    protected $except = [
        'callback',
    ];
    public function index(Request $request)
    {
        // $pesan = "3209020905980007#DAFTAR_UMUM#222#2022-09-12";
        $pesan = "POLI MATA NO. 0125B0240922P000613_RUJUKANFKTP#0125B0240922P000613#281#2022-09-14";
        $request['number'] = '6289529909036@c.us';
        return $this->cek_rujukan($pesan, $request);
    }
    public function send_message(Request $request)
    {
        $url = $this->baseUrl . "send-message";
        $response = Http::post($url, [
            'number' => $request->number,
            'message' => $request->message,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_button(Request $request)
    {
        $url = $this->baseUrl . "send-button";
        $response = Http::post($url, [
            'number' => $request->number,
            'contenttext' => $request->contenttext,
            'footertext' => $request->footertext,
            'titletext' => $request->titletext,
            'buttontext' => $request->buttontext, // 'UMUM,BPJS'
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_list(Request $request)
    {
        $url = $this->baseUrl . "send-list";
        $response = Http::post($url, [
            'number' => $request->number,
            'contenttext' => $request->contenttext,
            'footertext' => $request->footertext,
            'titletext' => $request->titletext,
            'buttontext' => $request->buttontext, #wajib
            'titlesection' => $request->titlesection,
            'rowtitle' => $request->rowtitle, #wajib
            'rowdescription' => $request->rowdescription,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_image(Request $request)
    {
        $url = $this->baseUrl . "send-media";
        $response = Http::post($url, [
            'number' => $request->number,
            'fileurl' => $request->fileurl,
            'caption' => $request->caption,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_filepath(Request $request)
    {
        $url = $this->baseUrl . "send-filepath";
        $response = Http::post($url, [
            'number' => $request->number,
            'filepath' => $request->filepath,
            'caption' => $request->caption,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function callback(Request $request)
    {
        $pesan = strtoupper($request->message);
        switch ($pesan) {
            case 'MESSAGE':
                $request['message'] = "Test Send Message";
                return $this->send_message($request);
                break;
            case 'BUTTON':
                $request['contenttext'] = "contenttext";
                $request['footertext'] = 'footertext';
                $request['buttontext'] = 'buttontext1,buttontext2,buttontext3';
                return $this->send_button($request);
                break;
            case 'LIST':
                $request['contenttext'] = "contenttext";
                $request['titletext'] = "titletext";
                $request['buttontext'] = 'buttontext';
                $request['rowtitle'] = 'rowtitle1,rowtitle2,rowtitle3';
                $request['rowdescription'] = 'rowdescription1,rowdescription2,rowdescription3';
                return $this->send_list($request);
                break;
            case 'INFO CARA PENDAFTARAN':
                $request['contenttext'] = "Untuk pendaftaran melalui Layanan Whatsapp RSUD Waled sebagai berikut :\n\n1. Pilih menu utama *DAFTAR PASIEN RAWAT JALAN*. \n2. Pilih *POLIKLINIK* yang akan dikunjungi dan yang tersedia untuk daftar online. \n3. Pilih *TANGGAL* kapan pasien akan berkunjung. \n4. Pilih *JADWAL DOKTER* yang akan dituju oleh pasien. \n5. Pilih *JENIS PASIEN* terdapat pilihan pasien *JKN / UMUM*. \n6. Menuliskan format pendaftaran, untuk pasien JKN memasukan *NOMOR RUJUKAN* dari faskes 1, untuk pasien Umum memasukan *NIK/KTP*. \n7. Konfirmasi pendaftaran lalu berhasil didaftarkan atau batal pendaftaran.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                $request['titletext'] = "Info Cara Pendaftaran";
                $request['buttontext'] = 'MENU UTAMA';
                $request['rowtitle'] = 'DAFTAR PASIEN RAWAT JALAN,INFO JADWAL POLIKLINIK,INFO CARA PENDAFTARAN,PERTANYAAN DAN PENGADUAN';
                return $this->send_list($request);
                break;
            case 'INFO JADWAL POLIKLINIK':
                $request['contenttext'] = "Silahkan pilih poliklinik yang tersedia untuk daftar online rawat jalan pasien dibawah ini.";
                $request['titletext'] = "Info Jadwal Poliklinik";
                $request['buttontext'] = 'PILIH POLIKLINIK';
                $rowpoli = null;
                $poliklinik = Poliklinik::where('status', 1)->get('namasubspesialis');
                foreach ($poliklinik as  $value) {
                    $rowpoli =  $rowpoli . 'JADWAL_POLIKLINIK_' . $value->namasubspesialis  . ',';
                }
                $request['rowtitle'] = $rowpoli;
                return $this->send_list($request);
            case 'DAFTAR PASIEN RAWAT JALAN':
                $this->pilih_poli($pesan, $request);
                break;
            default:
                // pilih poli terus tanggal
                if (substr($pesan, 0, 11) == 'POLIKLINIK_') {
                    $poli = explode('_', $pesan)[1];
                    $now = Carbon::now();
                    $rowtanggal = $poli . '_TANGGAL#' . $now->format('Y-m-d');

                    for ($i = 0; $i < 6; $i++) {
                        $rowtanggal = $rowtanggal . ',' . $poli . '_TANGGAL#' . $now->addDay(1)->format('Y-m-d');
                    }
                    $request['contenttext'] = "Silahkan pilih tanggal rawat jalan poliklinik " . strtoupper($poli) . " dibawah ini.";
                    $request['titletext'] = "Poliklinik  " . strtoupper($poli);
                    $request['buttontext'] = 'PILIH TANGGAL';
                    $request['rowtitle'] = $rowtanggal;
                    return $this->send_list($request);
                }
                // tanggal poli terus pilih jadwal
                else if (str_contains($pesan, 'JADWAL_POLIKLINIK_')) {
                    $poli = explode('_', $pesan)[2];
                    $rowjadwaldokter = null;
                    $jadwaldokters = JadwalDokter::where('namasubspesialis', $poli)->get();
                    foreach ($jadwaldokters as  $value) {
                        $rowjadwaldokter = $rowjadwaldokter . $this->hari[$value->hari] . '  : ' . $value->namadokter . ' ' . $value->jadwal . "\n";
                    }
                    $request['contenttext'] = "Jadwal dokter poliklinik " . $poli . " sebagai berikut : \n\n" . $rowjadwaldokter;
                    $request['titletext'] = "Jadwal Poliklinik " . $poli;
                    $request['buttontext'] = 'INFO JADWAL POLIKLINIK';
                    return $this->send_button($request);
                }
                // tanggal poli terus pilih jadwal
                else if (str_contains($pesan, '_TANGGAL#')) {
                    $poli = explode('_', $pesan)[0];
                    $tanggal = Carbon::parse(explode('#', $pesan)[1]);
                    $hari = $tanggal->dayOfWeek;
                    $rowjadwaldokter = null;
                    $jadwaldokters = JadwalDokter::where('hari', $hari)
                        ->where('namasubspesialis', $poli)->get();
                    if ($jadwaldokters->count() == 0) {
                        $request['contenttext'] = "Mohon maaf tidak ada jadwal dokter poliklinik " . $poli . " di tanggal " . $tanggal->format('Y-m-d') . ".\n\nSilahkan pilih jadwal dokter poliklinik " . $poli . " pada tanggal tanggal yang lain dibawah ini.";
                        $request['titletext'] = "Pilih Tanggal Jadwal Dokter Poliklinik";
                        $request['buttontext'] = 'PILIH JADWAL POLIKLINIK';
                        $now = Carbon::now();
                        $rowhari = $poli . '_TANGGAL#' . $now->format('Y-m-d');
                        for ($i = 0; $i < 6; $i++) {
                            $rowhari = $rowhari . ',' . $poli . '_TANGGAL#' . $now->addDay(1)->format('Y-m-d');
                        }
                        $request['rowtitle'] = $rowhari;
                        return $this->send_list($request);
                    }
                    foreach ($jadwaldokters as  $value) {
                        $rowjadwaldokter = $rowjadwaldokter . $value->jadwal . " " . $value->namadokter . '_JADWAL.ID#' . $value->id . '#' . $tanggal->format('Y-m-d') . ',';
                    }
                    $request['contenttext'] = "Silahkan pilih jadwal dokter poliklinik " . $poli . " pada tanggal " . $tanggal->format('Y-m-d') . " dibawah ini.";
                    $request['titletext'] = "Pilih Jadwal Dokter";
                    $request['buttontext'] = 'PILIH JADWAL DOKTER';
                    $request['rowtitle'] = $rowjadwaldokter;
                    return $this->send_list($request);
                }
                // pilih dokter terus jenis pasien
                else if (str_contains($pesan, '_JADWAL.ID#')) {
                    $jadwalid = explode('#', $pesan)[1];
                    $tanggal = explode('#', $pesan)[2];
                    $jadwaldokter = JadwalDokter::find($jadwalid);
                    // jika jadwal libur
                    if ($jadwaldokter->libur == 1) {
                        $poli = $jadwaldokter->namasubspesialis;
                        $tanggal = Carbon::parse(explode('#', $pesan)[3]);
                        $hari = $tanggal->dayOfWeek;
                        $rowjadwaldokter = null;
                        $jadwaldokters = JadwalDokter::where('hari', $hari)
                            ->where('namasubspesialis', $poli)->get();
                        if ($jadwaldokters->count() == 0) {
                            $request['contenttext'] = "Mohon maaf tidak ada jadwal dokter poliklinik " . $poli . " di tanggal " . $tanggal->format('Y-m-d') . ".\n\nSilahkan pilih jadwal dokter poliklinik " . $poli . " pada tanggal tanggal yang lain dibawah ini.";
                            $request['titletext'] = "Pilih Tanggal Jadwal Dokter Poliklinik";
                            $request['buttontext'] = 'PILIH JADWAL POLIKLINIK';
                            $now = Carbon::now();
                            $rowhari = $poli . '_TANGGAL#' . $now->format('Y-m-d');
                            for ($i = 0; $i < 6; $i++) {
                                $rowhari = $rowhari . ',' . $poli . '_TANGGAL#' . $now->addDay(1)->format('Y-m-d');
                            }
                            $request['rowtitle'] = $rowhari;
                            return $this->send_list($request);
                        }
                        foreach ($jadwaldokters as  $value) {
                            $rowjadwaldokter = $rowjadwaldokter . $value->jadwal . " " . $value->namadokter . '_JADWAL.ID#' . $value->id . '#' . $tanggal->format('Y-m-d') . ',';
                        }
                        $request['contenttext'] = "Silahkan pilih jadwal dokter poliklinik " . $poli . " pada tanggal " . $tanggal->format('Y-m-d') . " dibawah ini.";
                        $request['titletext'] = "Pilih Jadwal Dokter";
                        $request['buttontext'] = 'PILIH JADWAL DOKTER';
                        $request['rowtitle'] = $rowjadwaldokter;
                        return $this->send_list($request);
                    }
                    // jika jadwal jalan
                    else {
                        $request['titletext'] = "Pilih Jenis Pasien";
                        $request['contenttext'] = "Jadwal dokter poliklinik yang anda pilih adalah sebagai berikut :\n\n*Poliklinik* : " . strtoupper($jadwaldokter->namasubspesialis) . "\n*Dokter* :  " . $jadwaldokter->namadokter . "\n*Jam Praktek* : " . $jadwaldokter->jadwal . "\n*Tanggal Periksa* :  " . $tanggal . "\n\nSilahakan pilih jenis pasien yang akan didaftarkan ini. \n\nCatatan :\nPasien JKN/BPJS : diharuskan memiliki rujukan faskes 1\nPasien UMUM : hanya pasien umum yang telah terdaftar saja dapat melakukan daftar online. Bagi yang belum terdaftar silahkan daftar langsung ditempat";
                        $request['buttontext'] = 'PASIEN BPJS_' . $jadwalid . '#' . $tanggal . ',PASIEN UMUM_' . $jadwalid . '#' . $tanggal;
                        return $this->send_button($request);
                    }
                }
                // pilih jenis pasien, masukan rujukan
                else if (substr($pesan, 0, 12) ==  'PASIEN BPJS_') {
                    $jadwalid = explode('_', $pesan)[1];
                    $request['message'] = "*KETIK NOMOR KARTU BPJS KEDALAM FORMAT*\nUntuk pasien JKN/BPJS silahkan ketik nomor kartu 13 digit yang tertera pada kartu bpjs anda dengan format seperti berikut : \n\nNomor Kartu#BPJS#" . $jadwalid . "\n\n(Contoh)\nXX00067XX23XX#PASIEN_BPJS#" . $jadwalid;
                    $this->send_message($request);
                    $request['message'] = "XX00067XX23XX#PASIEN_BPJS#" . $jadwalid;
                    return $this->send_message($request);
                }
                // pilih jenis pasien, masukan nik
                else if (substr($pesan, 0, 12) == 'PASIEN UMUM_') {
                    $jadwalid = explode('_', $pesan)[1];
                    $request['message'] = "*KETIK NIK/KTP KEDALAM FORMAT*\nUntuk pasien UMUM silahkan ketik nomor nik/ktp 16 digit yang tertera pada Kartu Tanda Penduduk (KTP) anda dengan format seperti berikut : \n\nNIK / KTP#UMUM#" . $jadwalid . "\n\n(Contoh)\n3209XXXX1234XXXX#PASIEN_UMUM#" . $jadwalid;
                    $this->send_message($request);
                    $request['message'] = "3209XXXX1234XXXX#PASIEN_UMUM#" . $jadwalid;
                    return $this->send_message($request);
                }
                // pilih jenis kunjungan untuk pasien bpjs
                else if (str_contains($pesan, '#PASIEN_BPJS#')) {
                    return $this->cek_nomorkartu_peserta($pesan, $request);
                }
                // pilih rujukan faskses 1 peserta
                else if (substr($pesan, 0, 17) == 'RUJUKAN FASKES 1_') {
                    return $this->rujukan_peserta($pesan, $request);
                }
                // pilih rujukan faskses 1 peserta
                else if (str_contains($pesan, '_RUJUKANFKTP#')) {
                    return $this->cek_rujukan($pesan, $request);
                }
                // insert antrian rujukan fk1
                else if (substr($pesan, 0, 20) == 'DAFTAR RUJUKAN FKTP_') {
                    return $this->daftar_rujukan_fktp($pesan, $request);
                }
                // insert antrian bpjs
                else if (str_contains($pesan, '#DAFTAR_BPJS#')) {
                    return $this->daftar_antrian_bpjs($pesan, $request);
                }
                // pilih jenis pasien, masukan nik
                else if (str_contains($pesan, '#PASIEN_UMUM#')) {
                    return $this->konfirmasi_antrian_umum($pesan, $request);
                }
                // pilih jenis pasien, masukan nik
                else if (str_contains($pesan, '#DAFTAR_UMUM#')) {
                    return $this->daftar_antrian_umum($pesan, $request);
                }
                // default
                else {
                    $request['contenttext'] = "Mohon maaf pesan yang anda masukan tidak dapat diproses oleh sistem.\n\nSilahkan pilih *MENU UTAMA* yang dapat diproses dibawah ini.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                    $request['titletext'] = "Layanan Whatsapp RSUD Waled";
                    $request['buttontext'] = 'MENU UTAMA';
                    $request['rowtitle'] = 'DAFTAR PASIEN RAWAT JALAN,INFO JADWAL POLIKLINIK,INFO CARA PENDAFTARAN,PERTANYAAN DAN PENGADUAN';
                    return $this->send_list($request);
                    break;
                }
        }
    }
    public function pilih_poli($pesan, Request $request)
    {
        $request['contenttext'] = "Silahkan pilih poliklinik yang tersedia untuk daftar online rawat jalan pasien dibawah ini.";
        $request['titletext'] = "Poliklinik Rawat Jalan";
        $request['buttontext'] = 'PILIH POLIKLINIK';
        $rowpoli = null;
        $poliklinik = Poliklinik::where('status', 1)->get('namasubspesialis');
        foreach ($poliklinik as  $value) {
            $rowpoli =  $rowpoli . 'POLIKLINIK_' . $value->namasubspesialis  . ',';
        }
        $request['rowtitle'] = $rowpoli;
        return $this->send_list($request);
    }
    public function konfirmasi_antrian_umum($pesan, Request $request)
    {
        // init
        try {
            $request['nik'] = explode('#', $pesan)[0];
            $tipepasien = "NON-JKN";
            $jadwalid = explode('#', $pesan)[2];
            $tanggalperiksa = explode('#', $pesan)[3];
            $jadwaldokter = JadwalDokter::find($jadwalid);
        } catch (\Throwable $th) {
            $request['message'] = "Error format pendaftaran : " . $th->getMessage() . "\nLihat dan sesuaikan kembali format pendaftaran pasien jkn. \n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
            return $this->send_message($request);
        }
        $vclaim = new VclaimBPJSController();
        $peserta =  $vclaim->peserta_nik($request);
        // berhasil
        if ($peserta->metaData->code == 200) {
            // pasien lama
            if (isset($peserta->response->peserta->mr->noMR)) {
                try {
                    $request['nomorkartu'] = $peserta->response->peserta->noKartu;
                    $request['nama'] = $peserta->response->peserta->nama;
                    $request['nik'] = $peserta->response->peserta->nik;
                    $request['norm'] = $peserta->response->peserta->mr->noMR;
                    $request['status'] = $peserta->response->peserta->statusPeserta->keterangan;
                    // $request['diagnosa'] = $peserta->response->diagnosa->nama;
                    // $request['polirujukan'] = $peserta->response->poliRujukan->nama;
                    $request['nohp'] = $request->number;
                    $request['tanggalperiksa'] = $tanggalperiksa;
                    $request['kodepoli'] = $jadwaldokter->kodesubspesialis;
                    $request['kodedokter'] = $jadwaldokter->kodedokter;
                    $request['jampraktek'] = $jadwaldokter->jadwal;
                    $request['jeniskunjungan'] = 3;
                    $request['titletext'] = "Konfirmasi Pendaftaran Pasien UMUM / NON-JKN";
                    $request['contenttext'] = "Jadwal dokter poliklinik yang dipilih sebagai berikut :\n\n*Poliklinik* : " . strtoupper($jadwaldokter->namasubspesialis) . "\n*Dokter* :  " . $jadwaldokter->namadokter . "\n*Jam Praktek* : " . $jadwaldokter->jadwal . "\n*Tanggal Periksa* :  " . $tanggalperiksa . "\n\n*Nama Pasien* : " . $request->nama . "\n*Status* : *" . $request->status . "*\n*NIK* : " . $request->nik . "\n*No BPJS* : " . $request->nomorkartu . "\n*No RM* : " . $request->norm .  "\n\nSebagai konfirmasi bahwa data yang diatas adalah benar pasien yang akan didaftarkan. Silahakan pilih tombol dibawah ini.";
                    $request['buttontext'] =  $request->nik . "#DAFTAR_UMUM#" . $jadwalid . "#" . $request->tanggalperiksa . ',BATAL PENDAFTARAN';
                    return $this->send_button($request);
                } catch (\Throwable $th) {
                    $request['message'] = "Error : " . $th;
                    return $this->send_message($request);
                }
            }
            // pasien baru
            else {
                $request['message'] = "Error : Mohon maaf untuk pasien baru tidak bisa daftar melalui whatsapp. Silahkan untuk daftar langsung ke RSUD Waled. Terima kasih.";
                return $this->send_message($request);
            }
        }
        // server bpjs gangguan
        else if ($peserta->metaData->code == 50000) {
            $request['message'] = "Mohon maaf server BPJS sedang gangguan.";
            return $this->send_message($request);
        }
        // gagal
        else {
            $request['message'] = "Error format pendaftaran : " . $peserta->metaData->message . "\nLihat dan sesuaikan kembali format pendaftaran pasien umum.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
            return $this->send_message($request);
        }
    }
    public function daftar_antrian_umum($pesan, Request $request)
    {
        // init
        try {
            $request['nik'] = explode('#', $pesan)[0];
            $tipepasien = "NON-JKN";
            $jadwalid = explode('#', $pesan)[2];
            $tanggalperiksa = explode('#', $pesan)[3];
            $jadwaldokter = JadwalDokter::find($jadwalid);
        } catch (\Throwable $th) {
            $request['message'] = "Error format pendaftaran : " . $th->getMessage() . "\nLihat dan sesuaikan kembali format pendaftaran pasien jkn. \n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
            return $this->send_message($request);
        }
        $vclaim = new VclaimBPJSController();
        $peserta =  $vclaim->peserta_nik($request);
        // berhasil
        if ($peserta->metaData->code == 200) {
            // pasien lama
            if (isset($peserta->response->peserta->mr->noMR)) {
                try {
                    $request['nomorkartu'] = $peserta->response->peserta->noKartu;
                    $request['nama'] = $peserta->response->peserta->nama;
                    $request['nik'] = $peserta->response->peserta->nik;
                    $request['norm'] = $peserta->response->peserta->mr->noMR;
                    $request['status'] = $peserta->response->peserta->statusPeserta->keterangan;
                    // $request['diagnosa'] = $peserta->response->diagnosa->nama;
                    // $request['polirujukan'] = $peserta->response->poliRujukan->nama;
                    $request['nohp'] = $request->number;
                    $request['tanggalperiksa'] = $tanggalperiksa;
                    $request['kodepoli'] = $jadwaldokter->kodesubspesialis;
                    $request['kodedokter'] = $jadwaldokter->kodedokter;
                    $request['jampraktek'] = $jadwaldokter->jadwal;
                    $request['jeniskunjungan'] = 3;
                    $antrian = new AntrianBPJSController();
                    $response = json_decode(json_encode($antrian->ambil_antrian($request)));
                    if ($response->metadata->code != 200) {
                        $request['message'] = "Error Pendaftaran : " .  $response->metadata->message;
                        return $this->send_message($request);
                    } else {
                        return $response;
                    }
                } catch (\Throwable $th) {
                    $request['message'] = "Error : " . $th;
                    return $this->send_message($request);
                }
            }
            // pasien baru
            else {
                $request['message'] = "Error : Mohon maaf untuk pasien baru tidak bisa daftar melalui whatsapp. Silahkan untuk daftar langsung ke RSUD Waled. Terima kasih.";
                return $this->send_message($request);
            }
        } else if ($peserta->metaData->code == 50000) {
            $request['message'] = "Mohon maaf server BPJS sedang gangguan.";
            return $this->send_message($request);
        }
        // gagal
        else {
            $request['message'] = "Error format pendaftaran : " . $peserta->metaData->message . "\nLihat dan sesuaikan kembali format pendaftaran pasien umum.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
            return $this->send_message($request);
        }
    }
    public function konfirmasi_antrian_bpjs($pesan, Request $request)
    {
        // init
        try {
            $request['nomorreferensi'] = explode('#', $pesan)[0];
            $request['jenisrujukan'] = 1;
            $tipepasien = explode('#', $pesan)[1];
            $jadwalid = explode('#', $pesan)[2];
            $tanggalperiksa = explode('#', $pesan)[3];
            $jadwaldokter = JadwalDokter::find($jadwalid);
        } catch (\Throwable $th) {
            //throw $th;
            $request['message'] = "Error : " . $th->getMessage() . "\nSilahkan hubungi admin. Terima kasih.";
            return $this->send_message($request);
        }

        $vclaim = new VclaimBPJSController();
        $jumlah_sep =  $vclaim->rujukan_jumlah_sep($request);
        //    server bpjs gangguan
        if ($jumlah_sep ==  null) {
            $request['message'] = "Mohon maaf server bpjs sedang gangguan.";
            return $this->send_message($request);
        }
        // berhasil
        else if ($jumlah_sep->metaData->code == 200) {
            // daftar pake rujukan
            if ($jumlah_sep->response->jumlahSEP == 0) {
                $rujukan  = $vclaim->rujukan_nomor($request);
                // rujukan 200 code
                if ($rujukan->metaData->code == 200) {
                    // pasien lama
                    if (isset($rujukan->response->rujukan->peserta->mr->noMR)) {
                        try {
                            $request['nomorkartu'] = $rujukan->response->rujukan->peserta->noKartu;
                            $request['nama'] = $rujukan->response->rujukan->peserta->nama;
                            $request['nik'] = $rujukan->response->rujukan->peserta->nik;
                            $request['norm'] = $rujukan->response->rujukan->peserta->mr->noMR;
                            $request['status'] = $rujukan->response->rujukan->peserta->statusPeserta->keterangan;
                            $request['diagnosa'] = $rujukan->response->rujukan->diagnosa->nama;
                            $request['polirujukan'] = $rujukan->response->rujukan->poliRujukan->nama;
                            $request['nohp'] = $request->number;
                            $request['tanggalperiksa'] = $tanggalperiksa;
                            $request['kodepoli'] = $jadwaldokter->kodesubspesialis;
                            $request['kodedokter'] = $jadwaldokter->kodedokter;
                            $request['jampraktek'] = $jadwaldokter->jadwal;
                            $request['jeniskunjungan'] = 1;
                            $request['titletext'] = "Konfirmasi Pendaftaran Pasien JKN";
                            $request['contenttext'] = "Jadwal dokter poliklinik yang dipilih sebagai berikut :\n\n*Poliklinik* : " . strtoupper($jadwaldokter->namasubspesialis) . "\n*Dokter* :  " . $jadwaldokter->namadokter . "\n*Jam Praktek* : " . $jadwaldokter->jadwal . "\n*Tanggal Periksa* :  " . $tanggalperiksa . "\n\n*Nama Pasien* : " . $request->nama . "\n*Status* : *" . $request->status . "*\n*NIK* : " . $request->nik . "\n*No BPJS* : " . $request->nomorkartu . "\n*No RM* : " . $request->norm . "\n*No Rujukan* : " . $request->nomorreferensi . "\n*Poli Rujukan* : " . $request->polirujukan . "\n*Diagnosa* : " . $request->diagnosa .  "\n\nSebagai konfirmasi bahwa data yang diatas adalah benar pasien yang akan didaftarkan. Silahakan pilih tombol dibawah ini.";
                            $request['buttontext'] =  $request->nomorreferensi . "#DAFTAR_BPJS#" . $jadwalid . "#" . $request->tanggalperiksa . ',BATAL PENDAFTARAN';
                            return $this->send_button($request);
                        } catch (\Throwable $th) {
                            $request['message'] = "Error : " . $th;
                            return $this->send_message($request);
                        }
                    }
                    // pasien baru
                    else {
                        $request['message'] = "Error : Mohon maaf untuk pasien baru tidak bisa daftar melalui whatsapp. Silahkan untuk daftar langsung ke RSUD Waled. Terima kasih.";
                        return $this->send_message($request);
                    }
                }
                // rujukan error
                else {
                    $request['message'] = "Error : " . $rujukan->metaData->message;
                    return $this->send_message($request);
                }
            }
            // daftar pake surat kontrol
            else {
                $request['jeniskunjungan'] = 3;
                $request['message'] = "Error : Mohon maaf kunjungan rujukan lebih dari 1. Anda harus mendaftar menggunakan Surat Kontrol yang dibuat saat berobat sebelumnya. Untuk pertanyaan silahkan hubungi admin. Terima kasih";
                return $this->send_message($request);
                dd($request->all());
            }
        }
        // gagal rujukan
        else {
            $request['message'] = "Error : " . $jumlah_sep->metaData->message;
            return $this->send_message($request);
        }
    }
    public function daftar_antrian_bpjs($pesan, Request $request)
    {
        // init
        try {
            $request['nomorreferensi'] = explode('#', $pesan)[0];
            $request['jenisrujukan'] = 1;
            $tipepasien = explode('#', $pesan)[1];
            $jadwalid = explode('#', $pesan)[2];
            $tanggalperiksa = explode('#', $pesan)[3];
            $jadwaldokter = JadwalDokter::find($jadwalid);
        } catch (\Throwable $th) {
            //throw $th;
            $request['message'] = "Error : " . $th->getMessage() . "\nSilahkan hubungi admin. Terima kasih.";
            return $this->send_message($request);
        }
        $vclaim = new VclaimBPJSController();
        $jumlah_sep =  $vclaim->rujukan_jumlah_sep($request);
        //    server bpjs gangguan
        if ($jumlah_sep ==  null) {
            $request['message'] = "Mohon maaf server bpjs sedang gangguan.";
            return $this->send_message($request);
        }
        // berhasil
        else if ($jumlah_sep->metaData->code == 200) {
            // daftar pake rujukan
            if ($jumlah_sep->response->jumlahSEP == 0) {
                $rujukan  = $vclaim->rujukan_nomor($request);
                // rujukan 200 code
                if ($rujukan->metaData->code == 200) {
                    // pasien lama
                    if (isset($rujukan->response->rujukan->peserta->mr->noMR)) {
                        try {
                            $request['nomorkartu'] = $rujukan->response->rujukan->peserta->noKartu;
                            $request['nama'] = $rujukan->response->rujukan->peserta->nama;
                            $request['nik'] = $rujukan->response->rujukan->peserta->nik;
                            $request['norm'] = $rujukan->response->rujukan->peserta->mr->noMR;
                            $request['status'] = $rujukan->response->rujukan->peserta->statusPeserta->keterangan;
                            $request['diagnosa'] = $rujukan->response->rujukan->diagnosa->nama;
                            $request['polirujukan'] = $rujukan->response->rujukan->poliRujukan->nama;
                            $request['nohp'] = $request->number;
                            $request['tanggalperiksa'] = $tanggalperiksa;
                            $request['kodepoli'] = $jadwaldokter->kodesubspesialis;
                            $request['kodedokter'] = $jadwaldokter->kodedokter;
                            $request['jampraktek'] = $jadwaldokter->jadwal;
                            $request['jeniskunjungan'] = 1;
                            $antrian = new AntrianBPJSController();
                            $response = json_decode(json_encode($antrian->ambil_antrian($request)));
                            if ($response->metadata->code != 200) {
                                $request['message'] = "Error Pendaftaran : " .  $response->metadata->message;
                                return $this->send_message($request);
                            } else {
                                return $response;
                            }
                        } catch (\Throwable $th) {
                            $request['message'] = "Error : " . $th;
                            return $this->send_message($request);
                        }
                    }
                    // pasien baru
                    else {
                        $request['message'] = "Error : Mohon maaf untuk pasien baru tidak bisa daftar melalui whatsapp. Silahkan untuk daftar langsung ke RSUD Waled. Terima kasih.";
                        return $this->send_message($request);
                    }
                }
                // rujukan error
                else {
                    $request['message'] = "Error : " . $rujukan->metaData->message;
                    return $this->send_message($request);
                }
            }
            // daftar pake surat kontrol
            else {
                $request['message'] = "Error : Mohon maaf kunjungan rujukan lebih dari 1. Anda harus mendaftar menggunakan Surat Kontrol. Silahkan hubungi admin. Terima kasih";
                return $this->send_message($request);
            }
        }
        // gagal rujukan
        else {
            $request['message'] = "Error : " . $jumlah_sep->metaData->message;
            return $this->send_message($request);
        }
    }
    public function cek_nomorkartu_peserta($pesan, Request $request)
    {
        $nomorkartu = explode('#', $pesan)[0];
        $jadwal = explode('#', $pesan)[2];
        $tanggalperiksa = explode('#', $pesan)[3];
        $vclaim = new VclaimBPJSController();
        $request['nomorkartu'] = $nomorkartu;
        $peserta = $vclaim->peserta_nomorkartu($request);
        // gagal peserta
        if ($peserta->metaData->code != 200) {
            $request['message'] = "*Pesan Gagal (204)*\n" . $peserta->metaData->message;
            return $this->send_message($request);
        }
        // berhasil peserta
        else {
            $peserta = $peserta->response->peserta;
            $request['contenttext'] = "Nomor kartu berhasil ditemukan dengan data sebagai berikut : \n*Nama* : " . $peserta->nama . "\n*NIK* : " . $peserta->nik . "\n*No Kartu* : " . $peserta->noKartu . "\n*No RM* : " . $peserta->mr->noMR . "\n\n*Status* : " . $peserta->statusPeserta->keterangan . "\n*Faskes 1* : " . $peserta->provUmum->nmProvider . "\n*Jenis Peserta* : " . $peserta->jenisPeserta->keterangan . "\n*Hak Kelas* : " . $peserta->hakKelas->keterangan . "\n\nSilahkan pilih jenis kunjungan dibawah ini.";
            $request['titletext'] = "Pilih Jenis Kunjungan";
            $request['buttontext'] = 'PILIH JENIS KUNJUNGAN';
            $request['rowtitle'] = "RUJUKAN FASKES 1_" . $nomorkartu . "#" . $jadwal . "#" . $tanggalperiksa . "," . "RUJUKAN INTERNAL_" . $nomorkartu . "#" . $jadwal . "#" . $tanggalperiksa . "," . "KONTROL_" . $nomorkartu . "#" . $jadwal . "#" . $tanggalperiksa . "," . "RUJUKAN ANTAR RS_" . $nomorkartu . "#" . $jadwal . "#" . $tanggalperiksa . ",";
            return $this->send_list($request);
        }
    }
    public function rujukan_peserta($pesan, Request $request)
    {
        $format = explode('_', $pesan)[1];
        $nomorkartu = explode('#', $format)[0];
        $jadwal = explode('#', $format)[1];
        $tanggalperiksa = explode('#', $format)[2];
        $vclaim = new VclaimBPJSController();
        $request['nomorkartu'] = $nomorkartu;
        $rujukans = $vclaim->rujukan_peserta($request);
        if ($rujukans->metaData->code != 200) {
            $request['message'] = "*Pesan Gagal (201)*\n" . $rujukans->metaData->message;
            return $this->send_message($request);
        } else {
            $rowrujukan = null;
            $descrujukan = null;
            $rujukans = $rujukans->response->rujukan;
            foreach ($rujukans as $value) {
                $rowrujukan =  $rowrujukan . "POLI " . $value->poliRujukan->nama  . " NO. " . $value->noKunjungan  . ',';
                $descrujukan =  $descrujukan . '_RUJUKANFKTP#' . $value->noKunjungan . "#" . $jadwal . "#" . $tanggalperiksa . ',';
            }
            $request['contenttext'] = "Silahkan pilih nomor rujukan yang akan digunakan untuk mendaftar.";
            $request['titletext'] = "Rujukan Faskes Tingkat 1";
            $request['buttontext'] = 'PILIH RUJUKAN';
            $request['rowtitle'] = $rowrujukan;
            $request['rowdescription'] = $descrujukan;
            return $this->send_list($request);
        }
    }
    public function cek_rujukan($pesan, Request $request)
    {
        // init
        try {
            $format = explode('_', $pesan)[1];
            $nomorrujukan = explode('#', $format)[1];
            $jadwalid = explode('#', $format)[2];
            $tanggalperiksa = explode('#', $format)[3];
            $jadwaldokter = JadwalDokter::find($jadwalid);
            $request['nomorreferensi'] = $nomorrujukan;
            $request['jenisrujukan'] = 1;
        } catch (\Throwable $th) {
            //throw $th;
            $request['message'] = "Error : " . $th->getMessage() . "\nSilahkan hubungi admin. Terima kasih.";
            return $this->send_message($request);
        }
        $vclaim = new VclaimBPJSController();
        $jumlah_sep =  $vclaim->rujukan_jumlah_sep($request);
        // gagal jumlah sep rujukan
        if ($jumlah_sep->metaData->code != 200) {
            $request['message'] = "Error : " . $jumlah_sep->metaData->message;
            return $this->send_message($request);
        }
        // berhasil cek jumlah sep rujukan
        else {
            // daftar pake rujukan
            if ($jumlah_sep->response->jumlahSEP == 0) {
                $rujukan  = $vclaim->rujukan_nomor($request);
                // gagal rujukan
                if ($rujukan->metaData->code != 200) {
                    $request['message'] = "Error : " . $rujukan->metaData->message;
                    return $this->send_message($request);
                }
                // rujukan berhasil
                else {
                    // pasien lama
                    if (isset($rujukan->response->rujukan->peserta->mr->noMR)) {
                        try {
                            $request['nomorkartu'] = $rujukan->response->rujukan->peserta->noKartu;
                            $request['nama'] = $rujukan->response->rujukan->peserta->nama;
                            $request['nik'] = $rujukan->response->rujukan->peserta->nik;
                            $request['norm'] = $rujukan->response->rujukan->peserta->mr->noMR;
                            $request['status'] = $rujukan->response->rujukan->peserta->statusPeserta->keterangan;
                            $request['diagnosa'] = $rujukan->response->rujukan->diagnosa->nama;
                            $request['polirujukan'] = $rujukan->response->rujukan->poliRujukan->nama;
                            //    jika jadwal dan poli tujuan berbeda
                            if (strtoupper($request->polirujukan) !=  strtoupper($jadwaldokter->namasubspesialis)) {
                                $request['message'] = "*Pesan Gagal (203)*\nJadwal Poli dan Poli Tujuan Rujukan yang dipilih berbeda. \nSilahkan plih jadwal sesuai dengan rujukan anda.";
                                $this->send_message($request);
                                return $this->pilih_poli($pesan, $request);
                            }
                            $request['nohp'] = $request->number;
                            $request['tanggalperiksa'] = $tanggalperiksa;
                            $request['kodepoli'] = $jadwaldokter->kodesubspesialis;
                            $request['kodedokter'] = $jadwaldokter->kodedokter;
                            $request['jampraktek'] = $jadwaldokter->jadwal;
                            $request['jeniskunjungan'] = 1;
                            $request['titletext'] = "Konfirmasi Pendaftaran Pasien JKN";
                            $request['contenttext'] = "Jadwal dokter poliklinik yang dipilih sebagai berikut :\n*Poliklinik* : " . strtoupper($jadwaldokter->namasubspesialis) . "\n*Dokter* :  " . $jadwaldokter->namadokter . "\n*Jam Praktek* : " . $jadwaldokter->jadwal . "\n*Tanggal Periksa* :  " . $tanggalperiksa . "\n\nData pasien yang akan didaftarkan sebagai berikut :\n*Nama Pasien* : " . $request->nama . "\n*Status* : *" . $request->status . "*\n*NIK* : " . $request->nik . "\n*No BPJS* : " . $request->nomorkartu . "\n*No RM* : " . $request->norm . "\n\nData rujukan yang dipilih sebagai berikut : \n*No Rujukan* : " . $request->nomorreferensi . "\n*Poli Rujukan* : " . $request->polirujukan . "\n*Diagnosa* : " . $request->diagnosa .  "\n\nSebagai konfirmasi bahwa data yang diatas adalah benar pasien yang akan didaftarkan. Silahakan pilih tombol dibawah ini.";
                            $request['buttontext'] =  "DAFTAR RUJUKAN FKTP_" . $request->nomorreferensi . "#" . $jadwalid . "#" . $request->tanggalperiksa . ',BATAL PENDAFTARAN';
                            return $this->send_button($request);
                        } catch (\Throwable $th) {
                            $request['message'] = "Error : " . $th;
                            return $this->send_message($request);
                        }
                    }
                    // pasien baru
                    else {
                        $request['message'] = "Error : Mohon maaf untuk pasien baru tidak bisa daftar melalui whatsapp. Silahkan untuk daftar langsung ke RSUD Waled. Terima kasih.";
                        return $this->send_message($request);
                    }
                }
            }
            // daftar pake surat kontrol
            else {
                $request['jeniskunjungan'] = 3;
                $request['message'] = "Pesan Gagal (202) : Mohon maaf kunjungan rujukan lebih dari 1. Anda harus mendaftar menggunakan Surat Kontrol yang dibuat saat berobat sebelumnya. Untuk pertanyaan silahkan hubungi admin. Terima kasih";
                return $this->send_message($request);
            }
        }
        // $vclaim = new VclaimBPJSController();
        // $request['nomorkartu'] = $nomorrujukan;
        // $rujukans = $vclaim->rujukan_nomor($request);
        // if ($rujukans->metaData->code != 200) {
        //     $request['message'] = "Pesan Gagal (201) : " . $rujukans->metaData->message;
        //     return $this->send_message($request);
        // } else {
        //     $rowrujukan = null;
        //     $descrujukan = null;
        //     $rujukans = $rujukans->response->rujukan;
        //     foreach ($rujukans as $value) {
        //         $rowrujukan =  $rowrujukan . "POLI " . $value->poliRujukan->nama  . " NO. " . $value->noKunjungan  . ',';
        //         $descrujukan =  $descrujukan . '_RUJUKANFKTP#' . $value->noKunjungan . "#" . $jadwal . "#" . $tanggalperiksa . ',';
        //     }
        //     $request['contenttext'] = "Silahkan pilih nomor rujukan yang akan digunakan untuk mendaftar.";
        //     $request['titletext'] = "Rujukan Faskes Tingkat 1";
        //     $request['buttontext'] = 'PILIH RUJUKAN';
        //     $request['rowtitle'] = $rowrujukan;
        //     $request['rowdescription'] = $descrujukan;
        //     return $this->send_list($request);
        // }

    }
    public function daftar_rujukan_fktp($pesan, Request $request)
    {
        // init
        try {
            $format = explode('_', $pesan)[1];
            $nomorrujukan = explode('#', $format)[0];
            $jadwalid = explode('#', $format)[1];
            $tanggalperiksa = explode('#', $format)[2];
            $jadwaldokter = JadwalDokter::find($jadwalid);
            $request['nomorreferensi'] = $nomorrujukan;
            $request['jenisrujukan'] = 1;
        } catch (\Throwable $th) {
            //throw $th;
            $request['message'] = "*Pesan Gagal (205)*\n" . $th->getMessage() . "\nSilahkan hubungi admin. Terima kasih.";
            return $this->send_message($request);
        }
        $vclaim = new VclaimBPJSController();
        $jumlah_sep =  $vclaim->rujukan_jumlah_sep($request);
        //    server bpjs gangguan
        if ($jumlah_sep ==  null) {
            $request['message'] = "Mohon maaf server bpjs sedang gangguan.";
            return $this->send_message($request);
        }
        // berhasil
        else if ($jumlah_sep->metaData->code == 200) {
            // daftar pake rujukan
            if ($jumlah_sep->response->jumlahSEP == 0) {
                $rujukan  = $vclaim->rujukan_nomor($request);
                // rujukan 200 code
                if ($rujukan->metaData->code == 200) {
                    // pasien lama
                    if (isset($rujukan->response->rujukan->peserta->mr->noMR)) {
                        try {
                            $request['nomorkartu'] = $rujukan->response->rujukan->peserta->noKartu;
                            $request['nama'] = $rujukan->response->rujukan->peserta->nama;
                            $request['nik'] = $rujukan->response->rujukan->peserta->nik;
                            $request['norm'] = $rujukan->response->rujukan->peserta->mr->noMR;
                            $request['status'] = $rujukan->response->rujukan->peserta->statusPeserta->keterangan;
                            $request['diagnosa'] = $rujukan->response->rujukan->diagnosa->nama;
                            $request['polirujukan'] = $rujukan->response->rujukan->poliRujukan->nama;
                            $request['nohp'] = $request->number;
                            $request['tanggalperiksa'] = $tanggalperiksa;
                            $request['kodepoli'] = $jadwaldokter->kodesubspesialis;
                            $request['kodedokter'] = $jadwaldokter->kodedokter;
                            $request['jampraktek'] = $jadwaldokter->jadwal;
                            $request['jeniskunjungan'] = 1;
                            $antrian = new AntrianBPJSController();
                            $response = json_decode(json_encode($antrian->ambil_antrian($request)));
                            if ($response->metadata->code != 200) {
                                $request['message'] = "Error Pendaftaran : " .  $response->metadata->message;
                                return $this->send_message($request);
                            } else {
                                return $response;
                            }
                        } catch (\Throwable $th) {
                            $request['message'] = "Error : " . $th;
                            return $this->send_message($request);
                        }
                    }
                    // pasien baru
                    else {
                        $request['message'] = "Error : Mohon maaf untuk pasien baru tidak bisa daftar melalui whatsapp. Silahkan untuk daftar langsung ke RSUD Waled. Terima kasih.";
                        return $this->send_message($request);
                    }
                }
                // rujukan error
                else {
                    $request['message'] = "Error : " . $rujukan->metaData->message;
                    return $this->send_message($request);
                }
            }
            // daftar pake surat kontrol
            else {
                $request['message'] = "Error : Mohon maaf kunjungan rujukan lebih dari 1. Anda harus mendaftar menggunakan Surat Kontrol. Silahkan hubungi admin. Terima kasih";
                return $this->send_message($request);
            }
        }
        // gagal rujukan
        else {
            $request['message'] = "Error : " . $jumlah_sep->metaData->message;
            return $this->send_message($request);
        }
    }
}
