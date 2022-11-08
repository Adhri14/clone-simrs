<?php

namespace App\Http\Controllers\SatuSehat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $organization = null;
        if (isset($request->partOf)) {
            $response = $this->organization_part_of($request->partOf);
            if ($response->status() == 200) {
                $organization = json_decode($response->content());
                Alert::success('Success', 'Organization Ditemukan');
            } else {
                Alert::error('Error', $response->statusText());
            }
        }
        return view('satusehat.location', compact([
            'request',
            'organization'
        ]));
    }
}
