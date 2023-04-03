@extends('adminlte::page')
@section('title', 'Bridging - Antrian BPJS')
@section('content_header')
    <h1 class="m-0 text-dark">Bridging Antrian BPJS</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Pencarian Bridging Antrian BPJS" theme="secondary" icon="fas fa-info-circle" collapsible>
                <form action="{{ route('bpjs.antrian.antrian') }}">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tanggal" value="{{ $request->tanggal }}" placeholder="Silahkan Pilih Tanggal"
                        label="Tanggal Periksa" :config="$config" />
                    <x-adminlte-select2 name="kodepoli" id="kodepoli" label="Poliklinik">
                        <option value="000">000 - SEMUA POLIKLINIK</option>
                        @foreach ($polikliniks as $poli)
                            <option value="{{ $poli->kdsubspesialis }}"
                                {{ $request->kodepoli == $poli->kdsubspesialis ? 'selected' : null }}>
                                {{ $poli->kdsubspesialis }} - {{ $poli->nmsubspesialis }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-button label="Cari Antrian" class="mr-auto withLoad" type="submit" theme="success"
                        icon="fas fa-search" />
                </form>
            </x-adminlte-card>
            <x-adminlte-card title="Data Briding Antrian BPJS" theme="secondary" collapsible>
                @php
                    $heads = ['Angka', 'Nomor', 'Tanggal Daftar', 'Kodebooking', 'No RM', 'No BPJS', 'Pasien', 'Poliklinik', 'Dokter', 'TaskID', 'Action'];
                @endphp
                <x-adminlte-datatable id="table2" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @isset($antrians)
                        @foreach ($antrians as $item)
                            <tr>
                                <td>{{ $item->angkaantrean }}</td>
                                <td>{{ $item->nomorantrean }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->kodebooking }}</td>
                                <td>{{ $item->norm }}</td>
                                <td>{{ $item->nomorkartu }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->namapoli }} ({{ $item->kodepoli }})</td>
                                <td>{{ $item->namadokter }} ({{ $item->kodedokter }})</td>
                                <td>{{ $item->taskid }} {{ $item->status_api }}</td>
                                <td></td>
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
