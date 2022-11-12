<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorJPG;

class BarcodeController extends Controller
{
    public function index()
    {



        $generator = new BarcodeGeneratorJPG();
        file_put_contents('barcode.jpg', $generator->getBarcode('081231723897', $generator::TYPE_CODE_39,  3, 100,));
        return view('admin.barcode');
    }
}
