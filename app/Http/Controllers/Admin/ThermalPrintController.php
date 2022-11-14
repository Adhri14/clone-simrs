<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use RealRashid\SweetAlert\Facades\Alert;

class ThermalPrintController extends Controller
{
    public function thermal_printer(Request $request)
    {
        return view('admin.thermal_printer', compact([
            'request',
        ]));
    }
    public function thermal_print()
    {
        try {
            $printer_connector = "EPSON TM-T82X Receipt";
            $connector = new WindowsPrintConnector($printer_connector);
            $printer = new Printer($connector);
            $printer->text("Test Printer\n");
            $printer->text("Printer Connector : " . $printer_connector . "\n");
            $printer->barcode('BARCODE');
            $printer->qrCode('QRCODE');
            $printer->setEmphasis(true);
            $printer->text("setEmphasis true\n");
            $printer->setEmphasis(false);
            $printer->text("setEmphasis false\n");
            $printer->setFont(2);
            $printer->text("setFont 2\n");
            $printer->setFont(1);
            $printer->text("setFont 1\n");
            $printer->setFont(0);
            $printer->text("setFont 0\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("setJustification JUSTIFY_RIGHT\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("setJustification JUSTIFY_CENTER\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("setJustification JUSTIFY_LEFT\n");
            for ($i = 1; $i <= 8; $i++) {
                $printer->setTextSize($i, $i);
                $printer->text($i . "\n");
            }
            $printer->cut();
            $printer->close();
            Alert::success('Success', 'Test Printer Berhasil');
        } catch (Exception $e) {
            Alert::error('Error', 'Test Printer Error ' . $e->getMessage());
        }
        return redirect()->route('thermal_printer');
    }
}
