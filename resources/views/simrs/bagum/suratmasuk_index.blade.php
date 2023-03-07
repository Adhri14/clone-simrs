@extends('adminlte::page')
@section('title', 'Surat Masuk')
@section('content_header')
    <h1>Surat Masuk</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                {{-- <form action="" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            <x-adminlte-input name="user" label="User" readonly value="{{ Auth::user()->name }}" />
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            @can('admin')
                                <x-adminlte-select2 name="kodepoli" label="Poliklinik">
                                    <option value="">00000 - SEMUA POLIKLINIK</option>
                                    @foreach ($polis as $item)
                                        <option value="{{ $item->kodesubspesialis }}"
                                            {{ $item->kodesubspesialis == $request->kodepoli ? 'selected' : null }}>
                                            {{ $item->kodesubspesialis }}
                                            -
                                            {{ $item->namasubspesialis }}
                                        </option>
                                    @endforeach
                                </x-adminlte-select2>
                            @else
                                @can('poliklinik')
                                    <x-adminlte-input name="kodepoli" label="Poliklinik" readonly
                                        value="{{ Auth::user()->username }}" />
                                @endcan
                            @endcan
                        </div>
                        <div class="col-md-3">
                            <x-adminlte-select2 name="kodedokter" label="Dokter">
                                <option value="">00000 - SEMUA DOKTER</option>
                                @foreach ($dokters as $item)
                                    <option value="{{ $item->kode_dokter_jkn }}"
                                        {{ $item->kode_dokter_jkn == $request->kodedokter ? 'selected' : null }}>
                                        {{ $item->kode_dokter_jkn }} -
                                        {{ $item->nama_paramedis }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                </form> --}}
            </x-adminlte-card>
            <x-adminlte-card theme="primary" icon="fas fa-envelope" collapsible
                title="Info cara menggunakan aplikasi antrian Online">
                <x-adminlte-button theme="success" label="Tambah Surat Masuk" class=" btn-sm" />
                <a href="{{ route('bagianumum.disposisi.create') }}" class="btn btn-primary btn-sm ">Blanko Disposisi</a>
                @php
                    $heads = ['Action', 'No', 'Kode', 'Tanggal', 'No Surat', 'Asal', 'Perihal', 'Tgl Disposisi', 'Urutan Disposisi', 'Tgl Diteruskan', 'Disposisi', 'Pengolah', 'T Terima', ' Tgl Selesai', 'Tgl Terima'];
                    $config['scrollX'] = true;
                    $config['paging'] = false;
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
    <x-adminlte-modal id="modal" title="Data Pasien" size="lg" theme="success" v-centered>
        <form action="" id="form">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="no_urut" label="No Urut" igroup-size="sm" enable-old-support readonly />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input name="kode" label="Kode Surat" igroup-size="sm" enable-old-support />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="no_surat" label="Nomor Surat" igroup-size="sm" enable-old-support required />
                </div>
                <div class="col-md-6">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tgl_surat" label="Tgl Surat" igroup-size="sm" :config="$config"
                        enable-old-support required />
                </div>
            </div>
            <x-adminlte-input name="asal_surat" label="Asal Surat" igroup-size="sm" enable-old-support />
            <x-adminlte-input name="perihal" label="Perihal" igroup-size="sm" enable-old-support />
            <div class="row">
                <div class="col-md-6">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tgl_disposisi" label="Tgl Disposisi" igroup-size="sm" :config="$config"
                        enable-old-support required />
                </div>
                <div class="col-md-6">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tgl_diteruskan" label="Tgl Diteruskan" igroup-size="sm" :config="$config"
                        enable-old-support required />
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <x-adminlte-input name="disposisi" label="Disposisi" igroup-size="sm" enable-old-support />
                    <x-adminlte-input name="pengolah" label="Pengolah" igroup-size="sm" enable-old-support />
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="tindaklanjuti">
                            <label for="tindaklanjuti" class="custom-control-label">Untuk ditindaklanjuti</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_kulit_jaringan">
                            <label for="infeksi_kulit_jaringan" class="custom-control-label">Proses sesuai kemampuan / peraturan yang berlaku</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_mata_telinga">
                            <label for="infeksi_mata_telinga" class="custom-control-label">Koordinasikan / konfirmasi dengan ybs / instansi terkait</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_sendi_tulang">
                            <label for="infeksi_sendi_tulang" class="custom-control-label">Untuk dibantu / difasilitasi / dipenuhi sesuai kebutuhan</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="pnemonia_non_bakterial">
                            <label for="pnemonia_non_bakterial" class="custom-control-label">Pelajari / telaah / sarannya</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_saluran_cerna">
                            <label for="infeksi_saluran_cerna" class="custom-control-label">Wakili / hadiri / terima / laporkan hasilnya</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_ginjal_kemih">
                            <label for="infeksi_ginjal_kemih" class="custom-control-label">Agendakan / persiapkan / koordinasikan </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_otak_sumsum">
                            <label for="infeksi_otak_sumsum" class="custom-control-label">Jadwalkan ingatkan waktunya</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="infeksi_obsteri">
                            <label for="infeksi_obsteri" class="custom-control-label">Siapkan pointer / sambutan / bahan</label>
                        </div>
                    </div>
                </div>
            </div>

            <x-adminlte-input name="tanda_terima" label="Tanda Terima" igroup-size="sm" enable-old-support />
            <x-adminlte-input name="no_bpjs" label="No BPJS" igroup-size="sm" enable-old-support />
            <x-adminlte-input name="no_ihs" label="No Satu Sehat" igroup-size="sm" enable-old-support />
            <div class="row">
                <div class="col-md-6">

                </div>
                {{-- <div class="col-md-4">
                    <input id="id" type="hidden" name="id">
                    <x-adminlte-input name="nama" label="Nama" igroup-size="sm" enable-old-support required />
                    <x-adminlte-select name="sex" label="Jenis Kelamin" igroup-size="sm" enable-old-support required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </x-adminlte-select>

                    <x-adminlte-input name="nohp" label="No HP" igroup-size="sm" enable-old-support required />
                </div>
                <div class="col-md-4">
                    <x-adminlte-select2 name="provinsi" label="Provinsi">
                        <option value="" disabled>PILIH PROVINSI</option>
                        @foreach ($provinsi as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="kabupaten" label="Kabupaten">
                        <option value="" disabled>PILIH KABUPATEN</option>
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="kecamatan" label="Kecamatan">
                        <option value="" disabled>PILIH KECAMATAN</option>
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="desa" label="Desa">
                        <option value="" disabled>PILIH DESA</option>
                    </x-adminlte-select2>
                    <x-adminlte-textarea name="alamat" placeholder="Alamat" label="Alamat" igroup-size="sm"
                        enable-old-support required />
                </div> --}}
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

@section('css')
    <style>
        #table1 tr td {
            /* height: 500px !important; */
            /* min-height: 20px !important; */
            /* max-height: 20px !important; */
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.cetakDisposisi').click(function() {
                var id = $(this).data('id');
                var url = "{{ route('bagianumum.disposisi.index') }}/" + id;
                window.open(url, 'window name', 'window settings');
                return false;
            });
            $('.editSuratMasuk').click(function() {
                var id = $(this).data('id');
                $.LoadingOverlay("show");
                $.get("{{ route('bagianumum.suratmasuk.index') }}/" + id, function(data) {
                    console.log(data);
                    // $('#kodebooking').html(data.kodebooking);
                    // $('#angkaantrean').html(data.angkaantrean);
                    // $('#nomorantrean').html(data.nomorantrean);
                    // $('#tanggalperiksa').html(data.tanggalperiksa);
                    // $('#norm').html(data.norm);
                    // $('#nik').html(data.nik);
                    // $('#nomorkartu').html(data.nomorkartu);
                    // $('#nama').html(data.nama);
                    // $('#nohp').html(data.nohp);
                    // $('#nomorrujukan').html(data.nomorrujukan);
                    // $('#nomorsuratkontrol').html(data.nomorsuratkontrol);
                    // $('#nomorsep').html(data.nomorsep);
                    // $('#jenispasien').html(data.jenispasien);
                    // $('#namapoli').html(data.namapoli);
                    // $('#namadokter').html(data.namadokter);
                    // $('#jampraktek').html(data.jampraktek);
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
                    // $('#jeniskunjungan').html(jeniskunjungan);
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
                    // var urlLanjutFarmasi = "{{ route('landingpage') }}" +
                    //     "/poliklinik/lanjut_farmasi/" + data
                    //     .kodebooking;
                    // $("#lanjutFarmasi").attr("href", urlLanjutFarmasi);

                    // var urlLanjutFarmasiRacikan = "{{ route('landingpage') }}" +
                    //     "/poliklinik/lanjut_farmasi_racikan/" + data
                    //     .kodebooking;
                    // $("#lanjutFarmasiRacikan").attr("href", urlLanjutFarmasiRacikan);

                    // var urlSelesaiPoliklinik = "{{ route('landingpage') }}" +
                    //     "/poliklinik/selesai_poliklinik/" + data
                    //     .kodebooking;
                    // $("#selesaiPoliklinik").attr("href", urlSelesaiPoliklinik);
                    $('#modal').modal('show');
                    $.LoadingOverlay("hide", true);
                })
            });
        });
    </script>
@endsection
