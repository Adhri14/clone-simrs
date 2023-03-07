<?php

use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\LaravotLocationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ThermalPrintController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WhatsappController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BPJS\Antrian\AntrianController as AntrianAntrianController;
use App\Http\Controllers\BPJS\Vclaim\VclaimController as VclaimVclaimController;
use App\Http\Controllers\FileRMController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Icd10Controller;
use App\Http\Controllers\JadwalDokterController;
use App\Http\Controllers\JadwalLiburController;
use App\Http\Controllers\JadwalOperasiController;
use App\Http\Controllers\KPOController;
use App\Http\Controllers\PoliklinikController;
use App\Http\Controllers\RekamMedis\IndexController;
use App\Http\Controllers\SatuSehat\EncounterController;
use App\Http\Controllers\SatuSehat\LocationController;
use App\Http\Controllers\SatuSehat\OrganizationController;
use App\Http\Controllers\SatuSehat\PatientController;
use App\Http\Controllers\SatuSehat\PractitionerController;
use App\Http\Controllers\SatuSehat\TokenController;
use App\Http\Controllers\SIMRS\AntrianController as SIMRSAntrianController;
use App\Http\Controllers\SIMRS\BukuTamuController;
use App\Http\Controllers\SIMRS\DisposisiController;
use App\Http\Controllers\SIMRS\DokterController as SIMRSDokterController;
use App\Http\Controllers\SIMRS\JadwalDokterController as SIMRSJadwalDokterController;
use App\Http\Controllers\SIMRS\JadwalOperasiController as SIMRSJadwalOperasiController;
use App\Http\Controllers\SIMRS\KunjunganController;
use App\Http\Controllers\SIMRS\MonitoringController;
use App\Http\Controllers\SIMRS\ObatController;
use App\Http\Controllers\SIMRS\PasienController;
use App\Http\Controllers\SIMRS\PoliklinikController as SIMRSPoliklinikController;
use App\Http\Controllers\SIMRS\SimrsController;
use App\Http\Controllers\SIMRS\SuratKontrolController;
use App\Http\Controllers\SIMRS\SuratMasukController;
use App\Http\Controllers\SIMRS\TarifLayananController;
use App\Http\Controllers\TarifKelompokLayananController;
use App\Http\Controllers\VclaimController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('', [HomeController::class, 'landingpage'])->name('landingpage');
Auth::routes();
Route::get('profile', [UserController::class, 'profile'])->name('profile');
Route::get('verifikasi_akun', [VerificationController::class, 'verifikasi_akun'])->name('verifikasi_akun');
Route::post('verifikasi_kirim', [VerificationController::class, 'verifikasi_kirim'])->name('verifikasi_kirim');

Route::get('info_jadwaldokter', [JadwalDokterController::class, 'index'])->name('info_jadwaldokter');
Route::get('info_jadwallibur', [JadwalLiburController::class, 'index'])->name('info_jadwallibur');
Route::get('info_jadwaloperasi', [JadwalOperasiController::class, 'index'])->name('info_jadwaloperasi');
Route::get('jadwaloperasi_info', [JadwalOperasiController::class, 'jadwaloperasi_info'])->name('jadwaloperasi_info');
Route::get('jadwaloperasi_display', [JadwalOperasiController::class, 'jadwaloperasi_display'])->name('jadwaloperasi_display');

