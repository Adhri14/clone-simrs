@extends('adminlte::print')
@section('title', '404 Error')
@section('content_header')
    <h1>404 Error</h1>
@stop
@section('content')
    <div class="error-page">
        <h2 class="headline text-warning"> 404</h2>
        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Halaman Tidak Ditemukan.</h3>
            <p>
                Kami tidak menemukan halaman yang anda cari.
                <br>
                Silahkan Perika kembali alamat Url anda.
                <br>
                Atau kembali ke <a href="">Halaman Utama</a>
            </p>
            <a href="{{ url()->previous() }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="{{ route('landingpage') }}" class="btn btn-warning"><i class="fas fa-home"></i> Halaman Awal</a>
        </div>
    </div>
@stop
