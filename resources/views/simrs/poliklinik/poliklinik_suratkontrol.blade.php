@extends('adminlte::page')
@section('title', 'Surat Kontrol Poliklinik')
@section('content_header')
    <h1>Surat Kontrol Poliklinik</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Filter Data Kunjungan" theme="secondary" collapsible>
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <x-adminlte-select2 name="kodepoli" label="Poliklinik">
                                <option value="">SEMUA POLIKLINIK (-)</option>
                                @foreach ($unit as $item)
                                    <option value="{{ $item->KDPOLI }}"
                                        {{ $item->KDPOLI == $request->kodepoli ? 'selected' : null }}>
                                        {{ $item->nama_unit }} ({{ $item->KDPOLI }})
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
                                        {{ $item->nama_paramedis }} ({{ $item->kode_dokter_jkn }} )
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Pencarian" />
                </form>
            </x-adminlte-card>
        </div>
        @if (isset($kunjungans))
            <div class="col-md-6">
                <x-adminlte-card title="Kunjungan Poliklinik ({{ $kunjungans->count() }} Orang)" theme="primary"
                    icon="fas fa-info-circle" collapsible>
                    @php
                        $heads = ['Tgl Kunjungan', 'Pasien', 'Action', 'SEP / Ref', 'Poliklinik', 'NIK / Tgl Lahir'];
                        $config['paging'] = false;
                        $config['info'] = false;
                        $config['scrollY'] = '400px';
                        $config['scrollX'] = true;
                        $config['scrollCollapse'] = true;
                    @endphp
                    <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" :config="$config" striped
                        bordered hoverable compressed>
                        @foreach ($kunjungans as $item)
                            <tr class={{ $item->surat_kontrol ? 'text-success' : null }}>
                                <td>
                                    {{ $item->tgl_masuk }}
                                    <br>{{ $item->kode_kunjungan }}
                                </td>
                                <td>
                                    RM :
                                    {{ $item->no_rm ? $item->no_rm : null }}<br>
                                    <b>{{ $item->pasien ? $item->pasien->nama_px : null }}</b>
                                    <br>
                                    {{ $item->pasien ? $item->pasien->no_Bpjs : null }}
                                </td>
                                <td>
                                    <x-adminlte-button class="btn-xs btnBuatSuratKontrol" label="S. Kontrol" theme="primary"
                                        icon="fas fa-file-medical" data-toggle="tooltop" title="Buat Surat Kontrol"
                                        data-id="{{ $item->kode_kunjungan }}" />
                                </td>
                                <td>
                                    SEP : {{ $item->no_sep }} <br>
                                    Ref : {{ $item->no_rujukan }}
                                </td>
                                <td>{{ $item->unit->nama_unit }}<br>{{ $item->dokter->nama_paramedis }}</td>
                                <td>
                                    {{ $item->pasien ? $item->pasien->nik_bpjs : null }}
                                    {{-- <br> --}}
                                    {{-- {{ \Carbon\Carbon::parse($item->pasien->tgl_lahir)->format('Y-m-d') }} --}}
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                    Catatan : <br>
                    *Warna teks hijau adalah kunjungan yang telah dibuatkan surat kontrol.
                </x-adminlte-card>
            </div>
            <div class="col-md-6">
                <x-adminlte-card title="Surat Kontrol Poliklinik ({{ $surat_kontrols->count() }})" theme="success"
                    icon="fas fa-info-circle" collapsible>
                    @php
                        $heads = ['Tgl Dibuat', 'Tgl Kontrol', 'No S. Kontrol', 'Pasien', 'Poliklinik / Dokter', 'No SEP Asal'];
                        $config['paging'] = false;
                        $config['order'] = ['2', 'desc'];
                        $config['info'] = false;
                        $config['scrollX'] = true;
                        $config['scrollY'] = '400px';
                        $config['scrollCollapse'] = true;
                    @endphp
                    <x-adminlte-datatable id="table1" class="nowrap text-xs" :heads="$heads" :config="$config" striped
                        bordered hoverable compressed>
                        @foreach ($surat_kontrols as $item)
                            <tr>
                                <td>{{ $item->tglTerbitKontrol }}</td>
                                <td>{{ $item->tglRencanaKontrol }}</td>

                                <td>{{ $item->noSuratKontrol }}
                                    <br>
                                    <a href="{{ route('bpjs.vclaim.surat_kontrol_print', $item->noSuratKontrol) }}"
                                        target="_blank" class="btn btn-xs btn-success" data-toggle="tooltip"
                                        title="Print Surat Kontrol {{ $item->kode_kunjungan }}"> <i
                                            class="fas fa-print"></i> Print</a>
                                    <x-adminlte-button class="btn-xs btnEditSuratKontrol" label="Edit" theme="warning"
                                        icon="fas fa-edit" data-toggle="tooltip"
                                        title="Edit Surat Kontrol {{ $item->noSuratKontrol }}"
                                        data-id="{{ $item->id }}" />

                                </td>
                                <td>
                                    {{ $item->nama }} <br>
                                    {{ $item->noKartu }}
                                </td>
                                <td>{{ $item->namaPoliTujuan }} <br>
                                    {{ $item->namaDokter }}
                                </td>
                                <td>{{ $item->noSepAsalKontrol }}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
            <x-adminlte-modal id="modalSuratKontrol" name="modalSuratKontrol" title="Surat Kontrol Rawat Jalan"
                theme="success" icon="fas fa-file-medical" v-centered>
                <form id="formSuratKontrol" name="formSuratKontrol">
                    @csrf
                    <x-adminlte-input name="nama_suratkontrol" placeholder="Nama Pasien" label="Nama Pasien" readonly />
                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="nomor_suratkontrol" placeholder="Nomor Surat Kontrol"
                                label="Nomor Surat Kontrol" readonly />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="nomorsep_suratkontrol" placeholder="Nomor SEP" label="Nomor SEP"
                                readonly />
                        </div>
                    </div>
                    @php
                        $config = [
                            'format' => 'YYYY-MM-DD',
                            'dayViewHeaderFormat' => 'MMMM YYYY',
                            'minDate' => 'js:moment()',
                            'daysOfWeekDisabled' => [0],
                        ];
                    @endphp
                    <x-adminlte-input-date name="tanggal_suratkontrol" label="Tanggal Rencana Surat Kontrol"
                        :config="$config" placeholder="Pilih Tanggal Surat Kontrol ..."
                        value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-primary">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                    <x-adminlte-select2 name="kodepoli_suratkontrol" label="Poliklinik">
                        @foreach ($unit as $item)
                            <option value="{{ $item->KDPOLI }}"
                                {{ $item->KDPOLI == $request->kodepoli ? 'selected' : null }}>
                                {{ $item->nama_unit }} ({{ $item->KDPOLI }})
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="kodedokter_suratkontrol" label="DPJP Surat Kontrol">
                        @foreach ($dokters as $item)
                            <option value="{{ $item->kode_dokter_jkn }}"
                                {{ $item->kode_dokter_jkn == $request->kodedokter ? 'selected' : null }}>
                                {{ $item->nama_paramedis }} ({{ $item->kode_dokter_jkn }})
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-slot name="footerSlot">
                        <x-adminlte-button id="btnStore" class="mr-auto" icon="fas fa-file-medical" theme="success"
                            label="Buat Surat Kontrol" />
                        <x-adminlte-button id="btnUpdate" class="mr-auto" icon="fas fa-edit" theme="warning"
                            label="Update Surat Kontrol" />
                        <x-adminlte-button id="btnDelete" icon="fas fa-trash" theme="danger" label="Hapus"
                            data-toggle="tooltip" title="Delete Surat Kontrol {{ $item->noSuratKontrol }}" />
                        <x-adminlte-button theme="danger" icon="fas fa-times" label="Kembali" data-dismiss="modal" />
                    </x-slot>
                </form>
            </x-adminlte-modal>
        @endif
    </div>