// antrian routes
Route::prefix('antrian')->name('antrian.')->group(function () {
    Route::get('console', [SIMRSAntrianController::class, 'console'])->name('console');
    Route::get('jadwaldokter_poli', [SIMRSJadwalDokterController::class, 'jadwaldokter_poli'])->name('jadwaldokter_poli');
    Route::get('daftar_pasien_bpjs_offline', [SIMRSAntrianController::class, 'daftar_pasien_bpjs_offline'])->name('daftar_pasien_bpjs_offline');
    Route::get('daftar_pasien_umum_offline', [SIMRSAntrianController::class, 'daftar_pasien_umum_offline'])->name('daftar_pasien_umum_offline');


    Route::get('cek_post', [AntrianController::class, 'cek_post'])->name('cek_post');
    Route::get('console_jadwaldokter/{poli}/{tanggal}', [AntrianController::class, 'console_jadwaldokter'])->name('console_jadwaldokter');
    Route::get('tambah_offline/{poli}/{dokter}/{jam}', [AntrianController::class, 'tambah_offline'])->name('tambah_offline');
    Route::get('checkin_update', [AntrianController::class, 'checkin_update'])->name('checkin_update');
});
Route::prefix('antrian')->name('antrian.')->middleware(['auth'])->group(function () {
    // console
    Route::get('laporan', [AntrianController::class, 'laporan'])->name('laporan');
    Route::get('laporan_tanggal', [AntrianController::class, 'laporan_tanggal'])->name('laporan_tanggal');
    Route::get('laporan_bulan', [AntrianController::class, 'laporan_bulan'])->name('laporan_bulan');
    Route::get('{kodebookig}/edit', [AntrianController::class, 'edit'])->name('edit');
    // pendafataran
    Route::get('pendaftaran', [AntrianController::class, 'pendaftaran'])->name('pendaftaran')->middleware('permission:pendaftaran');
    Route::get('panggil_pendaftaran/{kodebooking}/{loket}/{lantai}', [AntrianController::class, 'panggil_pendaftaran'])->name('panggil_pendaftaran')->middleware('permission:pendaftaran');
    Route::post('update_pendaftaran_offline', [AntrianController::class, 'update_pendaftaran_offline'])->name('update_pendaftaran_offline')->middleware('permission:pendaftaran');
    Route::post('update_pendaftaran_online', [AntrianController::class, 'update_pendaftaran_online'])->name('update_pendaftaran_online')->middleware('permission:pendaftaran');
    Route::get('batal_antrian/{kodebooking}', [AntrianController::class, 'batal_antrian'])->name('batal_antrian')->middleware('permission:pendaftaran');
    // pembayaran
    Route::get('pembayaran', [AntrianController::class, 'pembayaran'])->name('pembayaran')->middleware('permission:kasir');
    Route::post('update_pembayaran', [AntrianController::class, 'update_pembayaran'])->name('update_pembayaran')->middleware('permission:kasir');
    // poliklinik
    Route::get('poli', [AntrianController::class, 'poli'])->name('poli')->middleware('permission:poliklinik');
    Route::get('panggil_poli/{kodebooking}', [AntrianController::class, 'panggil_poli'])->name('panggil_poli')->middleware('permission:poliklinik');
    Route::get('lanjut_farmasi/{kodebooking}', [AntrianController::class, 'lanjut_farmasi'])->name('lanjut_farmasi')->middleware('permission:poliklinik');
    Route::get('lanjut_farmasi_racikan/{kodebooking}', [AntrianController::class, 'lanjut_farmasi_racikan'])->name('lanjut_farmasi_racikan')->middleware('permission:poliklinik');
    Route::get('selesai/{kodebooking}', [AntrianController::class, 'selesai'])->name('selesai')->middleware('permission:poliklinik');
    Route::get('selesai_semua/{kodepoli}', [AntrianController::class, 'selesai_semua'])->name('selesai_semua')->middleware('permission:poliklinik');
    // Route::get('surat_kontrol_poli', [AntrianController::class, 'surat_kontrol_poli'])->name('surat_kontrol_poli')->middleware('permission:poliklinik');
    Route::post('surat_kontrol_create', [AntrianController::class, 'surat_kontrol_create'])->name('surat_kontrol_create')->middleware('permission:poliklinik');
    Route::get('laporan_kunjungan_poliklinik', [AntrianController::class, 'laporan_kunjungan_poliklinik'])->name('laporan_kunjungan_poliklinik')->middleware('permission:poliklinik');
    // farmasi
    Route::get('farmasi', [AntrianController::class, 'farmasi'])->name('farmasi')->middleware('permission:farmasi');
    Route::get('panggil_farmasi/{kodebooking}', [AntrianController::class, 'panggil_farmasi'])->name('panggil_farmasi')->middleware('permission:farmasi');
    Route::get('racik_farmasi/{kodebooking}', [AntrianController::class, 'racik_farmasi'])->name('racik_farmasi')->middleware('permission:farmasi');
    Route::get('selesai_farmasi/{kodebooking}', [AntrianController::class, 'selesai_farmasi'])->name('selesai_farmasi')->middleware('permission:farmasi');
    Route::get('{kodebookig}/show', [AntrianController::class, 'show'])->name('show');
    Route::get('display_pendaftaran', [AntrianController::class, 'display_pendaftaran'])->name('display_pendaftaran');
    Route::get('/', [AntrianController::class, 'index'])->name('index');
    Route::get('cari_pasien/{nik}', [AntrianController::class, 'cari_pasien'])->name('cari_pasien');
    Route::prefix('ref')->name('ref.')->group(function () {
        Route::get('poli', [AntrianController::class, 'ref_poli'])->name('poli');
        Route::get('get_poli_bpjs', [AntrianController::class, 'get_poli_bpjs'])->name('get_poli_bpjs');
        Route::get('dokter', [AntrianController::class, 'ref_dokter'])->name('dokter');
        Route::get('get_dokter_bpjs', [AntrianController::class, 'get_dokter_bpjs'])->name('get_dokter_bpjs');
        Route::get('jadwaldokter', [AntrianController::class, 'ref_jadwaldokter'])->name('jadwaldokter');
        Route::get('get_jadwal_bpjs', [AntrianController::class, 'get_jadwal_bpjs'])->name('get_jadwal_bpjs');
    });
    Route::post('store', [AntrianController::class, 'store'])->name('store');
    Route::get('baru_online/{kodebooking}', [AntrianController::class, 'baru_online'])->name('baru_online');
    Route::post('simpan_baru_online/{kodebooking}', [AntrianController::class, 'simpan_baru_online'])->name('simpan_baru_online');
    Route::get('baru_offline/{kodebooking}', [AntrianController::class, 'baru_offline'])->name('baru_offline');
});
// vcalim
Route::prefix('vclaim')->name('vclaim.')->middleware(['auth'])->group(function () {
    Route::get('/', [VclaimController::class, 'index'])->name('index');
    Route::get('monitoring_pelayanan_peserta', [VclaimController::class, 'monitoring_pelayanan_peserta'])->name('monitoring_pelayanan_peserta');
    Route::get('data_surat_kontrol', [VclaimController::class, 'data_surat_kontrol'])->name('data_surat_kontrol');
    Route::get('edit_surat_kontrol/{id}', [VclaimController::class, 'edit_surat_kontrol'])->name('edit_surat_kontrol');
    Route::get('sep_internal', [VclaimController::class, 'sep_internal'])->name('sep_internal');
    Route::post('buat_surat_kontrol', [VclaimController::class, 'buat_surat_kontrol'])->name('buat_surat_kontrol');
    Route::post('update_surat_kontrol', [VclaimController::class, 'update_surat_kontrol'])->name('update_surat_kontrol');
    Route::delete('sep_internal_delete', [VclaimController::class, 'sep_internal_delete'])->name('sep_internal_delete');
    Route::delete('delete_sep/{noSep}', [VclaimController::class, 'delete_sep'])->name('delete_sep');
    Route::delete('delete_surat_kontrol/{noSuratKontrol}', [VclaimController::class, 'delete_surat_kontrol'])->name('delete_surat_kontrol');
});

