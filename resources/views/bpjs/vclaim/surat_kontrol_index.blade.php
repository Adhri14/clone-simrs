@extends('adminlte::page')
@section('title', 'Surat Kontrol & SPRI - Vclaim BPJS')
@section('content_header')
    <h1>Surat Kontrol & SPRI - Vclaim BPJS </h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Surat Kontrol & SPRI" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-6">
                            @php
                                $config = [
                                    'locale' => ['format' => 'YYYY/MM/DD'],
                                ];
                            @endphp
                            <x-adminlte-date-range name="tanggal" label="Periode Tanggal Antrian"
                                enable-default-ranges="Today" :config="$config">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-date-range>
                        </div>
                        <div class="col-6">
                            <x-adminlte-select2 name="formatfilter" label="Format Filter">
                                <option value="1" {{ $request->jenispelayanan == 1 ? 'selected' : null }}>Tanggal Entri
                                </option>
                                <option value="2" {{ $request->jenispelayanan == 2 ? 'selected' : null }}>Tanggal
                                    Kontrol
                                </option>
                                <x-slot name="appendSlot">
                                    <x-adminlte-button type="submit" class="withLoad" theme="primary"
                                        label="Cari Surat Kontrol" />
                                </x-slot>
                            </x-adminlte-select2>
                        </div>
                    </div>
                </form>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-4">
                            @php
                                $config = ['format' => 'YYYY-MM'];
                            @endphp
                            <x-adminlte-input-date name="bulan" label="Tanggal Antrian" :config="$config"
                                value="{{ $request->bulan }}" placeholder="Pilih Bulan">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                        </div>
                        <div class="col-4">
                            <x-adminlte-input name="nomorkartu" label="Nomor Kartu" value="{{ $request->nomorkartu }}"
                                placeholder="Pencarian Berdasarkan Nomor Kartu BPJS">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="col-4">
                            <x-adminlte-select2 name="formatfilter" label="Format Filter">
                                <option value="1" {{ $request->jenispelayanan == 1 ? 'selected' : null }}>Tanggal Entri
                                </option>
                                <option value="2" {{ $request->jenispelayanan == 2 ? 'selected' : null }}>Tanggal
                                    Kontrol
                                </option>
                                <x-slot name="appendSlot">
                                    <x-adminlte-button type="submit" class="withLoad" theme="primary"
                                        label="Cari Surat Kontrol" />
                                </x-slot>
                            </x-adminlte-select2>
                        </div>
                    </div>

                </form>
            </x-adminlte-card>
        </div>
        <div class="col-12">
            <x-adminlte-card title="Data Surat Kontrol & SPRI" theme="secondary" collapsible>
                @php
                    $heads = ['Tgl Entry', 'Tgl Kontrol', 'No Surat Kontrol', 'No SEP', 'Jns Pelayanan', 'Poliklinik', 'Dokter', 'Pasien', 'Terbit SEP'];
                @endphp
                <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" bordered hoverable compressed>
                    @isset($suratkontrol)
                        @foreach ($suratkontrol as $item)
                            <tr>
                                <td>{{ $item->tglTerbitKontrol }}</td>
                                <td>{{ $item->tglRencanaKontrol }}</td>
                                <td>
                                    {{ $item->noSuratKontrol }}
                                </td>
                                <td>
                                    {{ $item->noSepAsalKontrol }}
                                    <br>
                                    {{ $item->tglSEP }}
                                </td>
                                <td>
                                    {{ $item->namaJnsKontrol }}
                                    <br>
                                    {{ $item->jnsPelayanan }}
                                </td>
                                <td>
                                    Tujuan : {{ $item->namaPoliTujuan }}
                                    <br>
                                    Asal : {{ $item->namaPoliAsal }}
                                </td>
                                <td>
                                    {{ $item->namaDokter }}
                                    <br>
                                    {{ $item->kodeDokter }}
                                </td>
                                <td>
                                    {{ $item->nama }}
                                    <br>
                                    {{ $item->noKartu }}
                                </td>
                                <td>{{ $item->terbitSEP }}</td>
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
