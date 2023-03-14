@extends('adminlte::page')
@section('title', 'Antrian Offline Pendaftaran')
@section('content_header')
    <h1>Antrian Offline Pendaftaran</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Antrian Offline Pasien" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $config = ['format' => 'YYYY-MM-DD'];
                            @endphp
                            <x-adminlte-input-date name="tanggal" label="Tanggal Antrian Pasien" :config="$config"
                                value="{{ \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d') }}">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-select name="jenispasien" label="Jenis Pasien">
                                <option value="JKN" {{ $request->jenispasien == 'JKN' ? 'selected' : null }}>BPJS
                                </option>
                                <option value="NON-JKN" {{ $request->jenispasien == 'NON-JKN' ? 'selected' : null }}>UMUM
                                </option>
                            </x-adminlte-select>
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-select name="loket" label="Loket">
                                <x-adminlte-options :options="[
                                    1 => 'Loket 1',
                                    2 => 'Loket 2',
                                    3 => 'Loket 3',
                                    4 => 'Loket 4',
                                    5 => 'Loket 5',
                                ]" :selected="$request->loket ?? 1" />
                            </x-adminlte-select>
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-select name="lantai" label="Lantai">
                                <x-adminlte-options :options="[
                                    1 => 'Lantai 1',
                                    2 => 'Lantai 2',
                                    3 => 'Lantai 3',
                                    4 => 'Lantai 4',
                                    5 => 'Lantai 5',
                                ]" :selected="$request->lantai ?? 1" />
                            </x-adminlte-select>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Pencarian" />
                </form>
            </x-adminlte-card>
            @if (isset($antrians))
                <div class="row">
                    <div class="col-md-3">
                        <x-adminlte-small-box
                            title="{{ $antrians->where('taskid', 2)->where('lantaipendaftaran', $request->lantai)->first()->nomorantrean ?? '0' }}"
                            text="Antrian Saat Ini" theme="primary" icon="fas fa-user-injured" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-small-box
                            title="{{ $antrians->where('method', 'Offline')->where('taskid', 0)->where('lantaipendaftaran', $request->lantai)->first()->nomorantrean ?? '0' }}"
                            text="Antrian Selanjutnya" theme="success" icon="fas fa-user-injured" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-small-box
                            title="{{ $antrians->where('method', 'Offline')->where('taskid', '<', 1)->where('lantaipendaftaran', $request->lantai)->count() }}"
                            text="Sisa Antrian" theme="warning" icon="fas fa-user-injured" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-small-box
                            title="{{ $antrians->where('method', 'Offline')->where('lantaipendaftaran', $request->lantai)->count() }}"
                            text="Total Antrian" theme="success" icon="fas fa-user-injured" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-card
                            title="Antrian Offline Pasien ({{ $antrians->where('method', 'Offline')->where('lantaipendaftaran', $request->lantai)->count() }} Orang)"
                            theme="warning" icon="fas fa-info-circle" collapsible>
                            @php
                                $heads = ['Antrian', 'Kunjungan', 'Status / Action'];
                                $config['order'] = ['2', 'asc'];
                                $config['paging'] = false;
                                $config['info'] = false;
                                $config['scrollY'] = '400px';
                                $config['scrollCollapse'] = true;
                                $config['scrollX'] = true;
                            @endphp
                            <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" :config="$config"
                                striped bordered hoverable compressed>
                                @foreach ($antrians->where('method', 'Offline')->where('lantaipendaftaran', $request->lantai)->where('jenispasien', $request->jenispasien) as $item)
                                    <tr>
                                        <td>
                                            {{ $item->angkaantrean }}<br>
                                            {{ $item->nomorantrean }}<br>
                                        </td>
                                        {{-- <td>
                                            RM : {{ $item->norm }}<br>
                                            <b>{{ $item->nama }}</b>
                                            @isset($item->nomorkartu)
                                                <br>{{ $item->nomorkartu }}
                                            @endisset
                                        </td> --}}
                                        <td>
                                            @if ($item->jeniskunjungan == 0)
                                                Offline
                                            @endif
                                            @if ($item->jeniskunjungan == 1)
                                                Rujukan FKTP
                                            @endif
                                            @if ($item->jeniskunjungan == 3)
                                                Kontrol
                                            @endif
                                            @if ($item->jeniskunjungan == 4)
                                                Rujukan RS
                                            @endif
                                            <br>{{ $item->jenispasien }}
                                            @if ($item->pasienbaru == 1)
                                                <span class="badge bg-secondary">Baru</span>
                                            @endif
                                            @if ($item->pasienbaru == 0)
                                                <span class="badge bg-secondary">Lama</span>
                                            @endif
                                            <span class="badge bg-success">{{ $item->kodepoli }}</span>
                                            <br>{{ substr($item->namadokter, 0, 19) }}...
                                        </td>
                                        <td>
                                            @if ($item->taskid == 0)
                                                <span class="badge bg-secondary">0. Antri Pendaftaran</span>
                                            @endif
                                            @if ($item->taskid == 1)
                                                <span class="badge bg-secondary">{{ $item->taskid }}. Chekcin</span>
                                            @endif
                                            @if ($item->taskid == 2)
                                                <span class="badge bg-secondary">{{ $item->taskid }}. Pendaftaran</span>
                                            @endif
                                            @if ($item->taskid == 3)
                                                @if ($item->status_api == 0)
                                                    <span class="badge bg-warning">{{ $item->taskid }}. Belum
                                                        Pembayaran</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $item->taskid }}. Tunggu Poli</span>
                                                @endif
                                            @endif
                                            @if ($item->taskid == 4)
                                                <span class="badge bg-success">{{ $item->taskid }}. Periksa Poli</span>
                                            @endif
                                            @if ($item->taskid == 5)
                                                @if ($item->status_api == 0)
                                                    <span class="badge bg-success">{{ $item->taskid }}. Tunggu
                                                        Farmasi</span>
                                                @endif
                                                @if ($item->status_api == 1)
                                                    <span class="badge bg-success">{{ $item->taskid }}. Selesai</span>
                                                @endif
                                            @endif
                                            @if ($item->taskid == 6)
                                                <span class="badge bg-success">{{ $item->taskid }}. Racik Obat</span>
                                            @endif
                                            @if ($item->taskid == 7)
                                                <span class="badge bg-success">{{ $item->taskid }}. Selesai</span>
                                            @endif
                                            @if ($item->taskid == 99)
                                                <span class="badge bg-danger">{{ $item->taskid }}. Batal</span>
                                            @endif
                                            <br>
                                            @if ($item->taskid == 0)
                                                <x-adminlte-button class="btn-xs mt-1 withLoad" label="Panggil"
                                                    theme="success" icon="fas fa-volume-down" data-toggle="tooltip"
                                                    title="Panggil Antrian {{ $item->nomorantrean }}"
                                                    onclick="window.location='{{ route('pendaftaran.panggil_pendaftaran', [$item->kodebooking, $request->loket, $request->lantai]) }}'" />
                                            @endif
                                            <x-adminlte-button class="btn-xs mt-1 withLoad" theme="danger"
                                                icon="fas fa-times" data-toggle="tooltop"
                                                title="Batal Antrian {{ $item->nomorantrean }}"
                                                onclick="window.location='{{ route('poliklinik.antrian_batal', $item) }}'" />
                                        </td>
                                        {{-- <td>
                                            @isset($item->nomorsep)
                                                SEP : {{ $item->nomorsep }}
                                            @else
                                                SEP : -
                                            @endisset
                                            @isset($item->nomorrujukan)
                                                <br>Rujukan : {{ $item->nomorrujukan }}
                                            @else
                                                <br>Rujukan : -
                                            @endisset
                                            @isset($item->nomorsuratkontrol)
                                                <br>S. Kontrol : {{ $item->nomorsuratkontrol }}
                                            @else
                                                <br>S. Kontrol : -
                                            @endisset

                                        </td> --}}
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>
                        </x-adminlte-card>
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-card theme="success" icon="fas fa-info-circle" collapsible
                            title="Proses Pendaftaran Antrian Offline ({{ $antrians->where('taskid', 2)->where('lantaipendaftaran', $request->lantai)->count() }} Orang)">
                            @php
                                $heads = ['Antrian', 'Kunjungan', 'Status/Action'];
                                $config['order'] = ['2', 'asc'];
                                $config['paging'] = false;
                                $config['info'] = false;
                                $config['scrollY'] = '400px';
                                $config['scrollCollapse'] = true;
                                $config['scrollX'] = true;
                            @endphp
                            <x-adminlte-datatable id="table4" class="nowrap text-xs" :heads="$heads" :config="$config"
                                striped bordered hoverable compressed>
                                @foreach ($antrians->where('taskid', 2)->where('lantaipendaftaran', $request->lantai) as $antrian)
                                    <tr>
                                        <td>
                                            {{ $antrian->angkaantrean }}<br>
                                            {{ $antrian->nomorantrean }}<br>
                                        </td>
                                        <td>
                                            @if ($antrian->jeniskunjungan == 0)
                                                Offline
                                            @endif
                                            @if ($antrian->jeniskunjungan == 1)
                                                Rujukan FKTP
                                            @endif
                                            @if ($antrian->jeniskunjungan == 3)
                                                Kontrol
                                            @endif
                                            @if ($antrian->jeniskunjungan == 4)
                                                Rujukan RS
                                            @endif
                                            <br>{{ $antrian->jenispasien }}
                                            @if ($antrian->pasienbaru == 1)
                                                <span class="badge bg-secondary">Baru</span>
                                            @endif
                                            @if ($antrian->pasienbaru == 0)
                                                <span class="badge bg-secondary">Lama</span>
                                            @endif
                                            <span class="badge bg-success">{{ $antrian->kodepoli }}</span>
                                            <br>{{ substr($antrian->namadokter, 0, 19) }}...
                                        </td>
                                        {{-- <td>
                                            RM : {{ $antrian->norm }}<br>
                                            <b>{{ $antrian->nama }}</b>
                                            @isset($antrian->nomorkartu)
                                                <br>{{ $antrian->nomorkartu }}
                                            @endisset

                                        </td> --}}
                                        <td>
                                            @if ($antrian->taskid == 0)
                                                <span class="badge bg-secondary">0. Antri Pendaftaran</span>
                                            @endif
                                            @if ($antrian->taskid == 1)
                                                <span class="badge bg-secondary">{{ $antrian->taskid }}. Chekcin</span>
                                            @endif
                                            @if ($antrian->taskid == 2)
                                                <span class="badge bg-secondary">{{ $antrian->taskid }}. Pendaftaran</span>
                                            @endif
                                            @if ($antrian->taskid == 3)
                                                @if ($antrian->status_api == 0)
                                                    <span class="badge bg-warning">{{ $antrian->taskid }}. Belum
                                                        Pembayaran</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $antrian->taskid }}. Tunggu
                                                        Poli</span>
                                                @endif
                                            @endif
                                            @if ($antrian->taskid == 4)
                                                <span class="badge bg-success">{{ $antrian->taskid }}. Periksa Poli</span>
                                            @endif
                                            @if ($antrian->taskid == 5)
                                                @if ($antrian->status_api == 0)
                                                    <span class="badge bg-success">{{ $antrian->taskid }}. Tunggu
                                                        Farmasi</span>
                                                @endif
                                                @if ($antrian->status_api == 1)
                                                    <span class="badge bg-success">{{ $antrian->taskid }}. Selesai</span>
                                                @endif
                                            @endif
                                            @if ($antrian->taskid == 6)
                                                <span class="badge bg-success">{{ $antrian->taskid }}. Racik Obat</span>
                                            @endif
                                            @if ($antrian->taskid == 7)
                                                <span class="badge bg-success">{{ $antrian->taskid }}. Selesai</span>
                                            @endif
                                            @if ($antrian->taskid == 99)
                                                <span class="badge bg-danger">{{ $antrian->taskid }}. Batal</span>
                                            @endif
                                            <br>
                                            @if ($antrian->taskid == 2)
                                                <x-adminlte-button class="btn-xs mt-1 withLoad btnLayani" label="Layani"
                                                    theme="success" icon="fas fa-volume-down" data-toggle="tooltip"
                                                    title="Layani Antrian {{ $antrian->nomorantrean }}"
                                                    data-id="{{ $antrian->id }}" />
                                                <x-adminlte-button class="btn-xs mt-1" label="Panggil" theme="primary"
                                                    icon="fas fa-volume-down" data-toggle="tooltip"
                                                    title="Panggil Antrian {{ $antrian->nomorantrean }}"
                                                    onclick="window.location='{{ route('pendaftaran.panggil_pendaftaran', [$antrian->kodebooking, $request->loket, $request->lantai]) }}'" />
                                            @endif
                                            <x-adminlte-button class="btn-xs mt-1 withLoad" theme="danger"
                                                icon="fas fa-times" data-toggle="tooltop"
                                                title="Batal Antrian {{ $antrian->nomorantrean }}"
                                                onclick="window.location='{{ route('poliklinik.antrian_batal', $antrian) }}'" />
                                        </td>

                                        {{-- <td>
                                            @isset($antrian->nomorsep)
                                                SEP : {{ $antrian->nomorsep }}
                                            @else
                                                SEP : -
                                            @endisset
                                            @isset($antrian->nomorrujukan)
                                                <br>Rujukan : {{ $antrian->nomorrujukan }}
                                            @else
                                                <br>Rujukan : -
                                            @endisset
                                            @isset($antrian->nomorsuratkontrol)
                                                <br>S. Kontrol : {{ $antrian->nomorsuratkontrol }}
                                            @else
                                                <br>S. Kontrol : -
                                            @endisset

                                        </td> --}}
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>
                        </x-adminlte-card>
                    </div>
                    <div class="col-md-12">
                        <x-adminlte-card title="Total Data Antrian Pasien ({{ $antrians->count() }} Orang)"
                            theme="warning" icon="fas fa-info-circle" collapsible='collapsed'>
                            @php
                                $heads = ['No Antrian', 'Pasien', 'Status / Action', 'Kunjungan', 'SEP / Ref'];
                                $config['order'] = ['2', 'asc'];
                                $config['paging'] = false;
                                $config['info'] = false;
                                $config['scrollY'] = '400px';
                                $config['scrollCollapse'] = true;
                                $config['scrollX'] = true;
                            @endphp
                            <x-adminlte-datatable id="table5" class="nowrap text-xs" :heads="$heads"
                                :config="$config" striped bordered hoverable compressed>
                                @foreach ($antrians as $item)
                                    <tr>
                                        <td>
                                            {{ $item->angkaantrean }}<br>
                                            {{ $item->nomorantrean }}<br>
                                        </td>
                                        <td>
                                            RM : {{ $item->norm }}<br>
                                            <b>{{ $item->nama }}</b>
                                            @isset($item->nomorkartu)
                                                <br>{{ $item->nomorkartu }}
                                            @endisset
                                        </td>
                                        <td>
                                            @if ($item->taskid == 0)
                                                <span class="badge bg-secondary">0. Antri Pendaftaran</span>
                                            @endif
                                            @if ($item->taskid == 1)
                                                <span class="badge bg-secondary">{{ $item->taskid }}. Chekcin</span>
                                            @endif
                                            @if ($item->taskid == 2)
                                                <span class="badge bg-secondary">{{ $item->taskid }}. Pendaftaran</span>
                                            @endif
                                            @if ($item->taskid == 3)
                                                @if ($item->status_api == 0)
                                                    <span class="badge bg-warning">{{ $item->taskid }}. Belum
                                                        Pembayaran</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $item->taskid }}. Tunggu Poli</span>
                                                @endif
                                            @endif
                                            @if ($item->taskid == 4)
                                                <span class="badge bg-success">{{ $item->taskid }}. Periksa Poli</span>
                                            @endif
                                            @if ($item->taskid == 5)
                                                @if ($item->status_api == 0)
                                                    <span class="badge bg-success">{{ $item->taskid }}. Tunggu
                                                        Farmasi</span>
                                                @endif
                                                @if ($item->status_api == 1)
                                                    <span class="badge bg-success">{{ $item->taskid }}. Selesai</span>
                                                @endif
                                            @endif
                                            @if ($item->taskid == 6)
                                                <span class="badge bg-success">{{ $item->taskid }}. Racik Obat</span>
                                            @endif
                                            @if ($item->taskid == 7)
                                                <span class="badge bg-success">{{ $item->taskid }}. Selesai</span>
                                            @endif
                                            @if ($item->taskid == 99)
                                                <span class="badge bg-danger">{{ $item->taskid }}. Batal</span>
                                            @endif
                                            <br>
                                            @if ($item->taskid == 3)
                                                @if ($item->status_api == 1)
                                                    <x-adminlte-button class="btn-xs mt-1 withLoad" label="Panggil"
                                                        theme="warning" icon="fas fa-volume-down" data-toggle="tooltip"
                                                        title="Panggil Antrian {{ $item->nomorantrean }}"
                                                        onclick="window.location='{{ route('poliklinik.antrian_panggil', $item) }}'" />
                                                @endif
                                            @endif
                                            <x-adminlte-button class="btn-xs mt-1 withLoad" theme="danger"
                                                icon="fas fa-times" data-toggle="tooltop"
                                                title="Batal Antrian {{ $item->nomorantrean }}"
                                                onclick="window.location='{{ route('poliklinik.antrian_batal', $item) }}'" />
                                        </td>

                                        <td>
                                            @if ($item->jeniskunjungan == 0)
                                                Offline
                                            @endif
                                            @if ($item->jeniskunjungan == 1)
                                                Rujukan FKTP
                                            @endif
                                            @if ($item->jeniskunjungan == 3)
                                                Kontrol
                                            @endif
                                            @if ($item->jeniskunjungan == 4)
                                                Rujukan RS
                                            @endif
                                            <br>{{ $item->jenispasien }}
                                            @if ($item->pasienbaru == 1)
                                                <span class="badge bg-secondary">Baru</span>
                                            @endif
                                            @if ($item->pasienbaru == 0)
                                                <span class="badge bg-secondary">Lama</span>
                                            @endif
                                            @if (isset($item->method))
                                                <span class="badge bg-secondary">{{ $item->method }}</span>
                                            @else
                                                <span class="badge bg-secondary">NULL</span>
                                            @endif
                                            <span class="badge bg-success">{{ $item->kodepoli }}</span>
                                            <br>{{ substr($item->namadokter, 0, 19) }}...
                                        </td>


                                        <td>
                                            @isset($item->nomorsep)
                                                SEP : {{ $item->nomorsep }}
                                            @else
                                                SEP : -
                                            @endisset
                                            @isset($item->nomorrujukan)
                                                <br>Rujukan : {{ $item->nomorrujukan }}
                                            @else
                                                <br>Rujukan : -
                                            @endisset
                                            @isset($item->nomorsuratkontrol)
                                                <br>S. Kontrol : {{ $item->nomorsuratkontrol }}
                                            @else
                                                <br>S. Kontrol : -
                                            @endisset

                                        </td>
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>
                        </x-adminlte-card>
                    </div>
                </div>
                <x-adminlte-modal id="modalPelayanan" title="Pendaftaran Pasien" size="xl" theme="success"
                    icon="fas fa-user-plus" v-centered static-backdrop scrollable>
                    <form name="formLayanan" id="formLayanan" action="" method="post">
                        @csrf
                        <x-adminlte-card theme="primary" title="Informasi Kunjungan Berobat">
                            <input type="hidden" name="antrianid" id="antrianid" value="">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Kode Booking</dt>
                                        <dd class="col-sm-7">: <span id="kodebooking"></span></dd>
                                        <dt class="col-sm-5">Antrian</dt>
                                        <dd class="col-sm-7">: <span id="angkaantrean"></span> / <span
                                                id="nomorantrean"></span>
                                        </dd>
                                        <dt class="col-sm-5 ">Tanggal Perikasa</dt>
                                        <dd class="col-sm-7">: <span id="tanggalperiksa"></span></dd>
                                        <dt class="col-sm-5">Metode Daftar</dt>
                                        <dd class="col-sm-7">: <span id="method"></span></dd>
                                        <dt class="col-sm-5">Poliklinik</dt>
                                        <dd class="col-sm-7">: <span id="namapoli"></span></dd>
                                        <dt class="col-sm-5">Dokter</dt>
                                        <dd class="col-sm-7">: <span id="namadokter"></span></dd>
                                        <dt class="col-sm-5">Jadwal</dt>
                                        <dd class="col-sm-7">: <span id="jampraktek"></span></dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">No RM</dt>
                                        <dd class="col-sm-7">: <span id="norm"></span></dd>
                                        <dt class="col-sm-5">NIK</dt>
                                        <dd class="col-sm-7">: <span id="nik"></span></dd>
                                        <dt class="col-sm-5">No BPJS</dt>
                                        <dd class="col-sm-7">: <span id="nomorkartu"></span></dd>
                                        <dt class="col-sm-5">Nama</dt>
                                        <dd class="col-sm-7">: <span id="nama"></span></dd>
                                        <dt class="col-sm-5">No HP</dt>
                                        <dd class="col-sm-7">: <span id="nohp"></span></dd>
                                        <dt class="col-sm-5">Jenis Pasien</dt>
                                        <dd class="col-sm-7">: <span id="jenispasien"></span></dd>
                                    </dl>
                                </div>
                            </div>
                        </x-adminlte-card>
                        <x-adminlte-card theme="primary" title="Pendaftaran Pasien Baru" collapsible="collapsed">
                            <div class="row">
                                Kosong
                            </div>
                        </x-adminlte-card>
                        <x-slot name="footerSlot">
                            <a href="#" id="lanjutFarmasi" class="btn btn-success mr-auto withLoad"> <i
                                    class="fas fa-prescription-bottle-alt"></i> Lanjut Poliklinik</a>
                            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
                        </x-slot>
                    </form>
                </x-adminlte-modal>
            @endif
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)

@section('js')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.btnLayani').click(function() {
                var antrianid = $(this).data('id');
                $.LoadingOverlay("show");
                var url = "{{ route('antrian.index') }}" + '/' + antrianid + '/edit';
                $.get(url, function(data) {
                    console.log(data);
                    $('#kodebooking').html(data.kodebooking);
                    $('#angkaantrean').html(data.angkaantrean);
                    $('#nomorantrean').html(data.nomorantrean);
                    $('#tanggalperiksa').html(data.tanggalperiksa);
                    $('#norm').html(data.norm);
                    $('#nik').html(data.nik);
                    $('#nomorkartu').html(data.nomorkartu);
                    $('#nama').html(data.nama);
                    $('#nohp').html(data.nohp);
                    $('#jenispasien').html(data.jenispasien);

                    $('#method').html(data.method);
                    $('#namapoli').html(data.namapoli);
                    $('#namadokter').html(data.namadokter);
                    $('#jampraktek').html(data.jampraktek);

                    // switch (data.jeniskunjungan) {
                    //     case "1":
                    //         var jeniskunjungan = "Rujukan FKTP";
                    //         break;
                    //     case "2":
                    //         var jeniskunjungan = "Rujukan Internal";
                    //         break;
                    //     case "3":
                    //         var jeniskunjungan = "Kontrol";
                    //         break;
                    //     case "4":
                    //         var jeniskunjungan = "Rujukan Antar RS";
                    //         break;
                    //     default:
                    //         break;
                    // }
                    // $('#user').html(data.user);
                    // $('#antrianid').val(antrianid);
                    // $('#namapoli').val(data.namapoli);
                    // $('#namap').val(data.kodepoli);
                    // $('#namadokter').val(data.namadokter);
                    // $('#kodepoli').val(data.kodepoli);
                    // $('#kodedokter').val(data.kodedokter);
                    // $('#jampraktek').val(data.jampraktek);
                    // $('#nomorsep_suratkontrol').val(data.nomorsep);
                    // $('#kodepoli_suratkontrol').val(data.kodepoli);
                    // $('#namapoli_suratkontrol').val(data.namapoli);
                    var urlLanjutFarmasi = "{{ route('landingpage') }}" +
                        "/pendaftaran/selesai_pendaftaran/" + data
                        .kodebooking;
                    $("#lanjutFarmasi").attr("href", urlLanjutFarmasi);
                    // var urlSelesaiPoliklinik = "{{ route('landingpage') }}" +
                    //     "/poliklinik/selesai_poliklinik/" + data
                    //     .kodebooking;
                    // $("#selesaiPoliklinik").attr("href", urlSelesaiPoliklinik);
                    $('#modalPelayanan').modal('show');
                    $.LoadingOverlay("hide", true);
                })
            });
        });
    </script>
@endsection