Route::resource('poli', PoliklinikController::class)->only(['index', 'create', 'edit', 'show', 'store'])->middleware('permission:pelayanan-medis');
Route::resource('jadwallibur', JadwalLiburController::class)->middleware(['auth', 'permission:pelayanan-medis']);
Route::resource('jadwaldokter', JadwalDokterController::class)->middleware(['auth']);
Route::resource('jadwaloperasi', JadwalOperasiController::class)->only(['index', 'store', 'edit'])->middleware('permission:pelayanan-medis');
Route::get('pasien_daerah', [PasienController::class, 'pasien_daerah'])->name('pasien_daerah');
Route::get('pasien_demografi', [PasienController::class, 'pasien_demografi'])->name('pasien_demografi');
Route::get('index_penyakit_rajal', [IndexController::class, 'index_penyakit_rajal'])->name('index_penyakit_rajal');
Route::get('index_dokter', [IndexController::class, 'index_dokter'])->name('index_dokter');

Route::resource('tindakan', PasienController::class)->middleware('permission:rekam-medis');
Route::resource('tarif_kelompok_layanan', TarifKelompokLayananController::class);
Route::resource('icd10', Icd10Controller::class);
Route::resource('efilerm', FileRMController::class);
Route::resource('kpo', KPOController::class);
Route::get('kpo/tanggal/{tanggal}', [KPOController::class, 'kunjungan_tanggal'])->name('kpo.kunjungan_tanggal');

