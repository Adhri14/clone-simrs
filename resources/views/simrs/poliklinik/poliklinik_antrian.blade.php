@extends('adminlte::page')
@section('title', 'Antrian Pasien Poliklinik')
@section('content_header')
    <h1>Antrian Pasien Poliklinik</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Antrian Pasien" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <x-adminlte-select2 name="kodepoli" label="Poliklinik">
                                <option value="">SEMUA POLIKLINIK (-)</option>
                                @foreach ($polis as $item)
                                    <option value="{{ $item->kodesubspesialis }}"
                                        {{ $item->kodesubspesialis == $request->kodepoli ? 'selected' : null }}>
                                        {{ $item->namasubspesialis }} ({{ $item->kodesubspesialis }})
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                        <div class="col-md-4">
                            <x-adminlte-select2 name="kodedokter" label="Dokter">
                                <option value="">SEMUA DOKTER (-)</option>
                                @foreach ($dokters as $item)
                                    <option value="{{ $item->kode_dokter_jkn }}"
                                        {{ $item->kode_dokter_jkn == $request->kodedokter ? 'selected' : null }}>
                                        {{ $item->nama_paramedis }} ({{ $item->kode_dokter_jkn }})
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Pencarian" />
                </form>
            </x-adminlte-card>
            @if (isset($antrians))
                <div class="row">
                    <div class="col-md-3">
                        <x-adminlte-small-box title="{{ $antrians->where('taskid', 4)->first()->nomorantrean ?? '0' }}"
                            text="Antrian Saat Ini" theme="primary" icon="fas fa-user-injured" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-small-box
                            title="{{ $antrians->where('taskid', 3)->where('status_api', 1)->first()->nomorantrean ?? '0' }}"
                            text="Antrian Selanjutnya" theme="success" icon="fas fa-user-injured" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-small-box
                            title="{{ $antrians->where('taskid', '<', 4)->where('taskid', '>=', 1)->count() }}"
                            text="Sisa Antrian" theme="warning" icon="fas fa-user-injured" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-small-box title="{{ $antrians->count() }}" text="Total Antrian" theme="success"
                            icon="fas fa-user-injured" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-card
                            title="Antrian Tunggu Poliklinik ({{ $antrians->where('taskid', '!=', 4)->count() }} Orang)"
                            theme="warning" icon="fas fa-info-circle" collapsible>
                            @php
                                $heads = ['Kodeboking', 'Kunjungan', 'Pasien', 'Action', 'SEP / Ref'];
                                $config['order'] = ['3', 'asc'];
                                $config['paging'] = false;
                                $config['info'] = false;
                                $config['scrollY'] = '400px';
                                $config['scrollCollapse'] = true;
                                $config['scrollX'] = true;
                            @endphp
                            <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" :config="$config"
                                striped bordered hoverable compressed>
                                @foreach ($antrians->where('taskid', '!=', 4) as $item)
                                    <tr>
                                        <td>
                                            {{ $item->nomorantrean }}<br>
                                            {{ $item->kodebooking }}<br>

                                        </td>
                                        <td>
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
                                            RM : {{ $item->norm }}<br>
                                            <b>{{ $item->nama }}</b>
                                            @isset($item->nomorkartu)
                                                <br>{{ $item->nomorkartu }}
                                            @endisset
                                        </td>
                                        <td>
                                            @if ($item->taskid == 0)
                                                <span class="badge bg-secondary">0. Belum Checkin</span>
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
                    <div class="col-md-6">
                        <x-adminlte-card theme="success" icon="fas fa-info-circle" collapsible
                            title="Antrian Pelayanan Poliklinik ({{ $antrians->where('taskid', 4)->count() }} Orang)">
                            @php
                                $heads = ['Kodeboking', 'Kunjungan', 'Pasien', 'Action', 'SEP / Ref'];
                                $config['order'] = ['3', 'asc'];
                                $config['paging'] = false;
                                $config['info'] = false;
                                $config['scrollY'] = '400px';
                                $config['scrollCollapse'] = true;
                                $config['scrollX'] = true;
                            @endphp
                            <x-adminlte-datatable id="table4" class="nowrap text-xs" :heads="$heads" :config="$config"
                                striped bordered hoverable compressed>
                                @foreach ($antrians->where('taskid', 4) as $antrian)
                                    <tr>
                                        <td>
                                            {{ $antrian->nomorantrean }}<br>
                                            {{ $antrian->kodebooking }}<br>
                                        </td>
                                        <td>
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
                                            @if (isset($antrian->method))
                                                <span class="badge bg-secondary">{{ $antrian->method }}</span>
                                            @else
                                                <span class="badge bg-secondary">NULL</span>
                                            @endif
                                            <span class="badge bg-success">{{ $antrian->kodepoli }}</span>
                                            <br>{{ substr($antrian->namadokter, 0, 19) }}...
                                        </td>
                                        <td>
                                            RM : {{ $antrian->norm }}<br>
                                            <b>{{ $antrian->nama }}</b>
                                            @isset($antrian->nomorkartu)
                                                <br>{{ $antrian->nomorkartu }}
                                            @endisset
                                        </td>
                                        <td>
                                            @if ($antrian->taskid == 0)
                                                <span class="badge bg-secondary">Belum Chekcin</span>
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
                                            @if ($antrian->taskid == 4)
                                                <x-adminlte-button class="btn-xs mt-1 withLoad" label="Panggil"
                                                    theme="primary" icon="fas fa-volume-down" data-toggle="tooltip"
                                                    title="Panggil Antrian {{ $antrian->nomorantrean }}"
                                                    onclick="window.location='{{ route('poliklinik.antrian_panggil_ulang', $antrian) }}'" />

                                                <x-adminlte-button class="btn-xs mt-1 btnLayani" label="Layani"
                                                    theme="success" icon="fas fa-hand-holding-medical"
                                                    data-toggle="tooltop"
                                                    title="Layani Pasien {{ $antrian->nomorantrean }}"
                                                    data-id="{{ $antrian->id }}" />
                                            @endif
                                            <x-adminlte-button class="btn-xs mt-1 withLoad" theme="danger"
                                                icon="fas fa-times" data-toggle="tooltop"
                                                title="Batal Antrian {{ $antrian->nomorantrean }}"
                                                onclick="window.location='{{ route('poliklinik.antrian_batal', $antrian) }}'" />
                                        </td>
                                        <td>
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

                                        </td>
                                    </tr>
                                @endforeach
                            </x-adminlte-datatable>
                        </x-adminlte-card>
                    </div>
                </div>
                <x-adminlte-modal id="modalPelayanan" title="Pelayanan Pasien Poliklinik" size="xl" theme="success"
                    icon="fas fa-user-plus" v-centered static-backdrop scrollable>
                    <form name="formLayanan" id="formLayanan" action="" method="post">
                        @csrf
                        <input type="hidden" name="antrianid" id="antrianid" value="">
                        <dl class="row">
                            <dt class="col-sm-3">Kode Booking</dt>
                            <dd class="col-sm-8">: <span id="kodebooking"></span></dd>
                            <dt class="col-sm-3">Antrian</dt>
                            <dd class="col-sm-8">: <span id="angkaantrean"></span> / <span id="nomorantrean"></span>
                            </dd>
                            <dt class="col-sm-3 ">Tanggal Perikasa</dt>
                            <dd class="col-sm-8">: <span id="tanggalperiksa"></span></dd>
                        </dl>
                        <x-adminlte-card theme="primary" title="Informasi Kunjungan Berobat">
                            <div class="row">
                                <div class="col-md-5">
                                    <dl class="row">
                                        <dt class="col-sm-4">No RM</dt>
                                        <dd class="col-sm-8">: <span id="norm"></span></dd>
                                        <dt class="col-sm-4">NIK</dt>
                                        <dd class="col-sm-8">: <span id="nik"></span></dd>
                                        <dt class="col-sm-4">No BPJS</dt>
                                        <dd class="col-sm-8">: <span id="nomorkartu"></span></dd>
                                        <dt class="col-sm-4">Nama</dt>
                                        <dd class="col-sm-8">: <span id="nama"></span></dd>
                                        <dt class="col-sm-4">No HP</dt>
                                        <dd class="col-sm-8">: <span id="nohp"></span></dd>
                                        <dt class="col-sm-4">Jenis Pasien</dt>
                                        <dd class="col-sm-8">: <span id="jenispasien"></span></dd>
                                    </dl>
                                </div>
                                <div class="col-md-7">
                                    <dl class="row">
                                        <dt class="col-sm-4">Jenis Kunjungan</dt>
                                        <dd class="col-sm-8">: <span id="jeniskunjungan"></span></dd>
                                        <dt class="col-sm-4">No SEP</dt>
                                        <dd class="col-sm-8">: <span id="nomorsep"></span></dd>
                                        <dt class="col-sm-4">No Rujukan</dt>
                                        <dd class="col-sm-8">: <span id="nomorrujukan"></span></dd>
                                        <dt class="col-sm-4">No Surat Kontrol</dt>
                                        <dd class="col-sm-8">: <span id="nomorsuratkontrol"></span></dd>
                                        <dt class="col-sm-4">Poliklinik</dt>
                                        <dd class="col-sm-8">: <span id="namapoli"></span></dd>
                                        <dt class="col-sm-4">Dokter</dt>
                                        <dd class="col-sm-8">: <span id="namadokter"></span></dd>
                                        <dt class="col-sm-4">Jadwal</dt>
                                        <dd class="col-sm-8">: <span id="jampraktek"></span></dd>
                                    </dl>
                                </div>
                            </div>
                        </x-adminlte-card>
                        <x-adminlte-card theme="primary" title="E-Rekam Medis Pasien" collapsible="collapsed">
                            <div class="row">
                                Kosong
                            </div>
                        </x-adminlte-card>
                        <x-slot name="footerSlot">
                            <x-adminlte-button class="mr-auto btnSuratKontrol" label="Buat Surat Kontrol" theme="primary"
                                icon="fas fa-prescription-bottle-alt" />
                            <a href="#" id="lanjutFarmasi" class="btn btn-success withLoad"> <i
                                    class="fas fa-prescription-bottle-alt"></i> Lanjut Farmasi</a>
                            <a href="#" id="selesaiPoliklinik" class="btn btn-warning withLoad"> <i
                                    class="fas fa-check"></i> Selesai</a>
                            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
                        </x-slot>
                    </form>
                </x-adminlte-modal>
                @if ($antrians->count() > 0)
                    {{-- <x-adminlte-modal id="modalKPO" name="modalKPO" title="Buat Surat Kontrol Rawat Jalan"
                        size="lg" theme="warning" icon="fas fa-prescription-bottle-alt" v-centered>
                        <form action="{{ route('vclaim.buat_surat_kontrol') }}" id="formSuratKontrol" method="post">
                            @csrf
                            @php
                                $config = [
                                    'format' => 'YYYY-MM-DD',
                                    'dayViewHeaderFormat' => 'MMM YYYY',
                                    'minDate' => "js:moment().startOf('month')",
                                    'daysOfWeekDisabled' => [0],
                                ];
                            @endphp
                            <x-adminlte-input name="nomorsep_suratkontrol" placeholder="Nomor SEP" label="Nomor SEP"
                                readonly />
                            <x-adminlte-input name="namapoli_suratkontrol" placeholder="Nama Poliklinik"
                                label="Poliklinik" readonly />
                            <x-adminlte-input-date name="tanggal_suratkontrol" label="Tanggal Surat Kontrol"
                                :config="$config" placeholder="Pilih Tanggal Surat Kontrol ..."
                                value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-date>
                            <input type="hidden" name="kodepoli_suratkontrol" id="kodepoli_suratkontrol">
                            <x-adminlte-select2 name="kodedokter_suratkontrol" label="DPJP Surat Kontrol">
                                @foreach ($dokters as $item)
                                    <option value="{{ $item->kode_dokter_jkn }}"
                                        {{ $item->kode_dokter_jkn == $request->kodedokter ? 'selected' : null }}>
                                        {{ $item->kode_dokter_jkn }} -
                                        {{ $item->nama_paramedis }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                            <x-slot name="footerSlot">
                                <button type="submit" form="formSuratKontrol" value="Submit"
                                    class="mr-auto btn btn-success">Buat Surat Kontrol</button>
                                <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal" />
                            </x-slot>
                        </form>
                    </x-adminlte-modal> --}}
                @endif
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
                $.get("{{ route('antrian.index') }}" + '/' + antrianid + '/edit', function(data) {
                    $('#kodebooking').html(data.kodebooking);
                    $('#angkaantrean').html(data.angkaantrean);
                    $('#nomorantrean').html(data.nomorantrean);
                    $('#tanggalperiksa').html(data.tanggalperiksa);
                    $('#norm').html(data.norm);
                    $('#nik').html(data.nik);
                    $('#nomorkartu').html(data.nomorkartu);
                    $('#nama').html(data.nama);
                    $('#nohp').html(data.nohp);
                    $('#nomorrujukan').html(data.nomorrujukan);
                    $('#nomorsuratkontrol').html(data.nomorsuratkontrol);
                    $('#nomorsep').html(data.nomorsep);
                    $('#jenispasien').html(data.jenispasien);
                    $('#namapoli').html(data.namapoli);
                    $('#namadokter').html(data.namadokter);
                    $('#jampraktek').html(data.jampraktek);
                    switch (data.jeniskunjungan) {
                        case "1":
                            var jeniskunjungan = "Rujukan FKTP";
                            break;
                        case "2":
                            var jeniskunjungan = "Rujukan Internal";
                            break;
                        case "3":
                            var jeniskunjungan = "Kontrol";
                            break;
                        case "4":
                            var jeniskunjungan = "Rujukan Antar RS";
                            break;
                        default:
                            break;
                    }
                    $('#jeniskunjungan').html(jeniskunjungan);
                    $('#user').html(data.user);
                    $('#antrianid').val(antrianid);
                    $('#namapoli').val(data.namapoli);
                    $('#namap').val(data.kodepoli);
                    $('#namadokter').val(data.namadokter);
                    $('#kodepoli').val(data.kodepoli);
                    $('#kodedokter').val(data.kodedokter);
                    $('#jampraktek').val(data.jampraktek);
                    $('#nomorsep_suratkontrol').val(data.nomorsep);
                    $('#kodepoli_suratkontrol').val(data.kodepoli);
                    $('#namapoli_suratkontrol').val(data.namapoli);
                    var urlLanjutFarmasi = "{{ route('landingpage') }}" +
                        "/poliklinik/lanjut_farmasi/" + data
                        .kodebooking;
                    $("#lanjutFarmasi").attr("href", urlLanjutFarmasi);
                    var urlSelesaiPoliklinik = "{{ route('landingpage') }}" +
                        "/poliklinik/selesai_poliklinik/" + data
                        .kodebooking;
                    $("#selesaiPoliklinik").attr("href", urlSelesaiPoliklinik);
                    $('#modalPelayanan').modal('show');
                    $.LoadingOverlay("hide", true);
                })
            });
            $('.btnSuratKontrol').click(function() {
                $('#modalKPO').modal('show');
            });
        });
    </script>
@endsection
