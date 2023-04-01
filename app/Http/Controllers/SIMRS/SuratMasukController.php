<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\SuratMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $surats = SuratMasuk::orderBy('id_surat_masuk', 'desc')
            ->where(function ($query) use ($request) {
                $query->where('asal_surat', "like", "%" . $request->search . "%")
                    ->orWhere('perihal', "like", "%" . $request->search . "%");
            })->paginate(25);
        $surat_total = SuratMasuk::count();
        return view('simrs.bagum.suratmasuk_index', compact([
            'request',
            'surats',
            'surat_total'
        ]));
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $request->validate([
            'no_surat' => 'required',
            'tgl_surat' => 'required|date',
            'asal_surat' => 'required',
            'perihal' => 'required',
            'sifat' => 'required',
            'tgl_disposisi' => 'required|date',
        ]);
        // setting no urut disposisi per bulan
        $tgl_disposisi = Carbon::parse($request->tgl_disposisi);
        $no_urut_bulan = SuratMasuk::whereYear('tgl_disposisi', $tgl_disposisi->year)
            ->whereMonth('tgl_disposisi', $tgl_disposisi->month)
            ->count();
        $request['no_urut'] = $no_urut_bulan + 1;
        // insert surat masuk
        SuratMasuk::create([
            'no_urut' => $request->no_urut,
            'kode' => $request->kode,
            'sifat' => $request->sifat,
            'no_surat' => $request->no_surat,
            'tgl_surat' => $request->tgl_surat,
            'asal_surat' => $request->asal_surat,
            'perihal' => $request->perihal,
            'tgl_disposisi' => $request->tgl_disposisi,
            'user' => Auth::user()->name,
        ]);
        Alert::success('Success', 'Surat Masuk Berhasil Diinputkan');
        return redirect()->back();
    }
    public function show($id)
    {
        $surat = SuratMasuk::find($id);
        return response()->json($surat);
    }
    public function edit($id)
    {
        //
    }
    public function update(Request $request, $id)
    {

        $tindakan = [];
        if (isset($request->tindaklanjuti)) {
            array_push($tindakan, "tindaklanjuti");
        }
        if (isset($request->proses_sesuai_kemampuan)) {
            array_push($tindakan, "proses_sesuai_kemampuan");
        }
        if (isset($request->untuk_dibantu)) {
            array_push($tindakan, "untuk_dibantu");
        }
        if (isset($request->pelajari)) {
            array_push($tindakan, "pelajari");
        }
        if (isset($request->wakili_hadiri)) {
            array_push($tindakan, "wakili_hadiri");
        }
        if (isset($request->agendakan)) {
            array_push($tindakan, "agendakan");
        }
        if (isset($request->ingatkan_waktunya)) {
            array_push($tindakan, "ingatkan_waktunya");
        }
        if (isset($request->siapkan_bahan)) {
            array_push($tindakan, "siapkan_bahan");
        }
        if (isset($request->simpan_arsipkan)) {
            array_push($tindakan, "simpan_arsipkan");
        }
        // ttd direktur
        if (isset($request->ttd_direktur)) {
            $request['ttd_direktur'] = now();
        }
        $surat = SuratMasuk::firstWhere('id_surat_masuk', $request->id_surat);
        $surat->update($request->all());
        Alert::success('Success', 'Surat Berhasil Diupdate');
        return redirect()->back();
    }
    public function destroy($id)
    {
        SuratMasuk::where('id_surat_masuk', $id)->delete();
        Alert::success('Success', 'Surat Berhasil Dihapus');
        return redirect()->back();
    }
}
