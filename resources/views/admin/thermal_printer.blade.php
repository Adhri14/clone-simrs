@extends('adminlte::page')
@section('title', 'Bar & QR Code Scanner')
@section('content_header')
    <h1>Bar & QR Code Scanner</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-6">
            <x-adminlte-card title="Modul Scanner Bar & QR Code" theme="secondary" collapsible>
                <h3>Thermal Printer</h3>
                Package :
                <a href="https://github.com/mike42/escpos-php" target="_blank">https://github.com/mike42/escpos-php</a>
                <br>
                <br>
                <form action="{{ route('thermal_print') }}" method="GET">
                    <x-adminlte-input name="printer_connector" value="{{ $request->printer_connector }}"
                        label="Printer Connector" placeholder="Printer Connector" enable-old-support />
                    <x-adminlte-button icon="fas fa-print" type="submit" theme="success" label="Test Print" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
