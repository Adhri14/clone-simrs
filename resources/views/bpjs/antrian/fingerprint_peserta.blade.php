@extends('adminlte::page')

@section('title', 'Cek Fingerprint Peserta')

@section('content_header')
    <h1>Cek Fingerprint Peserta</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Fingerprint Peserta" theme="secondary" collapsible>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" id="myform" method="get">
                            <x-adminlte-input name="nomorkartu" label="Nomor Kartu" value="{{ $request->nomorkartu }}"
                                placeholder="Pencarian Berdasarkan Nomor Kartu BPJS">
                                <x-slot name="appendSlot">
                                    <x-adminlte-button theme="success" class="withLoad" type="submit" label="Cari!" />
                                </x-slot>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text text-success">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </form>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
        @isset($peserta)
            <div class="col-md-4">
                <x-adminlte-profile-widget name="{{ $peserta->nik }}" desc="{{ $peserta->nomorkartu }}" theme="lightblue"
                    layout-type="classic">
                    <dl class="row">
                        <dt class="col-sm-4">Tgl Lahir</dt>
                        <dd class="col-sm-8">{{ $peserta->tgllahir }}</dd>
                        <dt class="col-sm-4">Status </dt>
                        <dd class="col-sm-8">{{ $peserta->daftarfp ? 'Sudah Fingerprint' : 'Belum Fingerprint' }}</dd>
                    </dl>
                </x-adminlte-profile-widget>
            </div>
        @endisset
    </div>
@stop

@section('plugins.Datatables', true)
