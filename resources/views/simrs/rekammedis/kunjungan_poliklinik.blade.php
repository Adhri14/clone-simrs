@extends('adminlte::page')

@section('title', 'Kunjungan Poliklinik')

@section('content_header')
    <h1>Kunjungan Poliklinik</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Kunjungan" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $config = ['format' => 'YYYY-MM-DD'];
                            @endphp
                            <x-adminlte-input-date name="tanggal" label="Tanggal Antrian" :config="$config"
                                value="{{ \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d') }}">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-select2 name="kodepoli" label="Poliklinik">
                                @foreach ($units as $item)
                                    <option value="{{ $item->kode_unit }}"
                                        {{ $item->kode_unit == $request->kode_unit ? 'selected' : null }}>
                                        {{ $item->kode_unit }}
                                        -
                                        {{ $item->nama_unit }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                </form>
            </x-adminlte-card>

            <x-adminlte-card title="Data Kunjungan Poliklinik" theme="secondary" collapsible>
                @php
                    $heads = ['No', 'Tanggal Pelayanan', 'Nama Pasien', 'No SEP', 'Ceklist'];
                @endphp
                <x-adminlte-datatable id="table1" class="nowrap" :heads="$heads" bordered hoverable compressed>
                    @foreach ($kunjungans as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->tgl_masuk }}</td>
                            <td>{{ $item->pasien->nama_px }}</td>
                            <td>{{ $item->no_sep }}</td>
                            <td>{{ $item->status->status_kunjungan }}</td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop
