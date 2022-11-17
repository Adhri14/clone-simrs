@extends('adminlte::page')
@section('title', 'Dashboard Tanggal - Antrian BPJS')
@section('content_header')
    <h1 class="m-0 text-dark">Dashboard Tanggal Antrian BPJS</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Pencarian Dashboad Tanggal Antrian" theme="secondary" icon="fas fa-info-circle"
                collapsible>
                <form action="{{ route('bpjs.antrian.dashboard_tanggal') }}">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tanggal" placeholder="Silahkan Pilih Tanggal" value="{{ $request->tanggal }}"
                        label="Tanggal Periksa" :config="$config" />
                    <x-adminlte-select name="waktu" label="Waktu">
                        <option value="rs">Waktu RS</option>
                        <option value="server">Waktu BPJS</option>
                    </x-adminlte-select>
                    <x-adminlte-button label="Cari Antrian" class="mr-auto withLoad" type="submit" theme="success"
                        icon="fas fa-search" />
                </form>
            </x-adminlte-card>
            <x-adminlte-card title="Data Task ID Antrian" theme="secondary" collapsible>
                @php
                    $heads = ['Poliklinik', '1', '2', '3', '4', '5', '6', 'Total', 'Input'];
                @endphp
                <x-adminlte-datatable id="table2" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @isset($antrians)
                        @foreach ($antrians as $key => $item)
                            <tr>
                                <td>
                                    {{ $item->namapoli }} ({{ $item->kodepoli }})
                                    <br>
                                    {{ $item->nmppk }} ({{ $item->kdppk }})
                                </td>
                                <td>
                                    {{ $item->waktu_task1 }}
                                    <br>
                                    {{ $item->avg_waktu_task1 }}
                                </td>
                                <td>
                                    {{ $item->waktu_task2 }}
                                    <br>
                                    {{ $item->avg_waktu_task2 }}
                                </td>
                                <td>
                                    {{ $item->waktu_task3 }}
                                    <br>
                                    {{ $item->avg_waktu_task3 }}
                                </td>
                                <td>
                                    {{ $item->waktu_task4 }}
                                    <br>
                                    {{ $item->avg_waktu_task4 }}
                                </td>
                                <td>
                                    {{ $item->waktu_task5 }}
                                    <br>
                                    {{ $item->avg_waktu_task5 }}
                                </td>
                                <td>
                                    {{ $item->waktu_task6 }}
                                    <br>
                                    {{ $item->avg_waktu_task6 }}
                                </td>
                                <td>{{ $item->jumlah_antrean }}</td>
                                <td>{{ $item->tanggal }}</td>
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
@section('plugins.Select2', true)
