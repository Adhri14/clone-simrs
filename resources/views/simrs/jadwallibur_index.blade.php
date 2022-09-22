@extends('adminlte::page')

@section('title', 'Jadwal Libur Poliklinik')

@section('content_header')
    <h1>Jadwal Libur Poliklinik</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-small-box title="{{ $jadwals->total() }}" text="Total Jadwal Libur" theme="success"
                        icon="fas fa-users" />
                </div>
            </div>
            @if ($errors->any())
                <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger" dismissable>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-adminlte-alert>
            @endif
            <x-adminlte-card title="Tabel Jadwal Libur Poliklinik" theme="secondary" collapsible>
                <div class="dataTables_wrapper dataTable">
                    <div class="row">
                        <div class="col-md-8">
                            <x-adminlte-button label="Tambah" class="btn-sm" theme="success"
                                title="Tambah Unit & Poliklinik" icon="fas fa-plus" data-toggle="modal"
                                data-target="#modalCustom" />
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('admin.user.index') }}" method="get">
                                <x-adminlte-input name="search" placeholder="Pencarian NIK / Nama" igroup-size="sm"
                                    value="{{ $request->search }}">
                                    <x-slot name="appendSlot">
                                        <x-adminlte-button type="submit" theme="outline-primary" label="Go!" />
                                    </x-slot>
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text text-primary">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $heads = ['No.', 'Unit', 'Tanggal Libur', 'Keterangan', 'Antrian', 'Status', 'Action'];
                                $config['paging'] = false;
                                $config['lengthMenu'] = false;
                                $config['searching'] = false;
                                $config['info'] = false;
                                $config['responsive'] = true;
                            @endphp
                            <x-adminlte-datatable id="table1" :heads="$heads" :config="$config" hoverable bordered
                                compressed class="nowrap">
                                @foreach ($jadwals as $jadwal)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($jadwal->kode_poli == 0)
                                                SEMUA POLIKLINIK
                                            @else
                                                {{ $jadwal->unit->nama_unit }}
                                            @endif
                                        </td>
                                        <td>{{ Carbon\Carbon::parse($jadwal->tanggal_awal)->locale('id')->isoFormat('LL') }}
                                            -
                                            {{ Carbon\Carbon::parse($jadwal->tanggal_akhir)->locale('id')->isoFormat('LL') }}
                                        </td>
                                        <td>{{ $jadwal->keterangan }}</td>
                                        <td>
                                            @if ($jadwal->kode_poli == 0)
                                                {{ App\Models\Antrian::whereBetween('tanggalperiksa', [$jadwal->tanggal_awal, $jadwal->tanggal_akhir])->count() }}
                                            @else
                                                {{ $jadwal->unit->antrians->whereBetween('tanggalperiksa', [$jadwal->tanggal_awal, $jadwal->tanggal_akhir])->count() }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($jadwal->status == 1)
                                                <span class="badge bg-warning">{{ $jadwal->status }}. Libur belum
                                                    dikonformasi</span>
                                            @endif
                                            @if ($jadwal->status == 2)
                                                <span class="badge bg-success">{{ $jadwal->status }}. Libur
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('jadwallibur.destroy', $jadwal) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                @if ($jadwal->status == 1)
                                                    <x-adminlte-button theme="primary" icon="fas fa-question-circle"
                                                        data-toggle="tooltip"
                                                        title="Konfirmasi LIBUR {{ $jadwal->kode_poli == 0 ? 'SEMUA POLIKLINIK' : $jadwal->unit->nama_unit }} {{ $jadwal->tanggal }}"
                                                        onclick="window.location='{{ route('jadwallibur.show', $jadwal->id) }}'"
                                                        class="btn btn-xs" />
                                                @endif
                                                @if ($jadwal->status == 2)
                                                    <x-adminlte-button theme="success" icon="fas fa-check"
                                                        data-toggle="tooltip"
                                                        title="LIBUR {{ $jadwal->kode_poli == 0 ? 'SEMUA POLIKLINIK' : $jadwal->unit->nama_unit }} {{ $jadwal->tanggal }} Telah dikonformasi"
                                                        class="btn btn-xs" />
                                                @endif
                                                <x-adminlte-button theme="warning" icon="fas fa-edit" class="btn btn-xs"
                                                    data-toggle="tooltip"
                                                    title="Edit LIBUR {{ $jadwal->kode_poli == 0 ? 'SEMUA POLIKLINIK' : $jadwal->unit->nama_unit }} {{ $jadwal->tanggal }}"
                                                    onclick="window.location='{{ route('jadwallibur.edit', $jadwal->id) }}'" />
                                                <x-adminlte-button class="btn-xs" theme="danger" icon="fas fa-trash-alt"
                                                    type="submit"
                                                    onclick="return confirm('Apakah anda akan menghapus Role LIBUR {{ $jadwal->kode_poli == 0 ? 'SEMUA POLIKLINIK' : $jadwal->unit->nama_unit }} {{ $jadwal->tanggal }} ?')" />
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>
                        </div>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modalCustom" title="Tambah Jadwal Libur" theme="success" v-centered static-backdrop>
        <form action="{{ route('jadwallibur.store') }}" id="myform" method="post">
            @csrf
            <x-adminlte-select2 name="kode_poli" label="Poliklinik">
                <option value="0">SEMUA POLIKLINIK</option>
                <x-adminlte-options :options=$polikliniks />
            </x-adminlte-select2>
            @php
                $config = [
                    'locale' => ['format' => 'YYYY/MM/DD'],
                ];
            @endphp
            <x-adminlte-date-range name="tanggal" label="Tanggal Libur" :config="$config" />
            <x-adminlte-textarea name="keterangan" placeholder="Masukan keterangan libur." label="Keterangan" />
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button form="myform" class="mr-auto" type="submit" theme="success" label="Simpan" />
            <x-adminlte-button theme="danger" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.DateRangePicker', true)
