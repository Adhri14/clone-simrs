@extends('adminlte::page')
@section('title', 'Dokter - Antrian BPJS')
@section('content_header')
    <h1 class="m-0 text-dark">Dokter Antrian BPJS</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
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
