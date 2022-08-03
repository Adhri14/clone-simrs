@extends('adminlte::page')

@section('title', 'Monitoring Pelayanan Peserta')

@section('content_header')
    <h1>Monitoring Pelayanan Peserta</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger" dismissable>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-adminlte-alert>
            @endif
            <x-adminlte-card title="Pencarian Peserta BPJS" theme="secondary" collapsible>
                <div class="row">
                    <div class="col-md-6">
                        <form action="" id="myform" method="get">
                            <x-adminlte-input name="nomorkartu" label="Nomor Kartu" value="{{ $request->kartu }}"
                                placeholder="Pencarian Berdasarkan Nomor Kartu BPJS">
                                <x-slot name="appendSlot">
                                    <x-adminlte-button theme="success" class="withLoad" type="submit" label="Cari!" />
                                </x-slot>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text text-success">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="" id="myform" method="get">
                            <x-adminlte-input name="nik" label="NIK" value="{{ $request->nik }}"
                                placeholder="Pencarian Berdasarkan NIK">
                                <x-slot name="appendSlot">
                                    <x-adminlte-button theme="success" class="withLoad" type="submit" label="Cari!" />
                                </x-slot>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text text-success">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </form>
                    </div>
                </div>
            </x-adminlte-card>
            @empty($response)
                <x-adminlte-alert title="Informasi !" theme="info" dismissable>
                    Silahkan lakukan pencarian berdasarkan NIK atau Nomor Kartu BPJS
                </x-adminlte-alert>
            @else
                @if ($response->metaData->code != 200)
                    <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger" dismissable>
                        {{ session()->get('error') }}
                        {{ $response->metaData->message }}
                    </x-adminlte-alert>
                @else
                    <div class="row">
                        <div class="col-md-4">
                            <x-adminlte-profile-widget name="{{ $response->response->peserta->nama }}"
                                desc="NIK : {{ $response->response->peserta->nik }}" theme="lightblue" layout-type="classic">
                                <dl class="row">
                                    <dt class="col-sm-4">NIK</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->nik }}</dd>
                                    <dt class="col-sm-4">No Kartu</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->noKartu }}</dd>
                                    <dt class="col-sm-4">Nama</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->nama }}</dd>
                                    <dt class="col-sm-4">Pisa</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->pisa }}</dd>
                                    <dt class="col-sm-4">Jenis Kelamin</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->sex }}</dd>
                                    <dt class="col-sm-4">Tanggal Lahir</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->tglLahir }}</dd>
                                    <dt class="col-sm-4">Umur</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->umur->umurSekarang }}</dd>
                                    <dt class="col-sm-4">Status Peserta</dt>
                                    <dd class="col-sm-8">
                                        {{ $response->response->peserta->statusPeserta->keterangan }}
                                    </dd>
                                    <dt class="col-sm-4">Hak Kelas</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->hakKelas->keterangan }}
                                    </dd>
                                    <dt class="col-sm-4">Jenis Peserta</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->jenisPeserta->keterangan }}
                                    </dd>
                                    <dt class="col-sm-4">Tgl TAT</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->tglTAT }}</dd>
                                    <dt class="col-sm-4">Tgl TMT</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->tglTMT }}</dd>
                                    <dt class="col-sm-4">Tgl Cetak Kartu</dt>
                                    <dd class="col-sm-8">{{ $response->response->peserta->tglCetakKartu }}</dd>
                                </dl>
                            </x-adminlte-profile-widget>
                        </div>
                        <div class="col-md-8">
                            @php
                                $heads = ['Tgl SEP', 'Tgl Pulang', 'Nomor SEP / Rujukan', 'Nama', 'PPK Pelayanan', 'Poliklinik / Kelas', 'Diagnosa', 'Action'];
                                $config['order'] = ['7', 'asc'];
                            @endphp

                            <x-adminlte-datatable id="table1" class="nowrap" :heads="$heads" :config="$config" striped
                                bordered hoverable compressed>
                                @isset($monitoring->response->histori)
                                    @foreach ($monitoring->response->histori as $item)
                                        <tr>
                                            <td>{{ $item->tglSep }}</td>
                                            <td>{{ $item->tglPlgSep }}</td>
                                            <td>
                                                {{ $item->noSep }}<br>
                                                {{ $item->noRujukan }}
                                            </td>
                                            <td>{{ $item->namaPeserta }}</td>
                                            <td>
                                                {{ $item->ppkPelayanan }}<br>
                                                {{ $item->jnsPelayanan }}
                                            </td>
                                            <td>{{ $item->poli }}<br>
                                                {{ $item->kelasRawat }}
                                            </td>
                                            <td>{{ $item->diagnosa }}</td>
                                            <td>
                                                <form action="{{ route('vclaim.delete_sep', $item->noSep) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-adminlte-button class="btn-xs" theme="danger" icon="fas fa-trash-alt"
                                                        type="submit"
                                                        onclick="return confirm('Apakah anda akan menghapus {{ $item->noSep }} ?')" />
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endisset

                            </x-adminlte-datatable>
                        </div>
                    </div>
                @endif
            @endisset
        </div>
    </div>
@stop

{{-- @section('plugins.Select2', true) --}}
@section('plugins.Datatables', true)
