@extends('adminlte::page')

@section('title', 'Laporan Antrian ' . $request->tanggal)

@section('content_header')
    <h1>Laporan Antrian Per Bulan {{ $request->tanggal ? \Carbon\Carbon::parse($request->tanggal)->format('F Y') : '' }}
    </h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $config = ['format' => 'YYYY-MM'];
                            @endphp
                            <x-adminlte-input-date name="tanggal" label="Tanggal Laporan" value="{{ $request->tanggal }}"
                                :config="$config">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-select label="Waktu Server" name="waktu">
                                <option value="rs">Server RS</option>
                                <option value="server">Server BPJS</option>
                            </x-adminlte-select>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                </form>
            </x-adminlte-card>
            @if (isset($antrians))
                {{-- <x-adminlte-card title="Antrian Pendaftaran" theme="primary" icon="fas fa-info-circle" collapsible>
                    @if ($errors->any())
                        <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger" dismissable>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-adminlte-alert>
                    @endif --}}
                {{-- @php
                        $heads = ['Tanggal', 'Nama Poli', 'Checkin', 'Pendaftaran', 'Tunggu Poli', 'Layanan Poli', 'Tunggu Farmasi', 'Layanan Farmasi', 'Jumlah'];
                        $config['order'] = ['2', 'desc'];
                    @endphp
                    <x-adminlte-datatable id="table1" class="nowrap" :heads="$heads" :config="$config" striped bordered
                        hoverable compressed with-buttons>
                        @foreach ($antrians as $item)
                            <tr>
                                <td>{{ $item->tanggal }}</td>
                                <td>{{ $item->namapoli }}</td>
                                <td>
                                    Total : {{ round($item->waktu_task1 / 60) }} menit <br>
                                    Rata : {{ round($item->avg_waktu_task1 / 60) }} menit
                                </td>
                                <td>
                                    Total : {{ round($item->waktu_task2 / 60) }} menit<br>
                                    Rata : {{ round($item->avg_waktu_task2 / 60) }} menit
                                </td>
                                <td>
                                    Total : {{ round($item->waktu_task3 / 60) }} menit<br>
                                    Rata : {{ round($item->avg_waktu_task3 / 60) }} menit
                                </td>
                                <td>
                                    Total : {{ round($item->waktu_task4 / 60) }} menit<br>
                                    Rata : {{ round($item->avg_waktu_task4 / 60) }} menit
                                </td>
                                <td>
                                    Total : {{ round($item->waktu_task5 / 60) }} menit<br>
                                    Rata : {{ round($item->avg_waktu_task5 / 60) }} menit
                                </td>
                                <td>
                                    Total : {{ round($item->waktu_task6 / 60) }} menit<br>
                                    Rata : {{ round($item->avg_waktu_task6 / 60) }} menit
                                </td>
                                <td>{{ $item->jumlah_antrean }}</td>
                                <td>{{ date('d/m/Y H:i:s', $item->insertdate / 1000) }}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable> --}}
                {{-- </x-adminlte-card> --}}
                <x-adminlte-card title="Antrian Pendaftaran" theme="primary" icon="fas fa-info-circle" collapsible>
                    {{-- @php
                        $heads = ['Bulan', 'Nama Poli', 'Checkin', 'Pendaftaran', 'Tunggu Poli', 'Layanan Poli', 'Tunggu Farmasi', 'Layanan Farmasi', 'Jumlah'];
                        $config['order'] = ['2', 'desc'];
                    @endphp
                    <x-adminlte-datatable id="tablex" class="nowrap" :heads="$heads" :config="$config" striped bordered
                        hoverable compressed with-buttons>
                    </x-adminlte-datatable> --}}
                    <table class="table table-sm table-hover table-bordered" id="tablex">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Nama Poliklinik</th>
                                <th>Checkin</th>
                                <th>Pendaftaran</th>
                                <th>Tunggu Poli</th>
                                <th>Layanan Poli</th>
                                <th>Tunggu Farmasi</th>
                                <th>Layanan Farmasi</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        @foreach ($antri_group as $key => $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->first()->tanggal)->format('F Y') }}</td>
                                <td>{{ $key }}</td>
                                <td>{{ round($item->sum('avg_waktu_task1') / $item->count() / 60) }} menit</td>
                                <td>{{ round($item->sum('avg_waktu_task2') / $item->count() / 60) }} menit </td>
                                <td>{{ round($item->sum('avg_waktu_task3') / $item->count() / 60) }} menit</td>
                                <td>{{ round($item->sum('avg_waktu_task4') / $item->count() / 60) }} menit</td>
                                <td>{{ round($item->sum('avg_waktu_task5') / $item->count() / 60) }} menit</td>
                                <td>{{ round($item->sum('avg_waktu_task6') / $item->count() / 60) }} menit</td>
                                <td>{{ $item->sum('jumlah_antrean') }} pasien</td>
                            </tr>
                        @endforeach
                        <tfoot>
                            <tr>
                                <th>{{ \Carbon\Carbon::parse($antrians->first()->tanggal)->format('F Y') }}</th>
                                <th>RSUD WALED</th>
                                <th>{{ round($antrians->sum('avg_waktu_task1') / $antrians->count() / 60) }} menit</th>
                                <th>{{ round($antrians->sum('avg_waktu_task2') / $antrians->count() / 60) }} menit</th>
                                <th>{{ round($antrians->sum('avg_waktu_task3') / $antrians->count() / 60) }} menit</th>
                                <th>{{ round($antrians->sum('avg_waktu_task4') / $antrians->count() / 60) }} menit</th>
                                <th>{{ round($antrians->sum('avg_waktu_task5') / $antrians->count() / 60) }} menit</th>
                                <th>{{ round($antrians->sum('avg_waktu_task6') / $antrians->count() / 60) }} menit</th>
                                <th>{{ $antrians->sum('jumlah_antrean') }} pasien</th>
                            </tr>
                        </tfoot>
                    </table>
                </x-adminlte-card>

            @endif
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.DateRangePicker', true)

@section('js')
    <script>
        $(document).ready(function() {
            $('#tablex').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true
                    },
                    {
                        extend: 'csvHtml5',
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true
                    }
                ]
            });
        });
    </script>

@endsection
