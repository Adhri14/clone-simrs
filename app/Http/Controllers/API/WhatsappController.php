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
    public $baseUrl = "192.168.2.10:3000/";
    protected $except = [
        'callback',
    ];
    public function index(Request $request)
    {
        $pesan = "0125U0120822P000346#DAFTAR_BPJS#233#2022-09-12";
        $request['number'] = substr('089529909036@c.us', 0, -5);
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
        // berhasil
        if ($jumlah_sep->metaData->code == 200) {
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
                            $response =  $antrian->ambil_antrian($request);

                            $request['message'] =  $response;
                            return $this->send_message($request);
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
            case 'DAFTAR PASIEN RAWAT JALAN':
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
                // dd('asd');
                // $now = Carbon::now();
                // $rowhari = 'RAWAT JALAN TANGGAL ' . $now->format('Y-m-d');
                // for ($i = 0; $i < 6; $i++) {
                //     $rowhari = $rowhari . ',RAWAT JALAN TANGGAL ' . $now->addDay(1)->format('Y-m-d');
                // }
                // $request['rowtitle'] = $rowhari;
                // return $this->send_list($request);
                break;
            default:
                // pilih poli terus tanggal
                if (str_contains($pesan, 'POLIKLINIK_')) {
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
                        $rowjadwaldokter = $rowjadwaldokter . $value->namadokter . '_JADWALID#' . $value->id . '#' . $tanggal->format('Y-m-d') . ',';
                    }
                    $request['contenttext'] = "Silahkan pilih jadwal dokter poliklinik " . $poli . " pada tanggal " . $tanggal->format('Y-m-d') . " dibawah ini.";
                    $request['titletext'] = "Pilih Jadwal Dokter";
                    $request['buttontext'] = 'PILIH JADWAL DOKTER';
                    $request['rowtitle'] = $rowjadwaldokter;
                    return $this->send_list($request);
                }
                // pilih dokter terus jenis pasien
                else if (str_contains($pesan, '_JADWALID#')) {
                    $jadwalid = explode('#', $pesan)[1];
                    $tanggal = explode('#', $pesan)[2];
                    $jadwaldokter = JadwalDokter::find($jadwalid);
                    $request['titletext'] = "Pilih Jenis Pasien";
                    $request['contenttext'] = "Jadwal dokter poliklinik yang anda pilih adalah sebagai berikut :\n\n*Poliklinik* : " . strtoupper($jadwaldokter->namasubspesialis) . "\n*Dokter* :  " . $jadwaldokter->namadokter . "\n*Jam Praktek* : " . $jadwaldokter->jadwal . "\n*Tanggal Periksa* :  " . $tanggal . "\n\nSilahakan pilih jenis pasien yang akan didaftarkan ini. \n\nCatatan :\nPasien JKN/BPJS : diharuskan memiliki rujukan faskes 1\nPasien UMUM : hanya pasien umum yang telah terdaftar saja dapat melakukan daftar online. Bagi yang belum terdaftar silahkan daftar langsung ditempat";
                    $request['buttontext'] = 'PASIEN JKN_' . $jadwalid . '#' . $tanggal . ',PASIEN UMUM_' . $jadwalid . '#' . $tanggal;
                    return $this->send_button($request);
                }
                // pilih jenis pasien, masukan rujukan
                else if (str_contains($pesan, 'PASIEN JKN_')) {
                    // PASIEN JKN_233#2022-09-12
                    $jadwalid = explode('_', $pesan)[1];
                    $request['message'] = "*KETIK RUJUKAN KEDALAM FORMAT*\nUntuk pasien JKN/BPJS silahkan ketik nomor rujukan dengan format seperti berikut : \n\nNomor Rujukan#BPJS#" . $jadwalid . "\n(Contoh)\n1234A1234B1234C1234#BPJS#" . $jadwalid;
                    $this->send_message($request);
                    $request['message'] = "0125XXXXXXXXP000XXX#BPJS#" . $jadwalid;
                    return $this->send_message($request);
                }
                // pilih jenis pasien, masukan nik
                else if (str_contains($pesan, 'PASIEN UMUM_')) {
                    $jadwalid = explode('_', $pesan)[1];
                    $request['message'] = "*KETIK NIK/KTP KEDALAM FORMAT*\nUntuk pasien UMUM silahkan ketik nomor nik/ktp dengan format seperti berikut : \n\nNIK / KTP#JKN#" . $jadwalid . "\n(Contoh)\n3209XXXX1234XXXX#UMUM#" . $jadwalid;
                    return $this->send_message($request);
                }
                // pilih jenis pasien, masukan nik
                else if (str_contains($pesan, '#BPJS#')) {
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
                        $request['message'] = "Error format pendaftaran : " . $th->getMessage() . "\nLihat dan sesuaikan kembali format pendaftaran pasien jkn. \n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                        return $this->send_message($request);
                    }
                    $vclaim = new VclaimBPJSController();
                    $jumlah_sep =  $vclaim->rujukan_jumlah_sep($request);
                    // berhasil
                    if ($jumlah_sep->metaData->code == 200) {
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
                                        $request['contenttext'] = "Jadwal dokter poliklinik yang dipilih sebagai berikut :\n\n*Poliklinik* : " . strtoupper($jadwaldokter->namasubspesialis) . "\n*Dokter* :  " . $jadwaldokter->namadokter . "\n*Jam Praktek* : " . $jadwaldokter->jadwal . "\n*Tanggal Periksa* :  " . $tanggalperiksa . "\n\n*Nama Pasien* : " . $request->nama . "\n*Status* : *" . $request->status . "*\n*NIK* : " . $request->nik . "\n*No BPJS* : " . $request->nomorkartu . "\n*No RM* : " . $request->norm . "\n*No Rujukan* : " . $request->nomorreferensi . "\n*Poli Rujukan* : " . $request->polirujukan . "\n*Diagnosa* : " . $request->diagnosa . "\n\nSebagai konfirmasi bahwa data yang diatas adalah benar pasien yang akan didaftarkan. Silahakan pilih tombol dibawah ini.";
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
                            $request['message'] = "Error : Mohon maaf kunjungan rujukan lebih dari 1. Anda harus mendaftar menggunakan Surat Kontrol.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                            return $this->send_message($request);
                        }
                    }
                    // gagal rujukan
                    else {
                        $request['message'] = "Error format pendaftaran : " . $jumlah_sep->metaData->message . "\nLihat dan sesuaikan kembali format pendaftaran pasien jkn.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                        return $this->send_message($request);
                    }
                }
                // insert antrian bpjs
                else if (str_contains($pesan, '#DAFTAR_BPJS#')) {
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
                    // berhasil
                    if ($jumlah_sep->metaData->code == 200) {
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
                                        return $antrian->ambil_antrian($request);
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
                // default
                else {
                    $request['contenttext'] = "Mohon maaf pesan yang anda masukan tidak dapat diproses oleh sistem.\n\nSilahkan pilih menu yang dapat diproses dibawah ini.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                    $request['titletext'] = "Error System Sedang Dalam Perbaikan";
                    $request['buttontext'] = 'MENU UTAMA';
                    $request['rowtitle'] = 'Daftar Pasien Rawat Jalan,MESSAGE,BUTTON,LIST';
                    // $request['rowdescription'] = 'Untuk daftar antrian pasien,Test send message,Test send buttons,Test send list';
                    return $this->send_list($request);
                    break;
                }
        }
        // return  $request->chatid;
        // switch ($pesan) {
        //     case 'DAFTAR':
        //         $request['contenttext'] = "Selamat datang dilayanan Antrian Online via Whatsapp RSUD Waled. Silahkan pilih tipe pasien yang akan didaftarkan :";
        //         $request['footertext'] = 'Pilih salah satu dibawah ini';
        //         $request['buttonid'] = 'id1, id2';
        //         $request['buttontext'] = 'PASIEN UMUM, PASIEN BPJS';
        //         return $this->send_button($request);
        //         // $request['message'] = "Untuk pendaftaran antrian online Silahkan daftar dengan format: \n\nDAFTAR#UMUM/BPJS#NAMA#NIK#POLI#TANGGAL(DD-MM-YYYY) \n\nContoh : \nDAFTAR#UMUM#BUDI#1234123412341234#GIGI#25-08-2022";
        //         // return $this->send_message($request);
        //         break;
        //     case 'DAFTAR ULANG':
        //         $request['contenttext'] = "Selamat datang dilayanan Antrian Online via Whatsapp RSUD Waled. Silahkan pilih tipe pasien yang akan didaftarkan :";
        //         $request['footertext'] = 'Pilih salah satu dibawah ini';
        //         $request['buttonid'] = 'id1,id2';
        //         $request['buttontext'] = 'PASIEN UMUM,PASIEN BPJS';
        //         return $this->send_button($request);
        //         break;
        //     case 'PASIEN UMUM':
        //         $request['message'] = "Untuk antrian PASIEN UMUM Silahkan daftar dengan format: \n\nDAFTAR#UMUM#NAMA#NIK#POLI#TANGGAL BEROBAT(DD-MM-YYYY) \n\nContoh : \nDAFTAR#UMUM#BUDI#320902090XXXXXXX#GIGI#25-08-2022";
        //         $this->send_message($request);
        //         $request['file'] =  asset('vendor/adminlte/dist/img/info poli.jpeg');
        //         $request['caption'] = "Daftar poliklinik yang tersedia untuk antrian Online WhatsApp ada di RSUD Waled, silahkan menggunakan *Kode Online Poliklinik* untuk menentukan pilihan.";
        //         return $this->send_image($request);
        //         break;
        //     case 'PASIEN BPJS':
        //         $request['message'] = "Untuk antrian PASIEN BPJS Silahkan daftar dengan format: \n\nDAFTAR#BPJS#NAMA#NIK#POLI#TANGGAL BEROBAT(DD-MM-YYYY) \n\nContoh : \nDAFTAR#BPJS#BUDI#320902090XXXXXXX#GIGI#25-08-2022\n\n*Catatan :* untuk pendaftaran pasien BPJS pastikan telah memiliki *Surat Rujukan dari Faskes 1 seusai tertera pada kartu atau Surat Kontrol yang masih berlaku.*";
        //         $this->send_message($request);
        //         $request['file'] =  asset('vendor/adminlte/dist/img/info poli.jpeg');
        //         $request['caption'] = "Daftar poliklinik yang tersedia untuk antrian Online WhatsApp ada di RSUD Waled, silahkan menggunakan *Kode Online Poliklinik* untuk menentukan pilihan.";
        //         return $this->send_image($request);
        //         break;
        //     case 'INFO':
        //         $request['message'] = "Silahkan ketikan informasi yang tersedia di Layanan Whatsapp ini. \n\n*INFO* : Melihat informasi yang terdapat dilayanan Whatsapp\n\n*INFO POLI* : Melihat poliklinik dan kodenya yang ada di RSUD Waled\n\n*INFO ANTRIAN* : Melihat infromasi antrian yang sedang berjalan\n\n*INFO JADWAL* : Melihat infromasi jadwal poliklinik yang tersedia di RSUD Waled\n\n*INFO JADWAL LIBUR* : Melihat infromasi jadwal libur poliklinik di RSUD Waled\n\n*INFO PERSYARATAN* : Melihat infromasi persyaratan pasien yang akan berobat di RSUD Waled\n\n*DAFTAR* : Mendaftarkan diri untuk antrian pasien yang akan berobat";
        //         return $this->send_message($request);
        //         break;
        //     case 'INFO POLI':
        //         $request['file'] =  asset('vendor/adminlte/dist/img/info poli.jpeg');
        //         $request['caption'] = "Daftar poliklinik yang tersedia untuk antrian Online WhatsApp ada di RSUD Waled, silahkan menggunakan *Kode Online Poliklinik* untuk menentukan pilihan.";
        //         $this->send_image($request);
        //         $request['text'] = "Untuk melihat detail informasi poliklinik silahkan klik tombol dibawah ini";
        //         $request['buttonlabel'] = 'Informasi Poliklinik';
        //         $request['buttonurl'] = 'http://103.94.5.210/simrs/info/poliklinik';
        //         return $this->send_button_link($request);
        //         break;
        //     case 'INFO ANTRIAN':
        //         $request['text'] = "Untuk melihat info antrian silahkan klik tombol dibawah ini";
        //         $request['buttonlabel'] = 'Cek Status Antrian';
        //         $request['buttonurl'] = 'http://103.94.5.210/simrs/info/antrian';
        //         return $this->send_button_link($request);
        //         break;
        //     case 'INFO JADWAL':
        //         $request['text'] = "Untuk melihat info jadwal poliklinik silahkan klik tombol dibawah ini";
        //         $request['buttonlabel'] = 'Jadwal Poliklinik';
        //         $request['buttonurl'] = 'http://103.94.5.210/simrs/info/jadwal_poliklinik';
        //         return $this->send_button_link($request);
        //         break;
        //     case 'INFO JADWAL LIBUR':
        //         $request['text'] = "Untuk melihat info jadwal libur poliklinik silahkan klik tombol dibawah ini";
        //         $request['buttonlabel'] = 'Jadwal Libur Poliklinik';
        //         $request['buttonurl'] = 'http://103.94.5.210/simrs/info/jadwal_poli_libur';
        //         return $this->send_button_link($request);
        //         break;
        //     case 'INFO PERSYARATAN':
        //         $request['contenttext'] = "Untuk melihat informasi persyaratan berobat pasien silahkan pilih tipe pasien yang akan didaftarkan :";
        //         $request['footertext'] = 'Pilih salah satu dibawah ini';
        //         $request['buttonid'] = 'id1, id2';
        //         $request['buttontext'] = 'PERSYARATAN PASIEN UMUM, PERSYARATAN PASIEN BPJS';
        //         return $this->send_button($request);
        //         break;
        //     case 'PERSYARATAN PASIEN UMUM':
        //         $request['message'] = "Persyaratan berobat *Pasien Umum yang Baru*\n1. KTP\n2. Kartu Keluarga\n\nPersyaratan berobat *Pasien Umum yang Lama*\n1. KTP\n2. Kartu Keluarga\n3. Kartu Berobat Pasien (bila pernah mendaftar sebelumnya)";
        //         return $this->send_message($request);
        //         break;
        //     case 'PERSYARATAN PASIEN BPJS':
        //         $request['message'] = "Persyaratan berobat *Pasien BPJS yang Baru*\n1. KTP\n2. Kartu Keluarga\n3. Surat Rujukan dari Faskes 1 (Puskesmas) yg tertera di kartu BPJS\n4. Menunjukkan Kartu JKN / KIS / BPJS / Askes Asli / KIS Digital yang asli\n\nPersyaratan berobat *Pasien BPJS yang Lama*\n1. Surat Rujukan atau Surat Kontrol yang masih berlaku\n2. Menunjukkan Kartu JKN / KIS / BPJS / Askes Asli / KIS Digital yang asli";
        //         return $this->send_message($request);
        //         break;
        //     case 'SANGAT MEMBANTU':
        //         $request['message'] = "Terimakasih atas masukan anda untuk pelayanan RSUD Waled agar lebih baik. Semoga Lekas Sembuh.\n\nRSUD Waled\nMelayani Sepenuh Hati";
        //         return $this->send_message($request);
        //         break;
        //     case 'TOLONG PERBAIKI':
        //         $request['message'] = "Terimakasih atas masukan anda untuk pelayanan RSUD Waled agar lebih baik. Semoga Lekas Sembuh.\n\nRSUD Waled\nMelayani Sepenuh Hati";
        //         return $this->send_message($request);
        //         break;
        //     case 'CEK BPJS':
        //         // $request['message'] = "Terimakasih atas masukan anda untuk pelayanan RSUD Waled agar lebih baik. Semoga Lekas Sembuh.\n\nRSUD Waled\nMelayani Sepenuh Hati";
        //         // return $this->send_message($request);
        //         break;
        //     case 'QRCODE':
        //         try {
        //             QrCode::size(500)
        //                 ->format('png')
        //                 ->generate('codingdriver.com', storage_path('app/public/images/qrantrian/qrcode.png'));
        //             $img = Image::make(public_path('images/antrian-template.png'));
        //             $img->insert(public_path('images/qrcode.png'), 'center');
        //             $img->text("Tiket Checkin Antrian WhatsApp", 500, 200, function ($font) {
        //                 $font->file(public_path('font/tnrbold.ttf'));
        //                 $font->align('center');
        //                 $font->size(40);
        //             });
        //             $img->text('Scan kode QR ini ke mesin antrian di RSUD Waled untuk checkin antrian.', 500, 780, function ($font) {
        //                 $font->file(public_path('font/tnr.ttf'));
        //                 $font->align('center');
        //                 $font->size(18);
        //             });
        //             $img->save(public_path('images/antrian1.png'));
        //             $request['file'] =  asset('images/antrian1.png');
        //             $request['caption'] = "Antrian berhasil didaftarkan dengan data sebagai berikut : \n\n*Nomor Antrian :* 1\n*Kode Antrian :* PDD0021030123\n*Nama :* Marwan\n*Poli :* PENYAKIT DALAM (KLINIK)\n*Tanggal Berobat :* Minggu, 6 Januari 2022\n\nSilahkan datang pada hari tersebut untuk menunggu giliran antrian pendaftaran di *LOKET ANTRIAN WA Lantai 1 RSUD WALED* dibuka mulai pukul *07.30 WIB* serta membawa persyaratan perobat untuk informasinya ketik *INFO PERSYARATAN* dan anda dapat memantau status antrian dengan klik tombol berikut.";
        //             return $this->send_image($request);
        //         } catch (\Throwable $th) {
        //             $request['message'] = "Error : " . $th->getMessage();
        //             return $this->send_message($request);
        //         }
        //         break;
        //     default:
        //         $kode = explode('#', $pesan);
        //         if ($kode[0] == 'DAFTAR') {
        //             return $this->daftar_antrian($request);
        //         } else if ($kode[0] == 'DAFTAR2') {
        //             return $this->daftar_antrian_qr($request);
        //         } else if ($kode[0] == 'BATAL-ANTRIAN') {
        //             return $this->batal_antrian($request);
        //         } else if ($kode[0] == 'DAFTAR-ULANG') {
        //             return $this->daftar_ulang($request);
        //         } else {
        //             $request['message'] = "Maaf, pesan yang anda kirimkan tidak dapat kami proses. \nLayanan ini diatur melalui sistem. \nSilahkan ketik *INFO* untuk melihat informasi yang tersedia.\nSilahkan ketik *DAFTAR* untuk mendaftarkan Antrian Online Layanan Whatsapp ini.\n\nUntuk pertanyaan & pengaduan silahkan hubungi *Humas RSUD Waled 08983311118* ";
        //             return $this->send_message($request);
        //         }
        //         break;
        // }
    }
    // public function daftar_antrian(Request $request)
    // {
    //     // cek format antrian
    //     try {
    //         $pesan = explode('#', $request->message);
    //         $tipe = $pesan[1];
    //         $nama = $pesan[2];
    //         $nik = $pesan[3];
    //         $poli = $pesan[4];
    //         $tanggal = Carbon::parse($pesan[5])->format('Y-m-d');
    //         $hari = Carbon::parse($pesan[5])->dayOfWeek - 1;

    //         //cek tipe format pasien
    //         if ($tipe != 'UMUM' && $tipe != 'BPJS') {
    //             $request['message'] = "Mohon maaf anda salah memasukan tipe pasien. Silahkan pilih format daftar antrian pasien UMUM / BPJS. \n\nContoh Pasien Umum : \nDAFTAR#UMUM#BUDI#320902090XXXXXXX#GIGI#25-08-2022  \n\nContoh Pasien BPJS: \nDAFTAR#BPJS#BUDI#320902090XXXXXXX#GIGI#25-08-2022";
    //             return $this->send_message($request);
    //         }
    //         //cek ktp 16 digit
    //         if (strlen($nik) != 16) {
    //             $request['message'] = 'Nomor NIK harus 16 digit';
    //             return $this->send_message($request);
    //         }
    //         //cek nik angka
    //         if (is_numeric($nik) == false) {
    //             $request['message'] = "Mohon maaf anda salah memasukan NIK. NIK harus berisikan angka.";
    //             return $this->send_message($request);
    //         }
    //         //cek poliklinik
    //         $poliklinik = UnitDB::with(['jadwals'])->firstWhere('nama_panggil', $poli);
    //         if (empty($poliklinik)) {
    //             $request['message'] = 'Mohon maaf kode poliklinik tidak ditemukan. Silahkan pilih poliklinik tersedia untuk Antrian Whastapp Online.';
    //             $this->send_message($request);
    //             $request['file'] =  asset('vendor/adminlte/dist/img/info poli.jpeg');
    //             $request['caption'] = "Daftar poliklinik yang tersedia untuk antrian Online WhatsApp ada di RSUD Waled, silahkan menggunakan *Kode Online Poliklinik* untuk menentukan pilihan.";
    //             return $this->send_image($request);
    //         }
    //         // cek satu minggu
    //         $satuminggu = Carbon::parse($pesan[5])->between(Carbon::today(), Carbon::today()->addDays(7));
    //         if ($satuminggu == false) {
    //             $request['message'] = "Pendaftaran antrian gagal karena batas tanggal pendaftaran hanya dalam satu minggu dari hari ini. Silahkan daftarkan ditanggal yang lain.";
    //             return $this->send_message($request);
    //         }
    //         // cek libur nasional
    //         $libur_nasional = JadwalLiburPoliDB::where('tanggal_awal', '<=', $tanggal)->where('tanggal_akhir', '>=', $tanggal)->where('kode_poli', 0)->first();
    //         if (isset($libur_nasional)) {
    //             $request['message'] = "Mohon maaf pada tanggal tersebut semua poliklik libur dengan keterangan *" . $libur_nasional->keterangan . "*. Silahkan daftar dihari yang lain. Terima kasih.";
    //             return $this->send_message($request);
    //         }
    //         // cek jadwal libur poli
    //         $libur = JadwalLiburPoliDB::where('tanggal_awal', '<=', $tanggal)->where('tanggal_akhir', '>=', $tanggal)->where('kode_poli', $poliklinik->kode_unit)->first();
    //         if (isset($libur)) {
    //             $request['text'] = "Mohon maaf poliklinik pada tanggal tesebut sedang libur. Silahkan daftar dihari yang lain. Terima kasih. \n\nUntuk informasi jadwal libur poliklinik dapat dilihat melalui link berikut.";
    //             $request['buttonlabel'] = 'Jadwal Libur Poliklinik';
    //             $request['buttonurl'] = 'http://103.94.5.210/simrs/antrian/jadwal_poli_libur';
    //             return $this->send_button_link($request);
    //         }
    //         //cek hari ini jam 10 atw lebih
    //         $jam = Carbon::now();
    //         if (Carbon::parse($pesan[5])->isToday()) {
    //             if ($jam >= Carbon::parse('10:00')) {
    //                 $request['message'] = "Untuk pendaftaran hari ini hanya dapat dilakukan sebelum jam 10:00 WIB. Silahkan daftar lagi dihari yang lain. Terimakasih.";
    //                 return $this->send_message($request);
    //             }
    //         }
    //         //cek jadwal
    //         $jadwalpoli = $poliklinik->jadwals;
    //         if ($jadwalpoli->where('hari', $hari)->count() == 0) {
    //             $request['text'] = "Mohon maaf poliklinik pada hari tesebut tidak ada jadwal. Silahkan daftar dihari yang lain. Terima kasih. \n\nUntuk informasi jadwal poliklinik dapat dilihat melalui link berikut.";
    //             $request['buttonlabel'] = 'Jadwal Poliklinik';
    //             $request['buttonurl'] = 'http://103.94.5.210/simrs/antrian/jadwal_poli';
    //             return $this->send_button_link($request);
    //         }
    //         // cek kuota poli
    //         $kuotapoli = AntrianDB::where('tanggal', $tanggal)->where('kode_poli', $poliklinik->kode_unit)->count();
    //         if ($kuotapoli  > $poliklinik->kuota_online - 1) {
    //             $request['text'] = "Pendaftaran antrian gagal karena kuota online poliklinik sudah penuh. Silahkan daftar dihari yang lain.\n\nSilahkan klik tombol dibawah ini untuk melihat informasi poliklinik. Terima kasih.";
    //             $request['buttonlabel'] = 'Informasi Poliklinik';
    //             $request['buttonurl'] = 'http://103.94.5.210/simrs/info/poliklinik';
    //             return $this->send_button_link($request);
    //         }
    //         //cek tipe pasien bpjs
    //         if ($tipe == "BPJS") {
    //             $pasien  = PasienDB::firstWhere('nik_bpjs', $nik);
    //             // try {
    //             //     $api = new ApiBpjsController;
    //             //     // $response = $api->get_peserta_nik_V1($nik);
    //             //     if ($response->metaData->code != 200) {
    //             //         $request['message'] = 'Mohon maaf pendaftaran antrian gagal. Silahkan cek kembali (' . $response->metaData->message . ')';
    //             //         return $this->send_message($request);
    //             //     } else {
    //             //         if ($response->response->peserta->statusPeserta->kode == 0) {
    //             //             $pasien  = PasienDB::firstWhere('nik_bpjs', $nik);
    //             //         } else {
    //             //             $request['message'] = 'Mohon maaf pendaftaran antrian tidak bisa dilakukan karena kartu peserta bpjs bermasalah. (' . $response->response->peserta->statusPeserta->keterangan . ')';
    //             //             return $this->send_message($request);
    //             //         }
    //             //     }
    //             // } catch (\Throwable $th) {
    //             //     $request['message'] = 'Mohon maaf pendaftaran gagal silahkan coba lagi. error (' . $th->getMessage() . ')';
    //             //     return $this->send_message($request);
    //             // }
    //         }
    //         //cek tipe pasien umum
    //         if ($tipe == "UMUM") {
    //             $pasien  = PasienDB::firstWhere('nik_bpjs', $nik);
    //         }
    //         //cek pasien baru / lama
    //         if (empty($pasien)) {
    //             $pasien['nama_px'] = 'Pasien Baru';
    //             $pasien['no_rm'] = 'Pasien Baru';
    //         }
    //     } catch (\Throwable $th) {
    //         dd($th->getMessage());
    //         $request['message'] = "Format pendaftaran antrian salah. \nSilahkan periksa kembali dan perbaiki format pendaftaran. Terima kasih.";
    //         return $this->send_message($request);
    //     }
    //     // input antrian
    //     try {
    //         $no_urut = AntrianDB::whereDate('tanggal', $tanggal)->count();
    //         $kode = $poliklinik['prefix_unit'] . Carbon::parse($tanggal)->format('dmY') . str_pad($no_urut + 1, 4, '0', STR_PAD_LEFT);
    //         $antrian = AntrianDB::create([
    //             'kode_antrian' => $kode,
    //             'tanggal' => $tanggal,
    //             'nik' => $nik,
    //             'nama_antrian' => $nama,
    //             'nama' => $pasien['nama_px'],
    //             'no_rm' => $pasien['no_rm'],
    //             'phone' => $request->number,
    //             'kode_poli' => $poliklinik['kode_unit'],
    //             'status' => 1,
    //             'tipe' => $tipe,
    //             'no_urut' => $no_urut + 1,
    //         ]);
    //         $request['message'] = "Antrian berhasil didaftarkan dengan data sebagai berikut : \n\n*Nomor Antrian :* " . $antrian->no_urut . "\n*Kode Antrian :* " . $antrian->kode_antrian . "\n*Nama :* " . $nama . "\n*Poli :* " . $poliklinik['nama_unit'] . "\n*Tanggal Berobat :* " . Carbon::parse($antrian->tanggal)->format('d-m-Y') . "\n\nSilahkan datang pada hari tersebut untuk menunggu giliran antrian pendaftaran di *LOKET ANTRIAN WA Lantai 1 RSUD WALED* dibuka mulai pukul *07.30 WIB* serta membawa persyaratan perobat untuk informasinya ketik *INFO PERSYARATAN* dan anda dapat memantau status antrian dengan klik tombol berikut.";
    //         return $this->send_message($request);
    //         // $request['buttonlabel'] = 'Cek Status Antrian';
    //         // $request['buttonurl'] = 'http://103.94.5.210/simrs/info/antrian';
    //         // return $this->send_button_link($request);
    //     } catch (\Throwable $th) {
    //         $request['message'] = "Gagal daftar antrian karena sistem error. (" . $th->getMessage() . ")";
    //         return $this->send_message($request);
    //     }
    // }
    // public function daftar_antrian_qr(Request $request)
    // {
    //     // cek format antrian
    //     try {
    //         $pesan = explode('#', $request->message);
    //         $tipe = $pesan[1];
    //         $nama = $pesan[2];
    //         $nik = $pesan[3];
    //         $poli = $pesan[4];
    //         $tanggal = Carbon::parse($pesan[5])->format('Y-m-d');
    //         $hari = Carbon::parse($pesan[5])->dayOfWeek - 1;

    //         //cek tipe format pasien
    //         if ($tipe != 'UMUM' && $tipe != 'BPJS') {
    //             $request['message'] = "Mohon maaf anda salah memasukan tipe pasien. Silahkan pilih format daftar antrian pasien UMUM / BPJS. \n\nContoh Pasien Umum : \nDAFTAR#UMUM#BUDI#320902090XXXXXXX#GIGI#25-08-2022  \n\nContoh Pasien BPJS: \nDAFTAR#BPJS#BUDI#320902090XXXXXXX#GIGI#25-08-2022";
    //             return $this->send_message($request);
    //         }
    //         //cek ktp 16 digit
    //         if (strlen($nik) != 16) {
    //             $request['message'] = 'Nomor NIK harus 16 digit';
    //             return $this->send_message($request);
    //         }
    //         //cek nik angka
    //         if (is_numeric($nik) == false) {
    //             $request['message'] = "Mohon maaf anda salah memasukan NIK. NIK harus berisikan angka.";
    //             return $this->send_message($request);
    //         }
    //         //cek poliklinik
    //         $poliklinik = UnitDB::with(['jadwals'])->firstWhere('nama_panggil', $poli);
    //         if (empty($poliklinik)) {
    //             $request['message'] = 'Mohon maaf kode poliklinik tidak ditemukan. Silahkan pilih poliklinik tersedia untuk Antrian Whastapp Online.';
    //             $this->send_message($request);
    //             $request['file'] =  asset('vendor/adminlte/dist/img/info poli.jpeg');
    //             $request['caption'] = "Daftar poliklinik yang tersedia untuk antrian Online WhatsApp ada di RSUD Waled, silahkan menggunakan *Kode Online Poliklinik* untuk menentukan pilihan.";
    //             return $this->send_image($request);
    //         }
    //         // cek satu minggu
    //         $satuminggu = Carbon::parse($pesan[5])->between(Carbon::today(), Carbon::today()->addDays(7));
    //         if ($satuminggu == false) {
    //             $request['message'] = "Pendaftaran antrian gagal karena batas tanggal pendaftaran hanya dalam satu minggu dari hari ini. Silahkan daftarkan ditanggal yang lain.";
    //             return $this->send_message($request);
    //         }
    //         // cek libur nasional
    //         $libur_nasional = JadwalLiburPoliDB::where('tanggal_awal', '<=', $tanggal)->where('tanggal_akhir', '>=', $tanggal)->where('kode_poli', 0)->first();
    //         if (isset($libur_nasional)) {
    //             $request['message'] = "Mohon maaf pada tanggal tersebut semua poliklik libur dengan keterangan *" . $libur_nasional->keterangan . "*. Silahkan daftar dihari yang lain. Terima kasih.";
    //             return $this->send_message($request);
    //         }
    //         // cek jadwal libur poli
    //         $libur = JadwalLiburPoliDB::where('tanggal_awal', '<=', $tanggal)->where('tanggal_akhir', '>=', $tanggal)->where('kode_poli', $poliklinik->kode_unit)->first();
    //         if (isset($libur)) {
    //             $request['text'] = "Mohon maaf poliklinik pada tanggal tesebut sedang libur. Silahkan daftar dihari yang lain. Terima kasih. \n\nUntuk informasi jadwal libur poliklinik dapat dilihat melalui link berikut.";
    //             $request['buttonlabel'] = 'Jadwal Libur Poliklinik';
    //             $request['buttonurl'] = 'http://103.94.5.210/simrs/antrian/jadwal_poli_libur';
    //             return $this->send_button_link($request);
    //         }
    //         //cek hari ini jam 10 atw lebih
    //         $jam = Carbon::now();
    //         if (Carbon::parse($pesan[5])->isToday()) {
    //             if ($jam >= Carbon::parse('10:00')) {
    //                 $request['message'] = "Untuk pendaftaran hari ini hanya dapat dilakukan sebelum jam 10:00 WIB. Silahkan daftar lagi dihari yang lain. Terimakasih.";
    //                 return $this->send_message($request);
    //             }
    //         }
    //         //cek jadwal
    //         $jadwalpoli = $poliklinik->jadwals;
    //         if ($jadwalpoli->where('hari', $hari)->count() == 0) {
    //             $request['text'] = "Mohon maaf poliklinik pada hari tesebut tidak ada jadwal. Silahkan daftar dihari yang lain. Terima kasih. \n\nUntuk informasi jadwal poliklinik dapat dilihat melalui link berikut.";
    //             $request['buttonlabel'] = 'Jadwal Poliklinik';
    //             $request['buttonurl'] = 'http://103.94.5.210/simrs/antrian/jadwal_poli';
    //             return $this->send_button_link($request);
    //         }
    //         // cek kuota poli
    //         $kuotapoli = AntrianDB::where('tanggal', $tanggal)->where('kode_poli', $poliklinik->kode_unit)->count();
    //         if ($kuotapoli  > $poliklinik->kuota_online - 1) {
    //             $request['text'] = "Pendaftaran antrian gagal karena kuota online poliklinik sudah penuh. Silahkan daftar dihari yang lain.\n\nSilahkan klik tombol dibawah ini untuk melihat informasi poliklinik. Terima kasih.";
    //             $request['buttonlabel'] = 'Informasi Poliklinik';
    //             $request['buttonurl'] = 'http://103.94.5.210/simrs/info/poliklinik';
    //             return $this->send_button_link($request);
    //         }
    //         //cek tipe pasien bpjs
    //         if ($tipe == "BPJS") {
    //             try {
    //                 $api = new ApiBpjsController;
    //                 $response = $api->get_peserta_nik_V1($nik);
    //                 if ($response->metaData->code != 200) {
    //                     $request['message'] = 'Mohon maaf pendaftaran antrian gagal. Silahkan cek kembali (' . $response->metaData->message . ')';
    //                     return $this->send_message($request);
    //                 } else {
    //                     if ($response->response->peserta->statusPeserta->kode == 0) {
    //                         $pasien  = PasienDB::firstWhere('nik_bpjs', $nik);
    //                     } else {
    //                         $request['message'] = 'Mohon maaf pendaftaran antrian tidak bisa dilakukan karena kartu peserta bpjs bermasalah. (' . $response->response->peserta->statusPeserta->keterangan . ')';
    //                         return $this->send_message($request);
    //                     }
    //                 }
    //             } catch (\Throwable $th) {
    //                 $request['message'] = 'Mohon maaf pendaftaran gagal silahkan coba lagi. error (' . $th->getMessage() . ')';
    //                 return $this->send_message($request);
    //             }
    //         }
    //         //cek tipe pasien umum
    //         if ($tipe == "UMUM") {
    //             $pasien  = PasienDB::firstWhere('nik_bpjs', $nik);
    //         }
    //         //cek pasien baru / lama
    //         if (empty($pasien)) {
    //             $pasien['nama_px'] = 'Pasien Baru';
    //             $pasien['no_rm'] = 'Pasien Baru';
    //         }
    //     } catch (\Throwable $th) {
    //         $request['message'] = "Format pendaftaran antrian salah. \nSilahkan periksa kembali dan perbaiki format pendaftaran. Terima kasih.";
    //         return $this->send_message($request);
    //     }
    //     // input antrian
    //     try {
    //         $no_urut = AntrianDB::whereDate('tanggal', $tanggal)->count();
    //         $kode = $poliklinik['prefix_unit'] . Carbon::parse($tanggal)->format('dmY') . str_pad($no_urut + 1, 4, '0', STR_PAD_LEFT);
    //         $antrian = AntrianDB::create([
    //             'kode_antrian' => $kode,
    //             'tanggal' => $tanggal,
    //             'nik' => $nik,
    //             'nama_antrian' => $nama,
    //             'nama' => $pasien['nama_px'],
    //             'no_rm' => $pasien['no_rm'],
    //             'phone' => $request->number,
    //             'kode_poli' => $poliklinik['kode_unit'],
    //             'status' => 0,
    //             'tipe' => $tipe,
    //             'no_urut' => $no_urut + 1,
    //         ]);
    //         QrCode::size(500)
    //             ->format('png')
    //             ->generate($antrian->kode_antrian, public_path("images/" . $antrian->kode_antrian . ".png"));
    //         $img = Image::make(public_path('images/antrian-template.png'));
    //         $img->insert(public_path("images/" . $antrian->kode_antrian . ".png"), 'center');
    //         $img->text("Tiket Checkin Antrian WhatsApp", 500, 200, function ($font) {
    //             $font->file(public_path('font/tnrbold.ttf'));
    //             $font->align('center');
    //             $font->size(40);
    //         });
    //         $img->text('Scan kode QR ini ke mesin antrian di RSUD Waled untuk checkin antrian. ' . $antrian->kode_antrian, 500, 780, function ($font) {
    //             $font->file(public_path('font/tnr.ttf'));
    //             $font->align('center');
    //             $font->size(18);
    //         });
    //         $img->save(public_path('images/antrian1.png'));
    //         // $request['file'] =  asset('images/antrian1.png');
    //         // $request['caption'] = "Antrian berhasil didaftarkan dengan data sebagai berikut : \n\n*Nomor Antrian :* " . $antrian->no_urut . "\n*Kode Antrian :* " . $antrian->kode_antrian . "\n*Nama :* " . $nama . "\n*Poli :* " . $poliklinik['nama_unit'] . "\n*Tanggal Berobat :* " . Carbon::parse($antrian->tanggal)->format('d-m-Y') . "\n\nSilahkan datang pada hari tersebut untuk menunggu giliran antrian pendaftaran di *LOKET ANTRIAN WA Lantai 1 RSUD WALED* dibuka mulai pukul *07.30 WIB* serta membawa persyaratan perobat untuk informasinya ketik *INFO PERSYARATAN* dan anda dapat memantau status antrian dengan klik tombol berikut.";
    //         // $request['caption'] = "Antrian berhasil didaftarkan dengan data sebagai berikut : \n\n*Nomor Antrian :* 1\n*Kode Antrian :* PDD0021030123\n*Nama :* Marwan\n*Poli :* PENYAKIT DALAM (KLINIK)\n*Tanggal Berobat :* Minggu, 6 Januari 2022\n\nSilahkan datang pada hari tersebut untuk menunggu giliran antrian pendaftaran di *LOKET ANTRIAN WA Lantai 1 RSUD WALED* dibuka mulai pukul *07.30 WIB* serta membawa persyaratan perobat untuk informasinya ketik *INFO PERSYARATAN* dan anda dapat memantau status antrian dengan klik tombol berikut.";
    //         // return $this->send_image($request);

    //         $request['message'] = "Antrian berhasil didaftarkan dengan data sebagai berikut : \n\n*Nomor Antrian :* " . $antrian->no_urut . "\n*Kode Antrian :* " . $antrian->kode_antrian . "\n*Nama :* " . $nama . "\n*Poli :* " . $poliklinik['nama_unit'] . "\n*Tanggal Berobat :* " . Carbon::parse($antrian->tanggal)->format('d-m-Y') . "\n\nSilahkan datang pada hari tersebut untuk menunggu giliran antrian pendaftaran di *LOKET ANTRIAN WA Lantai 1 RSUD WALED* dibuka mulai pukul *07.30 WIB* serta membawa persyaratan perobat untuk informasinya ketik *INFO PERSYARATAN* dan anda dapat memantau status antrian dengan klik tombol berikut.";
    //         return $this->send_message($request);
    //         // $request['buttonlabel'] = 'Cek Status Antrian';
    //         // $request['buttonurl'] = 'http://103.94.5.210/simrs/info/antrian';
    //         // return $this->send_button_link($request);
    //     } catch (\Throwable $th) {
    //         $request['message'] = "Gagal daftar antrian karena sistem error. (" . $th->getMessage() . ")";
    //         return $this->send_message($request);
    //     }
    // }
    // public function batal_antrian(Request $request)
    // {
    //     // cek format antrian
    //     try {
    //         $pesan = explode('#', $request->message);
    //         $kode = $pesan[1];
    //         $antrian = AntrianDB::where('kode_antrian', $kode)->first();
    //         if ($antrian->phone == $request->number) {
    //             $antrian->update([
    //                 'status' => 99,
    //             ]);
    //             $request['message'] = "Nomor antrian anda telah dibatalkan";
    //             return $this->send_message($request);
    //         } else {
    //             $request['message'] = "Anda tidak berhak membatalkan nomor antrian ini";
    //             return $this->send_message($request);
    //         }
    //     } catch (\Throwable $th) {
    //         $request['message'] = "Format pendaftaran antrian salah";
    //         return $this->send_message($request);
    //     }
    // }
    // public function daftar_ulang(Request $request)
    // {
    //     try {
    //         // cek format antrian
    //         $pesan = explode('#', $request->message);
    //         $kode = $pesan[1];
    //         $antrian = AntrianDB::where('kode_antrian', $kode)->first();
    //         if ($antrian->phone == $request->number) {
    //             $antrian->update([
    //                 'status' => 97,
    //             ]);
    //             $tanggal = $antrian->tanggal;
    //             $poliklinik = $antrian->unit;

    //             $no_urut = AntrianDB::whereDate('tanggal', $tanggal)->count();
    //             $kode = $poliklinik['prefix_unit'] . Carbon::parse($tanggal)->format('dmY') . str_pad($no_urut + 1, 4, '0', STR_PAD_LEFT);
    //             $antrian = AntrianDB::create([
    //                 'kode_antrian' => $kode,
    //                 'tanggal' => $tanggal,
    //                 'nik' => $antrian->nik,
    //                 'nama_antrian' => $antrian->nama_antrian,
    //                 'nama' => $antrian->nama,
    //                 'no_rm' => $antrian->no_rm,
    //                 'nomor_bpjs' => $antrian->nomor_bpjs,
    //                 'phone' => $antrian->phone,
    //                 'kode_poli' => $antrian->kode_poli,
    //                 'status' => 1,
    //                 'tipe' => $antrian->tipe,
    //                 'no_urut' => $no_urut + 1,
    //             ]);

    //             $request['text'] = "Antrian berhasil didaftarkan ulang dengan data sebagai berikut : \n\n*Nomor Antrian :* " . $antrian->no_urut . "\n*Kode Antrian :* " . $antrian->kode_antrian . "\n*Nama :* " . $antrian->nama . "\n*Poli :* " . $poliklinik['nama_unit'] . "\n*Tanggal Berobat :* " . Carbon::parse($antrian->tanggal)->format('d-m-Y') . "\n\nSilahkan datang pada hari tersebut untuk menunggu giliran antrian pendaftaran di *LOKET ANTRIAN WA Lantai 1 RSUD WALED* dibuka mulai pukul *07.30 WIB* serta membawa persyaratan perobat untuk informasinya ketik *INFO PERSYARATAN* dan anda dapat memantau status antrian dengan klik tombol berikut.";
    //             $request['buttonlabel'] = 'Cek Status Antrian';
    //             $request['buttonurl'] = 'http://103.94.5.210/simrs/info/antrian';
    //             return $this->send_button_link($request);
    //         } else {
    //             $request['message'] = "Anda tidak berhak membatalkan nomor antrian ini";
    //             return $this->send_message($request);
    //         }
    //     } catch (\Throwable $th) {
    //         $request['message'] = "Error Daftar Ulang Antrian. error : " . $th->getMessage();
    //         return $this->send_message($request);
    //     }
    // }
}
