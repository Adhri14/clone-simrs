<?php

namespace App\Http\Controllers\SIMRS;

use App\Http\Controllers\Controller;
use App\Models\ICD10;
use Illuminate\Http\Request;

class ICD10Controller extends Controller
{
    public function get_icd10(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $icd10 = ICD10::orderby('diag', 'asc')
                ->select('diag', 'nama')
                ->limit(5)->get();
        } else {
            $icd10 = ICD10::orderby('diag', 'asc')
                ->select('diag', 'nama')
                ->where('diag', 'like', '%' . $search . '%')
                ->orWhere('nama', 'like', '%' . $search . '%')
                ->limit(5)->get();
        }
        $response = array();
        foreach ($icd10 as $item) {
            $response[] = array(
                "id" => $item->diag,
                "text" => $item->diag . ' - ' . $item->nama
            );
        }
        return response()->json($response);
    }

}
