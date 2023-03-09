<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BPJS\Antrian\AntrianController;
use App\Http\Controllers\BPJS\Vclaim\VclaimController;
use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\BPJS\Antrian\JadwalDokterAntrian;
use App\Models\PasienDB;
use App\Models\PoliklinikDB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class WhatsappController extends Controller
{
    public $hari = ["MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"];
    public function test(Request $request)
    {
        $request['message']  = "DAFTAR KONTROL_1018R0011222K001074#504#2022-12-06";
        $request['number'] = '6289529909036@c.us';
        $sk = $this->callback($request);
    }
    public function whatsapp(Request $request)
    {
        $url = env('WHATASAPP_URL') . "send-message";
        $response = Http::post($url, [
            'number' => '089529909036',
            'message' => 'Test',
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_message(Request $request)
    {
        $url = env('WHATASAPP_URL') . "send-message";
        $response = Http::post($url, [
            'number' => $request->number,
            'message' => $request->message,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_notif(Request $request)
    {
        $url = env('WHATASAPP_URL') . "notif";
        $response = Http::post($url, [
            'message' => $request->notif,
        ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function send_button(Request $request)
    {
        $url = env('WHATASAPP_URL') . "send-button";
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
        $url = env('WHATASAPP_URL') . "send-list";
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
        $url = env('WHATASAPP_URL') . "send-media";
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
        $url = env('WHATASAPP_URL') . "send-filepath";
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
            case 'NOTIF':
                $request['notif'] = "Test Send Notif";
                return $this->send_notif($request);
                break;
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
                $request['message'] = "Untuk pendaftaran melalui Layanan Whatsapp RSUD Waled sebagai berikut :\n\n1. Klik *MENU UTAMA* pilih *DAFTAR PASIEN RAWAT JALAN*. \n2. Pilih *POLIKLINIK* yang akan dikunjungi dan yang tersedia untuk daftar online. \n3. Pilih *TANGGAL* kapan pasien akan berkunjung. \n4. Pilih *JADWAL DOKTER* yang akan dituju oleh pasien. \n5. Pilih *JENIS PASIEN* terdapat pilihan pasien *JKN / UMUM*. \n6. Menuliskan format pendaftaran, untuk *PASIEN JKN* memasukan *Nomor Kartu* JKN/BPJS/KIS, untuk *PASIEN UMUM* memasukan *NIK/KTP*. \n7. Jika *PASIEN JKN/BPJS* akan memilih jenis kunjungan melalui Rujukan Faskes 1, Kontrol, atau Rujukan Antar RS. Kemudian pilih nomor rujukan/surat tersebut. \n8. Konfirmasi pendaftaran lalu berhasil didaftarkan atau batal pendaftaran. Anda akan mendapatkan gambar *QR Code* untuk discan saat checkin di Rumah Sakit.\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                return $this->send_message($request);
                break;
            case 'PERTANYAAN DAN PENGADUAN':
                $request['message'] = "Untuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*";
                return $this->send_message($request);
                break;
            case 'INFO JADWAL POLIKLINIK':
                $request['contenttext'] = "Link Jadwal Dokter Poliklinik : \nhttp://sim.rsudwaled.id/simrs/#jadwalrawatjalan \n\nSilahkan pilih poliklinik yang tersedia untuk daftar online rawat jalan pasien dibawah ini.";
                $request['titletext'] = "Info Jadwal Dokter Poliklinik 🧑🏻‍⚕🏥";
                $request['buttontext'] = 'PILIH POLIKLINIK';
                $rowpoli = null;
                $poliklinik = PoliklinikDB::where('status', 1)->get('namasubspesialis');
                foreach ($poliklinik as  $value) {
                    $rowpoli =  $rowpoli . 'JADWAL_POLIKLINIK_' . $value->namasubspesialis  . ',';
                }
                $request['rowtitle'] = $rowpoli;
                return $this->send_list($request);
            case 'DAFTAR RAWAT JALAN':
                $request['titletext'] = "1. Pilih Jenis Pasien 😷";
                $request['contenttext'] = "Silahakan pilih jenis pasien yang akan didaftarkan dibawah ini.";
                $request['buttontext'] = 'PASIEN BPJS,PASIEN UMUM';
                return $this->send_button($request);
                break;
            case 'PASIEN BPJS':
                $request['message'] = "*2. Ketik Format Pasien BPJS*\nUntuk pasien JKN/BPJS silahkan ketik nomor kartu 13 digit yang tertera pada kartu bpjs anda dengan format seperti berikut : \n_*Nomor Kartu*_#BPJS\n(Contoh)\n0000067XX23XX#BPJS";
                $this->send_message($request);
                $request['message'] = "0000067XX23XX#BPJS";
                return $this->send_message($request);
                break;
            default:
                if (substr($pesan, 13) ==  '#BPJS') {
                    $request['nomorKartu'] = explode('#', $pesan)[0];
                    $request['tanggal'] = now()->format('Y-m-d');
                    $vclaim = new VclaimController();
                    $response = $vclaim->peserta_nomorkartu($request);
                    if ($response->status() == 200) {
                        $peserta = $response->getData()->response->peserta;
                        $request['contenttext'] = "Nomor kartu berhasil ditemukan dengan data sebagai berikut : \n*Nama* : " . $peserta->nama . "\n*NIK* : " . $peserta->nik . "\n*No Kartu* : " . $peserta->noKartu . "\n*No RM* : " . $peserta->mr->noMR . "\n\n*Status* : " . $peserta->statusPeserta->keterangan . "\n*FKTP* : " . $peserta->provUmum->nmProvider . "\n*Jenis Peserta* : " . $peserta->jenisPeserta->keterangan . "\n*Hak Kelas* : " . $peserta->hakKelas->keterangan . "\n\nSilahkan pilih jenis kunjungan dibawah ini.";
                        $request['titletext'] = "3. Pilih Jenis Kunjungan";
                        $request['buttontext'] = 'PILIH JENIS KUNJUNGAN';
                        $request['rowtitle'] = "RUJUKAN FKTP,SURAT KONTROL,RUJUKAN ANTAR RS";
                        $request['rowdescription'] = "@FKTP#" . $request->nomorKartu . ",@KONTROL#" . $request->nomorKartu . ",@ANTARRS#" . $request->nomorKartu;
                        return $this->send_list($request);
                    } else {
                        $request['message'] = "*2. Ketik Format Pasien BPJS*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                }
                // DAFTAR PAKE RUJUKAN FKTP
                else if (str_contains($pesan, "@FKTP#")) {
                    $request['nomorKartu'] = explode("#", explode('@', $pesan)[1])[1];
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_peserta($request);
                    if ($response->status() == 200) {
                        $rujukans = $response->getData()->response->rujukan;
                        $rowrujukan = null;
                        $descrujukan = null;
                        foreach ($rujukans as $value) {
                            if (Carbon::parse($value->tglKunjungan)->addMonth(3) > Carbon::now()) {
                                $rowrujukan =  $rowrujukan . "POLI " . $value->poliRujukan->nama  . ',';
                                $descrujukan =  $descrujukan . '@RFKTP#' . $value->noKunjungan . ',';
                            }
                        }
                        if ($rowrujukan == null) {
                            $request['message'] = "*4. Pilih Rujukan FKTP*\nMohon maaf semua rujukan anda sudah lebih adri 3 bulan lalu. Silahkan untuk mendapatkan surat rujukan ke faskes 1.";
                            return $this->send_message($request);
                        }
                        $request['contenttext'] = "Silahkan pilih nomor rujukan yang akan digunakan untuk mendaftar.";
                        $request['titletext'] = "4. Pilih Rujukan FKTP";
                        $request['buttontext'] = 'PILIH RUJUKAN';
                        $request['rowtitle'] = $rowrujukan;
                        $request['rowdescription'] = $descrujukan;
                        return $this->send_list($request);
                    } else {
                        $request['message'] = "*3. Pilih Rujukan FKTP Aktif*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@RFKTP#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['jenisRujukan'] = 1;
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_jumlah_sep($request);
                    if ($response->status() == 200) {
                        $jumlah_sep = $response->getData()->response->jumlahSEP;
                        if ($jumlah_sep == 0) {
                            $response = $vclaim->rujukan_nomor($request);
                            if ($response->status() == 200) {
                                $rujukan = $response->getData()->response->rujukan;
                                $peserta = $rujukan->peserta;
                                $poli = $rujukan->poliRujukan;
                                $diagnosa = $rujukan->diagnosa;
                                $rowtanggal = now()->translatedFormat('l') . ' ' . now()->translatedFormat('d M Y');
                                $rowdesc = "@TGLRFKTP#" . $rujukan->noKunjungan . "#" . now()->translatedFormat('Y-m-d') . "#" . $poli->kode;
                                for ($i = 0; $i < 6; $i++) {
                                    $rowtanggal = $rowtanggal . ',' .   now()->addDays($i + 1)->translatedFormat('l') . ' ' . now()->addDays($i + 1)->translatedFormat('d M Y');
                                    $rowdesc = $rowdesc . ',' .  "@TGLRFKTP#" . $rujukan->noKunjungan . "#" . now()->addDays($i + 1)->translatedFormat('Y-m-d') . "#" . $poli->kode;
                                }
                                $request['contenttext'] = "Informasi rujukan pasien :\n*No Rujukan* : " . $rujukan->noKunjungan . "\n*Tgl Rujukan* : " . $rujukan->tglKunjungan . "\n*Asal Rujukan* : " . $rujukan->provPerujuk->nama . "\n*Pasien* : " . $peserta->nama . "\n*No RM* : " . $peserta->mr->noMR . "\n*Status* : " . $peserta->statusPeserta->keterangan . "\n\n*Poliklinik* : " . $poli->nama  . "\n*Diagnosa* : " . $diagnosa->nama . "\n*Keluhan* : " . $rujukan->keluhan    . "\n\nSilahkan pilih tanggal daftar menggunakan rujukan dibawah ini.";
                                $request['titletext'] = "4. Pilih Tanggal Kunjungan 🗓";
                                $request['buttontext'] = 'PILIH TANGGAL';
                                $request['rowtitle'] = $rowtanggal;
                                $request['rowdescription'] = $rowdesc;
                                return $this->send_list($request);
                            } else {
                                $request['message'] = "*4. Konfirmasi Rujukan FKTP*\nMohon maaf " . $response->getData()->metadata->message;
                                return $this->send_message($request);
                            }
                        } else {
                            $request['message'] = "Mohon maaf rujukan yang anda pilih telah digunakan. Anda harus mendaftar menggunakan *SURAT KONTROL* yang dibuat saat berobat sebelumnya.";
                            return $this->send_message($request);
                        }
                    } else {
                        $request['message'] = "*4. Konfirmasi Rujukan FKTP*\nMohon maaf jumlah SEP rujukan " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@TGLRFKTP#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['tanggal'] = explode("#", explode('@', $pesan)[1])[2];
                    $request['kodePoli'] = explode("#", explode('@', $pesan)[1])[3];
                    $hari = Carbon::parse($request->tanggal)->dayOfWeek;
                    $jadwals = JadwalDokterAntrian::where('hari', $hari)->where('kodesubspesialis', $request->kodePoli)->get();
                    if ($jadwals->count() == 0) {
                        $request['message'] = "*5. Daftar Kontrol*\nMohon maaf tidak ada jadwal dokter poliklinik pada hari tersebut.";
                        return $this->send_message($request);
                    }
                    $rowjadwal = null;
                    $rowdesc = null;
                    foreach ($jadwals as $value) {
                        $rowjadwal = $rowjadwal . str_replace(",", ".", $value->namadokter)  .  " " . $value->namahari .  " " . $value->jadwal . ",";
                        $rowdesc = $rowdesc . "@JFKTP#" .  $request->nomorRujukan . "#" .  $request->tanggal . "#" .  $value->id . ",";
                    }
                    $request['contenttext'] = "Silahkan pilih jadwal dokter dibawah ini.";
                    $request['titletext'] = "4. Pilih Jadwal Dokter 🗓";
                    $request['buttontext'] = 'PILIH JADWAL DOKTER';
                    $request['rowtitle'] = $rowjadwal;
                    $request['rowdescription'] = $rowdesc;
                    return $this->send_list($request);
                } else if (str_contains($pesan, "@JFKTP#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['tanggal'] = explode("#", explode('@', $pesan)[1])[2];
                    $request['idJadwal'] = explode("#", explode('@', $pesan)[1])[3];
                    $request['jenisRujukan'] = 1;
                    $hari = Carbon::parse($request->tanggal)->dayOfWeek;
                    $jadwal = JadwalDokterAntrian::find($request->idJadwal);
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_nomor($request);
                    if ($response->status() == 200) {
                        $rujukan =  $response->getData()->response->rujukan;
                        $peserta = $rujukan->peserta;
                        $diagnosa = $rujukan->diagnosa;
                        $poli = $rujukan->poliRujukan;
                    } else {
                        $request['message'] = "*5. Konfirmasi Daftar Menggunakan Rujukan*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                    $request['contenttext'] = "Sebelum didaftarkan silahkan konfirmasi data pasien yang akan didaftarkan dibawah ini : \n*No Rujukan* : " . $rujukan->noKunjungan . "\n*Tgl Rujukan* : " . $rujukan->tglKunjungan . "\n*Faskes 1* : " . $rujukan->provPerujuk->nama . "\n*Pasien* : " . $peserta->nama . "\n*No RM* : " . $peserta->mr->noMR . "\n*NIK* : " . $peserta->nik . "\n*No BPJS* : " . $peserta->noKartu . "\n*Status* : " . $peserta->statusPeserta->keterangan . "\n*Diagnosa* : " . $diagnosa->nama . "\n*Keluhan* : " . $rujukan->keluhan . "\n\nAkan didaftarkan rawat jalan pada jadwal poliklinik berikut : \n*Poliklinik* : " . $jadwal->namasubspesialis . "\n*Dokter* : " . $jadwal->namadokter . "\n*Waktu* : " . $jadwal->namahari . " " . $jadwal->jadwal . "\n*Tanggal* : " . $request->tanggal . "\n\nSilahkan pilih jawaban konfirmasi dimenu dibawah ini.";
                    $request['titletext'] = "5. Konfirmasi Daftar Menggunakan Rujukan";
                    $request['buttontext'] = 'PILIH MENU';
                    $request['rowtitle'] = 'DAFTAR TANGGAL ' . $request->tanggal . ' ,DAFTAR TANGGAL LAIN';
                    $request['rowdescription'] = '@DAFTARFKTP#' . $request->nomorRujukan . '#' . $request->tanggal . '#' . $request->idJadwal . ',@RFKTP#' . $request->nomorRujukan;
                    return $this->send_list($request);
                } else if (str_contains($pesan, "@DAFTARFKTP#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['tanggal'] = explode("#", explode('@', $pesan)[1])[2];
                    $request['idJadwal'] = explode("#", explode('@', $pesan)[1])[3];
                    $hari = Carbon::parse($request->tanggal)->dayOfWeek;
                    $jadwaldokter = JadwalDokterAntrian::find($request->idJadwal);
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_nomor($request);
                    if ($response->status() == 200) {
                        $rujukan =  $response->getData()->response->rujukan;
                        $peserta = $rujukan->peserta;
                        $diagnosa = $rujukan->diagnosa;
                        $poli = $rujukan->poliRujukan;
                        $pasien = PasienDB::where('no_Bpjs', $peserta->noKartu)->first();
                        if ($pasien == null) {
                            $request['message'] = "Maaf anda tidak bisa daftar dikarenakan data Kartu BPJS pasien belum terdaftar di RSUD Waled. Silahkan lakukan pendaftaran offline. Terima kasih";
                            return $this->send_message($request);
                        }
                        $request['nomorkartu'] = $peserta->noKartu;
                        $request['nik'] =  $pasien->nik_bpjs;
                        $request['nohp'] =  "0" . substr(str_replace("@c.us", "", $request->number), 2);
                        $request['kodepoli'] =  $poli->kode;
                        $request['norm'] =  $pasien->no_rm;
                        $request['tanggalperiksa'] =  $request->tanggal;
                        $request['kodedokter'] =  $jadwaldokter->kodedokter;
                        $request['jampraktek'] =  $jadwaldokter->jadwal;
                        $request['jeniskunjungan'] = 1;
                        $request['method'] = "Whatsapp";
                        $request['nomorreferensi'] = $rujukan->noKunjungan;
                        $antrian = new AntrianController();
                        $response = $antrian->ambil_antrian($request);
                        if ($response->status() === 200) {
                            return $response->getData();
                        } else {
                            $request['message'] = "Maaf anda tidak bisa daftar : " .  $response->getData()->metadata->message . " Atau daftar melalui offline.";
                            return $this->send_message($request);
                        }
                    } else {
                        $request['message'] = "*5. Konfirmasi Daftar Menggunakan Rujukan*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                }
                // DAFTAR PAKE SURAT KONTROL
                else if (str_contains($pesan, "@KONTROL#")) {
                    $request['nomorKartu'] = explode("#", explode('@', $pesan)[1])[1];

                    $rowbulan = 'Kontrol ' . now()->translatedFormat('F');
                    $rowdesc = '@BULANKONTROL#' . now()->translatedFormat('m') . '#' . $request->nomorKartu;
                    for ($i = 1; $i < 3; $i++) {
                        $rowbulan = $rowbulan . ', Kontrol ' . now()->addMonth($i)->translatedFormat('F');
                        $rowdesc = $rowdesc . ', @BULANKONTROL#' . now()->addMonth($i)->translatedFormat('m') . '#' . $request->nomorKartu;
                    }
                    $request['contenttext'] = "Silahkan bulan kapan anda akan periksa kontrol ?";
                    $request['titletext'] = "4. Pilih Bulan Kontrol";
                    $request['buttontext'] = 'PILIH BULAN KONTROL';
                    $request['rowtitle'] = $rowbulan;
                    $request['rowdescription'] = $rowdesc;
                    return $this->send_list($request);
                } else if (str_contains($pesan, "@BULANKONTROL#")) {
                    $request['nomorKartu'] = explode("#", explode('@', $pesan)[1])[2];
                    $request['bulan'] =  explode("#", explode('@', $pesan)[1])[1];
                    $request['tahun'] = now()->format('Y');
                    $request['formatFilter'] = 2;
                    $vclaim = new VclaimController();
                    $response = $vclaim->suratkontrol_peserta($request);
                    if ($response->status() == 200) {
                        $rowsuratkontrol = null;
                        $descsurarujukan = null;
                        $suratkontrol = $response->getData()->response->list;
                        foreach ($suratkontrol as $value) {
                            if ($value->terbitSEP == "Belum") {
                                if ($value->jnsKontrol == 2) {
                                    $rowsuratkontrol =  $rowsuratkontrol .  "KONTROL " . $value->namaPoliTujuan  . " " . $value->namaDokter . ',';
                                    $descsurarujukan =  $descsurarujukan . '@SURATKONTROL#' . $value->noSuratKontrol .  ',';
                                }
                            }
                        }
                        if ($rowsuratkontrol == null) {
                            $request['message'] = "*4. Pilih Aktif Kontrol Aktif*\nMohon maaf semua surat kontrol anda telah digunakan. \nUntuk penyesuaian yang akan datang silahkan minta daftarkan surat kontrol yang terintegrasi dengan sistem setelah pelayanan di Poliklinik. \nTerima kasih. ";
                            return $this->send_message($request);
                        };
                        $request['contenttext'] = "Silahkan pilih nomor surat kontrol yang akan digunakan untuk mendaftar.";
                        $request['titletext'] = "4. Pilih Aktif Kontrol Aktif";
                        $request['buttontext'] = 'PILIH SURAT KONTROL';
                        $request['rowtitle'] = $rowsuratkontrol;
                        $request['rowdescription'] = $descsurarujukan;
                        return $this->send_list($request);
                    } else {
                        $request['message'] = "*3. Pilih Surat Kontrol Aktif*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@SURATKONTROL#")) {
                    $request['noSuratKontrol'] = explode("#", explode('@', $pesan)[1])[1];
                    $vclaim = new VclaimController();
                    $response = $vclaim->suratkontrol_nomor($request);
                    if ($response->status() == 200) {
                        $suratkontrol = $response->getData()->response;
                        $sep = $suratkontrol->sep;
                        $peserta = $sep->peserta;
                        $request['contenttext'] = "Silahkan konfirmasi pendaftaran data pasien menggunakan surat kontrol berikut \n\n*No Surat* : " . $suratkontrol->noSuratKontrol . "\n*Tgl Surat* : " . $suratkontrol->tglTerbit . "\n*Pasien* : " . $peserta->nama  . "\n*Diagnosa* : " . $sep->diagnosa . "\n*Poli Tujuan* : " . $suratkontrol->namaPoliTujuan  . "\n*Dokter* : " . $suratkontrol->namaDokter . "\n*Tgl Kontrol* : " . $suratkontrol->tglRencanaKontrol . "\n\nApakah anda akan mendaftar pada tanggal tersebut atau ingin merubah jadwal kontrol ? Silahkan tentukan pilihannya dibawah ini.";
                        $request['titletext'] = "4. Konfirmasi Daftar Kontrol";
                        $request['buttontext'] = 'PILIHAN KONTROL';
                        $request['rowtitle'] = 'DAFTAR KONTROL TGL ' . $suratkontrol->tglRencanaKontrol . ',UBAH TANGGAL KONTROL';
                        $request['rowdescription'] = '@DAFTARKONTROL#' . $suratkontrol->noSuratKontrol . ',@UBAHKONTROL#' . $suratkontrol->noSuratKontrol;
                        // $request['rowtitle'] = 'DAFTAR KONTROL TGL ' . $suratkontrol->tglRencanaKontrol;
                        // $request['rowdescription'] = '@DAFTARKONTROL#' . $suratkontrol->noSuratKontrol;
                        return $this->send_list($request);
                    } else {
                        $request['message'] = "*4. Konfirmasi Daftar Kontrol*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@DAFTARKONTROL#")) {
                    $request['noSuratKontrol'] = explode("#", explode('@', $pesan)[1])[1];
                    $vclaim = new VclaimController();
                    $response = $vclaim->suratkontrol_nomor($request);
                    if ($response->status() == 200) {
                        $suratkontrol = $response->getData()->response;
                        $sep = $suratkontrol->sep;
                        $peserta = $sep->peserta;
                        $pasien = PasienDB::where('no_Bpjs', $peserta->noKartu)->first();
                        if ($pasien == null) {
                            $request['message'] = "Maaf anda tidak bisa daftar dikarenakan data Kartu BPJS pasien belum terdaftar di RSUD Waled. Silahkan lakukan pendaftaran offline. Terima kasih";
                            return $this->send_message($request);
                        }
                        $hari = Carbon::parse($suratkontrol->tglRencanaKontrol)->dayOfWeek;
                        $jadwaldokter = JadwalDokterAntrian::where('kodeDokter',  $suratkontrol->kodeDokter)
                            ->where('hari', $hari)->first();
                        if (empty($jadwaldokter) || $jadwaldokter->libur) {
                            $request['message'] = "*5. Daftar Kontrol*\nMohon maaf pada tanggal " . $suratkontrol->tglRencanaKontrol . " jadwal dokter dipoliklinik tersebut diliburkan / ditutup.";
                            return $this->send_message($request);
                        }
                        $request['nomorkartu'] = $peserta->noKartu;
                        $request['nik'] =  $pasien->nik_bpjs;
                        $request['nohp'] =  "0" . substr(str_replace("@c.us", "", $request->number), 2);
                        $request['kodepoli'] =  $suratkontrol->poliTujuan;
                        $request['norm'] =  $pasien->no_rm;
                        $request['tanggalperiksa'] =  $suratkontrol->tglRencanaKontrol;
                        $request['kodedokter'] =  $suratkontrol->kodeDokter;
                        $request['jampraktek'] =  $jadwaldokter->jadwal;
                        $request['jeniskunjungan'] = 3;
                        $request['method'] = "Whatsapp";
                        $request['nomorreferensi'] = $suratkontrol->noSuratKontrol;
                        $antrian = new AntrianController();
                        $response = $antrian->ambil_antrian($request);
                        if ($response->status() === 200) {
                            return $response->getData();
                        } else {
                            $request['message'] = "Maaf anda tidak bisa daftar : " .  $response->getData()->metadata->message . " Atau daftar melalui offline.";
                            return $this->send_message($request);
                        }
                    } else {
                        $request['message'] = "*5. Daftar Kontrol*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@UBAHKONTROL#")) {
                    $request['noSuratKontrol'] = explode("#", explode('@', $pesan)[1])[1];
                    $vclaim = new VclaimController();
                    $response = $vclaim->suratkontrol_nomor($request);
                    if ($response->status() == 200) {
                        $suratkontrol = $response->getData()->response;
                        $tanggal = Carbon::parse($suratkontrol->tglRencanaKontrol);
                        $rowtanggal = $tanggal->addDay(1)->translatedFormat('l Y-m-d');
                        $rowdesc = "@TGLKONTROL#" . $tanggal->translatedFormat('Y-m-d') . '#' . $request->noSuratKontrol;
                        for ($i = 0; $i < 6; $i++) {
                            $rowtanggal = $rowtanggal . ',' . $tanggal->addDay(1)->translatedFormat('l Y-m-d');
                            $rowdesc = $rowdesc . ',' . "@TGLKONTROL#" . $tanggal->translatedFormat('Y-m-d') . '#' . $request->noSuratKontrol;
                        }
                        $request['contenttext'] = "*Catatan*\n1. Mengubah tanggal kontrol hanya untuk pasien yang terlewat tanggal kontrol.\n2. Kuota pasien penuh pada tanggal kontrol yang telah ditentukan.\n3. Tanggal yang tersedia hanya 7 hari dari tanggal yang telah ditentukan dokter.\n4. Diluar keperluan tersebut silahkan konsultasikan dengan Tenaga Medis. \n\nSilahkan pilih tanggal untuk merubah surat kontrol dibawah ini.";
                        $request['titletext'] = "UBAH TANGGAL SURAT KONTROL";
                        $request['buttontext'] = 'PILIH TANGGAL';
                        $request['rowtitle'] = $rowtanggal;
                        $request['rowdescription'] = $rowdesc;
                        return $this->send_list($request);
                    } else {
                        $request['message'] = "*5. Daftar Kontrol*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@TGLKONTROL#")) {
                    $request['noSuratKontrol'] = explode("#", explode('@', $pesan)[1])[2];
                    $vclaim = new VclaimController();
                    $response = $vclaim->suratkontrol_nomor($request);
                    try {
                        $suratkontrol = $response->getData()->response;
                        $request['noSep'] = $suratkontrol->sep->noSep;
                        $request['kodeDokter'] = $suratkontrol->kodeDokter;
                        $request['poliKontrol'] = $suratkontrol->poliTujuan;
                        $request['tglRencanaKontrol'] = explode("#", explode('@', $pesan)[1])[1];
                        $request['user'] = $request->number;
                    } catch (\Throwable $th) {
                        $request['message'] = "*UBAH SURAT KONTROL*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                    $response = $vclaim->suratkontrol_update($request);
                    if ($response->status() == 200) {
                        $suratkontrol = $response->getData()->response;
                        $noSuratKontrol = $suratkontrol->noSuratKontrol;
                        $tglRencanaKontrol = $suratkontrol->tglRencanaKontrol;
                        $namaDokter = $suratkontrol->namaDokter;
                        $noKartu = $suratkontrol->noKartu;
                        $nama = $suratkontrol->nama;
                        $namaDiagnosa = $suratkontrol->namaDiagnosa;
                        $request['contenttext'] = "Anda berhasil ubah tanggal kontrol dengan data dibawah ini. \n\n*No Surat :* " . $noSuratKontrol . "\n*Tgl Kontrol :* " . $tglRencanaKontrol . "\n*Dokter :* " . $namaDokter . "\n*No BPJS :* " . $noKartu . "\n*Pasien :* " . $nama . "\n*Diagnosa :* " . $namaDiagnosa . " \n\nSilahkan pilih daftar jika ingin mendaftarkan untuk kunjungan berikutnya.";
                        $request['titletext'] = "BERHASIL UBAH TANGGAL SURAT KONTROL";
                        $request['buttontext'] = 'PILIHAN KONTROL';
                        $request['rowtitle'] = 'DAFTAR KONTROL TGL ' . $suratkontrol->tglRencanaKontrol . ',UBAH TANGGAL KONTROL';
                        $request['rowdescription'] = '@DAFTARKONTROL#' . $suratkontrol->noSuratKontrol . ',@UBAHKONTROL#' . $suratkontrol->noSuratKontrol;
                        return $this->send_list($request);
                    } else {
                        $request['message'] = "*UBAH SURAT KONTROL*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                }
                // DAFTAR PAKE RUJUKAN FKTP
                else if (str_contains($pesan, "@ANTARRS#")) {
                    $request['nomorKartu'] = explode("#", explode('@', $pesan)[1])[1];
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_rs_peserta($request);
                    if ($response->status() == 200) {
                        $rujukans = $response->getData()->response->rujukan;
                        $rowrujukan = null;
                        $descrujukan = null;
                        foreach ($rujukans as $value) {
                            if (Carbon::parse($value->tglKunjungan)->addMonth(3) > Carbon::now()) {
                                $rowrujukan =  $rowrujukan . "POLI " . $value->poliRujukan->nama  . ',';
                                $descrujukan =  $descrujukan . '@RANTARRS#' . $value->noKunjungan . ',';
                            }
                        }
                        if ($rowrujukan == null) {
                            $request['message'] = "*4. Pilih Rujukan Antar RS*\nMohon maaf semua rujukan anda sudah lebih adri 3 bulan lalu. Silahkan untuk mendapatkan surat rujukan ke faskes 1.";
                            return $this->send_message($request);
                        }
                        $request['contenttext'] = "Silahkan pilih nomor rujukan yang akan digunakan untuk mendaftar.";
                        $request['titletext'] = "4. Pilih Rujukan Antar RS";
                        $request['buttontext'] = 'PILIH RUJUKAN';
                        $request['rowtitle'] = $rowrujukan;
                        $request['rowdescription'] = $descrujukan;
                        return $this->send_list($request);
                    } else {
                        $request['message'] = "*3. Pilih Rujukan Antar RS*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@RANTARRS#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['jenisRujukan'] = 2;
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_jumlah_sep($request);
                    if ($response->status() == 200) {
                        $jumlah_sep = $response->getData()->response->jumlahSEP;
                        if ($jumlah_sep == 0) {
                            $response = $vclaim->rujukan_rs_nomor($request);
                            if ($response->status() == 200) {
                                $rujukan = $response->getData()->response->rujukan;
                                $peserta = $rujukan->peserta;
                                $poli = $rujukan->poliRujukan;
                                $diagnosa = $rujukan->diagnosa;
                                $rowtanggal = now()->translatedFormat('l') . ' ' . now()->translatedFormat('d M Y');
                                $rowdesc = "@TGLANTARRS#" . $rujukan->noKunjungan . "#" . now()->translatedFormat('Y-m-d') . "#" . $poli->kode;
                                for ($i = 0; $i < 6; $i++) {
                                    $rowtanggal = $rowtanggal . ',' .   now()->addDays($i + 1)->translatedFormat('l') . ' ' . now()->addDays($i + 1)->translatedFormat('d M Y');
                                    $rowdesc = $rowdesc . ',' .  "@TGLANTARRS#" . $rujukan->noKunjungan . "#" . now()->addDays($i + 1)->translatedFormat('Y-m-d') . "#" . $poli->kode;
                                }
                                $request['contenttext'] = "Informasi rujukan antar RS pasien :\n*No Rujukan* : " . $rujukan->noKunjungan . "\n*Tgl Rujukan* : " . $rujukan->tglKunjungan . "\n*Asal Rujukan* : " . $rujukan->provPerujuk->nama . "\n*Pasien* : " . $peserta->nama . "\n*No RM* : " . $peserta->mr->noMR . "\n*Status* : " . $peserta->statusPeserta->keterangan . "\n\n*Poliklinik* : " . $poli->nama  . "\n*Diagnosa* : " . $diagnosa->nama . "\n*Keluhan* : " . $rujukan->keluhan    . "\n\nSilahkan pilih tanggal daftar menggunakan rujukan dibawah ini.";
                                $request['titletext'] = "4. Pilih Tanggal Kunjungan 🗓";
                                $request['buttontext'] = 'PILIH TANGGAL';
                                $request['rowtitle'] = $rowtanggal;
                                $request['rowdescription'] = $rowdesc;
                                return $this->send_list($request);
                            } else {
                                $request['message'] = "*4. Konfirmasi Rujukan FKTP*\nMohon maaf " . $response->getData()->metadata->message;
                                return $this->send_message($request);
                            }
                        } else {
                            $request['message'] = "Mohon maaf rujukan yang anda pilih telah digunakan. Anda harus mendaftar menggunakan *SURAT KONTROL* yang dibuat saat berobat sebelumnya.";
                            return $this->send_message($request);
                        }
                    } else {
                        $request['message'] = "*4. Konfirmasi Rujukan FKTP*\nMohon maaf jumlah SEP rujukan " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                } else if (str_contains($pesan, "@TGLANTARRS#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['tanggal'] = explode("#", explode('@', $pesan)[1])[2];
                    $request['kodePoli'] = explode("#", explode('@', $pesan)[1])[3];
                    $hari = Carbon::parse($request->tanggal)->dayOfWeek;
                    $jadwals = JadwalDokterAntrian::where('hari', $hari)->where('kodesubspesialis', $request->kodePoli)->get();
                    if ($jadwals->count() == 0) {
                        $request['message'] = "*5. Daftar Kontrol*\nMohon maaf tidak ada jadwal dokter poliklinik pada hari tersebut.";
                        return $this->send_message($request);
                    }
                    $rowjadwal = null;
                    $rowdesc = null;
                    foreach ($jadwals as $value) {
                        $rowjadwal = $rowjadwal . str_replace(",", ".", $value->namadokter)  .  " " . $value->namahari .  " " . $value->jadwal . ",";
                        $rowdesc = $rowdesc . "@JANTARRS#" .  $request->nomorRujukan . "#" .  $request->tanggal . "#" .  $value->id . ",";
                    }
                    $request['contenttext'] = "Silahkan pilih jadwal dokter dibawah ini.";
                    $request['titletext'] = "4. Pilih Jadwal Dokter 🗓";
                    $request['buttontext'] = 'PILIH JADWAL DOKTER';
                    $request['rowtitle'] = $rowjadwal;
                    $request['rowdescription'] = $rowdesc;
                    return $this->send_list($request);
                } else if (str_contains($pesan, "@JANTARRS#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['tanggal'] = explode("#", explode('@', $pesan)[1])[2];
                    $request['idJadwal'] = explode("#", explode('@', $pesan)[1])[3];
                    $request['jenisRujukan'] = 2;
                    $hari = Carbon::parse($request->tanggal)->dayOfWeek;
                    $jadwal = JadwalDokterAntrian::find($request->idJadwal);
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_rs_nomor($request);
                    if ($response->status() == 200) {
                        $rujukan =  $response->getData()->response->rujukan;
                        $peserta = $rujukan->peserta;
                        $diagnosa = $rujukan->diagnosa;
                        $poli = $rujukan->poliRujukan;
                    } else {
                        $request['message'] = "*5. Konfirmasi Daftar Menggunakan Rujukan*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                    $request['contenttext'] = "Sebelum didaftarkan silahkan konfirmasi data pasien yang akan didaftarkan dibawah ini : \n*No Rujukan* : " . $rujukan->noKunjungan . "\n*Tgl Rujukan* : " . $rujukan->tglKunjungan . "\n*Faskes 1* : " . $rujukan->provPerujuk->nama . "\n*Pasien* : " . $peserta->nama . "\n*No RM* : " . $peserta->mr->noMR . "\n*NIK* : " . $peserta->nik . "\n*No BPJS* : " . $peserta->noKartu . "\n*Status* : " . $peserta->statusPeserta->keterangan . "\n*Diagnosa* : " . $diagnosa->nama . "\n*Keluhan* : " . $rujukan->keluhan . "\n\nAkan didaftarkan rawat jalan pada jadwal poliklinik berikut : \n*Poliklinik* : " . $jadwal->namasubspesialis . "\n*Dokter* : " . $jadwal->namadokter . "\n*Waktu* : " . $jadwal->namahari . " " . $jadwal->jadwal . "\n*Tanggal* : " . $request->tanggal . "\n\nSilahkan pilih jawaban konfirmasi dimenu dibawah ini.";
                    $request['titletext'] = "5. Konfirmasi Daftar Menggunakan Rujukan";
                    $request['buttontext'] = 'PILIH MENU';
                    $request['rowtitle'] = 'DAFTAR TANGGAL ' . $request->tanggal . ' ,DAFTAR TANGGAL LAIN';
                    $request['rowdescription'] = '@DAFTARANTARRS#' . $request->nomorRujukan . '#' . $request->tanggal . '#' . $request->idJadwal . ',@RANTARRS#' . $request->nomorRujukan;
                    return $this->send_list($request);
                } else if (str_contains($pesan, "@DAFTARANTARRS#")) {
                    $request['nomorRujukan'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['tanggal'] = explode("#", explode('@', $pesan)[1])[2];
                    $request['idJadwal'] = explode("#", explode('@', $pesan)[1])[3];
                    $hari = Carbon::parse($request->tanggal)->dayOfWeek;
                    $jadwaldokter = JadwalDokterAntrian::find($request->idJadwal);
                    $vclaim = new VclaimController();
                    $response = $vclaim->rujukan_rs_nomor($request);
                    if ($response->status() == 200) {
                        $rujukan =  $response->getData()->response->rujukan;
                        $peserta = $rujukan->peserta;
                        $diagnosa = $rujukan->diagnosa;
                        $poli = $rujukan->poliRujukan;
                        $pasien = PasienDB::where('no_Bpjs', $peserta->noKartu)->first();
                        if ($pasien == null) {
                            $request['message'] = "Maaf anda tidak bisa daftar dikarenakan data Kartu BPJS pasien belum terdaftar di RSUD Waled. Silahkan lakukan pendaftaran offline. Terima kasih";
                            return $this->send_message($request);
                        }
                        $request['nomorkartu'] = $peserta->noKartu;
                        $request['nik'] =  $pasien->nik_bpjs;
                        $request['nohp'] =  "0" . substr(str_replace("@c.us", "", $request->number), 2);
                        $request['kodepoli'] =  $poli->kode;
                        $request['norm'] =  $pasien->no_rm;
                        $request['tanggalperiksa'] =  $request->tanggal;
                        $request['kodedokter'] =  $jadwaldokter->kodedokter;
                        $request['jampraktek'] =  $jadwaldokter->jadwal;
                        $request['jeniskunjungan'] = 4;
                        $request['method'] = "Whatsapp";
                        $request['nomorreferensi'] = $rujukan->noKunjungan;
                        $antrian = new AntrianController();
                        $response = $antrian->ambil_antrian($request);
                        if ($response->status() === 200) {
                            return $response->getData();
                        } else {
                            $request['message'] = "Maaf anda tidak bisa daftar : " .  $response->getData()->metadata->message . " Atau daftar melalui offline.";
                            return $this->send_message($request);
                        }
                    } else {
                        $request['message'] = "*5. Konfirmasi Daftar Menggunakan Rujukan*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                }
                // BATAL ANTRIAN
                else if (str_contains($pesan, "@BATALANTRI#")) {
                    $request['kodebooking'] = explode("#", explode('@', $pesan)[1])[1];
                    $request['keterangan'] = "Dibatalkan melalui whatsapp";
                    $antrian = Antrian::firstWhere('kodebooking', $request->kodebooking);
                    $api = new AntrianController();
                    $response = $api->batal_antrian($request);
                    if ($response->status() == 200) {
                        $request['message'] = "*Keterangan Batal Antrian*\nAntrian dengan kodebooking " . $request->kodebooking . " telah dibatalkan. Terima kasih.";
                        return $this->send_message($request);
                    } else {
                        $request['message'] = "*Keterangan Batal Antrian*\nMohon maaf " . $response->getData()->metadata->message;
                        return $this->send_message($request);
                    }
                }
                // INFO JADWAL POLI
                else if (str_contains($pesan, 'JADWAL_POLIKLINIK_')) {
                    $poli = explode('_', $pesan)[2];
                    $rowjadwaldokter = null;
                    $jadwaldokters = JadwalDokterAntrian::where('namasubspesialis', $poli)->orderBy('hari')->get();
                    foreach ($jadwaldokters as  $value) {
                        $rowjadwaldokter = $rowjadwaldokter . $this->hari[$value->hari] . '  : ' . $value->namadokter . ' ' . $value->jadwal . "\n";
                    }
                    $request['contenttext'] = "Jadwal dokter poliklinik " . $poli . " sebagai berikut : \n\n" . $rowjadwaldokter;
                    $request['titletext'] = "3. Pilih Jadwal Dokter " . $poli;
                    $request['buttontext'] = 'INFO JADWAL POLIKLINIK';
                    return $this->send_button($request);
                }
                // default
                else {
                    $request['contenttext'] = "Selamat datang di layanan kami. Pesan ini dibalas oleh sistem pelayanan otomatis.🙏\n\nSilahkan klik *MENU UTAMA* yang dapat diproses dibawah ini ⬇";
                    $request['titletext'] = "Layanan Whatsapp RSUD Waled 📱🏥";
                    $request['buttontext'] = 'MENU UTAMA';
                    $request['rowtitle'] = 'INFO CARA PENDAFTARAN,DAFTAR RAWAT JALAN,INFO JADWAL POLIKLINIK,PERTANYAAN DAN PENGADUAN';
                    $this->send_list($request);

                    $request['message'] = "Di RSUD Waled kini dapat juga daftar di Mobile JKN. \nAyo Download aplikasi Mobile JKN di Playstore \n\nLink Download : https://play.google.com/store/apps/details?id=app.bpjs.mobile \n\nYoutube BPJS Kesehatan : https://youtu.be/nA6X209v2uk";
                    return $this->send_message($request);
                    break;
                }
        }
    }
}
