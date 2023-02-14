@extends('adminlte::page')
@section('title', 'Antrian Per Kodebooking')
@section('content_header')
    <h1>Antrian Per Kodebooking</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Fingerprint Peserta" theme="secondary" collapsible>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" id="myform" method="get">
                            <x-adminlte-input name="kodebooking" label="Kode Booking" value="{{ $request->kodebooking }}"
                                placeholder="Pencarian Berdasarkan Kodebooking">
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
        @isset($antrian)
            <div class="col-md-4">
                <x-adminlte-profile-widget name="{{ $antrian->norekammedis }}" desc="{{ $antrian->norekammedis }}"
                    theme="lightblue" layout-type="classic">
                    <dl class="row">
                        <dt class="col-sm-4">NIK Pasien</dt>
                        <dd class="col-sm-8">{{ $antrian->nik }}</dd>
                        <dt class="col-sm-4">Kartu BPJS</dt>
                        <dd class="col-sm-8">{{ $antrian->nokapst }}</dd>
                        <dt class="col-sm-4">No HP</dt>
                        <dd class="col-sm-8">{{ $antrian->nohp }}</dd>


                        <dt class="col-sm-4">Jenis Kunjungan</dt>
                        <dd class="col-sm-8">{{ $antrian->jeniskunjungan }}</dd>
                        <dt class="col-sm-4">No Referensi</dt>
                        <dd class="col-sm-8">{{ $antrian->nomorreferensi }}</dd>
                        <dt class="col-sm-4">Kode Booking</dt>
                        <dd class="col-sm-8">{{ $antrian->kodebooking }}</dd>
                        <dt class="col-sm-4">No Antrean</dt>
                        <dd class="col-sm-8">{{ $antrian->noantrean }}</dd>
                        <dt class="col-sm-4">Kode Poliklinik</dt>
                        <dd class="col-sm-8">{{ $antrian->kodepoli }}</dd>
                        <dt class="col-sm-4">Kode Dokter</dt>
                        <dd class="col-sm-8">{{ $antrian->kodedokter }}</dd>
                        <dt class="col-sm-4">Jam Praktek</dt>
                        <dd class="col-sm-8">{{ $antrian->jampraktek }}</dd>
                        <dt class="col-sm-4">Tanggal Kunjungan</dt>
                        <dd class="col-sm-8">{{ $antrian->tanggal }}</dd>
                        <dt class="col-sm-4">Status </dt>
                        <dd class="col-sm-8">{{ $antrian->status }}</dd>

                        {{-- <dd class="col-sm-8">{{ $antrian->daftarfp ? 'Sudah Fingerprint' : 'Belum Fingerprint' }}</dd>
                        <dt class="col-sm-4">Status </dt>
                        <dd class="col-sm-8">{{ $antrian->daftarfp ? 'Sudah Fingerprint' : 'Belum Fingerprint' }}</dd> --}}
                    </dl>
                </x-adminlte-profile-widget>
            </div>
        @endisset

    </div>
@stop

@section('plugins.Datatables', true)
