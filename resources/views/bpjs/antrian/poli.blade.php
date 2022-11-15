@extends('adminlte::page')
@section('title', 'Poliklinik - Antrian BPJS')
@section('content_header')
    <h1 class="m-0 text-dark">Poliklinik Antrian BPJS</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Data Location SIMRS" theme="secondary" collapsible>
                @php
                    $heads = ['No', 'Nama Subspesialis', 'Kode Subspesialis', 'Nama Poli', 'Kode Poli', 'Status','Action'];
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
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
