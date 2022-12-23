@extends('adminlte::page')
@section('title', 'Monitoring Pelayanan Peserta')
@section('content_header')
    <h1>Monitoring Pelayanan Peserta</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Pencarian Peserta BPJS" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-6">
                            @php
                                $config = ['format' => 'YYYY-MM-DD'];
                            @endphp
                            <x-adminlte-input-date name="tanggal" label="Tanggal Antrian" :config="$config"
                                value="{{ \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d') }}">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                        </div>
                        <div class="col-6">
                            <x-adminlte-input name="nomorKartu" label="Nomor Kartu" value="{{ $request->nomorKartu }}"
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
                        </div>
                    </div>
                </form>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-6">
                            @php
                                $config = ['format' => 'YYYY-MM-DD'];
                            @endphp
                            <x-adminlte-input-date name="tanggal" label="Tanggal Antrian" :config="$config"
                                value="{{ \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d') }}">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                        </div>
                        <div class="col-6">
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
                        </div>
                    </div>
                </form>
            </x-adminlte-card>
        </div>
        @isset($peserta)
            <div class="col-md-4">
                <x-adminlte-profile-widget name="{{ $peserta->nama }}" desc="NIK : {{ $peserta->nik }}" theme="lightblue"
                    layout-type="classic">
                    <x-adminlte-profile-row-item title="Rujukan FKTP : {{ $rujukan ? count($rujukan) : 0 }}" size=6 />
                    <x-adminlte-profile-row-item title="Rujukan RS : {{ $rujukan_rs ? count($rujukan_rs) : 0 }}" size=6 />
                    <x-adminlte-profile-row-item title="SEP : {{ $sep ? count($sep) : 0 }}" size=6 />
                    <x-adminlte-profile-row-item title="Surat Kontrol : {{ $surat_kontrol ? count($surat_kontrol) : 0 }}"
                        size=6 />
                    <br><br>
                    <dl class="row">
                        <dt class="col-sm-4">NIK</dt>
                        <dd class="col-sm-8">{{ $peserta->nik }}</dd>
                        <dt class="col-sm-4">No Kartu</dt>
                        <dd class="col-sm-8">{{ $peserta->noKartu }}</dd>
                        <dt class="col-sm-4">No RM</dt>
                        <dd class="col-sm-8">{{ $peserta->mr->noMR }}</dd>
                        <dt class="col-sm-4">No HP</dt>
                        <dd class="col-sm-8">{{ $peserta->mr->noTelepon }}</dd>
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $peserta->nama }}</dd>
                        <dt class="col-sm-4">Pisa</dt>
                        <dd class="col-sm-8">{{ $peserta->pisa }}</dd>
                        <dt class="col-sm-4">Jenis Kelamin</dt>
                        <dd class="col-sm-8">{{ $peserta->sex }}</dd>
                        <dt class="col-sm-4">Tanggal Lahir</dt>
                        <dd class="col-sm-8">{{ $peserta->tglLahir }}</dd>
                        <dt class="col-sm-4">Umur</dt>
                        <dd class="col-sm-8">{{ $peserta->umur->umurSekarang }}</dd>
                        <dt class="col-sm-4">Status Peserta</dt>
                        <dd class="col-sm-8">
                            {{ $peserta->statusPeserta->keterangan }}
                        </dd>
                        <dt class="col-sm-4">Hak Kelas</dt>
                        <dd class="col-sm-8">{{ $peserta->hakKelas->keterangan }}
                        </dd>
                        <dt class="col-sm-4">Jenis Peserta</dt>
                        <dd class="col-sm-8">{{ $peserta->jenisPeserta->keterangan }}
                        </dd>
                        <dt class="col-sm-4">Faskes 1</dt>
                        <dd class="col-sm-8">{{ $peserta->provUmum->nmProvider }} {{ $peserta->provUmum->kdProvider }}
                        </dd>
                        <dt class="col-sm-4">Tgl TAT</dt>
                        <dd class="col-sm-8">{{ $peserta->tglTAT }}</dd>
                        <dt class="col-sm-4">Tgl TMT</dt>
                        <dd class="col-sm-8">{{ $peserta->tglTMT }}</dd>
                        <dt class="col-sm-4">Tgl Cetak Kartu</dt>
                        <dd class="col-sm-8">{{ $peserta->tglCetakKartu }}</dd>
                    </dl>
                </x-adminlte-profile-widget>
            </div>
            <div class="col-md-8">
                @php
                    $heads = ['Tgl Masuk \ Pulang', 'No SEP \ Rujukam', 'Pasien', 'Pelayanan', 'Poliklinik', 'Diagnosa', 'Action'];
                    $config['order'] = ['0', 'desc'];
                @endphp
                <x-adminlte-card title="SEP Peserta" theme="primary" icon="fas fa-info-circle" collapsible>
                    <x-adminlte-datatable id="table1" class="nowrap" :heads="$heads" :config="$config" bordered hoverable
                        compressed>
                        @isset($sep)
                            @foreach ($sep as $item)
                                <tr>
                                    <td>
                                        Msk : {{ $item->tglSep }}<br>Plg : {{ $item->tglPlgSep }}
                                    </td>
                                    <td>
                                        SEP : {{ $item->noSep }}<br>REF : {{ $item->noRujukan }}
                                    </td>
                                    <td>
                                        {{ $item->noKartu }}<br>{{ $item->namaPeserta }}
                                    </td>
                                    <td>
                                        {{ $item->jnsPelayanan == 1 ? 'Rawat Inap' : 'Rawat Jalan' }}<br>{{ $item->ppkPelayanan }}
                                    </td>
                                    <td>
                                        {{ $item->poli }}<br>{{ $item->kelasRawat }}
                                    </td>
                                    <td>
                                        {{ $item->diagnosa }}
                                    </td>
                                    <td>
                                        <form action="{{ route('api.bpjs.vclaim.sep_delete') }}" method="POST">
                                            {{-- <x-adminlte-button class="btn-xs" theme="success" icon="fas fa-check"
                                                title="Edit User {{ $item->name }}"
                                                onclick="window.location='{{ route('user_verifikasi', $item) }}'" />
                                            <x-adminlte-button class="btn-xs" theme="warning" icon="fas fa-edit"
                                                title="Edit User {{ $item->name }}"
                                                onclick="window.location='{{ route('user.edit', $item) }}'" /> --}}
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="noSep" value="{{ $item->noSep }}">
                                            <x-adminlte-button class="btn-xs" theme="danger" icon="fas fa-trash-alt"
                                                type="submit"
                                                onclick="return confirm('Apakah anda akan menghapus SEP {{ $item->noSep }} ?')" />
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
            <div class="col-md-6">
                <x-adminlte-card title="Rujukan FKTP Peserta" theme="primary" icon="fas fa-info-circle" collapsible>
                    @php
                        $heads = ['Tgl Rujukan', 'Rujukan / FKTP', 'Poli Tujuan', 'Diagnosa'];
                        $config['order'] = ['0', 'ASC'];
                    @endphp
                    <x-adminlte-datatable id="table2" class="nowrap" :heads="$heads" :config="$config" bordered hoverable
                        compressed>
                        @isset($rujukan)
                            @foreach ($rujukan as $rujukan_fktp)
                                <tr>
                                    <td>
                                        {{ $rujukan_fktp->tglKunjungan }}
                                    </td>
                                    <td>
                                        {{ $rujukan_fktp->noKunjungan }}
                                        <br>
                                        {{ $rujukan_fktp->provPerujuk->nama }}
                                    </td>
                                    <td>
                                        {{ $rujukan_fktp->pelayanan->nama }}
                                        <br>
                                        {{ $rujukan_fktp->poliRujukan->nama }} {{ $rujukan_fktp->poliRujukan->kode }}
                                    </td>
                                    <td>
                                        {{ $rujukan_fktp->diagnosa->kode }} {{ $rujukan_fktp->diagnosa->nama }}
                                        <br>
                                        {{ $rujukan_fktp->keluhan }}
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
            <div class="col-md-6">
                <x-adminlte-card title="Rujukan Antar RS Peserta" theme="primary" icon="fas fa-info-circle" collapsible>
                    @php
                        $heads = ['Tgl Rujukan', 'Rujukan / FKTP', 'Poli Tujuan', 'Diagnosa'];
                        $config['order'] = ['0', 'ASC'];
                    @endphp
                    <x-adminlte-datatable id="table3" class="nowrap" :heads="$heads" :config="$config" bordered hoverable
                        compressed>
                        @isset($rujukan_rs)
                            @foreach ($rujukan_rs as $rujukan_ars)
                                <tr>
                                    <td>
                                        {{ $rujukan_ars->tglKunjungan }}
                                    </td>
                                    <td>
                                        {{ $rujukan_ars->noKunjungan }}
                                        <br>
                                        {{ $rujukan_ars->provPerujuk->nama }}
                                    </td>
                                    <td>
                                        {{ $rujukan_ars->pelayanan->nama }}
                                        <br>
                                        {{ $rujukan_ars->poliRujukan->nama }} {{ $rujukan_ars->poliRujukan->kode }}
                                    </td>
                                    <td>
                                        {{ $rujukan_ars->diagnosa->kode }} {{ $rujukan_ars->diagnosa->nama }}
                                        <br>
                                        {{ $rujukan_ars->keluhan }}
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
            <div class="col-md-12">
                <x-adminlte-card title="Surat Kontrol Peserta" theme="primary" icon="fas fa-info-circle" collapsible>
                    @php
                        $heads = ['Tgl Kontrol', 'Tgl Terbit', 'No S. Kontrol', 'Pelayanan', 'Polklinik', 'Dokter', 'Tgl SEP', 'Status', 'Action'];
                        $config['order'] = ['1', 'DESC'];
                    @endphp
                    <x-adminlte-datatable id="table4" class="nowrap" :heads="$heads" :config="$config" striped bordered
                        hoverable compressed>
                        @isset($surat_kontrol)
                            @foreach ($surat_kontrol as $suratkontrol)
                                <tr>
                                    <td>
                                        {{ $suratkontrol->tglRencanaKontrol }}
                                    </td>
                                    <td>
                                        {{ $suratkontrol->tglTerbitKontrol }}
                                    </td>
                                    <td>
                                        {{ $suratkontrol->noSuratKontrol }}
                                        <br>
                                        REF : {{ $suratkontrol->noSepAsalKontrol }}
                                    </td>
                                    <td>
                                        {{ $suratkontrol->jnsPelayanan }}
                                        <br>
                                        {{ $suratkontrol->namaJnsKontrol }}
                                    </td>
                                    <td>
                                        Asal : {{ $suratkontrol->namaPoliAsal }}
                                        <br>
                                        Tujuan : {{ $suratkontrol->namaPoliTujuan }}
                                    </td>
                                    <td>
                                        {{ $suratkontrol->kodeDokter }}
                                        <br>
                                        {{ $suratkontrol->namaDokter }}
                                    </td>
                                    <td>
                                        {{ $suratkontrol->tglSEP }}
                                    </td>
                                    <td>
                                        {{ $suratkontrol->terbitSEP }}
                                    </td>
                                    <td>
                                        <form action="{{ route('api.bpjs.vclaim.suratkontrol_delete') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="noSuratKontrol"
                                                value="{{ $suratkontrol->noSuratKontrol }}">
                                            <x-adminlte-button class="btn-xs" theme="danger" icon="fas fa-trash-alt"
                                                type="submit" data-toggle="tooltip"
                                                title="Hapus Surat Kontrol {{ $suratkontrol->noSuratKontrol }}"
                                                onclick="return confirm('Apakah anda akan menghapus Surat Kontrol {{ $suratkontrol->noSuratKontrol }} ?')" />
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        @endisset
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
        @endisset
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
