@extends('adminlte::page')
@section('title', 'Obat')
@section('content_header')
    <h1>Obat</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Referensi Obat SIMRS" theme="secondary" collapsible>
                @php
                    $heads = ['No', 'Nama Dokter', 'Kode Dokter', 'Action'];
                @endphp
                <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    {{-- @foreach ($dokters as $dokter)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dokter->namaDokter }}</td>
                            <td>{{ $dokter->kodeDokter }}</td>
                            <td></td>
                        </tr>
                    @endforeach --}}
                </x-adminlte-datatable>
                {{-- <a href="{{ route('pelayanan-medis.dokter_antrian_refresh') }}" class="btn btn-success">Refresh
                    Dokter</a> --}}
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
