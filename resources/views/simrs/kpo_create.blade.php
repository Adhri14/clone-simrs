@extends('adminlte::page')

@section('title', 'KPO Elektronik')

@section('content_header')
    <h1>KPO Elektronik</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            {{-- pencarian pasien --}}
            <x-adminlte-card title="Data Pasien" theme="warning" collapsible>
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
                <x-adminlte-button theme="success" class="cariPasien" type="submit" label="Pilih Pasien" />
                <dl class="row">
                    <input type="hidden" name="kode_kunjungan" id="kode_kunjungan">
                    <dt class="col-sm-3">No RM</dt>
                    <dd class="col-sm-9">: <span id="no_rm"></span></dd>
                    <dt class="col-sm-3">Nama</dt>
                    <dd class="col-sm-9">: <span id="nama_px"></span></dd>
                    <dt class="col-sm-3">Alamat</dt>
                    <dd class="col-sm-9">: -</dd>
                    <dt class="col-sm-3">Dokter DPJP</dt>
                    <dd class="col-sm-9">: -</dd>
                    <dt class="col-sm-3">Ruangan</dt>
                    <dd class="col-sm-9">: -</dd>
                    <dt class="col-sm-3">Dokter</dt>
                    <dd class="col-sm-9">: -</dd>
                    <dt class="col-sm-3">Nama</dt>
                    <dd class="col-sm-9">: -</dd>
                </dl>
            </x-adminlte-card>
            {{-- kelompok diagnisa --}}
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs">
                        <li class="pt-2 px-3">
                            <h3 class="card-title"><b>Diagnosa</b></h3>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#icd10Tab">ICD-10</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#icd9Tab">ICD-9</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#diagTab">Infeksi</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="icd10Tab">
                            <x-adminlte-select2 name="icd10_primer" label="Diagnosa Primer ICD-10" required>
                                {{-- @foreach ($icd10 as $item)
                                    <option value="" disabled selected>Pilih Diagnosa Sekunder</option>
                                    <option value="{{ $item->diag }}">
                                        {{ $item->diag }} - {{ $item->nama }}
                                    </option>
                                @endforeach --}}
                            </x-adminlte-select2>
                            <x-adminlte-select2 name="icd10_sekunder" id="icd10_sekunder" :config="$config"
                                name="icd10_sekunder[]" label="Diagnosa Sekunder ICD-10" multiple>
                                {{-- @foreach ($icd10 as $item)
                                    <option value="{{ $item->diag }}">
                                        {{ $item->diag }} - {{ $item->nama }}
                                    </option>
                                @endforeach --}}
                            </x-adminlte-select2>
                        </div>
                        <div class="tab-pane fade" id="icd9Tab">
                            @php
                                $config = [
                                    'placeholder' => 'Select multiple options...',
                                    'allowClear' => true,
                                ];
                            @endphp
                            <x-adminlte-select2 id="icd9" name="icd9[]" label="Tindakan ICD-9" :config="$config"
                                multiple>
                                {{-- @foreach ($icd10 as $item)
                                    <option value="{{ $item->diag }}">
                                        {{ $item->diag }} - {{ $item->nama }}
                                    </option>
                                @endforeach --}}
                            </x-adminlte-select2>
                        </div>
                        <div class="tab-pane fade" id="diagTab">
                            <h5>Kelompok Diagnosa Infeksi</h5>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="sepsis" value="sepsis">
                                    <label for="sepsis" class="custom-control-label">Sepsis</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="pnemonia-bakterial">
                                    <label for="pnemonia-bakterial" class="custom-control-label">Pnemonia Bakterial</label>
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
                                    <label for="infeksi_saluran_cerna" class="custom-control-label">Infeksi Saluran Cerna
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
                                    <label for="infeksi_otak_sumsum" class="custom-control-label">Infeksi Otak & Sumsum
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
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs">
                        <li class="pt-2 px-3">
                            <h3 class="card-title"><b>Riwayat Pasien</b></h3>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#orderTab">List Order
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
                        {{-- order obat --}}
                        <div class="tab-pane fade show active" id="orderTab">
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
    </div>

    <x-adminlte-modal id="kunjunganPasien" size="lg" title="Daftar Kunjungnan Pasien" theme="success"
        icon="fas fa-user-md">
        <dl class="row">
            <dt class="col-sm-2">Tgl Kunjungan</dt>
            <dd class="col-sm-10">: {{ $request->tanggal }}</dd>
        </dl>
        @php
            $heads = ['Kode Kunjungan', 'No RM', 'Nama Pasien', 'Unit', 'Dokter', 'Action'];
            $config['paging'] = false;
            $config['info'] = false;
            $config['scrollY'] = '400px';
        @endphp
        <x-adminlte-datatable id="table1" class="nowrap text-xs" :heads="$heads" :config="$config" bordered
            hoverable compressed>
            @isset($kunjungans)
                @foreach ($kunjungans as $item)
                    <tr>
                        <td>{{ $item->kode_kunjungan }}</td>
                        <td>{{ $item->no_rm }}</td>
                        <td>{{ $item->pasien->nama_px }}</td>
                        <td>{{ $item->kode_unit }}</td>
                        <td>{{ $item->kode_dokter }}</td>
                        <td>
                            <x-adminlte-button theme="success" class="btn-xs pilihKunjungan"
                                data-id="{{ $item->kode_kunjungan }}" label="Pilih" />
                        </td>
                    </tr>
                @endforeach
            @endisset
        </x-adminlte-datatable>
    </x-adminlte-modal>

@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('js')
    <script>
        $(function() {
            $('.cariPasien').click(function() {
                $.LoadingOverlay("show");
                $('#kunjunganPasien').modal('show');
                $.LoadingOverlay("hide", true);
            });
            $('.pilihKunjungan').click(function() {
                $.LoadingOverlay("show");
                var kode = $(this).data('id');
                var url = "{{ route('simrs.kunjungan.index') }}/" + kode + "/edit";
                $.get(url, function(data) {
                    console.log(data);
                    $('#kunjunganPasien').modal('hide');
                    $('#nama_px').html(data.nama_pasien);
                    $('#no_rm').html(data.no_rm);
                    $('#kode_kunjungan').val(data.kode_kunjungan);
                    $.LoadingOverlay("hide");
                });
            });
            $('.tambahObat').click(function() {
                alert('simpan obat sementara')
            });
            $('#btnLayanan').click(function() {
                $.LoadingOverlay("show");
                var kode_kunjungan = $('#kode_kunjungan').val();;
                var url = "{{ route('api.simrs.get_layanans') }}";
                var dataInput = {
                    kode_kunjungan: kode_kunjungan,
                }
                $.ajax({
                    data: dataInput,
                    url: url,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        alert('ok');
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        console.log(data);
                        $.LoadingOverlay("hide");
                    }
                });
                $.LoadingOverlay("hide", true);
            });
        });
    </script>
    <script>
        $(function() {
            $("#icd10_primer").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.simrs.get_icd10') }}",
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
            $("#icd10_sekunder").select2({
                placeholder: 'Silahkan pilih diagnosa sekunder',
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.simrs.get_icd10') }}",
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
            $("#icd9").select2({
                placeholder: 'Silahkan pilih diagnosa sekunder',
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('api.simrs.get_icd9') }}",
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
@endsection
