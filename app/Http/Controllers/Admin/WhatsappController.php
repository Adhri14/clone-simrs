<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class WhatsappController extends Controller
{
    public function whatsapp(Request $request)
    {
        if (empty($request->number)) {
            $request['number'] = "089529909036";
            $request['message'] = "Send Test Message";
        } else {
            $this->send_message($request);
            Alert::success('OK', 'Test Message Success');
        }
        return view('admin.whatsapp', compact([
            'request',
        ]));
    }
    // API SIMRS
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
            case 'INFO JADWAL POLIKLINIK':
                $request['contenttext'] = "Link Jadwal Dokter Poliklinik : \nhttp://sim.rsudwaled.id/antrian/info_jadwaldokter \n\nLink Jadwal Libur Poliklinik : \nhttp://sim.rsudwaled.id/antrian/info_jadwallibur \n\nSilahkan pilih poliklinik yang tersedia untuk daftar online rawat jalan pasien dibawah ini.";
                $request['titletext'] = "Info Jadwal Dokter Poliklinik 🧑🏻‍⚕🏥";
                $request['buttontext'] = 'PILIH POLIKLINIK';
                $rowpoli = null;
                $poliklinik = PoliklinikDB::where('status', 1)->get('namasubspesialis');
                foreach ($poliklinik as  $value) {
                    $rowpoli =  $rowpoli . 'JADWAL_POLIKLINIK_' . $value->namasubspesialis  . ',';
                }
                $request['rowtitle'] = $rowpoli;
                return $this->send_list($request);
            case 'DAFTAR PASIEN RAWAT JALAN':
                // if ($request->number == "6289529909036@c.us") {
                $this->pilih_poli($pesan, $request);
                // } else {
                // $request['message'] = "Mohon maaf server BPJS sedang dalam perbaikan (201)";
                // $this->send_message($request);
                // }
                break;
            default:
                // info jadwal poli
                if (str_contains($pesan, 'JADWAL_POLIKLINIK_')) {
                    $poli = explode('_', $pesan)[2];
                    $rowjadwaldokter = null;
                    $jadwaldokters = JadwalDokter::where('namasubspesialis', $poli)->get();
                    foreach ($jadwaldokters as  $value) {
                        $rowjadwaldokter = $rowjadwaldokter . $this->hari[$value->hari] . '  : ' . $value->namadokter . ' ' . $value->jadwal . " KUOTA : " . $value->kapasitaspasien . "\n";
                    }
                    $request['contenttext'] = "Jadwal dokter poliklinik " . $poli . " sebagai berikut : \n\n" . $rowjadwaldokter;
                    $request['titletext'] = "3. Pilih Jadwal Dokter " . $poli;
                    $request['buttontext'] = 'INFO JADWAL POLIKLINIK';
                    return $this->send_button($request);
                }
                // 2. pilih poli terus tanggal
                else if (substr($pesan, 0, 11) == 'POLIKLINIK_') {
                    $poli = explode('_', $pesan)[1];
                    $now = Carbon::now();
                    $rowtanggal = 'TANGGAL_' . $now->format('Y-m-d') . "#" . $poli;
                    for ($i = 0; $i < 6; $i++) {
                        $rowtanggal = $rowtanggal . ',' .  'TANGGAL_' . $now->addDay(1)->format('Y-m-d') . "#" . $poli;
                    }
                    $request['contenttext'] = "Silahkan pilih tanggal kunjungan rawat jalan Poliklinik _" . strtoupper($poli) . "_ dibawah ini.";
                    $request['titletext'] = "2. Pilih Tanggal Kunjungan 🗓";
                    $request['buttontext'] = 'PILIH TANGGAL';
                    $request['rowtitle'] = $rowtanggal;
                    return $this->send_list($request);
                }
                // 3. tanggal poli terus pilih jadwal
                else if (str_contains($pesan, 'TANGGAL_')) {
                    $format = explode('_', $pesan)[1];
                    $tanggal = Carbon::parse(explode('#', $format)[0]);
                    $poli = explode('#', $format)[1];
                    $hari = $tanggal->dayOfWeek;
                    $rowjadwaldokter = null;
                    $jadwaldokters = JadwalDokter::where('hari', $hari)
                        ->where('namasubspesialis', $poli)->get();
                    if ($jadwaldokters->count() == 0) {
                        $request['contenttext'] = "Mohon maaf *Tidak Ada Jadwal Dokter* di Poliklinik _" . $poli . "_ pada tanggal " . $tanggal->format('Y-m-d') . " ❎🙏\n\nSilahkan pilih jadwal dokter poliklinik pada tanggal yang berbeda dibawah ini.";
                        $request['titletext'] = "2. Pilih Tanggal Lain Kunjungan 🗓";
                        $request['buttontext'] = 'PILIH TANGGAL';
                        $now = Carbon::now();
                        $rowhari = 'TANGGAL_' . $now->format('Y-m-d') . "#" . $poli;
                        for ($i = 0; $i < 6; $i++) {
                            $rowhari = $rowhari . ','  . 'TANGGAL_' . $now->addDay(1)->format('Y-m-d') .  "#" . $poli;
                        }
                        $request['rowtitle'] = $rowhari;
                        return $this->send_list($request);
                    }
                    foreach ($jadwaldokters as  $value) {
                        $rowjadwaldokter = $rowjadwaldokter . $value->jadwal . " Dr." . $value->namadokter . ' _JADWAL.ID#' . $value->id . '#' . $tanggal->format('Y-m-d') . ',';
                    }
                    $request['contenttext'] = "Silahkan pilih jadwal dokter Poliklinik _" . $poli . "_ pada tanggal " . $tanggal->format('Y-m-d') . " dibawah ini.";
                    $request['titletext'] = "3. Pilih Jadwal Dokter 🧑🏻‍⚕";
                    $request['buttontext'] = 'PILIH JADWAL DOKTER';
                    $request['rowtitle'] = $rowjadwaldokter;
                    return $this->send_list($request);
                }
                // 4. pilih dokter terus jenis pasien
                else if (str_contains($pesan, '_JADWAL.ID#')) {
                    $jadwalid = explode('#', $pesan)[1];
                    $tanggal = explode('#', $pesan)[2];
                    $jadwaldokter = JadwalDokter::find($jadwalid);
                    // jika jadwal libur
                    if ($jadwaldokter->libur == 1) {
                        $poli = $jadwaldokter->namasubspesialis;
                        $now = Carbon::now();
                        $request['notif'] = '4 cek jadwal libur poli ' . $poli . " tanggal " . $tanggal;
                        $this->send_notif($request);
                        $rowtanggal = 'TANGGAL_' . $now->format('Y-m-d') . "#" . $poli;
                        for ($i = 0; $i < 6; $i++) {
                            $rowtanggal = $rowtanggal . ',' . 'TANGGAL_' . $now->addDay(1)->format('Y-m-d') . "#" . $poli;
                        }
                        $request['contenttext'] = "Silahkan pilih tanggal lain rawat jalan poliklinik " . strtoupper($poli) . " dibawah ini.";
                        $request['titletext'] = "Jadwal Dokter Pilihan sedang Libur/Tutup";
                        $request['buttontext'] = 'PILIH TANGGAL';
                        $request['rowtitle'] = $rowtanggal;
                        return $this->send_list($request);
                    }
                    // jika jadwal jalan
                    else {
                        $request['titletext'] = "4. Pilih Jenis Pasien 😷";
                        $request['contenttext'] = "Jadwal dokter poliklinik yang anda pilih adalah sebagai berikut :\n\n*Poliklinik* : " . strtoupper($jadwaldokter->namasubspesialis) . "\n*Dokter* :  " . $jadwaldokter->namadokter . "\n*Jam Praktek* : " . $jadwaldokter->jadwal . "\n*Tanggal Periksa* :  " . $tanggal . "\n\nSilahakan pilih jenis pasien yang akan didaftarkan ini. \n\nCatatan :\nPasien JKN/BPJS : diharuskan memiliki rujukan faskes 1 yang masih aktif, atau Surat Kontol Poliklinik.\nPasien UMUM : hanya pasien umum yang telah terdaftar saja dapat melakukan daftar online. Bagi yang belum terdaftar silahkan daftar langsung ditempat";
                        $request['buttontext'] = 'PASIEN BPJS_' . $jadwalid . '#' . $tanggal . ',PASIEN UMUM_' . $jadwalid . '#' . $tanggal;
                        return $this->send_button($request);
                    }
                }
                // 5. pilih jenis pasien, masukan rujukan
                else if (substr($pesan, 0, 12) ==  'PASIEN BPJS_') {
                    $jadwalid = explode('_', $pesan)[1];
                    $request['message'] = "*5. Ketik Format Pasien BPJS*\nUntuk pasien JKN/BPJS silahkan ketik nomor kartu 13 digit yang tertera pada kartu bpjs anda dengan format seperti berikut : \n\n_*Nomor Kartu*_#BPJS#" . $jadwalid . "\n(Contoh)\n0000067XX23XX#BPJS#" . $jadwalid;
                    $this->send_message($request);
                    $request['message'] = "0000067XX23XX#BPJS#" . $jadwalid;
                    return $this->send_message($request);
                }
                // 5. pilih jenis pasien, masukan nik
                else if (substr($pesan, 0, 12) == 'PASIEN UMUM_') {
                    $jadwalid = explode('_', $pesan)[1];
                    $request['message'] = "*5. Ketik Format Pasien Umum*\nUntuk pasien UMUM silahkan ketik nomor nik/ktp 16 digit yang tertera pada Kartu Tanda Penduduk (KTP) anda dengan format seperti berikut : \n\n_*NIK / KTP*_#UMUM#" . $jadwalid . "\n\n(Contoh)\n3209XXXX1234XXXX#UMUM#" . $jadwalid;
                    $this->send_message($request);
                    $request['message'] = "3209XXXX1234XXXX#UMUM#" . $jadwalid;
                    return $this->send_message($request);
                }
                // 6. cek pasien umum
                else if (str_contains($pesan, '#UMUM#')) {
                    return $this->konfirmasi_antrian_umum($pesan, $request);
                }
                // 6. pilih jenis kunjungan untuk pasien bpjs
                else if (str_contains($pesan, '#BPJS#')) {
                    return $this->cek_nomorkartu_peserta($pesan, $request);
                }
                // 7. pilih jenis pasien, masukan nik
                else if (str_contains($pesan, '#DAFTAR_UMUM#')) {
                    return $this->daftar_antrian_umum($pesan, $request);
                }
                // 7. pilih rujukan faskses 1 peserta
                else if (substr($pesan, 0, 17) == 'RUJUKAN FASKES 1_') {
                    return $this->rujukan_peserta($pesan, $request);
                }
                // 7. pilih rujukan faskses 1 peserta
                else if (substr($pesan, 0, 8) == 'KONTROL_') {
                    return $this->surat_kontrol_peserta($pesan, $request);
                }
                // 7. pilih rujukan faskses 1 peserta
                else if (substr($pesan, 0, 17) == 'RUJUKAN ANTAR RS_') {
                    return $this->rujukan_rs_peserta($pesan, $request);
                }
                // 8. pilih rujukan faskses 1 peserta
                else if (str_contains($pesan, '_RUJUKANFKTP#')) {
                    return $this->cek_rujukan($pesan, $request);
                }
                // 8. pilih suratkontrol , kemudian cek
                else if (str_contains($pesan, '_SURATKONTROL#')) {
                    return $this->cek_surat_kontrol($pesan, $request);
                }
                // 8. pilih rujukan faskses 1 peserta
                else if (str_contains($pesan, '_RUJUKANRS#')) {
                    return $this->cek_rujukan_rs($pesan, $request);
                }
                // 9. insert antrian rujukan fk1
                else if (substr($pesan, 0, 20) == 'DAFTAR RUJUKAN FKTP_') {
                    return $this->daftar_rujukan_fktp($pesan, $request);
                }
                // 9. insert antrian surat rujukan
                else if (substr($pesan, 0, 15) == 'DAFTAR KONTROL_') {
                    return $this->daftar_surat_kontrol($pesan, $request);
                }
                // 9. insert antrian rujukan rs
                else if (substr($pesan, 0, 18) == 'DAFTAR RUJUKAN RS_') {
                    return $this->daftar_rujukan_rs($pesan, $request);
                }
                // default
                else {
                    $request['contenttext'] = "Mohon maaf pesan yang anda kirim tidak dapat diproses oleh sistem ❎🙏\n\nUntuk pertanyaan & pengaduan silahkan hubungi :\n*Humas RSUD Waled 08983311118*\n\nSilahkan klik *MENU UTAMA* yang dapat diproses dibawah ini ⬇";
                    $request['titletext'] = "Layanan Whatsapp RSUD Waled 📱🏥";
                    $request['buttontext'] = 'MENU UTAMA';
                    $request['rowtitle'] = 'INFO CARA PENDAFTARAN,DAFTAR PASIEN RAWAT JALAN,INFO JADWAL POLIKLINIK,PERTANYAAN DAN PENGADUAN';
                    return $this->send_list($request);
                    break;
                }
        }
    }
}
