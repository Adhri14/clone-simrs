@extends('adminlte::page')
@section('title', 'Dokter Antrian BPJS - Pelayanan Medis')
@section('content_header')
    <h1 class="m-0 text-dark">Dokter Antrian BPJS - Pelayanan Medis</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Referensi Dokter Antrian BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['No', 'Nama Dokter', 'Kode Dokter', 'Action'];
                @endphp
                <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @foreach ($dokters as $dokter)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dokter->namaDokter }}</td>
                            <td>{{ $dokter->kodeDokter }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <a href="{{ route('pelayanan-medis.dokter_antrian_refresh') }}" class="btn btn-success">Refresh
                    Dokter</a>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
