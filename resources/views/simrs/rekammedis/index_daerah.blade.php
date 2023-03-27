@extends('adminlte::page')

@section('title', 'Index Penyakit Rawat Jalan')

@section('content_header')
    <h1>Index Penyakit Rawat Jalan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $config = [
                                    'locale' => ['format' => 'YYYY/MM/DD'],
                                ];
                            @endphp
                            <x-adminlte-date-range name="tanggal" value="{{ $request->tanggal }}"
                                label="Periode Tanggal Kunjungan" :config="$config">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-date-range>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="success" label="Submit Pencarian"
                        icon="fas fa-search" />
                </form>
            </x-adminlte-card>
            @isset($kunjungans)
                <x-adminlte-card title="Data Kunjungan Daerah" theme="secondary" collapsible>
                    <table id="table1" class="table table-bordered table-hover table-sm nowrap">
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2">Kabupaten</th>
                                <th scope="col" rowspan="2">Kecamatan</th>
                                <th scope="col" rowspan="2">Jumlah</th>
                                <th scope="col" colspan="12" class="text-center">Rawat Jalan</th>
                                <th scope="col" colspan="12" class="text-center">Rawat Inap</th>
                            </tr>
                            <tr>
                                <th scope="col">Januari</th>
                                <th scope="col">Februari</th>
                                <th scope="col">Maret</th>
                                <th scope="col">April</th>
                                <th scope="col">Mei</th>
                                <th scope="col">Juni</th>
                                <th scope="col">Juli</th>
                                <th scope="col">Agustus</th>
                                <th scope="col">September</th>
                                <th scope="col">Oktober</th>
                                <th scope="col">November</th>
                                <th scope="col">Desember</th>
                                <th scope="col">Januari</th>
                                <th scope="col">Februari</th>
                                <th scope="col">Maret</th>
                                <th scope="col">April</th>
                                <th scope="col">Mei</th>
                                <th scope="col">Juni</th>
                                <th scope="col">Juli</th>
                                <th scope="col">Agustus</th>
                                <th scope="col">September</th>
                                <th scope="col">Oktober</th>
                                <th scope="col">November</th>
                                <th scope="col">Desember</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kunjungans->groupBy('kode_kecamatan') as $key => $item)
                                <tr>
                                    <td>{{ $item->first()->nama_kabupaten_kota }}</td>
                                    <td>{{ $item->first()->nama_kecamatan }}</td>
                                    <td>{{ $item->count() }}</td>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <td>{{ $item->where('bulan', $i)->count() }}</td>
                                    @endfor
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-adminlte-card>


                {{-- <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">First</th>
                            <th scope="col">Last</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kunjungans->groupBy('kode_kecamatan') as $key => $item)
                            <tr>
                                <td>{{ $key }} </td>
                                <td>{{ $item->first()->nama_kecamatan }}</td>
                                <td>{{ $item->count() }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table> --}}


            @endisset
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
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
    <script>
        $('#table1').dataTable({
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
            "scrollX": true,
            "scrollY": "500px",
            "scrollCollapse": true,
            "paging": false,
            "info": false,
            "order": [
                [2, 'desc']
            ],
        });
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
