@extends('adminlte::page')

@section('title', 'E-KPO Rawat Jalan')

@section('content_header')
    <h1>E-KPO Rawat Jalan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            {{-- pencarian pasien --}}
            <x-adminlte-card title="Pencarian Pasien ({{ $kunjungans ? $kunjungans->count() : '0' }} Pasien)" theme="warning"
                collapsible="{{ $request->kunjungan ? 'collapsed' : '' }}">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-5">
                            @php
                                $config = ['format' => 'YYYY-MM-DD'];
                            @endphp
                            <x-adminlte-input-date name="tanggal" id="tanggal" label="Tanggal Kunjungan" :config="$config"
                                value="{{ \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d') }}">
                            </x-adminlte-input-date>
                        </div>
                        <div class="col-md-7">
                            <x-adminlte-select name="unit" label="Ruangan">
                                @foreach ($units as $kode => $nama)
                                    <option value="{{ $kode }}" {{ $request->unit == $kode ? 'selected' : null }}>
                                        {{ $nama }}</option>
                                @endforeach
                                <x-slot name="appendSlot">
                                    <x-adminlte-button theme="success" class="withLoad" type="submit" label="Cari!" />
                                </x-slot>
                            </x-adminlte-select>
                        </div>
                    </div>
                </form>
                @php
                    $heads = ['Kunjungan', 'No RM', 'Nama Pasien', 'No Kartu', 'SEP', 'Tgl Lahir', 'Unit', 'Action'];
                    $config['paging'] = false;
                    $config['info'] = false;
                    $config['scrollY'] = '400px';
                    $config['scrollX'] = true;
                @endphp
                <x-adminlte-datatable id="table5" class="nowrap text-xs" :heads="$heads" :config="$config" bordered
                    hoverable compressed>
                    @isset($kunjungans)
                        @foreach ($kunjungans as $item)
                            <tr>
                                <td>{{ $item->kode_kunjungan }}</td>
                                <td>{{ $item->no_rm }}</td>
                                <td>{{ $item->pasien->nama_px }} ({{ $item->pasien->jenis_kelamin }})</td>
                                <td>{{ $item->pasien->no_Bpjs }}</td>
                                <td>{{ $item->no_sep }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->pasien->tgl_lahir)->format('Y-m-d') }}</td>
                                <td>{{ $item->unit->nama_unit }}</td>
                                <td>
                                    {{-- <a href="{{ route('kpo.index') . '?kunjungan=' . $item->kode_kunjungan }}"
                                        class="btn-xs btn-success">Pilih</a> --}}
                                    <a href="{{ url()->full() . '&kunjungan=' . $item->kode_kunjungan }}"
                                        class="btn-xs btn-success">Pilih</a>
                                    {{-- <x-adminlte-button theme="success" class="btn-xs pilihKunjungan"
                                    data-id="{{ $item->kode_kunjungan }}" label="Pilih" /> --}}
                                </td>
                            </tr>
                        @endforeach
                    @endisset
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
        @if ($request->kunjungan)
            <div class="col-md-4">
                {{-- pencarian pasien --}}
                <x-adminlte-card title="Data Pasien" theme="warning" collapsible>
                    <x-adminlte-input name="nomor_kartu" label="Nomor Kartu" value="{{ $kunjungan->pasien->no_Bpjs }}" />
                    <x-adminlte-input name="nomor_sep" label="Nomor SEP" value="{{ $kunjungan->no_sep }}" />
                    <x-adminlte-input name="nomor_rm" label="Nomor RM" value="{{ $kunjungan->pasien->no_rm }}" />
                    <x-adminlte-input name="nama_pasien" label="Nama Pasien" value="{{ $kunjungan->pasien->nama_px }}" />
                    <x-adminlte-input name="tgl_lahir" label="Tgl Lahir" value="{{ $kunjungan->pasien->tgl_lahir }}" />
                    <x-adminlte-input name="gender" label="Gender" value="{{ $kunjungan->pasien->jenis_kelamin }}" />
                    <x-adminlte-button theme="success" id="btnNewClaim" label="Create Claim" />

                </x-adminlte-card>
            </div>
            <div class="col-md-8">
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs">
                            <li class="pt-2 px-3">
                                <h3 class="card-title"><b>Riwayat Pasien</b></h3>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" href="#inacbg">INACBG</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#diagTab">Infeksi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#orderTab">List Order
                                    Obat</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" id="btnLayanan" href="#resumeTab">Resume
                                    Medis</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#antibiotikTab">Evaluasi
                                    Antibiotik</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            {{-- icd 10 --}}
                            <div class="tab-pane fade show active" id="inacbg">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-adminlte-input name="tgl_masuk" label="Tgl Masuk"
                                            value="{{ $kunjungan->tgl_masuk }}" />
                                        <x-adminlte-select name="cara_masuk" label="Cara Masuk">
                                            <option value="gp">Rujukan FKTP</option>
                                            <option value="hosp-trans">Rujukan FKRTL</option>
                                            <option value="mp">Rujukan Spesialis</option>
                                            <option value="outp">Dari Rawat Jalan</option>
                                            <option value="inp">Dari Rawat Inap</option>
                                            <option value="emd">Dari Rawat Darurat</option>
                                            <option value="born">Lahir di RS</option>
                                            <option value="nursing">Rujukan Panti Jompo</option>
                                            <option value="psych">Rujukan dari RS Jiwa</option>
                                            <option value="rehab"> Rujukan Fasilitas Rehab</option>
                                            <option value="other">Lain-lain</option>
                                        </x-adminlte-select>
                                        <input type="hidden" name="jenis_rawat" id="jenis_rawat" value="2">
                                    </div>
                                    <div class="col-md-6">
                                        {{-- <x-adminlte-select name="jenis_rawat" label="Jenis Rawat">
                                    <option value="1">Rawat Inap</option>
                                    <option value="2" selected>Rawat Jalan</option>
                                    <option value="3">Rawat IGD</option>
                                </x-adminlte-select> --}}
                                        <x-adminlte-select name="kelas_rawat" label="Kelas Rawat">
                                            <option value="3" selected>Kelas 3</option>
                                            <option value="2">Kelas 2</option>
                                            <option value="1">Kelas 1</option>
                                        </x-adminlte-select>
                                        <x-adminlte-select name="discharge_status" label="Cara Pulang">
                                            <option value="1">Atas persetujuan dokter</option>
                                            <option value="2">Dirujuk</option>
                                            <option value="3">Atas permintaan sendiri</option>
                                            <option value="4">Meninggal</option>
                                            <option value="5">Lain-lain</option>
                                        </x-adminlte-select>
                                    </div>
                                </div>
                                {{-- multipe diagnosa --}}
                                <label class=" mb-2">Diagnosa ICD-10</label>
                                <button id="rowAdder" type="button" class="btn btn-xs btn-success  mb-2">
                                    <span class="fas fa-plus">
                                    </span> Tambah Diagnosa
                                </button>
                                <div id="row">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <select name="diagnosa[]" class="form-control diagnosaID ">
                                            </select>
                                            <div class="input-group-append"><button type="button"
                                                    class="btn btn-warning">
                                                    <i class="fas fa-diagnoses "></i> Diagnosa Utama </button></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="newinput"></div>
                                {{-- multipe tindakan --}}
                                <label class="mb-2">Tindakan ICD-9</label>
                                <button id="rowAddTindakan" type="button" class="btn btn-xs btn-success  mb-2">
                                    <span class="fas fa-plus">
                                    </span> Tambah Tindakan
                                </button>
                                <div id="rowTindakan" class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-hand-holding-medical "></i>
                                                    </span>
                                                </div>
                                                <select name="procedure[]" class="form-control procedure ">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <b>@</b>
                                                    </span>
                                                </div>
                                                <input type="number" class="form-control" value="1">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-warning">
                                            <i class="fas fa-hand-holding-medical "></i> </button>
                                    </div>
                                </div>
                                <div id="newTindakan"></div>



                                <dl class="row">
                                    <dt class="col-sm-3">Kode</dt>
                                    <dd class="col-sm-9">: <span id="kode_inacbg"></span></dd>

                                    <dt class="col-sm-3">Keterangan</dt>
                                    <dd class="col-sm-9">: <span id="description_inacbg"></span></dd>

                                    <dt class="col-sm-3">Base Tarif</dt>
                                    <dd class="col-sm-9">: <span id="base_tariff"></span></dd>

                                    <dt class="col-sm-3">Tarif</dt>
                                    <dd class="col-sm-9">: <span id="tariff"></span></dd>

                                    <dt class="col-sm-3">Kelas</dt>
                                    <dd class="col-sm-9">: <span id="kelas"></span></dd>

                                    <dt class="col-sm-3">Tarif Kelas Inacbg</dt>
                                    <dd class="col-sm-9">: <span id="tarif_inacbg"></span></dd>
                                </dl>
                                {{-- <x-adminlte-button theme="success" id="btnSetClaim" label="Set Claim" /> --}}
                                <x-adminlte-button theme="primary" id="btnGroupperClaim" label="Groupper Claim" />
                            </div>
                            {{-- infeksi --}}
                            <div class="tab-pane fade" id="diagTab">
                                <h5>Kelompok Diagnosa Infeksi</h5>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="sepsis"
                                            value="sepsis">
                                        <label for="sepsis" class="custom-control-label">Sepsis</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="pnemonia-bakterial">
                                        <label for="pnemonia-bakterial" class="custom-control-label">Pnemonia
                                            Bakterial</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="intraabdomen-komplikata">
                                        <label for="intraabdomen-komplikata" class="custom-control-label">Infeksi
                                            Intra-abdomen
                                            Komplikata</label>
                                    </div>
                                    <br>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="meningitis-encefalitis">
                                        <label for="meningitis-encefalitis" class="custom-control-label">Meningitis /
                                            Encefalitis Bakterial</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_kulit_jaringan">
                                        <label for="infeksi_kulit_jaringan" class="custom-control-label">Infeksi Kulit &
                                            Jaringan</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_mata_telinga">
                                        <label for="infeksi_mata_telinga" class="custom-control-label">Infeksi Mata /
                                            Telinga</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_sendi_tulang">
                                        <label for="infeksi_sendi_tulang" class="custom-control-label">Infeksi Sendi &
                                            Tulang</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="pnemonia_non_bakterial">
                                        <label for="pnemonia_non_bakterial" class="custom-control-label">Pnemonia Non
                                            Bakterial</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_saluran_cerna">
                                        <label for="infeksi_saluran_cerna" class="custom-control-label">Infeksi Saluran
                                            Cerna
                                            /
                                            Hepatobilier Pancreas</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_ginjal_kemih">
                                        <label for="infeksi_ginjal_kemih" class="custom-control-label">Infeksi Ginjal &
                                            Saluran
                                            Kemih</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_otak_sumsum">
                                        <label for="infeksi_otak_sumsum" class="custom-control-label">Infeksi Otak &
                                            Sumsum
                                            Tulang</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_obsteri">
                                        <label for="infeksi_obsteri" class="custom-control-label">Infeksi Obsteri /
                                            Ginekologi</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_tb">
                                        <label for="infeksi_tb" class="custom-control-label">Infeksi TB Paru</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_tb_extra">
                                        <label for="infeksi_tb_extra" class="custom-control-label">Infeksi TB Extra
                                            Paru</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="lepra">
                                        <label for="lepra" class="custom-control-label">LEPRA</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_jamur">
                                        <label for="infeksi_jamur" class="custom-control-label">Infeksi Jamur</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="infeksi_virus">
                                        <label for="infeksi_virus" class="custom-control-label">Infeksi Virus</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="non_infeksi">
                                        <label for="non_infeksi" class="custom-control-label">Non Infeksi
                                            (Inflamasi)</label>
                                    </div>
                                </div>
                            </div>
                            {{-- order obat --}}
                            <div class="tab-pane fade" id="orderTab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-adminlte-select name="tipeobat" label="Tipe Obat">
                                            <option value="">81 Reguler</option>
                                            <option value="">82 Kronis</option>
                                            <option value="">83 Kemotherapi</option>
                                        </x-adminlte-select>
                                        <x-adminlte-select2 name="obat" label="Obat" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-adminlte-input name="signa" label="Signa" />
                                        <x-adminlte-input name="jumlah" label="Jumlah" />
                                    </div>
                                </div>
                                <x-adminlte-button theme="success" class="tambahObat" label="Tambahkan Obat" />
                                @php
                                    $heads = ['Kode', 'Tgl Kunjungan', 'Obat', 'Keterangan'];
                                    $config['paging'] = false;
                                    $config['info'] = false;
                                    $config['scrollY'] = '400px';
                                    $config['scrollX'] = true;
                                    $config['scrollCollapse'] = true;
                                @endphp
                                <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads"
                                    :config="$config" striped bordered hoverable compressed>
                                </x-adminlte-datatable>
                            </div>
                            {{-- evaluasi antibiotik --}}
                            <div class="tab-pane fade" id="antibiotikTab">
                                @php
                                    $heads = ['Kode', 'Tgl Kunjungan', 'Obat', 'Keterangan'];
                                    $config['paging'] = false;
                                    $config['info'] = false;
                                    $config['scrollY'] = '400px';
                                    $config['scrollX'] = true;
                                    $config['scrollCollapse'] = true;
                                @endphp
                                <x-adminlte-datatable id="table4" class="nowrap text-xs" :heads="$heads"
                                    :config="$config" striped bordered hoverable compressed>
                                </x-adminlte-datatable>
                            </div>
                            {{-- riwayat medis --}}
                            <div class="tab-pane fade" id="resumeTab">
                                @php
                                    $heads = ['Kode', 'Tgl Kunjungan', 'Obat', 'Keterangan'];
                                    $config['paging'] = false;
                                    $config['info'] = false;
                                    $config['scrollY'] = '400px';
                                    $config['scrollX'] = true;
                                    $config['scrollCollapse'] = true;
                                @endphp
                                <x-adminlte-datatable id="table3" class="nowrap text-xs" :heads="$heads"
                                    :config="$config" striped bordered hoverable compressed>
                                </x-adminlte-datatable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            pilih
        @endif

    </div>
    <x-adminlte-modal id="kunjunganPasien" size="lg" title="Daftar Kunjungnan Pasien" theme="success"
        icon="fas fa-user-md">
        <dl class="row">
            <dt class="col-sm-2">Tgl Kunjungan</dt>
            <dd class="col-sm-10">: {{ $request->tanggal }}</dd>
        </dl>

    </x-adminlte-modal>

