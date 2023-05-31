<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Admin\WhatsappController;
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
        $wa = new WhatsappController();
        $request['number'] = "120363115261279867@g.us";
        $request['message'] = "Telah diinput surat masuk oleh *" . Auth::user()->name .  "*\n\n*No Surat :* " . $request->no_surat . "\n*Asal Surat :* " . $request->asal_surat . "\n*Perihal :* " . $request->perihal . "\n\nSilahkan untuk mengeceknya dan men-disposisikan dapat diakses dengan link berikut. \nhttp://sim.rsudwaled.id/simrs/bagianumum/suratmasuk";
        $wa->send_message_group($request);
        $request['number'] = "089529909036";
        $request['message'] = "Telah diinput surat masuk oleh *" . Auth::user()->name .  "*\n\n*No Surat :* " . $request->no_surat . "\n*Asal Surat :* " . $request->asal_surat . "\n*Perihal :* " . $request->perihal . "\n\nSilahkan untuk mengeceknya dan men-disposisikan dapat diakses dengan link berikut. \nhttp://sim.rsudwaled.id/simrs/bagianumum/suratmasuk";
        $wa->send_message($request);
        Alert::success('Success', 'Surat Masuk Berhasil Diinputkan');
        return redirect()->back();
    }
    public function show($id)
    {
        $surat = SuratMasuk::find($id);
        return response()->json($surat);
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

        $nomor = str_pad($surat->no_urut, 3, '0', STR_PAD_LEFT) . '/' . $surat->kode . '/' . Carbon::parse($surat->tgl_disposisi)->translatedFormat('m/Y');
        if ($request->disposisi && $request->pengolah) {
            $wa = new WhatsappController();
            $request['number'] = "120363115261279867@g.us";
            $request['message'] = "Telah diupdate Disposisi oleh *" . Auth::user()->name .  "*\n\n*No Surat :* " . $surat->no_surat . "\n*Asal Surat :* " . $surat->asal_surat . "\n*Perihal :* " . $surat->perihal . "\n\n*No Disposisi :* " . $nomor . "\n*Ditujukan Untuk :* " . $request->pengolah . "\n*Disposisi :* " . $request->disposisi . "\n\nSilahkan untuk mengeceknya dengan link berikut. \nhttp://sim.rsudwaled.id/simrs/bagianumum/suratmasuk";
            $wa->send_message_group($request);
        }

        $surat->update($request->all());
        Alert::success('Success', 'Surat Berhasil Diupdate');
        return redirect()->back();
    }
    public function destroy($id, Request $request)
    {
        $surat = SuratMasuk::where('id_surat_masuk', $id)->first();
        $wa = new WhatsappController();
        $request['number'] = "120363115261279867@g.us";
        $request['message'] = "Telah dihapus surat masuk oleh *" . Auth::user()->name .  "*\n\n*No Surat :* " . $surat->no_surat . "\n*Asal Surat :* " . $surat->asal_surat . "\n*Perihal :* " . $surat->perihal . "\n\nSilahkan untuk mengeceknya dengan link berikut. \nhttp://sim.rsudwaled.id/simrs/bagianumum/suratmasuk";
        $wa->send_message_group($request);
        $surat->delete();
        Alert::success('Success', 'Surat Berhasil Dihapus');
        return redirect()->back();
    }
}
