@extends('adminlte::page')
@section('title', 'Monitoring Klaim Jasa Raharja - Vclaim BPJS')
@section('content_header')
    <h1>Monitoring Klaim Jasa Raharja - Vclaim BPJS </h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Klaim BPJS" theme="secondary" collapsible>
                <form action="" method="get">
                    @php
                        $config = [
                            'locale' => ['format' => 'YYYY/MM/DD'],
                        ];
                    @endphp
                    <x-adminlte-date-range name="tanggal" label="Periode Tanggal Antrian" :config="$config"
                        value="{{ $request->tanggal }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-primary">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-date-range>
                    <x-adminlte-select2 name="jenisPelayanan" label="Jenis Pelayanan">
                        <option value="1" {{ $request->jenisPelayanan == 1 ? 'selected' : null }}>Rawat Inap</option>
                        <option value="2" {{ $request->jenisPelayanan == 2 ? 'selected' : null }}>Rawat Jalan</option>
                    </x-adminlte-select2>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Data Kunjungan" />
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-12">
            <x-adminlte-card title="Data Klaim BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['SEP', 'Tgl SEP', 'Pasien', 'Pelayanan', 'Diagnosa', 'Tgl Kejadian', 'B. Dijamin','Plafon','Jml Dbayar','Keterangan'];
                @endphp
                <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" bordered hoverable compressed>
                    @php
                        $byTarifRS = 0;
                        $byTarifGruper = 0;
                        $byTopup = 0;
                        $byPengajuan = 0;
                        $bySetujui = 0;
                    @endphp
                    @isset($klaim)
                        @foreach ($klaim as $item)
                            {{-- {{ dd($item) }} --}}
                            <tr>
                                <td>{{ $item->sep->noSEP }}</td>
                                <td>{{ $item->sep->tglSEP }}</td>
                                <td>{{ $item->sep->peserta->nama }}</td>
                                <td>{{ $item->sep->jnsPelayanan }} {{ $item->sep->poli }}</td>
                                <td>{{ $item->sep->diagnosa }}</td>
                                <td>{{ $item->jasaRaharja->tglKejadian }}</td>
                                <td>{{ $item->jasaRaharja->biayaDijamin }}</td>
                                <td>{{ $item->jasaRaharja->plafon }}</td>
                                <td>{{ $item->jasaRaharja->jmlDibayar }}</td>
                                <td>{{ $item->jasaRaharja->resultsJasaRaharja }} {{ $item->jasaRaharja->ketStatusDikirim }} {{ $item->jasaRaharja->ketStatusDijamin }}</td>
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
@section('plugins.DateRangePicker', true)