Route::get('get_city', [LaravotLocationController::class, 'get_city'])->name('get_city');
Route::get('get_district', [LaravotLocationController::class, 'get_district'])->name('get_district');
Route::get('get_village', [LaravotLocationController::class, 'get_village'])->name('get_village');
Route::get('bar_qr_scanner', [BarcodeController::class, 'scanner'])->name('bar_qr_scanner');
Route::get('thermal_printer', [ThermalPrintController::class, 'thermal_printer'])->name('thermal_printer');
Route::get('thermal_print', [ThermalPrintController::class, 'thermal_print'])->name('thermal_print');
Route::get('whatsapp', [WhatsappController::class, 'whatsapp'])->name('whatsapp');
Route::get('bukutamu', [BukuTamuController::class, 'bukutamu'])->name('bukutamu');
Route::post('bukutamu', [BukuTamuController::class, 'store'])->name('bukutamu_store');
Route::get('bpjs/vclaim/surat_kontrol_print/{suratkontrol}', [SuratKontrolController::class, 'print'])->name('bpjs.vclaim.surat_kontrol_print');

// auth
Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get("log-message", function () {
        $message = "This is a sample message for Test.";
        Log::emergency($message);
        Log::alert($message);
        Log::critical($message);
        Log::error($message);
        Log::warning($message);
        Log::notice($message);
        Log::info($message);
        Log::debug($message);
    });
    Route::get('kunjungan/show/{kodekunjungan}', [KunjunganController::class, 'show'])->name('kunjungan.show');
    Route::get('kunjungan_tanggal/{tanggal}', [KunjunganController::class, 'kunjungan_tanggal'])->name('kunjungan_tanggal');
    // admin
    Route::middleware('permission:admin')->group(function () {
        Route::resource('user', UserController::class);
        Route::resource('role', RoleController::class);
        Route::resource('permission', PermissionController::class);
        Route::get('user_verifikasi/{user}', [UserController::class, 'user_verifikasi'])->name('user_verifikasi');
        Route::get('delet_verifikasi', [UserController::class, 'delet_verifikasi'])->name('delet_verifikasi');
    });
    // pendaftaran
    Route::middleware('permission:pendaftaran')->prefix('pendaftaran')->name('pendaftaran.')->group(function () {
        Route::get('antrian_pendaftaran', [SIMRSAntrianController::class, 'antrian_pendaftaran'])->name('antrian_pendaftaran');
        Route::get('panggil_pendaftaran/{kodebooking}/{loket}/{lantai}', [SIMRSAntrianController::class, 'panggil_pendaftaran'])->name('panggil_pendaftaran')->middleware('permission:pendaftaran');
        Route::get('selesai_pendaftaran/{kodebooking}', [SIMRSAntrianController::class, 'selesai_pendaftaran'])->name('selesai_pendaftaran')->middleware('permission:pendaftaran');
    });
    // poliklinik
    Route::prefix('poliklinik')->name('poliklinik.')->group(function () {
        Route::get('antrian', [SIMRSAntrianController::class, 'antrian_poliklinik'])->name('antrian');
        Route::get('antrian_panggil/{antrian}', [SIMRSAntrianController::class, 'panggil_poliklinik'])->name('antrian_panggil');
        Route::get('antrian_panggil_ulang/{antrian}', [SIMRSAntrianController::class, 'panggil_ulang_poliklinik'])->name('antrian_panggil_ulang');
        Route::get('antrian_batal/{antrian}', [SIMRSAntrianController::class, 'batal_antrian_poliklinik'])->name('antrian_batal');
        Route::get('lanjut_farmasi/{antrian}', [SIMRSAntrianController::class, 'lanjut_farmasi'])->name('lanjut_farmasi');
        Route::get('selesai_poliklinik/{antrian}', [SIMRSAntrianController::class, 'selesai_poliklinik'])->name('selesai_poliklinik');
        Route::get('suratkontrol_poliklinik', [SIMRSAntrianController::class, 'suratkontrol_poliklinik'])->name('suratkontrol_poliklinik');
        Route::get('laporan_kunjungan_poliklinik', [SIMRSAntrianController::class, 'laporan_kunjungan_poliklinik'])->name('laporan_kunjungan_poliklinik');
        Route::get('jadwaldokter', [JadwalDokterController::class, 'jadwaldokter_poliklinik'])->name('jadwaldokter');
        Route::get('laporan_antrian_poliklinik', [SIMRSAntrianController::class, 'laporan_antrian_poliklinik'])->name('laporan_antrian_poliklinik');
        Route::get('dashboard_antrian_tanggal', [SIMRSAntrianController::class, 'dashboard_antrian_tanggal'])->name('dashboard_antrian_tanggal');
        Route::get('dashboard_antrian_bulan', [SIMRSAntrianController::class, 'dashboard_antrian_bulan'])->name('dashboard_antrian_bulan');
        Route::resource('pasien', PasienController::class);
    });
    // yanmed
    Route::prefix('pelayananmedis')->name('pelayanan-medis.')->group(function () {
        Route::resource('dokter', SIMRSDokterController::class);
        Route::resource('tarif_layanan', TarifLayananController::class)->only(['index']);
        Route::get('poliklinik_antrian', [SIMRSPoliklinikController::class, 'poliklik_antrian_yanmed'])->name('poliklinik_antrian');
        Route::get('poliklinik_antrian_refresh', [SIMRSPoliklinikController::class, 'poliklik_antrian_refresh'])->name('poliklinik_antrian_refresh');
        Route::get('dokter_antrian', [SIMRSDokterController::class, 'dokter_antrian_yanmed'])->name('dokter_antrian');
        Route::get('dokter_antrian_refresh', [SIMRSDokterController::class, 'dokter_antrian_refresh'])->name('dokter_antrian_refresh');
        Route::get('jadwaldokter', [SIMRSJadwalDokterController::class, 'jadwaldokter_simrs'])->name('jadwaldokter.index');
        Route::post('jadwaldokter_add', [SIMRSJadwalDokterController::class, 'jadwaldokter_add'])->name('jadwaldokter_add');
        Route::get('jadwaldokter/{id}/get', [SIMRSJadwalDokterController::class, 'jadwaldokter_get'])->name('jadwaldokter_get');
        Route::put('jadwaldokter_update', [SIMRSJadwalDokterController::class, 'jadwaldokter_update'])->name('jadwaldokter_update');
        Route::delete('jadwaldokter_delete', [SIMRSJadwalDokterController::class, 'jadwaldokter_delete'])->name('jadwaldokter_delete');
    });
    // rekam medis
    Route::prefix('rekammedis')->name('rekammedis.')->group(function () {
        Route::resource('pasien', PasienController::class);
        Route::resource('kunjungan', KunjunganController::class);
        Route::get('kunjungan_poliklinik', [KunjunganController::class, 'kunjungan_poliklinik'])->name('kunjungan_poliklinik');
    });
    // bagum
    Route::prefix('bagianumum')->name('bagianumum.')->group(function () {
        Route::resource('suratmasuk', SuratMasukController::class);
        Route::resource('disposisi', DisposisiController::class);
    });
    // farmasi
    Route::prefix('farmasi')->name('farmasi.')->group(function () {
        Route::resource('obat', ObatController::class);
    });
    // simrs
    Route::prefix('simrs')->name('simrs.')->group(function () {
        Route::get('dashboard', [SimrsController::class, 'dashboard'])->name('dashboard');
        Route::prefix('antrian')->name('antrian.')->group(function () {
            Route::get('anjungan', [SIMRSAntrianController::class, 'anjungan'])->name('anjungan');
            Route::get('pendaftaran', [SIMRSAntrianController::class, 'pendaftaran'])->name('pendaftaran');
            Route::get('laporan', [SIMRSAntrianController::class, 'laporan'])->name('laporan');
            Route::get('laporan_kunjungan', [SIMRSAntrianController::class, 'laporan_kunjungan'])->name('laporan_kunjungan');
        });
        Route::resource('pasien', PasienController::class);
        Route::resource('dokter', SIMRSDokterController::class);
        Route::resource('kunjungan', KunjunganController::class);
    });
    // bpjs
    Route::prefix('bpjs')->name('bpjs.')->group(function () {
        // antrian
        Route::prefix('antrian')->name('antrian.')->group(function () {
            Route::get('status', [AntrianAntrianController::class, 'status'])->name('status');
            Route::get('poli', [SIMRSPoliklinikController::class, 'poliklik_antrian_bpjs'])->name('poli');
            Route::get('dokter', [SIMRSDokterController::class, 'dokter_antrian_bpjs'])->name('dokter');
            Route::get('jadwal_dokter', [SIMRSJadwalDokterController::class, 'jadwal_dokter_bpjs'])->name('jadwal_dokter');
            Route::get('fingerprint_peserta', [PasienController::class, 'fingerprint_peserta'])->name('fingerprint_peserta');
            Route::get('antrian', [AntrianAntrianController::class, 'antrian'])->name('antrian');
            Route::get('list_task', [AntrianAntrianController::class, 'list_task'])->name('list_task');
            Route::get('dashboard_tanggal', [AntrianAntrianController::class, 'dashboard_tanggal_index'])->name('dashboard_tanggal');
            Route::get('dashboard_bulan', [AntrianAntrianController::class, 'dashboard_bulan_index'])->name('dashboard_bulan');
            Route::get('jadwal_operasi', [SIMRSJadwalOperasiController::class, 'index'])->name('jadwal_operasi');
            Route::get('antrian_per_tanggal', [SIMRSAntrianController::class, 'antrian_per_tanggal'])->name('antrian_per_tanggal');
            Route::get('antrian_per_kodebooking', [SIMRSAntrianController::class, 'antrian_per_kodebooking'])->name('antrian_per_kodebooking');
            Route::get('antrian_belum_dilayani', [SIMRSAntrianController::class, 'antrian_belum_dilayani'])->name('antrian_belum_dilayani');
            Route::get('antrian_per_dokter', [SIMRSAntrianController::class, 'antrian_per_dokter'])->name('antrian_per_dokter');
        });
        // vclaim
        Route::prefix('vclaim')->name('vclaim.')->group(function () {
            Route::get('monitoring_data_kunjungan', [MonitoringController::class, 'monitoring_data_kunjungan_index'])->name('monitoring_data_kunjungan');
            Route::get('monitoring_data_klaim', [MonitoringController::class, 'monitoring_data_klaim_index'])->name('monitoring_data_klaim');
            Route::get('monitoring_pelayanan_peserta', [MonitoringController::class, 'monitoring_pelayanan_peserta_index'])->name('monitoring_pelayanan_peserta');
            Route::get('monitoring_klaim_jasaraharja', [MonitoringController::class, 'monitoring_klaim_jasaraharja_index'])->name('monitoring_klaim_jasaraharja');
            Route::get('referensi', [VclaimVclaimController::class, 'referensi_index'])->name('referensi');
            Route::get('ref_diagnosa_api', [VclaimVclaimController::class, 'ref_diagnosa_api'])->name('ref_diagnosa_api');
            Route::get('ref_poliklinik_api', [VclaimVclaimController::class, 'ref_poliklinik_api'])->name('ref_poliklinik_api');
            Route::get('ref_faskes_api', [VclaimVclaimController::class, 'ref_faskes_api'])->name('ref_faskes_api');
            Route::get('ref_dpjp_api', [VclaimVclaimController::class, 'ref_dpjp_api'])->name('ref_dpjp_api');
            Route::get('ref_provinsi_api', [VclaimVclaimController::class, 'ref_provinsi_api'])->name('ref_provinsi_api');
            Route::get('ref_kabupaten_api', [VclaimVclaimController::class, 'ref_kabupaten_api'])->name('ref_kabupaten_api');
            Route::get('ref_kecamatan_api', [VclaimVclaimController::class, 'ref_kecamatan_api'])->name('ref_kecamatan_api');
            Route::get('surat_kontrol', [VclaimVclaimController::class, 'surat_kontrol_index'])->name('surat_kontrol');
            Route::post('surat_kontrol_store', [SuratKontrolController::class, 'store'])->name('surat_kontrol_store');
            Route::put('surat_kontrol_update', [SuratKontrolController::class, 'update'])->name('surat_kontrol_update');
            Route::delete('surat_kontrol_delete', [SuratKontrolController::class, 'destroy'])->name('surat_kontrol_delete');
        });
    });
    // satu sehat
    Route::prefix('satusehat')->name('satusehat.')->group(function () {
        Route::get('status', [TokenController::class, 'status'])->name('status');
        Route::get('refresh_token', [TokenController::class, 'refresh_token'])->name('refresh_token');
        Route::resource('patient', PatientController::class)->only(['index']);
        Route::resource('practitioner', PractitionerController::class)->only(['index']);
        Route::resource('organization', OrganizationController::class)->only(['index']);
        Route::resource('location', LocationController::class)->only(['index', 'edit']);
        Route::resource('encounter', EncounterController::class)->only(['index', 'create', 'store']);
    });
});
