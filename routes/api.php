<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\API\AntrianBPJSController;
use App\Http\Controllers\API\VclaimBPJSController;
use App\Http\Controllers\API\WhatsappController;
use App\Http\Controllers\VclaimController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('antrian')->group(function () {
    Route::get('signature', [AntrianBPJSController::class, 'signature']);
    Route::prefix('ref')->group(function () {
        Route::get('poli', [AntrianBPJSController::class, 'ref_poli']);
        Route::get('dokter', [AntrianBPJSController::class, 'ref_dokter']);
        Route::get('jadwal', [AntrianBPJSController::class, 'ref_jadwal_dokter']);
        Route::post('updatejadwal', [AntrianBPJSController::class, 'update_jadwal_dokter']);
    });
    Route::post('tambah', [AntrianBPJSController::class, 'tambah_antrian']);
    Route::post('update', [AntrianBPJSController::class, 'update_antrian']);
    Route::post('batal', [AntrianBPJSController::class, 'batal_antrian_bpjs']);
    Route::post('listtask', [AntrianBPJSController::class, 'list_waktu_task']);
    Route::get('dashboard_tanggal', [AntrianBPJSController::class, 'dashboard_tanggal']);
    Route::get('dashboard_bulan', [AntrianBPJSController::class, 'dashboard_bulan']);
    Route::post('store_offline', [AntrianController::class, 'store_offline'])->name('api.store_offline');
});

Route::get('token', [AntrianBPJSController::class, 'token']);
Route::prefix('wsrs')->group(function () {
    Route::post('ambil_antrian', [AntrianBPJSController::class, 'ambil_antrian']);
    Route::post('status_antrian', [AntrianBPJSController::class, 'status_antrian']);
    Route::post('sisa_antrian', [AntrianBPJSController::class, 'sisa_antrian']);
    Route::post('batal_antrian', [AntrianBPJSController::class, 'batal_antrian']);
    Route::post('checkin_antrian', [AntrianBPJSController::class, 'checkin_antrian']);
    Route::post('info_pasien_baru', [AntrianBPJSController::class, 'info_pasien_baru']);
    Route::post('jadwal_operasi_rs', [AntrianBPJSController::class, 'jadwal_operasi_rs']);
    Route::post('jadwal_operasi_pasien', [AntrianBPJSController::class, 'jadwal_operasi_pasien']);

    Route::post('pasien_pendaftaran', [AntrianBPJSController::class, 'pasien_pendaftaran']);
    Route::post('panggil_pendaftaran', [AntrianBPJSController::class, 'panggil_pendaftaran']);
    Route::post('update_pendaftaran_offline', [AntrianBPJSController::class, 'update_pendaftaran_offline']);
    Route::post('update_pendaftaran_online', [AntrianBPJSController::class, 'update_pendaftaran_online']);
});

Route::prefix('vclaim')->group(function () {
    // ref
    Route::get('ref_provinsi', [VclaimBPJSController::class, 'ref_provinsi'])->name('ref_provinsi');
    Route::post('ref_kabupaten', [VclaimBPJSController::class, 'ref_kabupaten'])->name('ref_kabupaten');
    Route::post('ref_kecamatan', [VclaimBPJSController::class, 'ref_kecamatan'])->name('ref_kecamatan');
    // monitoring
    Route::get('monitoring_pelayanan_peserta', [VclaimBPJSController::class, 'monitoring_pelayanan_peserta']);
    // peserta cek
    Route::get('peserta_nomorkartu', [VclaimBPJSController::class, 'peserta_nomorkartu'])->name('api.cek_nomorkartu');
    Route::get('peserta_nik', [VclaimBPJSController::class, 'peserta_nik'])->name('api.cek_nik');
    // rujukan
    Route::get('rujukan_jumlah_sep', [VclaimBPJSController::class, 'rujukan_jumlah_sep'])->name('api.rujukan_jumlah_sep');
    Route::get('rujukan_nomor', [VclaimBPJSController::class, 'rujukan_nomor'])->name('api.rujukan_nomor');
    Route::get('rujukan_peserta', [VclaimBPJSController::class, 'rujukan_peserta']);
    // 0301U0331019P003283
    // sep
    Route::post('insert_sep', [VclaimBPJSController::class, 'insert_sep']);
    Route::delete('delete_sep', [VclaimBPJSController::class, 'delete_sep']);
    Route::get('cari_sep', [VclaimBPJSController::class, 'cari_sep']);
    Route::get('sep_internal', [VclaimBPJSController::class, 'sep_internal']);
    Route::delete('delete_sep_internal', [VclaimBPJSController::class, 'delete_sep_internal']);

    // surat kontrol
    Route::post('insert_rencana_kontrol', [VclaimBPJSController::class, 'insert_rencana_kontrol']);
    Route::get('surat_kontrol_nomor', [VclaimBPJSController::class, 'surat_kontrol_nomor'])->name('api.surat_kontrol_nomor');
    Route::post('buat_surat_kontrol', [VclaimController::class, 'buat_surat_kontrol'])->name('api.buat_surat_kontrol');
});

Route::prefix('wa')->group(function () {
    Route::get('index', [WhatsappController::class, 'index']);
    Route::post('send_message', [WhatsappController::class, 'send_message']);
    Route::post('callback', [WhatsappController::class, 'callback']);
    Route::post('daftar_antrian', [WhatsappController::class, 'daftar_antrian']);
});
