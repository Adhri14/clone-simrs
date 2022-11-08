<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

class LaravotLocationController extends Controller
{
    public function get_city(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $city = City::orderby('name', 'asc')
                ->where('province_code',  $request->code)
                ->select('code', 'name')
                ->limit(5)->get();
        } else {
            $city = City::orderby('name', 'asc')
                ->select('code', 'name')
                ->where('province_code',  $request->code)
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->limit(5)->get();
        }
        $response = array();
        foreach ($city as $item) {
            $response[] = array(
                "id" => $item->code,
                "text" => $item->code . ' - ' . $item->name
            );
        }
        return response()->json($response);
    }
    public function get_district(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $district = District::orderby('name', 'asc')
                ->where('city_code',  $request->code)
                ->select('code', 'name')
                ->limit(5)->get();
        } else {
            $district = District::orderby('name', 'asc')
                ->where('city_code',  $request->code)
                ->select('code', 'name')
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->limit(5)->get();
        }
        $response = array();
        foreach ($district as $item) {
            $response[] = array(
                "id" => $item->code,
                "text" => $item->code . ' - ' . $item->name
            );
        }
        return response()->json($response);
    }
    public function get_village(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $village = Village::orderby('name', 'asc')
                ->where('district_code',  $request->code)
                ->select('code', 'name')
                ->limit(5)->get();
        } else {
            $village = Village::orderby('name', 'asc')
                ->where('district_code',  $request->code)
                ->select('code', 'name')
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->limit(5)->get();
        }
        $response = array();
        foreach ($village as $item) {
            $response[] = array(
                "id" => $item->code,
                "text" => $item->code . ' - ' . $item->name
            );
        }
        return response()->json($response);
    }
}
