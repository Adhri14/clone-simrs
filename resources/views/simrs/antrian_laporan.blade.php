@extends('adminlte::page')

@section('title', 'Laporan Antrian')

@section('content_header')
    <h1>Laporan Antrian</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                <form action="{{ route('antrian.laporan') }}" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $config = [
                                    'locale' => ['format' => 'YYYY/MM/DD'],
                                ];
                            @endphp
                            <x-adminlte-date-range name="tanggal" label="Periode Tanggal Antrian"
                                enable-default-ranges="Last 30 Days" :config="$config">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-date-range>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                    <x-adminlte-button class="withLoad" theme="success" label="Tambah Antrian Offline" />
                </form>
            </x-adminlte-card>
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-small-box title="{{ $antrians->count() }}" text="Total Antrian Terdaftar" theme="success"
                        icon="fas fa-users" />
                </div>
                <div class="col-md-3">
                    <x-adminlte-small-box title="{{ $antrians->where('taskid', 99)->count() }}" text="Antrian Batal"
                        theme="danger" icon="fas fa-users" />
                </div>
            </div>
            @if (isset($request->tanggal))
                <x-adminlte-card title="Antrian Pendaftaran" theme="primary" icon="fas fa-info-circle" collapsible>
                    @if ($errors->any())
                        <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger" dismissable>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-adminlte-alert>
                    @endif
                    @php
                        $heads = ['No', 'Kode', 'Tanggal', 'No RM / NIK', 'Jenis / Pasien', 'No Kartu / Rujukan', 'Poliklinik / Dokter', 'Status'];
                        $config['order'] = ['2', 'desc'];
                    @endphp
                    <x-adminlte-datatable id="table1" class="nowrap" :heads="$heads" :config="$config" striped bordered
                        hoverable compressed>
                        {{-- @foreach ($antrians as $item)
                            <tr>
                                <td>{{ $item->angkaantrean }}</td>
                                <td>{{ $item->kodebooking }}<br>
                                    {{ $item->nomorantrean }}
                                </td>
                                <td>{{ $item->tanggalperiksa }}</td>
                                <td>
                                    {{ $item->norm }} <br>
                                    {{ $item->nik }}
                                </td>
                                <td>
                                    {{ $item->jenispasien }}
                                    @if ($item->pasienbaru == 1)
                                        <span class="badge bg-secondary">{{ $item->pasienbaru }}. Baru</span>
                                    @endif
                                    @if ($item->pasienbaru == 0)
                                        <span class="badge bg-secondary">{{ $item->pasienbaru }}. Lama</span>
                                    @endif
                                    @isset($item->pasien)
                                        <br>
                                        {{ $item->pasien->nama }}
                                    @endisset
                                </td>
                                <td>
                                    @isset($item->nomorkartu)
                                        {{ $item->nomorkartu }}
                                    @endisset
                                    @isset($item->nomorkartu)
                                        <br> {{ $item->nomorreferensi }}
                                    @endisset
                                </td>
                                <td>{{ $item->namapoli }}<br>{{ $item->namadokter }} <br>{{ $item->jampraktek }}
                                </td>
                                <td>
                                    @if ($item->taskid == 0)
                                        <span class="badge bg-secondary">{{ $item->taskid }}. Belum Checkin</span>
                                    @endif
                                    @if ($item->taskid == 1)
                                        <span class="badge bg-warning">{{ $item->taskid }}. Checkin</span>
                                    @endif
                                    @if ($item->taskid == 2)
                                        <span class="badge bg-primary">{{ $item->taskid }}. Proses Pendaftaran</span>
                                    @endif
                                    @if ($item->taskid == 3)
                                        @if ($item->status_api == 0)
                                            <span class="badge bg-warning">2. Belum Pembayaran</span>
                                        @else
                                            <span class="badge bg-success">{{ $item->taskid }}. Tunggu Poli</span>
                                        @endif
                                    @endif
                                    @if ($item->taskid >= 4 && $item->taskid <= 7)
                                        <span class="badge bg-success">{{ $item->taskid }}. Pelayanan Poli</span>
                                    @endif
                                    @if ($item->taskid == 99)
                                        <span class="badge bg-danger">{{ $item->taskid }}. Batal</span>
                                    @endif

                                </td>
                            </tr>
                        @endforeach --}}
                    </x-adminlte-datatable>
                </x-adminlte-card>
            @endif
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.DateRangePicker', true)
