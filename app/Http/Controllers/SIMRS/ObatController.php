<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\Barang;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $barangs = Barang::where(function ($query) use ($request) {
            $query->where('nama_barang', "like", "%" . $request->search . "%");
        })
            ->paginate();

        $barang_total = Barang::count();
        return view('simrs.farmasi.obat', compact([
            'request',
            'barangs',
            'barang_total',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $barang = Barang::where('kode_barang', $id)->first();
        return response()->json($barang);
    }
    public function get_obats(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $barangs = Barang::orderby('kode_barang', 'asc')
                ->select('kode_barang', 'nama_barang')
                ->limit(10)->get();
        } else {
            $barangs = Barang::orderby('kode_barang', 'asc')
                ->select('kode_barang', 'nama_barang')
                ->where('kode_barang', 'like', '%' . $search . '%')
                ->orWhere('nama_barang', 'like', '%' . $search . '%')
                ->limit(10)->get();
        }
        $response = array();
        foreach ($barangs as $item) {
            $response[] = array(
                "id" => $item->kode_barang,
                "text" =>  $item->nama_barang
            );
        }
        return response()->json($response);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
