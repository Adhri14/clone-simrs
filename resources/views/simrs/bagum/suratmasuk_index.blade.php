@extends('adminlte::page')
@section('title', 'Surat Masuk')
@section('content_header')
    <h1>Surat Masuk</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card theme="primary" icon="fas fa-envelope" collapsible title="Surat Masuk">
                <div class="row">
                    <div class="col-md-8">
                        <x-adminlte-button theme="success" label="Tambah Surat Masuk" class=" btn-sm" id="tambahSurat" />
                        <x-adminlte-button theme="primary" label="Blanko Disposisi" class=" btn-sm" id="cetakBlanko" />
                        <x-adminlte-button label="Refresh" class="btn-sm" theme="warning" title="Refresh User"
                            icon="fas fa-sync" onclick="window.location='{{ route('bagianumum.suratmasuk.index') }}'" />
                    </div>
                    <div class="col-md-4">
                        <form action="" method="get">
                            <x-adminlte-input name="search" placeholder="Pencarian Asal / Perihal Surat" igroup-size="sm"
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
                    $heads = ['Action', 'No / Kode', 'Tgl Surat', 'No Surat', 'Asal', 'Perihal', 'Tgl Disposisi', 'Tgl Diteruskan', 'Pengolah', 'Disposisi', 'Penerima', ' Tgl Terima'];
                    $config['scrollX'] = true;
                    $config['paging'] = false;
                    $config['searching'] = false;
                    $config['info'] = false;
                    $config['order'] = ['1', 'desc'];
                @endphp
                <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" :config="$config" bordered hoverable
                    compressed>
                    @foreach ($surats as $item)
                        <tr>
                            <td>
                                <x-adminlte-button class="btn-xs mb-1 cetakDisposisi" theme="success" icon="fas fa-print"
                                    title="Cetak Disposisi" data-id="{{ $item->id_surat_masuk }}" /> <br>
                                <x-adminlte-button class="btn-xs mb-1 editSuratMasuk" theme="warning" icon="fas fa-edit"
                                    title="Edit Surat Masuk" data-id="{{ $item->id_surat_masuk }}" />
                                <x-adminlte-button class="btn-xs mb-1 uploadLampiran" theme="primary" icon="fas fa-upload"
                                    title="Upload Surat" data-id="{{ $item->id_surat_masuk }}" />
                            </td>
                            <td>{{ $item->no_urut }} / {{ $item->kode }}</td>
                            <td>{{ $item->tgl_surat }}</td>
                            <td>{{ $item->no_surat }}</td>
                            <td>{{ $item->asal_surat }}</td>
                            <td>{{ $item->perihal }}</td>
                            <td>{{ $item->tgl_disposisi }}</td>
                            <td>{{ $item->tgl_diteruskan }}</td>
                            <td>{{ $item->pengolah }}</td>
                            <td>{{ $item->disposisi }}</td>
                            <td>{{ $item->tanda_terima }}</td>
                            <td>{{ $item->tgl_terima_surat }}</td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <br>
                <div class="text float-left ">
                    Data yang ditampilkan {{ $surats->count() }} dari total {{ $surat_total }}
                </div>
                <div class="float-right pagination-sm">
                    {{ $surats->appends(request()->input())->links() }}
                </div>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modal" title="Surat Masuk" size="xl" theme="success" v-centered>
        <form action="" id="formSurat" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <input type="hidden" name="id_surat" id="id_surat">
                    <input type="hidden" name="_method" id="method">
                    <x-adminlte-input name="no_urut" label="No Urut" igroup-size="sm" enable-old-support readonly />
                    <x-adminlte-input name="kode" label="Kode Surat" igroup-size="sm" enable-old-support />
                    <x-adminlte-input name="no_surat" label="Nomor Surat" igroup-size="sm" enable-old-support required />
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tgl_surat" value="{{ now()->format('Y-m-d') }}" label="Tgl Surat"
                        igroup-size="sm" :config="$config" enable-old-support required />
                    <x-adminlte-input name="asal_surat" label="Asal Surat" igroup-size="sm" enable-old-support required />
                    <x-adminlte-textarea name="perihal" rows=5 placeholder="Perihal" label="Perihal" required />
                    <x-adminlte-input-date name="tgl_disposisi" value="{{ now()->format('Y-m-d') }}" label="Tgl Disposisi"
                        igroup-size="sm" :config="$config" enable-old-support required />
                    <x-adminlte-select name="sifat" label="Sifat Surat">
                        <option value="1">Biasa</option>
                        <option value="2">Segera</option>
                        <option value="3">Sangat Segera</option>
                        <option value="4">Rahasia</option>
                    </x-adminlte-select>
                </div>
                <div class="col-md-7">
                    <x-adminlte-input-date name="tgl_diteruskan" label="Tgl Diteruskan" igroup-size="sm" :config="$config"
                        enable-old-support />
                    <x-adminlte-input name="pengolah" label="Diteruskan Kpd" igroup-size="sm" enable-old-support />
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="tindaklanjuti">
                            <label for="tindaklanjuti" class="custom-control-label">Untuk ditindaklanjuti</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_kulit_jaringan">
                            <label for="infeksi_kulit_jaringan" class="custom-control-label">Proses sesuai kemampuan /
                                peraturan yang berlaku</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_mata_telinga">
                            <label for="infeksi_mata_telinga" class="custom-control-label">Koordinasikan / konfirmasi
                                dengan
                                ybs / instansi terkait</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_sendi_tulang">
                            <label for="infeksi_sendi_tulang" class="custom-control-label">Untuk dibantu / difasilitasi /
                                dipenuhi sesuai kebutuhan</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="pnemonia_non_bakterial">
                            <label for="pnemonia_non_bakterial" class="custom-control-label">Pelajari / telaah /
                                sarannya</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_saluran_cerna">
                            <label for="infeksi_saluran_cerna" class="custom-control-label">Wakili / hadiri / terima /
                                laporkan hasilnya</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_ginjal_kemih">
                            <label for="infeksi_ginjal_kemih" class="custom-control-label">Agendakan / persiapkan /
                                koordinasikan </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_otak_sumsum">
                            <label for="infeksi_otak_sumsum" class="custom-control-label">Jadwalkan ingatkan
                                waktunya</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_obsteri">
                            <label for="infeksi_obsteri" class="custom-control-label">Siapkan pointer / sambutan /
                                bahan</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_obsteri">
                            <label for="infeksi_obsteri" class="custom-control-label">Simpan / arsipkan</label>
                        </div>
                    </div>
                    <x-adminlte-textarea name="disposisi" rows=5 placeholder="Catatan Disposisi"
                        label="Catatan Disposisi" />
                </div>
            </div>
            <x-adminlte-input name="tanda_terima" label="Tanda Terima" igroup-size="sm" enable-old-support />
            <x-adminlte-input-date name="tgl_terima_surat" label="Tgl Terima Disposisi" igroup-size="sm"
                :config="$config" enable-old-support />
        </form>
        <x-slot name="footerSlot">
            {{-- <button type="submit" form="formSurat" class="btn btn-success mr-auto">Simpan</button> --}}
            <x-adminlte-button class="mr-auto " id="btnStore" type="submit" theme="success" icon="fas fa-save"
                label="Simpan" />
            <x-adminlte-button class="mr-auto" id="btnUpdate" theme="warning" icon="fas fa-edit" label="Update" />
            <x-adminlte-button id="btnDelete" theme="danger" icon="fas fa-trash-alt" label="Delete" />
            <form name="formDelete" id="formDelete" action="" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id_surat" id="id_surat">
            </form>

            <x-adminlte-button theme="secondary" icon="fas fa-arrow-left" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="modalLampiran" title="Lampiran Surat Masuk" size="xl" theme="success" v-centered>
        <form action="{{ route('bagianumum.suratlampiran.store') }}" id="formLampiran" method="POST" enctype="multipart/form-data">
            @csrf
            <x-adminlte-input-file name="file" placeholder="Pilih file...">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-lightblue">
                        <i class="fas fa-upload"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-file>
            <button type="submit">Submit</button>
        </form>
        <x-slot name="footerSlot">
            <button type="submit" form="formLampiran" class="btn btn-success mr-auto">Simpan</button>
            {{-- <x-adminlte-button class="mr-auto " id="btnStore" type="submit" theme="success" icon="fas fa-save"
                label="Simpan" />
            <x-adminlte-button class="mr-auto" id="btnUpdate" theme="warning" icon="fas fa-edit" label="Update" />
            <x-adminlte-button id="btnDelete" theme="danger" icon="fas fa-trash-alt" label="Delete" />
            <form name="formDelete" id="formDelete" action="" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id_surat" id="id_surat">
            </form> --}}
            <x-adminlte-button theme="secondary" icon="fas fa-arrow-left" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Sweetalert2', true)
