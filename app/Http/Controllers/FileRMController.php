<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Imagick;

class FileRMController extends Controller
{
    public function index()
    {
        $file = "2022-10-11_15-57-18.884.tif";
        $im = new Imagick($file);
        $im->setImageFormat('PNG');
        // $format = $im->getImageFormat();
        // dd($format);
        $im_blob =  $im->getImagesBlob();
        // dd($im_blob);
        // echo '<img src="data:image/jpg;base64,' . base64_encode($im_blob) . '" />';

        return view('simrs.rekammedis.scanfile', [
            'im_blob' => $im_blob,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {
        //
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
