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
                    <x-adminlte-date-range name="tanggal" label="Periode Tanggal Antrian" enable-default-ranges="Today"
                        :config="$config">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-primary">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-date-range>
                    <x-adminlte-select2 name="jenispelayanan" label="Jenis Pelayanan">
                        <option value="1" {{ $request->jenispelayanan == 1 ? 'selected' : null }}>Rawat Inap</option>
                        <option value="2" {{ $request->jenispelayanan == 2 ? 'selected' : null }}>Rawat Jalan</option>
                    </x-adminlte-select2>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Data Kunjungan" />
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-12">
            <x-adminlte-card title="Data Klaim BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['FPK / SEP', 'Tgl Msuk / Plg', 'Poli / Kelas', 'Pasien', 'INACBG', 'Status', 'byTarifGruper', 'byTopup', 'byPengajuan', 'bySetujui', 'byTarifRS', 'LabaRugi'];
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
                            <tr>
                                <td>
                                    FPK : {{ $item->noFPK }}
                                    <br>
                                    SEP : {{ $item->noSEP }}
                                </td>
                                <td>
                                    Msk : {{ $item->tglSep }}
                                    <br>
                                    Plg : {{ $item->tglPulang }}
                                </td>
                                <td>{{ $item->kelasRawat }} {{ $item->poli }}</td>
                                <td>
                                    {{ $item->peserta->noKartu }} - {{ $item->peserta->noMR }}
                                    <br>
                                    {{ $item->peserta->nama }}
                                </td>
                                <td>
                                    {{ $item->Inacbg->kode }}
                                    <br>
                                    {{ substr($item->Inacbg->nama, 0, 20) }}..
                                </td>
                                <td>{{ $item->status }}</td>
                                <td>{{ money($item->biaya->byTarifGruper, 'IDR') }}</td>
                                <td>{{ money($item->biaya->byTopup, 'IDR') }}</td>
                                <td>{{ money($item->biaya->byPengajuan, 'IDR') }}</td>
                                <td>{{ money($item->biaya->bySetujui, 'IDR') }}</td>
                                <td>{{ money($item->biaya->byTarifRS, 'IDR') }}</td>
                                <td
                                    class="{{ $item->biaya->bySetujui - $item->biaya->byTarifRS > 0 ? 'table-success' : 'table-danger' }}">
                                    {{ number_format($item->biaya->bySetujui - $item->biaya->byTarifRS, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $byTarifRS += $item->biaya->byTarifRS;
                                $byTarifGruper += $item->biaya->byTarifGruper;
                                $byTopup += $item->biaya->byTopup;
                                $byPengajuan += $item->biaya->byPengajuan;
                                $bySetujui += $item->biaya->bySetujui;
                            @endphp
                        @endforeach
                        <tfoot>
                            <tr class="{{ $bySetujui - $byTarifRS > 0 ? 'table-success' : 'table-danger' }}">
                                <th colspan="6" class="text-center">Total</th>
                                <th>{{ money($byTarifGruper, 'IDR') }}</th>
                                <th>{{ money($byTopup, 'IDR') }}</th>
                                <th>{{ money($byPengajuan, 'IDR') }}</th>
                                <th>{{ money($bySetujui, 'IDR') }}</th>
                                <th>{{ money($byTarifRS, 'IDR') }}</th>
                                <th>{{ money($bySetujui - $byTarifRS, 'IDR') }}</th>
                            </tr>
                        </tfoot>
                    @endisset
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.DateRangePicker', true)