@section('plugins.BsCustomFileInput', true)


@section('js')
    <script>
        $(document).ready(function() {
            $('#tambahSurat').click(function() {
                $.LoadingOverlay("show");
                $('#formSurat').trigger("reset");
                $('#btnStore').show();
                $('#btnUpdate').hide();
                $('#modal').modal('show');
                $.LoadingOverlay("hide", true);
            });
            $('#cetakBlanko').click(function() {
                var url = "{{ route('bagianumum.disposisi.create') }}";
                window.open(url, 'window name', 'window settings');
                return false;
            });
            $('.cetakDisposisi').click(function() {
                var id = $(this).data('id');
                var url = "{{ route('bagianumum.disposisi.index') }}/" + id;
                window.open(url, 'window name', 'window settings');
                return false;
            });
            $('#btnStore').click(function(e) {
                $.LoadingOverlay("show");
                e.preventDefault();
                var url = "{{ route('bagianumum.suratmasuk.store') }}/";
                $('#formSurat').attr('action', url);
                $('#method').val('POST');
                $('#formSurat').submit();
            });
            $('#btnUpdate').click(function(e) {
                $.LoadingOverlay("show");
                e.preventDefault();
                var id = $('#id_surat').val();
                var url = "{{ route('bagianumum.suratmasuk.index') }}/" + id;
                $('#formSurat').attr('action', url);
                $('#method').val('PUT');
                $('#formSurat').submit();
            });
            $('#btnDelete').click(function(e) {
                e.preventDefault();
                swal.fire({
                    title: 'Apakah anda ingin menghapus surat ini ?',
                    showConfirmButton: false,
                    showDenyButton: true,
                    showCancelButton: true,
                    denyButtonText: `Ya, Hapus`,
                }).then((result) => {
                    if (result.isDenied) {
                        $.LoadingOverlay("show");
                        var id = $('#id_surat').val();
                        var url = "{{ route('bagianumum.suratmasuk.index') }}/" + id;
                        $('#formDelete').attr('action', url);
                        $('#formDelete').submit();
                    }
                })
            });
            $('.editSuratMasuk').click(function() {
                var id = $(this).data('id');
                $.LoadingOverlay("show");
                $('#formSurat').trigger("reset");
                $('#btnStore').hide();
                $('#btnUpdate').show();
                $.get("{{ route('bagianumum.suratmasuk.index') }}/" + id, function(data) {
                    console.log(data);
                    $('#id_surat').val(data.id_surat_masuk);
                    $('#no_urut').val(data.no_urut);
                    $('#kode').val(data.kode);
                    $('#no_surat').val(data.no_surat);
                    $('#tgl_surat').val(data.tgl_surat);
                    $('#asal_surat').val(data.asal_surat);
                    $('#perihal').val(data.perihal);
                    $('#disposisi').val(data.disposisi);
                    $('#tgl_disposisi').val(data.tgl_disposisi);
                    $('#tgl_diteruskan').val(data.tgl_diteruskan);
                    $('#pengolah').val(data.pengolah);
                    $('#tanda_terima').val(data.tanda_terima);
                    $('#tgl_terima_surat').val(data.tgl_terima_surat);
                    $('#modal').modal('show');
                    $.LoadingOverlay("hide", true);
                })
            });
            $('.uploadLampiran').click(function() {
                var id = $(this).data('id');
                $.LoadingOverlay("show");
                $('#formSurat').trigger("reset");
                $('#btnStore').hide();
                $('#btnUpdate').show();
                $('#modalLampiran').modal('show');
                $.LoadingOverlay("hide");

                // $.get("{{ route('bagianumum.suratmasuk.index') }}/" + id, function(data) {
                //     console.log(data);
                //     $('#id_surat').val(data.id_surat_masuk);
                //     $('#no_urut').val(data.no_urut);
                //     $('#kode').val(data.kode);
                //     $('#no_surat').val(data.no_surat);
                //     $('#tgl_surat').val(data.tgl_surat);
                //     $('#asal_surat').val(data.asal_surat);
                //     $('#perihal').val(data.perihal);
                //     $('#disposisi').val(data.disposisi);
                //     $('#tgl_disposisi').val(data.tgl_disposisi);
                //     $('#tgl_diteruskan').val(data.tgl_diteruskan);
                //     $('#pengolah').val(data.pengolah);
                //     $('#tanda_terima').val(data.tanda_terima);
                //     $('#tgl_terima_surat').val(data.tgl_terima_surat);
                //     $('#modal').modal('show');
                //     $.LoadingOverlay("hide", true);
                // })
            });
        });
    </script>
@endsection
