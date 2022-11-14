@extends('adminlte::page')
@section('title', 'Bar & QR Code Scanner')
@section('content_header')
    <h1>Bar & QR Code Scanner</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-6">
            <x-adminlte-card title="Contoh Modul Scanner Bar & QR Code" theme="secondary" collapsible>
                <h3>Thermal Printer</h3>
                <a href="{{ route('thermal_print') }}" class="btn btn-success"><i class="fas fa-print"></i> Test Printer</a>
                <br>
                Package :
                <a href="https://github.com/mike42/escpos-php" target="_blank">https://github.com/mike42/escpos-php</a>
                <br>
            </x-adminlte-card>
        </div>
    </div>
@stop

