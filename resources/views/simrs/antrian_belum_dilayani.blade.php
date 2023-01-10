@extends('adminlte::page')
@section('title', 'Antrian Belum Dilayani')
@section('content_header')
    <h1>Antrian Belum Dilayani</h1>
@stop

@section('content')
    <div class="row">
        {{-- <div class="col-md-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $config = ['format' => 'YYYY-MM-DD'];
                            @endphp
                            <x-adminlte-input-date name="tanggal" label="Tanggal Laporan" value="{{ $request->tanggal }}"
                                placeholder="Pilih Tanggal" :config="$config">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                </form>
            </x-adminlte-card>
        </div> --}}
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
                                <td>{{ $item->tanggal }}</td>
                                <td>
                                    {{ $item->kodebooking }}<br>
                                    {{ $item->noantrean }}
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

@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.DateRangePicker', true)
