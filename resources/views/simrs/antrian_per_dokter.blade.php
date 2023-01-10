@extends('adminlte::page')
@section('title', 'Antrian Per Jadwal Dokter')
@section('content_header')
    <h1>Antrian Per Jadwal Dokter</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-12">
                            <x-adminlte-select2 name="jadwaldokter" id="jadwaldokter" label="Jadwal Dokter">
                                @foreach ($jadwaldokter as $item)
                                    <option value="{{ $item->id }}">{{ $item->namahari }} -
                                        {{ $item->kodesubspesialis }} -
                                        {{ $item->namadokter }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-md-12">
            <x-adminlte-card title="Data Waktu Antrian" theme="primary" icon="fas fa-info-circle" collapsible>
                @php
                    $heads = ['Tanggal', 'Antrean', 'Pasien', 'No HP', 'Poliklinik', 'No Referensi', 'Estimasi', 'Created at', 'Status'];
                @endphp
                <x-adminlte-datatable id="table1" class="nowrap" :heads="$heads" bordered hoverable compressed>
                    {{ date_default_timezone_set('Asia/Jakarta') }}
                    @if (isset($antrians))
                        @foreach ($antrians as $item)
                            <tr>
                                <td>{{ date('Y-m-d H:i:s', $item->tanggal / 1000) }} </td>
                                <td>
                                    {{ $item->noantrean }} <br>
                                    {{ $item->kodebooking }}<br>
                                </td>
                                <td>
                                    {{ $item->norekammedis }} <br>
                                    {{ $item->nokapst }}
                                </td>
                                <td>{{ $item->nohp }}</td>
                                <td>
                                    {{ $item->kodepoli }} {{ $item->jampraktek }} <br>
                                    {{ $item->kodedokter }}
                                </td>
                                <td>{{ $item->jeniskunjungan }} <br>
                                    {{ $item->nomorreferensi }}
                                </td>
                                <td>{{ date('Y-m-d H:i:s', $item->estimasidilayani / 1000) }} </td>
                                <td>{{ date('Y-m-d H:i:s', $item->createdtime / 1000) }} <br>{{ $item->sumberdata }}</td>
                                <td>{{ $item->status }} {{ $item->ispeserta }}</td>
                            </tr>
                        @endforeach
                    @endif
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.DateRangePicker', true)
