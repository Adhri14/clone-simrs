@extends('adminlte::page')
@section('title', 'Whatsapp API')
@section('content_header')
    <h1>Whatsapp API</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-6">
            <x-adminlte-card title="Modul Scanner Bar & QR Code" theme="secondary" collapsible>
                <h3>Whastapp API</h3>
                Package :
                <a href="https://wwebjs.dev" target="_blank">https://wwebjs.dev</a>
                <br>
                Whatsapp URL :
                <a href="http://{{ env('WHATASAPP_URL') }}" target="_blank">http://{{ env('WHATASAPP_URL') }}</a>
                <br>
                <br>
                <form action="{{ route('whatsapp') }}" method="GET">
                    <x-adminlte-input name="number" value="{{ $request->number }}" label="Send To Number" />
                    <x-adminlte-textarea name="message" label="Message" placeholder="Message Body" enable-old-support>
                        {{ $request->message }}
                    </x-adminlte-textarea>
                    <x-adminlte-button icon="fas fa-paper-plane" type="submit" theme="success" label="Send Test Message" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
