@extends('adminlte::page')

@section('title', 'Barcode')

@section('content_header')
    <h1>Barcode</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Contoh Modul Barcode & QR Code" theme="secondary" collapsible>
                <h3>Barcode : {{ $request->barcode }}</h3>
                @php
                    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                @endphp
                <img
                    src="data:image/png; base64, {{ base64_encode($generatorPNG->getBarcode($request->barcode, $generatorPNG::TYPE_CODE_128, 3, 100)) }}">
                <br>
                <br>
                <h3>QR Code : {{ $request->barcode }}</h3>
                <img src="{{ asset('qrcode_test.png') }}" alt="">
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
