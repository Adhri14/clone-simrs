@extends('adminlte::page')
@section('title', 'Jadwal Dokter - Antrian BPJS')
@section('content_header')
    <h1 class="m-0 text-dark">Jadwal Dokter Antrian BPJS</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Pencarian Jadwal Dokter" theme="secondary" icon="fas fa-info-circle" collapsible>
                <form action="{{ route('bpjs.antrian.jadwal_dokter') }}">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tanggal" value="{{ $request->tanggal }}" placeholder="Silahkan Pilih Tanggal"
                        label="Tanggal Periksa" :config="$config" />
                    <x-adminlte-select2 name="kodepoli" id="kodepoli" label="Poliklinik">
                        @foreach ($polikliniks as $poli)
                            <option value="{{ $poli->kdsubspesialis }}"
                                {{ $request->kodepoli == $poli->kdsubspesialis ? 'selected' : null }}>
                                {{ $poli->kdsubspesialis }} - {{ $poli->nmsubspesialis }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-button label="Cari Jadwal Dokter" class="mr-auto withLoad" type="submit" theme="success"
                        icon="fas fa-search" />
                </form>
            </x-adminlte-card>
            <x-adminlte-card title="Referensi Jadwal Dokter Antrian BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['No.', 'Hari', 'Jadwal', 'Poliklinik', 'Subspesialis','Dokter','Kuota','Status','Action'];
                @endphp
                <x-adminlte-datatable id="table2" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @isset($jadwals)
                        @foreach ($jadwals as $jadwal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $jadwal->namahari }}</td>
                                <td>{{ $jadwal->jadwal }}</td>
                                <td>{{ $jadwal->namapoli }} - {{ $jadwal->kodepoli }}</td>
                                <td>{{ $jadwal->namasubspesialis }} - {{ $jadwal->kodesubspesialis }}</td>
                                <td>{{ $jadwal->namadokter }} - {{ $jadwal->kodedokter }}</td>
                                <td>{{ $jadwal->kapasitaspasien }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endisset
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Select2', true)
