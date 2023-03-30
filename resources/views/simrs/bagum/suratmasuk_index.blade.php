@extends('adminlte::page')
@section('title', 'Surat Masuk')
@section('content_header')
    <h1>Surat Masuk</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card theme="primary" icon="fas fa-envelope" collapsible title="Surat Masuk">
                <x-adminlte-button theme="success" label="Tambah Surat Masuk" class=" btn-sm" id="tambahSurat" />
                <x-adminlte-button theme="primary" label="Blanko Disposisi" class=" btn-sm" id="cetakBlanko" />
                @php
                    $heads = ['Action', 'No', 'Kode', 'Tanggal', 'No Surat', 'Asal', 'Perihal', 'Tgl Disposisi', 'Urutan Disposisi', 'Tgl Diteruskan', 'Disposisi', 'Pengolah', 'T Terima', ' Tgl Selesai', 'Tgl Terima'];
                    $config['scrollX'] = true;
                    $config['paging'] = false;
                    $config['searching'] = false;
                    $config['info'] = false;
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
                                <x-adminlte-button class="btn-xs mb-1 uploadSuratMasuk" theme="primary" icon="fas fa-upload"
                                    title="Upload Surat" data-id="{{ $item->id_surat_masuk }}" />
                            </td>
                            <td>{{ $item->no_urut }}</td>
                            <td>{{ $item->kode }}</td>
                            <td>{{ $item->tgl_surat }}</td>
                            <td>{{ $item->no_surat }}</td>
                            <td>{{ $item->asal_surat }}</td>
                            <td>{{ $item->perihal }}</td>
                            <td>{{ $item->tgl_disposisi }}</td>
                            <td></td>
                            <td>{{ $item->tgl_diteruskan }}</td>
                            <td>{{ $item->disposisi }}</td>
                            <td>{{ $item->pengolah }}</td>
                            <td>{{ $item->tanda_terima }}</td>
                            <td>{{ $item->tgl_penyelesaian }}</td>
                            <td>{{ $item->tgl_terima }}</td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modal" title="Surat Masuk" size="xl" theme="success" v-centered>
        <form action="" id="formSurat">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <x-adminlte-input name="no_urut" label="No Urut" igroup-size="sm" enable-old-support readonly />
                    <x-adminlte-input name="kode" label="Kode Surat" igroup-size="sm" enable-old-support />
                    <x-adminlte-input name="no_surat" label="Nomor Surat" igroup-size="sm" enable-old-support required />
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tgl_surat" label="Tgl Surat" igroup-size="sm" :config="$config"
                        enable-old-support required />
                    <x-adminlte-input name="asal_surat" label="Asal Surat" igroup-size="sm" enable-old-support required />
                    <x-adminlte-input name="perihal" label="Perihal" igroup-size="sm" enable-old-support required />
                    <label for="">Sifat Surat</label>
                    <div class="col-12">
                        <div class="form-group">
                            <div class="row">
                                <div class="custom-control custom-radio ml-1">
                                    <input class="custom-control-input" type="radio" id="biasa" name="sifatsurat">
                                    <label for="biasa" class="custom-control-label" value="Biasa">Biasa</label>
                                </div>
                                <div class="custom-control custom-radio ml-1">
                                    <input class="custom-control-input" type="radio" id="segera" name="sifatsurat">
                                    <label for="segera" class="custom-control-label" value="Segera">Segera</label>
                                </div>
                                <div class="custom-control custom-radio ml-1">
                                    <input class="custom-control-input" type="radio" id="ssegera" name="sifatsurat">
                                    <label for="ssegera" class="custom-control-label" value="Sangat Segera">Sangat
                                        Segera</label>
                                </div>
                                <div class="custom-control custom-radio ml-1">
                                    <input class="custom-control-input" type="radio" id="rahasia" name="sifatsurat">
                                    <label for="rahasia" class="custom-control-label" value="Rahasia">Rahasia</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <x-adminlte-input-date name="tgl_diterima" label="Tgl Diterima Surat" igroup-size="sm" :config="$config"
                        enable-old-support />
                </div>
                <div class="col-md-7">
                    <x-adminlte-input-date name="tgl_disposisi" label="Tgl Disposisi" igroup-size="sm" :config="$config"
                        enable-old-support required />
                    <x-adminlte-input-date name="tgl_diteruskan" label="Tgl Diteruskan" igroup-size="sm" :config="$config"
                        enable-old-support required />
                    <x-adminlte-input name="pengolah" label="Pengolah" igroup-size="sm" enable-old-support />
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
                    </div>
                    <x-adminlte-textarea name="disposisi" rows=5 placeholder="Catatan Disposisi"
                        label="Catatan Disposisi" />
                </div>

            </div>
            <div class="row">
                <div class="col-md-7">

                </div>
            </div>
            <x-adminlte-input name="tanda_terima" label="Tanda Terima" igroup-size="sm" enable-old-support />
            <div class="row">
                <div class="col-md-6">

                </div>
            </div>
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button class="mr-auto " id="btnStore" theme="success" icon="fas fa-save" label="Simpan" />
            <x-adminlte-button class="mr-auto" id="btnUpdate" theme="warning" icon="fas fa-edit" label="Update" />
            <x-adminlte-button id="btnDelete" theme="danger" icon="fas fa-trash-alt" label="Delete" />
            <x-adminlte-button theme="secondary" icon="fas fa-arrow-left" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)

@section('js')
    <script>
        $(document).ready(function() {
            $('.cetakDisposisi').click(function() {
                var id = $(this).data('id');
                var url = "{{ route('bagianumum.disposisi.index') }}/" + id;
                window.open(url, 'window name', 'window settings');
                return false;
            });


            $('#tambahSurat').click(function() {
                $.LoadingOverlay("show");
                $('#formSurat').trigger("reset");
                $('#modal').modal('show');
                $.LoadingOverlay("hide", true);
            });

            $('#cetakBlanko').click(function() {
                var url = "{{ route('bagianumum.disposisi.create') }}";
                window.open(url, 'window name', 'window settings');
                return false;
            });
            $('.editSuratMasuk').click(function() {
                var id = $(this).data('id');
                $.LoadingOverlay("show");
                $('#formSurat').trigger("reset");
                $.get("{{ route('bagianumum.suratmasuk.index') }}/" + id, function(data) {
                    console.log(data);
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
                    $('#modal').modal('show');
                    $.LoadingOverlay("hide", true);
                })
            });
        });
    </script>
@endsection
