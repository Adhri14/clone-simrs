@extends('adminlte::page')
@section('title', 'Obat')
@section('content_header')
    <h1>Obat</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Referensi Obat SIMRS" theme="secondary" collapsible>
                <div class="row">
                    <div class="col-md-8">
                        <x-adminlte-button label="Tambah" class="btn-sm" theme="success" title="Tambah User" icon="fas fa-plus"
                            data-toggle="modal" data-target="#createPasien" />
                        <x-adminlte-button label="Refresh" class="btn-sm" theme="warning" title="Refresh User"
                            icon="fas fa-sync" onclick="window.location='{{ route('user.index') }}'" />
                    </div>
                    <div class="col-md-4">
                        <form action="" method="get">
                            <x-adminlte-input name="search" placeholder="Pencarian Nama Obat" igroup-size="sm"
                                value="{{ $request->search }}">
                                <x-slot name="appendSlot">
                                    <x-adminlte-button type="submit" theme="primary" label="Cari!" />
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
                @php
                    $heads = ['Kode', 'Nama Barang', 'Nama Generik', 'Kelompok', 'Satuan', 'Action'];
                    $config['paging'] = false;
                    $config['lengthMenu'] = false;
                    $config['searching'] = false;
                    $config['info'] = false;
                    $config['responsive'] = true;
                @endphp
                <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" :config="$config" hoverable bordered
                    compressed>
                    @foreach ($barangs as $item)
                        <tr>
                            {{-- <td>{{ $loop->iteration }}</td> --}}
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nama_generik }}</td>
                            <td>{{ $item->klp_barang }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>
                                <x-adminlte-button class="btn-xs btnEdit" theme="warning" icon="fas fa-edit"
                                    title="Edit Barang {{ $item->nama_barang }}" data-id="{{ $item->kode_barang }}" />
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <div class="text-info float-left ">
                    Data yang ditampilkan {{ $barangs->count() }} dari total {{ $barang_total }}
                </div>
                <div class="float-right pagination-sm">
                    {{ $barangs->appends(request()->input())->links() }}
                </div>
                {{-- <a href="{{ route('pelayanan-medis.dokter_antrian_refresh') }}" class="btn btn-success">Refresh
                    Dokter</a> --}}
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modalEdit" name="modalEdit" title="Edit Obat" size="lg" theme="success"
        icon="fas fa-file-medical" v-centered>
        <form action="">
            <x-adminlte-input name="nama_barang" label="Nama Obat" placeholder="Nama Obat" required />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="sepsis">
                            <label for="sepsis" class="custom-control-label">Sepsis</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="pnemonia_bakterial">
                            <label for="pnemonia_bakterial" class="custom-control-label">Pnemonia Bakterial</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="intraabdomen_komplikata">
                            <label for="intraabdomen_komplikata" class="custom-control-label">Infeksi Intra-abdomen
                                Komplikata</label>
                        </div>
                        <br>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="meningitis_encefalitis">
                            <label for="meningitis_encefalitis" class="custom-control-label">Meningitis /
                                Encefalitis Bakterial</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="jaringan_kulit">
                            <label for="jaringan_kulit" class="custom-control-label">Infeksi Kulit &
                                Jaringan</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Mata /
                                Telinga</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Sendi &
                                Tulang</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Pnemonia Non
                                Bakterial</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Saluran Cerna /
                                Hepatobilier Pancreas</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Ginjal & Saluran
                                Kemih</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Otak & Sumsum
                                Tulang</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Obsteri /
                                Ginekologi</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi TB Paru</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi TB Extra
                                Paru</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">LEPRA</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Jamur</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Infeksi Virus</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1">
                            <label for="customCheckbox1" class="custom-control-label">Non Infeksi
                                (Inflamasi)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="meningitis-encefalitis"
                                value="meningitis-encefalitis">
                            <label for="meningitis-encefalitis" class="custom-control-label">Narkotika</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                            <label for="customCheckbox1" class="custom-control-label">Psikotropika</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                            <label for="customCheckbox1" class="custom-control-label">Obat Generik</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                            <label for="customCheckbox1" class="custom-control-label">Prekusor</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                            <label for="customCheckbox1" class="custom-control-label">Antibiotik</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                            <label for="customCheckbox1" class="custom-control-label">Formularium</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                            <label for="customCheckbox1" class="custom-control-label">Non Formularium</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="customCheckbox1" value="option1">
                            <label for="customCheckbox1" class="custom-control-label">Morphin</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button id="btnStore" class="mr-auto" icon="fas fa-file-medical" theme="success"
                label="Simpan Barang" />
            <x-adminlte-button id="btnUpdate" class="mr-auto" icon="fas fa-edit" theme="warning"
                label="Update Barang" />
            <x-adminlte-button id="btnDelete" icon="fas fa-trash" theme="danger" label="Hapus" data-toggle="tooltip"
                title="Delete Surat Kontrol {{ $item->noSuratKontrol }}" />
            <x-adminlte-button theme="danger" icon="fas fa-times" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('js')
    <script>
        $(function() {
            $('.btnEdit').click(function() {
                var kode = $(this).data('id');
                var url = "{{ route('farmasi.obat.index') }}/" + kode;
                $.LoadingOverlay("show");
                $.get(url, function(data) {
                    console.log(data);
                    $('#nama_barang').val(data.nama_barang);
                    // $('#nomor_suratkontrol').val(data.noSuratKontrol);
                    // $('#nomorsep_suratkontrol').val(data.noSepAsalKontrol);
                    // $('#tanggal_suratkontrol').val(data.tglRencanaKontrol);
                    // $('#kodepoli_suratkontrol').val(data.poliTujuan).trigger('change');
                    // $('#kodedokter_suratkontrol').val(data.kodeDokter).trigger('change');
                    $('#modalEdit').modal('show');
                    $.LoadingOverlay("hide", true);
                });
                // $('#btnStore').hide();
                // $('#btnUpdate').show();
                // $.LoadingOverlay("show");

            });
        });
    </script>
@endsection
