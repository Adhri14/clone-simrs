@extends('adminlte::page')

@section('title', 'Index Penyakit Rawat Jalan')

@section('content_header')
    <h1>Index Penyakit Rawat Jalan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                <form action="{{ route('index_dokter') }}" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $config = [
                                    'locale' => ['format' => 'YYYY/MM/DD'],
                                ];
                            @endphp
                            <x-adminlte-date-range name="tanggal" label="Periode Tanggal Kunjungan"
                                enable-default-ranges="Today" :config="$config">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-date-range>
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-select2 name="unit" label="Unit / Ruangan">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-clinic-medical"></i>
                                    </div>
                                </x-slot>
                                <option value="" selected disabled>PILIH UNIT / RUANGAN</option>
                                @foreach ($units as $key => $unit)
                                    <option value="{{ $key }}" {{ $request->unit == $key ? 'selected' : null }}>
                                        {{ $unit }}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="success" label="Submit Pencarian"
                        icon="fas fa-search" />
                </form>
            </x-adminlte-card>
        </div>
        @isset($kunjungans)
            <div class="col-12">
                <div id="printMe">
                    <section class="invoice p-3 mb-3">
                        <div class="row">
                            <img src="{{ asset('vendor/adminlte/dist/img/rswaledico.png') }}" style="width: 100px">
                            <div class="col">
                                <b>RUMAH SAKIT UMUM DAERAH WALED</b><br>
                                <b>KABUPATEN CIREBON</b><br>
                                Jalan Raden Walangsungsang Kecamatan Waled Kabupaten Cirebon 45188<br>
                                www.rsudwaled.id - brsud.waled@gmail.com - Call Center (0231) 661126
                            </div>
                        </div>
                        <hr width="100%" hight="20px" color="black" size="50px" />
                        <div class="row invoice-info">
                            <div class="col-sm-12 invoice-col text-center">
                                <b class="text-lg">LAPORAN INDEX DOKTER</b>
                                <br>
                                <br>
                            </div>
                            <div class="col-sm-4 invoice-col">
                                <dl class="row">
                                    <dt class="col-sm-4 m-0">ICD-10</dt>
                                    <dd class="col-sm-8 m-0"> : <b>{{ strtoupper($request->diagnosa) }} - </b>
                                    </dd>
                                    <dt class="col-sm-4 m-0">Diagnosa</dt>
                                    <dd class="col-sm-8 m-0"> : <b>{{ strtoupper($request->diagnosa) }} - </b>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-sm-4 invoice-col">
                                <dl class="row">
                                    <dt class="col-sm-4 m-0">Periode</dt>
                                    <dd class="col-sm-8 m-0"> : <b>{{ $request->tanggal }}</b>
                                    </dd>
                                    <dt class="col-sm-4  m-0">Waktu Cetak</dt>
                                    <dd class="col-sm-8  m-0"> : <b>{{ Carbon\Carbon::now() }}</b>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-sm-4 invoice-col">
                                <dl class="row">
                                    <dt class="col-sm-4  m-0">User</dt>
                                    <dd class="col-sm-8  m-0"> : <b>{{ Auth::user()->name }}</b>
                                    </dd>

                                </dl>
                            </div>
                        </div>
                        <div class="col-12 table-responsive">
                            <table class="table table-sm text-xs">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tgl. Masuk</th>
                                        <th>Tgl. Keluar</th>
                                        <th>No RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Dokter</th>
                                        <th>Penjamin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kunjungans as $kunjungan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kunjungan->tgl_masuk }}</td>
                                            <td>
                                                @isset($kunjungan->tgl_keluar)
                                                    {{ $kunjungan->tgl_keluar }}
                                                @else
                                                    Belum
                                                @endisset
                                            </td>
                                            <td>{{ $kunjungan->no_rm }}</td>
                                            <td>{{ $kunjungan->pasien->nama_px }}</td>
                                            <td>{{ $kunjungan->dokter->nama_paramedis }}</td>
                                            <td>{{ $kunjungan->penjamin_simrs->nama_penjamin }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <button class="btn btn-success" onclick="printDiv('printMe')"><i class="fas fa-print"> Print Laporan</i>
            </div>
        @endisset
    </div>
@stop

@section('plugins.Datatables', true)
{{-- @section('plugins.DatatablesPlugins', true) --}}
@section('plugins.DateRangePicker', true)
@section('plugins.Select2', true)
{{-- @section('plugins.Chartjs', true) --}}
@section('js')
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            tampilan_print = document.body.innerHTML = printContents;
            setTimeout('window.addEventListener("load", window.print());', 1000);
        }
    </script>
@endsection
@section('css')
    <style type="text/css" media="print">
        table,
        th,
        td {
            border: 1px solid #333333 !important;
        }

        hr {
            color: #333333 !important;
            border: 1px solid #333333 !important;
            line-height: 1.5;
        }
    </style>

@endsection
