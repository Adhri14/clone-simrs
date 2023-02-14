@extends('adminlte::page')
@section('title', 'Poliklinik - Antrian BPJS')
@section('content_header')
    <h1>Poliklinik Antrian BPJS</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Referensi Poliklinik Antrian BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['No', 'Nama Subspesialis', 'Kode Subspesialis', 'Nama Poli', 'Kode Poli', 'Status', 'Action'];
                @endphp
                <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @foreach ($polikliniks as $poliklinik)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $poliklinik->nmsubspesialis }}</td>
                            <td>{{ $poliklinik->kdsubspesialis }}</td>
                            <td>{{ $poliklinik->nmpoli }}</td>
                            <td>{{ $poliklinik->kdpoli }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
            <x-adminlte-card title="Referensi Poliklinik Fingerprint Antrian BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['No', 'Nama Subspesialis', 'Kode Subspesialis', 'Nama Poli', 'Kode Poli', 'Status', 'Action'];
                @endphp
                <x-adminlte-datatable id="table2" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @foreach ($fingerprint as $poliklinik)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $poliklinik->namasubspesialis }}</td>
                            <td>{{ $poliklinik->kodesubspesialis }}</td>
                            <td>{{ $poliklinik->namapoli }}</td>
                            <td>{{ $poliklinik->kodepoli }}</td>
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
