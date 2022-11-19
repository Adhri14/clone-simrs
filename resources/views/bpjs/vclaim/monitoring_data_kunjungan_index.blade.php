@extends('adminlte::page')
@section('title', 'Monitoring Data Kunjungan - Vclaim BPJS')
@section('content_header')
    <h1>Monitoring Data Kunjungan - Vclaim BPJS </h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Kunjungan BPJS" theme="secondary" collapsible>
                <form action="" method="get">
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
                    <x-adminlte-select2 name="jenispelayanan" label="Jenis Pelayanan">
                        <option value="1" {{ $request->jenispelayanan == 1 ? 'selected' : null }}>Rawat Inap</option>
                        <option value="2" {{ $request->jenispelayanan == 2 ? 'selected' : null }}>Rawat Jalan</option>
                    </x-adminlte-select2>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Data Kunjungan" />
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-12">
            <x-adminlte-card title="Data Kunjungan BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['No SEP', 'Tgl Masuk', 'Tgl Pulang', 'Jenis Pelayanan', 'Kelas Rawat', 'No Kartu BPJS', 'Nama', 'Poliklik', 'Diagnosa', 'No Rujukan'];
                @endphp
                <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" bordered hoverable compressed>
                    @isset($sep)
                        @foreach ($sep as $item)
                            <tr>
                                <td>{{ $item->noSep }}</td>
                                <td>{{ $item->tglSep }}</td>
                                <td>{{ $item->tglPlgSep }}</td>
                                <td>{{ $item->jnsPelayanan }}</td>
                                <td>{{ $item->kelasRawat }}</td>
                                <td>{{ $item->noKartu }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->poli }}</td>
                                <td>{{ $item->diagnosa }}</td>
                                <td>{{ $item->noRujukan }}</td>
                            </tr>
                        @endforeach
                    @endisset
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
