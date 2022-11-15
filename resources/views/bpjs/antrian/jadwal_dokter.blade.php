@extends('adminlte::page')
@section('title', 'Jadwal Dokter - Antrian BPJS')
@section('content_header')
    <h1 class="m-0 text-dark">Jadwal Dokter Antrian BPJS</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Pencarian Jadwal Dokter" theme="info" icon="fas fa-info-circle" collapsible>
                <form name="formJadwalHafiz" id="formJadwalHafiz" action="{{ route('jadwaldokter.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="method" value="GET">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tanggal" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                        label="Tanggal Periksa" :config="$config" />
                    <x-adminlte-select2 name="kodepoli" id="kodepoli" label="Poliklinik">
                        @foreach ($polikliniks as $poli)
                            <option value="{{ $poli->kdsubspesialis }}">{{ $poli->kdsubspesialis }} - {{ $poli->nmsubspesialis }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-button label="Get Jadwal Dokter" form="formJadwalHafiz" class="mr-auto" type="submit"
                        theme="success" icon="fas fa-download" />
                </form>
            </x-adminlte-card>
            <x-adminlte-card title="Referensi Dokter Antrian BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['No', 'Nama Dokter', 'Kode Dokter', 'Status', 'Action'];
                @endphp
                <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @foreach ($dokters as $dokter)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dokter->namadokter }}</td>
                            <td>{{ $dokter->kodedokter }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Select2', true)

