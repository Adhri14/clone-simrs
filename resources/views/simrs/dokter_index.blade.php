@extends('adminlte::page')

@section('title', 'Referensi Dokter')

@section('content_header')
    <h1>Referensi Dokter</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Data Dokter" theme="secondary" collapsible>
                <div class="row">
                    <div class="col-md-8">
                        <x-adminlte-button label="Tambah" class="btn-sm" theme="success" title="Tambah Pasien"
                            icon="fas fa-plus" data-toggle="modal" data-target="#modalCustom" />
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('simrs.dokter.index') }}" method="get">
                            <x-adminlte-input name="search" placeholder="Pencarian NIK / Nama" igroup-size="sm"
                                value="{{ $request->search }}">
                                <x-slot name="appendSlot">
                                    <x-adminlte-button type="submit" theme="outline-primary" label="Cari" />
                                </x-slot>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text text-primary">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </form>
                    </div>
                </div>
                @php
                    $heads = ['Kode SIMRS', 'Nama', 'Kode BPJS', 'Spesialis', 'Unit', 'SIP', 'Action'];
                    $config['paging'] = false;
                    $config['lengthMenu'] = false;
                    $config['searching'] = false;
                    $config['info'] = false;
                    $config['responsive'] = true;
                @endphp
                <x-adminlte-datatable id="table2" :heads="$heads" :config="$config" hoverable bordered compressed>
                    @foreach ($paramedis as $item)
                        <tr>
                            <td>{{ $item->kode_paramedis }}</td>
                            <td>{{ $item->nama_paramedis }}</td>
                            <td>{{ $item->kode_dokter_jkn }}</td>
                            <td>{{ $item->spesialis }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->sip_dr }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <div class="row">
                    <div class="col-md-5">
                        Tampil data {{ $paramedis->firstItem() }} sampai {{ $paramedis->lastItem() }} dari total
                        {{ $total_paramedis }}
                    </div>
                    <div class="col-md-7">
                        <div class="float-right pagination-sm">
                            {{ $paramedis->links() }}
                        </div>
                    </div>
                </div>
            </x-adminlte-card>
            <x-adminlte-card title="Data Dokter BPJS" theme="info" icon="fas fa-info-circle" collapsible maximizable>
                @php
                    $heads = ['Kode Dokter', 'Nama Dokter'];
                @endphp
                <x-adminlte-datatable id="table1" :heads="$heads" striped bordered hoverable compressed>
                    @foreach ($dokters as $item)
                        <tr>
                            <td>{{ $item->kodedokter }}</td>
                            <td>{{ $item->namadokter }}</td>
                            {{-- <td>
                                @if ($item->status == 1)
                                    <a href="{{ route('dokter.show', $item->kodedokter) }}">
                                        <x-adminlte-button class="btn-xs" type="button" label="aktif" theme="success" />
                                    </a>
                                @else
                                    <a href="{{ route('dokter.show', $item->kodedokter) }}">
                                        <x-adminlte-button class="btn-xs" type="button" label="nonaktif" theme="danger" />
                                    </a>
                                @endif
                            </td> --}}
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <a href="{{ route('simrs.dokter.create') }}" class="btn btn-success">Refresh</a>
            </x-adminlte-card>

        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
