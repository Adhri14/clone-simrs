@extends('adminlte::page')

@section('title', 'Barcode')

@section('content_header')
    <h1>Barcode</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Tabel Data User" theme="secondary" collapsible>
                <h1>How to Generate Bar Code in Laravel? - ItSolutionStuff.com</h1>

                <h3>Product: 0001245259636</h3>
                @php
                    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
                @endphp
                {!! $generator->getBarcode('6368EC9FCBA4B', $generator::TYPE_CODE_39, 3, 100) !!}
                <h3>Product 2: 000005263635</h3>
                @php
                    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                @endphp

                <img
                    src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('000005263635', $generatorPNG::TYPE_CODE_128)) }}">

            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
