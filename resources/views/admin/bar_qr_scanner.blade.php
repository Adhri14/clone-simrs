@extends('adminlte::page')
@section('title', 'Bar & QR Code Scanner')
@section('content_header')
    <h1>Bar & QR Code Scanner</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-6">
            <x-adminlte-card title="Modul Bar & QR Code" theme="secondary" collapsible>
                <h3>Barcode : {{ $request->barcode }}</h3>
                @php
                    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                @endphp
                <img src="{{ asset('storage/barcode_test.png') }}" alt="">
                <br>
                Package :
                <a href="https://github.com/picqer/php-barcode-generator"
                    target="_blank">https://github.com/picqer/php-barcode-generator</a>
                <br>
                <h3>QR Code : {{ $request->barcode }}</h3>
                <img src="{{ asset('storage/qrcode_test.png') }}" alt="">
                <br>
                Package :
                <a href="https://www.simplesoftware.io/#/docs/simple-qrcode"
                    target="_blank">https://www.simplesoftware.io/#/docs/simple-qrcode</a>
            </x-adminlte-card>
        </div>
        <div class="col-6">
            <x-adminlte-card title="Contoh Modul Scanner Bar & QR Code" theme="secondary" collapsible>
                <h3>OnScan JS</h3>
                <i class="fas fa-qrcode fa-10x"></i>
                <h4>Silahkan Scan Bar & QR Code</h4>
                Package :
                <a href="https://github.com/axenox/onscan.js/" target="_blank">https://github.com/axenox/onscan.js/</a>
                <br>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('js')
    <script src="{{ asset('vendor/onscan.js/onscan.min.js') }}"></script>
    {{-- scan --}}
    <script>
        $(function() {
            onScan.attachTo(document, {
                onScan: function(sCode, iQty) {
                    alert(sCode + ' ' + iQty);
                },
            });
        });
    </script>
@endsection
