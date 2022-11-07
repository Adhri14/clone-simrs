@extends('adminlte::page')

@section('title', 'Status Bridging Satu Sehat')

@section('content_header')
    <h1 class="m-0 text-dark">Status Bridging Satu Sehat</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <b>Auth URL :</b> {{ env('SATUSEHAT_AUTH_URL') }} <br>
            <b>Base URL :</b> {{ env('SATUSEHAT_BASE_URL') }} <br>
            <b>Client ID :</b> {{ Str::mask(env('SATUSEHAT_CLIENT_ID'), '*', -20, 15) }} <br>
            <b>Secret ID :</b> {{ Str::mask(env('SATUSEHAT_SECRET_ID'), '*', -20, 15) }} <br>
            <b>Token :</b> {{ Str::mask(session()->get('tokenSatuSehat'), '*', -20, 15) }} <br>
            <b>Timestamp :</b> {{ session()->get('TimestampSatuSehat') }} <br> <br>
            <x-adminlte-button theme="warning" icon="fas fa-sync" label="Refresh Token" title="Refresh Token"
                onclick="window.location='{{ route('satusehat.refresh_token') }}'" />
        </div>
    </div>
@stop
