<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorJPG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class BarcodeController extends Controller
{
    public function scanner(Request $request)
    {
        if (empty($request->barcode)) {
            $uniqid =  now()->month . str_pad(now()->day, 2, '0', STR_PAD_LEFT)  . '-' . strtoupper(Str::random(4));
            $request['barcode'] = $uniqid;
        }
        $generator = new BarcodeGeneratorJPG();
        QrCode::backgroundColor(225, 250, 255)->size(250)->format('png')->merge(asset('rsudwaled_icon_qrcode.png'), .15, true)->generate($request->barcode, "public/storage/qrcode_test.png");
        file_put_contents('storage/barcode_test.png', $generator->getBarcode($request->barcode, $generator::TYPE_CODE_128,  3, 100,));
        return view('admin.bar_qr_scanner', compact([
            'request',
        ]));
    }
}