@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script>
        $(function() {
            // $('.cariPasien').click(function() {
            //     $.LoadingOverlay("show");
            //     $('#kunjunganPasien').modal('show');
            //     $.LoadingOverlay("hide", true);
            // });
            $('.pilihKunjungan').click(function() {
                $.LoadingOverlay("show");
                var kode = $(this).data('id');
                var url = "{{ route('rekammedis.kunjungan.index') }}/" + kode + "/edit";
                $.get(url, function(data) {
                    console.log(data);
                    $('#kunjunganPasien').modal('hide');
                    $('#nomor_sep').val(data.no_sep);
                    $('#nomor_rm').val(data.no_rm);
                    $('#nama_pasien').val(data.nama_pasien);
                    $('#nomor_kartu').val(data.pasien.no_Bpjs);
                    $('#tgl_lahir').val(data.pasien.tgl_lahir);
                    $('#gender').val(data.pasien.jenis_kelamin);
                    $('#tgl_masuk').val(data.tgl_masuk);
                    $.LoadingOverlay("hide");
                });
            });
            $('.tambahObat').click(function() {
                alert('simpan obat sementara')
            });
            $('#btnLayanan').click(function() {
                $.LoadingOverlay("show");
                // var kode_kunjungan = $('#kode_kunjungan').val();;
                // var url = "{{ route('api.simrs.get_layanans') }}";
                // var dataInput = {
                //     kode_kunjungan: kode_kunjungan,
                // }
                // $.ajax({
                //     data: dataInput,
                //     url: url,
                //     type: "GET",
                //     dataType: 'json',
                //     success: function(data) {
                //         console.log(data);
                //         alert('ok');
                //         $.LoadingOverlay("hide");
                //     },
                //     error: function(data) {
                //         console.log(data);
                //         $.LoadingOverlay("hide");
                //     }
                // });
                $.LoadingOverlay("hide", true);
            });
            $('#btnNewClaim').click(function() {
                $.LoadingOverlay("show");
                var nomor_sep = $('#nomor_sep').val();
                var nomor_rm = $('#nomor_rm').val();
                var nama_pasien = $('#nama_pasien').val();
                var nomor_kartu = $('#nomor_kartu').val();
                var tgl_lahir = $('#tgl_lahir').val();
                var gender = $('#gender').val();
                var dataInput = {
                    nomor_sep: nomor_sep,
                    nomor_rm: nomor_rm,
                    nama_pasien: nama_pasien,
                    nomor_kartu: nomor_kartu,
                    tgl_lahir: tgl_lahir,
                    gender: gender,
                }
                var url = "{{ route('api.eclaim.new_claim') }}";
                $.ajax({
                    data: dataInput,
                    url: url,
                    type: "POST",
                    success: function(data) {
                        if (data.metadata.code == 200) {
                            swal.fire(
                                'Success',
                                data.metadata.message,
                                'success'
                            );
                        } else {
                            console.log(data);
                            swal.fire(
                                'Error',
                                data.metadata.message,
                                'error'
                            );

                        }
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        console.log(data);
                        swal.fire(
                            'Error ' + data.responseJSON.metadata.code,
                            data.responseJSON.metadata.message,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            $('#btnSetClaim').click(function() {
                $.LoadingOverlay("show");
                var nomor_sep = $('#nomor_sep').val();
                var nomor_kartu = $('#nomor_kartu').val();
                var tgl_masuk = $('#tgl_masuk').val();
                var cara_masuk = $('#cara_masuk').val();
                var jenis_rawat = $('#jenis_rawat').val();
                var kelas_rawat = $('#kelas_rawat').val();
                var discharge_status = $('#discharge_status').val();
                var diagnosa = $('#diagnosa').val();
                var procedure = $('#procedure').val();
                var dataInput = {
                    nomor_sep: nomor_sep,
                    nomor_kartu: nomor_kartu,
                    tgl_masuk: tgl_masuk,
                    cara_masuk: cara_masuk,
                    jenis_rawat: jenis_rawat,
                    kelas_rawat: kelas_rawat,
                    discharge_status: discharge_status,
                    diagnosa: diagnosa,
                    procedure: procedure,
                }
                var url = "{{ route('api.eclaim.set_claim_rajal') }}";
                $.ajax({
                    data: dataInput,
                    url: url,
                    type: "POST",
                    success: function(data) {
                        console.log(data);
                        if (data.metadata.code == 200) {
                            swal.fire(
                                'Success',
                                data.metadata.message,
                                'success'
                            );
                        } else {
                            console.log(data);
                            swal.fire(
                                'Error',
                                data.metadata.message,
                                'error'
                            );

                        }
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        console.log(data);
                        swal.fire(
                            'Error ' + data.responseJSON.metadata.code,
                            data.responseJSON.metadata.message,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            $('#btnGroupperClaim').click(function() {
                $.LoadingOverlay("show");
                // setklaim
                var nomor_sep = $('#nomor_sep').val();
                var nomor_kartu = $('#nomor_kartu').val();
                var tgl_masuk = $('#tgl_masuk').val();
                var cara_masuk = $('#cara_masuk').val();
                var jenis_rawat = $('#jenis_rawat').val();
                var kelas_rawat = $('#kelas_rawat').val();
                var discharge_status = $('#discharge_status').val();
                var diagnosa = $('select[name^=diagnosa]').find(":selected").map(function(idx, elem) {
                    return $(elem).val();
                }).get();
                var procedure = $('select[name^=procedure]').find(":selected").map(function(idx, elem) {
                    return $(elem).val();
                }).get();
                var dataInput = {
                    nomor_sep: nomor_sep,
                    nomor_kartu: nomor_kartu,
                    tgl_masuk: tgl_masuk,
                    cara_masuk: cara_masuk,
                    jenis_rawat: jenis_rawat,
                    kelas_rawat: kelas_rawat,
                    discharge_status: discharge_status,
                    diagnosa: diagnosa,
                    procedure: procedure,
                }
                var url = "{{ route('api.eclaim.set_claim_rajal') }}";
                $.ajax({
                    data: dataInput,
                    url: url,
                    type: "POST",
                    success: function(data) {
                        console.log(data);
                        if (data.metadata.code == 200) {
                            // grouper
                            var nomor_sep = $('#nomor_sep').val();
                            var dataInput = {
                                nomor_sep: nomor_sep,
                            }
                            var url = "{{ route('api.eclaim.grouper') }}";
                            $.ajax({
                                data: dataInput,
                                url: url,
                                type: "POST",
                                success: function(data) {
                                    console.log(data);
                                    if (data.metadata.code == 200) {

                                        const rupiah = (number) => {
                                            return new Intl.NumberFormat(
                                                "id-ID", {
                                                    style: "currency",
                                                    currency: "IDR"
                                                }).format(number);
                                        }

                                        $('#kode_inacbg').html(data.response.cbg
                                            .code);
                                        $('#description_inacbg').html(data.response
                                            .cbg.description);
                                        $('#base_tariff').html(rupiah(data.response
                                            .cbg
                                            .base_tariff));
                                        $('#tariff').html(rupiah(data.response.cbg
                                            .tariff));

                                        $('#kelas').html(data.response.kelas);
                                        var tarif_kelas = data.tarif_alt;
                                        var tarif_kelass = tarif_kelas.filter(x => x
                                            .kelas === data.response.kelas)
                                        $('#tarif_inacbg').html(rupiah(
                                            tarif_kelass[0].tarif_inacbg));
                                        swal.fire(
                                            'Success',
                                            data.metadata.message,
                                            'success'
                                        );
                                    } else {
                                        console.log(data);
                                        swal.fire(
                                            'Error',
                                            data.metadata.message,
                                            'error'
                                        );
                                        $.LoadingOverlay("hide");
                                    }
                                    $.LoadingOverlay("hide");
                                },
                                error: function(data) {
                                    console.log(data);
                                    swal.fire(
                                        'Error ' + data.responseJSON.metadata
                                        .code,
                                        data.responseJSON.metadata.message,
                                        'error'
                                    );
                                    $.LoadingOverlay("hide");
                                }
                            });
                        } else {
                            console.log(data);
                            swal.fire(
                                'Error',
                                data.metadata.message,
                                'error'
                            );
                            $.LoadingOverlay("hide");
                        }
                    },
                    error: function(data) {
                        console.log(data);
                        swal.fire(
                            'Error ' + data.responseJSON.metadata.code,
                            data.responseJSON.metadata.message,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
        });
    </script>
    {{-- search select2 --}}
    <script>
        $(function() {
            $(".diagnosaID").select2({
                placeholder: 'Silahkan pilih Diagnosa ICD-10',
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.eclaim.search_diagnosis') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $(".procedure").select2({
                placeholder: 'Silahkan pilih Tindakan ICD-9',
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.eclaim.search_procedures') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $("#obat").select2({
                placeholder: 'Silahkan pilih obat',
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.simrs.get_obats') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            search: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
    {{-- dynamic input --}}
    <script>
        // row select diagnosa
        $("#rowAdder").click(function() {
            newRowAdd =
                '<div id="row"><div class="form-group"><div class="input-group">' +
                '<select name="diagnosa[]" class="form-control diagnosaID"></select>' +
                '<div class="input-group-append"><button type="button" class="btn btn-danger" id="DeleteRow">' +
                '<i class="fas fa-trash "></i> Hapus </button></div>' +
                '</div></div></div>';
            $('#newinput').append(newRowAdd);
            $(".diagnosaID").select2({
                placeholder: 'Silahkan pilih Diagnosa ICD-10',
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.eclaim.search_diagnosis') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });
        $("body").on("click", "#DeleteRow", function() {
            $(this).parents("#row").remove();
        })
        // row select tindakan
        $("#rowAddTindakan").click(function() {
            newRowAdd =
                '<div id="row" class="row"><div class="col-md-7"><div class="form-group"><div class="input-group">' +
                '<div class="input-group-prepend"><span class="input-group-text">' +
                '<i class="fas fa-hand-holding-medical "></i></span></div>' +
                '<select name="procedure[]" class="form-control procedure "></select></div></div></div>' +
                '<div class="col-md-3"><div class="form-group"><div class="input-group"><div class="input-group-prepend">' +
                '<span class="input-group-text"><b>@</b></span></div><input type="number" class="form-control" value="1">' +
                '</div></div></div><div class="col-md-2"><button type="button" class="btn btn-danger" id="deleteRowTindakan"> ' +
                '<i class="fas fa-trash "></i> </button></div></div>';
            $('#newTindakan').append(newRowAdd);
            $(".procedure").select2({
                placeholder: 'Silahkan pilih Tindakan ICD-9',
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.eclaim.search_procedures') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });
        $("body").on("click", "#deleteRowTindakan", function() {
            $(this).parents("#row").remove();
        })
    </script>
@endsection
