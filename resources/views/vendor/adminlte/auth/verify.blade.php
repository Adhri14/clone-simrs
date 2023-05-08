@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', __('adminlte::adminlte.verify_message'))

@section('auth_body')

    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('adminlte::adminlte.verify_email_sent') }}
        </div>
    @endif
    Silahkan hubungi Adminstrator / Kepegawaian untuk memverifikasi akun anda. Klik tombol dibawah ini untuk meminta
    verifikasi.
    <br>
    <br>
    <label>Nama : {{ $user->name }}</label>
    <br>
    <label>Email : {{ $user->email }}</label>
    {{-- <label>Username : {{ $user->username }}</label> --}}
    <form class="d-inline" method="POST" action="{{ route('verifikasi_kirim') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $user->email }}">
        <x-adminlte-input name="username" value="{{ $user->username }}" label="Username"
            placeholder="Username" enable-old-support />
        <x-adminlte-input name="phone" value="{{ $user->phone }}" type="number" label="Nomor HP / Telepon"
            placeholder="Nomor HP yang dapat dihubungi" enable-old-support />
        <button type="submit" class="btn btn-sm btn-primary">
            Verifikasi Akun
        </button>
    </form>

@stop
