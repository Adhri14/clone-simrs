@extends('adminlte::page')

@section('title', 'Scan E-File Rekam Medis')

@section('content_header')
    <h1>Scan E-File Rekam Medis</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Data E-File Rekam Medis" theme="warning" collapsible>
                <a class="btn btn-success mb-1" href="{{ route('efilerm.create') }}">Scan E-File RM</a>
                @php
                    $heads = ['No.', 'No RM', ' Nama', 'Tgl Lahir', 'Nama Berkas', 'Tgl Scan', 'Jenis', 'Action'];
                    $config = [
                        'buttons' => [
                            [
                                'extend' => 'colvis',
                                'className' => 'btn-info',
                            ],
                            [
                                'extend' => 'print',
                                'className' => 'btn-info',
                            ],
                            [
                                'extend' => 'pdf',
                                'className' => 'btn-info',
                            ],
                        ],
                    ];
                @endphp
                <x-adminlte-datatable id="table1" class="nowrap" :heads="$heads" :config="$config" striped bordered
                    hoverable compressed with-buttons>
                    @foreach ($filerm as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->norm }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggallahir)->format('Y-m-d') }}</td>
                            <td>{{ $item->namafile }}</td>
                            <td>{{ $item->tanggalscan }}</td>
                            <td>
                                @if ($item->jenisberkas == 1)
                                    Rawat Inap
                                @endif
                                @if ($item->jenisberkas == 2)
                                    Rawat Jalan
                                @endif
                                @if ($item->jenisberkas == 3)
                                    Penunjang
                                @endif
                                @if ($item->jenisberkas == 4)
                                    Berkas Pasien
                                @endif
                            </td>
                            <td>
                                <a href="{{ $item->fileurl }}" target="_blank" class="btn btn-primary btn-xs"><i
                                        class=" fas fa-download"></i></a>
                                <x-adminlte-button icon="fas fa-eye" theme="warning" class="btn-xs" label="Lihat"
                                    data-toggle="modal" data-target="#modalMin" />
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modalMin" size="lg" theme="warning" title="E-File Rekam Medis">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <dt class="col-sm-4">Pasien</dt>
                    <dd class="col-sm-8">: </dd>
                    <dt class="col-sm-4">BPJS / NIK</dt>
                    <dd class="col-sm-8">: <span id="nomorkartu"></span> / <span id="nik"></span></dd>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <dt class="col-sm-4">Nama Berkas</dt>
                    <dd class="col-sm-8">:
                        <span id="namafile"></span> /
                        <span id="jenisberkas"></span>
                    </dd>
                    <dt class="col-sm-4">Tanggal Scan</dt>
                    <dd class="col-sm-8">: <span id="tanggalscan"></span></dd>
                </div>
            </div>
        </div>
        <iframe src="{{ asset('scanner/tmp/22101311285454821.pdf') }}" width="100%" height="500px">
        </iframe>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Select2', true)

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
                    // console.log(data);
                    $('#kodebooking').html(data.kodebooking);
                    $('#angkaantrean').html(data.angkaantrean);
                    $('#nomorantrean').html(data.nomorantrean);
                    $('#tanggalperiksa').html(data.tanggalperiksa);
                    $('#norm').html(data.norm);
                    $('#nik').html(data.nik);
                    $('#nomorkartu').html(data.nomorkartu);
                    $('#nama').html(data.nama);
                    $('#nohp').html(data.nohp);
                    // $('#nomorreferensi').html(data.nomorreferensi);
                    $('#nomorrujukan').html(data.nomorrujukan);
                    $('#nomorsuratkontrol').html(data.nomorsuratkontrol);
                    $('#nomorsep').html(data.nomorsep);
                    $('#jenispasien').html(data.jenispasien);
                    $('#namapoli').html(data.namapoli);
                    $('#namadokter').html(data.namadokter);
                    $('#jampraktek').html(data.jampraktek);
                    $('#jeniskunjungan').html(data.jeniskunjungan);
                    $('#user').html(data.user);
                    $('#antrianid').val(antrianid);
                    $('#namapoli').val(data.namapoli);
                    $('#namap').val(data.kodepoli);
                    $('#namadokter').val(data.namadokter);
                    $('#kodepoli').val(data.kodepoli);
                    $('#kodedokter').val(data.kodedokter);
                    $('#jampraktek').val(data.jampraktek);
                    // $('#kodepoli').val(data.kodepoli).trigger('change');
                    $('#nomorsep_suratkontrol').val(data.nomorsep);
                    $('#kodepoli_suratkontrol').val(data.kodepoli);
                    $('#namapoli_suratkontrol').val(data.namapoli);
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
