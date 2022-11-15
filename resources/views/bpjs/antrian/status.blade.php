@extends('adminlte::page')

@section('title', 'Status Bridging - Antrian BPJS')

@section('content_header')
    <h1 class="m-0 text-dark">Status Bridging Antrian BPJS</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <b>Base URL :</b> {{ env('ANTRIAN_URL') }} <br>
            <b>Cons ID :</b> {{ env('ANTRIAN_CONS_ID') }} <br>
            <b>Secret Key :</b> {{ Str::mask(env('ANTRIAN_SECRET_KEY'), '*', -7, 4) }} <br>
            <b>User Key :</b> {{ Str::mask(env('ANTRIAN_USER_KEY'), '*', -20, 15) }} <br>
        </div>
    </div>
@stop
