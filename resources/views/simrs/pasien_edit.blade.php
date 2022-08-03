@extends('adminlte::page')

@section('title', 'Edit Pasien')

@section('content_header')
    <h1>Edit Pasien {{ $pasien->no_rm }}</h1>
@stop

@section('content')
    @if ($errors->any())
        <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger" dismissable>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-adminlte-alert>
    @endif
    <form action="{{ route('pasien.store') }}" id="myform" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-input value="{{ $pasien->no_rm }}" name="no_rm" label="Nomor RM"
                    placeholder="Nomor Rekam Medis" enable-old-support required read-only />
                <x-adminlte-input value="{{ $pasien->nik_bpjs }}" name="nik" label="NIK"
                    placeholder="Nomor Induk Kependudukan" enable-old-support required />
                <x-adminlte-input value="{{ $pasien->nama_px }}" name="nama" label="Nama" placeholder="Nama Lengkap"
                    enable-old-support required />
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-input value="{{ $pasien->tempat_lahir }}" name="tempat_lahir" label="Tempat Lahir"
                            placeholder="Tempat Lahir" enable-old-support required />
                    </div>
                    <div class="col-md-6">
                        @php
                            $config = ['format' => 'DD-MM-YYYY'];
                        @endphp
                        <x-adminlte-input-date value="{{ $pasien->tgl_lahir }}" name="tanggal_lahir" label="Tanggal Lahir"
                            placeholder="Tanggal Lahir" :config="$config" enable-old-support required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-select name="gender" label="Jenis Kelamin" enable-old-support>
                            <x-adminlte-options :options="['L', 'P']" selected="{{ $pasien->jenis_kelamin }}"
                                placeholder="Jenis Kelamin" />
                        </x-adminlte-select>
                        <x-adminlte-select name="Agama" label="Agama" enable-old-support>
                            <x-adminlte-options :options="['Islam', 'Perempuan']" placeholder="Agama" />
                        </x-adminlte-select>
                        <x-adminlte-select name="perkawinan" label="Status Perkawinan" enable-old-support>
                            <x-adminlte-options :options="['Islam', 'Perempuan']" placeholder="Status Perkawinan" />
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-input name="pekerjaan" label="Pekerjaan" placeholder="Pekerjaan" enable-old-support />
                        <x-adminlte-input name="kewarganegaraan" label="Kewarganegaraan" placeholder="Kewarganegaraan"
                            enable-old-support />
                        <x-adminlte-select name="darah" label="Golongan Darah" enable-old-support>
                            <x-adminlte-options :options="['A', 'B', 'AB','O']" placeholder="Golongan Darah" />
                        </x-adminlte-select>
                    </div>
                </div>
                <x-adminlte-button theme="danger" label="Kembali" class="mr-1" data-dismiss="modal" />
                <x-adminlte-button form="myform" type="submit" theme="success" label="Simpan" />
            </div>
        </div>
    </form>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
