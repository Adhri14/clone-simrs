<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\AntrianDB;
use App\Models\JadwalDokter;
use App\Models\KunjunganDB;
use App\Models\PenjaminSimrs;
use App\Models\PoliklinikDB;
use App\Models\UnitDB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\Provinsi;

class AntrianController extends Controller
{
    public function anjungan()
    {
        $poliklinik = PoliklinikDB::with(['antrians', 'jadwals'])->where('status', 1)->get();
        $jadwal = JadwalDokter::where('hari',  now()->dayOfWeek)->get();
        return view('simrs.antrian_console', [
            'poliklinik' => $poliklinik,
            'jadwal' => $jadwal,
        ]);
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
    public function laporan(Request $request)
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
    public function laporan_kunjungan(Request $request)
    {

        $response = null;
        $kunjungans = null;
        if (isset($request->tanggal) && isset($request->kodepoli)) {
            $poli = UnitDB::where('KDPOLI', $request->kodepoli)->first();
            $kunjungans = KunjunganDB::whereDate('tgl_masuk', $request->tanggal)
                ->where('kode_unit', $poli->kode_unit)
                ->where('status_kunjungan',  2)
                ->with(['dokter', 'unit', 'pasien', 'diagnosapoli', 'pasien.kecamatans', 'penjamin', 'surat_kontrol'])
                ->get();
            $response = DB::connection('mysql2')->select("CALL SP_PANGGIL_PASIEN_RAWAT_JALAN_KUNJUNGAN('" . $poli->kode_unit . "','" . $request->tanggal . "')");
        }
        $unit = UnitDB::where('KDPOLI', "!=", null)->get();
        $penjaminrs = PenjaminSimrs::get();
        $response = collect($response);
        // dd($response);
        // dd($response->where('KODE_KUNJUNGAN', $kunjungans->first()->kode_kunjungan)->first()->diagx);
        return view('simrs.antrian_laporan_kunjungan', [
            'kunjungans' => $kunjungans,
            'request' => $request,
            'response' => $response,
            'penjaminrs' => $penjaminrs,
            'unit' => $unit,
        ]);
    }
}