@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        $(function() {
            $('.btnEditSuratKontrol').click(function() {
                $('#btnStore').hide();
                $('#btnUpdate').show();
                var nomorsuratkontrol = $(this).data('id');
                var url = "{{ route('vclaim.index') }}" + "/edit_surat_kontrol/" + nomorsuratkontrol;
                $.LoadingOverlay("show");
                $.get(url, function(data) {
                    $('#nama_suratkontrol').val(data.nama);
                    $('#nomor_suratkontrol').val(data.noSuratKontrol);
                    $('#nomorsep_suratkontrol').val(data.noSepAsalKontrol);
                    $('#tanggal_suratkontrol').val(data.tglRencanaKontrol);
                    $('#kodepoli_suratkontrol').val(data.poliTujuan).trigger('change');
                    $('#kodedokter_suratkontrol').val(data.kodeDokter).trigger('change');
                    $('#modalSuratKontrol').modal('show');
                    $.LoadingOverlay("hide", true);
                });
            });
            $('.btnBuatSuratKontrol').click(function() {
                $('#btnStore').show();
                $('#btnUpdate').hide();
                var kodekunjungan = $(this).data('id');
                var url = "{{ route('landingpage') }}" + "/kunjungan/show/" + kodekunjungan;
                $.LoadingOverlay("show");
                $.get(url, function(data) {
                    $('#formSuratKontrol').trigger("reset");
                    $('#nama_suratkontrol').val(data.data.namaPasien);
                    $('#nomorsep_suratkontrol').val(data.data.noSEP);
                    $('#kodepoli_suratkontrol').val(data.data.kodePoli).trigger('change');
                    $('#kodedokter_suratkontrol').val(data.data.kodeDokter).trigger('change');
                    $('#modalSuratKontrol').modal('show');
                    $.LoadingOverlay("hide", true);
                });
            });
            $('#btnStore').click(function(e) {
                $.LoadingOverlay("show");
                e.preventDefault();
                var url = "{{ route('bpjs.vclaim.surat_kontrol_store') }}";
                $.ajax({
                    data: $('#formSuratKontrol').serialize(),
                    url: url,
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        var urlPrint =
                            "{{ route('landingpage') }}/bpjs/vclaim/surat_kontrol_print/" +
                            data
                            .response.noSuratKontrol;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Surat Kontrol Berhasil Dibuat dengan Nomor ' + data
                                .response.noSuratKontrol,
                            footer: "<a href=" + urlPrint +
                                " target='_blank'>Print Surat Kontrol</a>"
                        }).then(okay => {
                            if (okay) {
                                $.LoadingOverlay("show");
                                location.reload();
                            }
                        });
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        console.log(data);
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.metadata.message,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            $('#btnUpdate').click(function(e) {
                $.LoadingOverlay("show");
                e.preventDefault();
                var url = "{{ route('bpjs.vclaim.surat_kontrol_update') }}";
                $.ajax({
                    data: $('#formSuratKontrol').serialize(),
                    url: url,
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        swal.fire(
                            'Success',
                            'Surat Kontrol Berhasil Diperbaharui dengan Nomor ' + data
                            .response
                            .noSuratKontrol,
                            'success'
                        ).then(okay => {
                            if (okay) {
                                $.LoadingOverlay("show");
                                location.reload();
                            }
                        });
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        console.log(data);
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.metadata.message,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            $('#btnDelete').click(function(e) {
                $.LoadingOverlay("show");
                e.preventDefault();
                var url = "{{ route('bpjs.vclaim.surat_kontrol_delete') }}";
                $.ajax({
                    data: $('#formSuratKontrol').serialize(),
                    url: url,
                    type: "DELETE",
                    dataType: 'json',
                    success: function(data) {
                        swal.fire(
                            'Success',
                            'Data Berhasil Disimpan',
                            'success'
                        ).then(okay => {
                            if (okay) {
                                $.LoadingOverlay("show");
                                location.reload();
                            }
                        });
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.metadata.message,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
        });
    </script>
@endsection
