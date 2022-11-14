@extends('adminlte::page')

@section('title', 'Bar & QR Code')

@section('content_header')
    <h1>Bar & QR Code</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Contoh Modul Bar & QR Code" theme="secondary" collapsible>
                <h3>Barcode : {{ $request->barcode }}</h3>
                @php
                    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                @endphp
                <img
                    src="data:image/png; base64, {{ base64_encode($generatorPNG->getBarcode($request->barcode, $generatorPNG::TYPE_CODE_128, 3, 100)) }}">

                <br>
                Package :
                <a
                    href="https://github.com/picqer/php-barcode-generator" target="_blank">https://github.com/picqer/php-barcode-generator</a>

                <br>
                <h3>QR Code : {{ $request->barcode }}</h3>
                <img src="{{ asset('qrcode_test.png') }}" alt="">
                <br>
                Package :
                <a
                    href="https://www.simplesoftware.io/#/docs/simple-qrcode" target="_blank">https://www.simplesoftware.io/#/docs/simple-qrcode</a>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
