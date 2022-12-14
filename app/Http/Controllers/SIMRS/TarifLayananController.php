<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\TarifLayananDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TarifLayananController extends Controller
{
    public function index()
    {
      $response = Http::get('http://sim.rsudwaled.id/simrs/api/penunjang/get_tarif_laboratorium');
      dd($response->jsons());

        $tariflayanans = TarifLayananDB::with(['tarifdeails'])->paginate();
        return view('simrs.pelyananmedis.tarif_layanan_index', [
            'tariflayanans' => $tariflayanans,
        ]);
    }
    public function create()
    {
        $tariflayanans = TarifLayananDB::where('USER_INPUT_ID', 1)->get();
        foreach ($tariflayanans as $tarif) {
            TarifLayanan::updateOrCreate(
                [
                    'kodetarif' => $tarif->KODE_TARIF_HEADER,
                ],
                [
                    'nosk' => $tarif->NO_SK,
                    'namatarif' => $tarif->NAMA_TARIF,
                    'tarifkelompokid' => $tarif->KELOMPOK_TARIF_ID,
                    'tarifvclaimid' => $tarif->ID_VCLAIM,
                    'keterangan' => $tarif->keterangan,
                    'status' => $tarif->USER_INPUT_ID,
                    'userid' => 1,
                ]
            );
            $tarifdetails = TarifLayananDetailDB::where('KODE_TARIF_HEADER', $tarif->KODE_TARIF_HEADER)->get();
            foreach ($tarifdetails as $detail) {
                TarifLayananDetail::updateOrCreate([
                    'kodetarifdetail' => $detail->KODE_TARIF_DETAIL,
                    'kodetarif' => $detail->KODE_TARIF_HEADER,
                    'kelas' => $detail->KELAS_TARIF,
                    'totaltarif' => $detail->TOTAL_TARIF_NEW,
                    'userid' => 1,
                ]);
            }
        }
        Alert::success('Berhasil', 'Data Tarif Kelompok Layanan Berhasil Diimport');
        return redirect()->route('tarif_layanan.index');
    }
}
