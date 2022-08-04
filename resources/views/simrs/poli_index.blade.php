@extends('adminlte::page')

@section('title', 'Referensi Poliklinik')

@section('content_header')
    <h1>Referensi Poliklinik</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-7">
            <x-adminlte-card title="Poliklinik Aktif RSUD Waled" theme="info" icon="fas fa-info-circle" collapsible
                maximizable>
                @php
                    $heads = ['Kode Poli', 'Nama Poli', 'Subspesialis', 'Kode Subspesialis', 'Nama Subpesialis', 'Lantai', 'Status'];
                @endphp
                <x-adminlte-datatable id="table1" :heads="$heads" striped bordered hoverable compressed>
                    @foreach ($polis->where('status', 1) as $item)
                        <tr>
                            <td>{{ $item->kodepoli }}</td>
                            <td>{{ $item->namapoli }}</td>
                            <td>
                                @if ($item->subspesialis)
                                    Ya <i class="fas fa-check-circle text-success"></i>
                                @else
                                    Bukan <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </td>
                            <td>{{ $item->kodesubspesialis }}</td>
                            <td>{{ $item->namasubspesialis }}</td>
                            <td>{{ $item->lantai }}</td>
                            <td>
                                @if ($item->status == 1)
                                    <a href="{{ route('poli.edit', $item->id) }}">
                                        <x-adminlte-button class="btn-xs" type="button" label="aktif" theme="success"
                                            title="Klik untuk non-aktifkan" />
                                    </a>
                                @else
                                    <a href="{{ route('poli.edit', $item->id) }}">
                                        <x-adminlte-button class="btn-xs" type="button" label="nonaktif" theme="danger"
                                            data-toggle="tooltop" title="Klik untuk aktifkan" />
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
        <div class="col-md-5">
            <x-adminlte-card title="Data Semua Poliklinik" theme="info" icon="fas fa-info-circle" collapsible maximizable>
                @php
                    $heads = ['Kode Poli', 'Nama Poli', 'Subspesialis', 'Status'];
                @endphp
                <x-adminlte-datatable id="table3" :heads="$heads" striped bordered hoverable compressed>
                    @foreach ($polis as $item)
                        <tr>
                            <td>{{ $item->kodesubspesialis }}</td>
                            <td>{{ $item->namasubspesialis }}</td>
                            <td>
                                @if ($item->subspesialis)
                                    Ya <i class="fas fa-check-circle text-success"></i>
                                @else
                                    Bukan <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 1)
                                    <a href="{{ route('poli.edit', $item->id) }}">
                                        <x-adminlte-button class="btn-xs" type="button" label="aktif" theme="success"
                                            title="Klik untuk non-aktifkan" />
                                    </a>
                                @else
                                    <a href="{{ route('poli.edit', $item->id) }}">
                                        <x-adminlte-button class="btn-xs" type="button" label="nonaktif" theme="danger"
                                            data-toggle="tooltop" title="Klik untuk aktifkan" />
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <a href="{{ route('poli.create') }}" class="btn btn-success">Refresh</a>
            </x-adminlte-card>
        </div>
        {{-- <div class="col-md-12">
            <x-adminlte-card title="Informasi Referensi Poliklinik" theme="info" icon="fas fa-info-circle" collapsible
                maximizable>
                <x-adminlte-select2 name="poli" id="poli" label="Poliklinik">
                    @foreach ($polis as $item)
                        @if ($item->kodepoli == $item->kodesubspesialis)
                            <option>{{ $item->kodepoli }} - {{ $item->namapoli }}
                            </option>
                        @endif
                    @endforeach
                </x-adminlte-select2>
                <x-adminlte-select2 name="subsplesialis" id="subsplesialis" label="Poli Subspesialis">
                    @foreach ($polis as $item)
                        @if ($item->kodepoli != $item->kodesubspesialis)
                            <option>{{ $item->kodesubspesialis }} - {{ $item->namasubspesialis }}
                            </option>
                        @endif
                    @endforeach
                </x-adminlte-select2>
            </x-adminlte-card>
        </div> --}}
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
