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
                    $heads = ['Task ID', 'Taskname', 'Waktu RS', 'Waktu BPJS', 'Kodebooking'];
                @endphp
                <x-adminlte-datatable id="table2" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @isset($antrians)
                        @foreach ($antrians as $item)
                            <tr>
                                <td>{{ $item->antrians }}</td>
                                <td>{{ $item->taskname }}</td>
                                <td>{{ $item->wakturs }}</td>
                                <td>{{ $item->waktu }}</td>
                                <td>{{ $item->kodebooking }}</td>
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
