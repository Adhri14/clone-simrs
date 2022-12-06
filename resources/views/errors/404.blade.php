@extends('adminlte::page')
@section('title', 'Halaman Tidak Ditemukan (404)')
@section('content_header')
    <h1>Halaman Tidak Ditemukan (404)</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            Silahkan periksa kembali link anda
            <br>
            <a href="{{ route('home') }}" class="btn btn-danger">Kembali Menu Utama</a>
        </div>
    </div>
@stop
