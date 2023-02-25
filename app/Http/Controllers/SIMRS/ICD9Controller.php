<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\SIMRS\ICD9;
use Illuminate\Http\Request;

class ICD9Controller extends Controller
{
    public function get_icd9(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $icd10 = ICD9::orderby('diag', 'asc')
                ->select('diag', 'nama_panjang')
                ->limit(5)->get();
        } else {
            $icd10 = ICD9::orderby('diag', 'asc')
                ->select('diag', 'nama_panjang')
                ->where('diag', 'like', '%' . $search . '%')
                ->orWhere('nama_panjang', 'like', '%' . $search . '%')
                ->limit(5)->get();
        }
        $response = array();
        foreach ($icd10 as $item) {
            $response[] = array(
                "id" => $item->diag,
                "text" => $item->diag . ' ' . $item->nama_panjang
            );
        }
        return response()->json($response);
    }
}
