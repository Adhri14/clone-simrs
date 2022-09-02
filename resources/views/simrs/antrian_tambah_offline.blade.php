@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('title', 'Antrian QR Code')
@section('body')
    <div class="wrapper">
        <div class="row p-3">
            <div class="col-md-4">
                <x-adminlte-card title="Tambah Antrian Offline RSUD Waled" theme="primary" icon="fas fa-qrcode">
                    <dl class="row">
                        <dt class="col-sm-3">Poliklinik</dt>
                        <dd class="col-sm-8">: {{ $jadwal->namapoli }}</dd>
                        <dt class="col-sm-3">Dokter</dt>
                        <dd class="col-sm-8">: {{ $jadwal->namadokter }}</dd>
                        <dt class="col-sm-3">Hari Waktu</dt>
                        <dd class="col-sm-8">: {{ $jadwal->namahari }}, {{ $jadwal->jadwal }}</dd>
                        <dt class="col-sm-3">Hari Waktu</dt>
                        <dd class="col-sm-8">: {{ $tanggal }}</dd>
                    </dl>
                    <x-adminlte-input name="nik_cek" label="Nomor Induk Keluarga (NIK / KTP)"
                        placeholder="Masukan Nomor Induk Keluarga (NIK / KTP)">
                        <x-slot name="appendSlot">
                            <x-adminlte-button name="btn_check_nik" id="btn_check_nik" theme="success" label="Cek" />
                        </x-slot>
                    </x-adminlte-input>
                    {{-- <x-adminlte-input name="nomorkartuform" label="Nomor Kartu BPJS/JKN"
                        placeholder="Masukan Nomor Kartu BPJS/JKN">
                        <x-slot name="appendSlot">
                            <x-adminlte-button name="btn_check_nomorkartu" id="btn_check_nomorkartu" theme="success"
                                label="Cek" />
                        </x-slot>
                    </x-adminlte-input> --}}
                    <x-adminlte-input name="norujukan" label="Nomor Rujukan" placeholder="Masukan Nomor Rujukan">
                        <x-slot name="appendSlot">
                            <x-adminlte-button name="btn_check_rujukan" id="btn_check_rujukan" theme="success"
                                label="Cek" />
                        </x-slot>
                    </x-adminlte-input>
                    <x-adminlte-input name="suratkontrol_cek" label="Nomor Surat Kontrol"
                        placeholder="Masukan Nomor Surat Kontrol">
                        <x-slot name="appendSlot">
                            <x-adminlte-button name="btn_cek_suratkontrol" id="btn_cek_suratkontrol" theme="success"
                                label="Cek" />
                        </x-slot>
                    </x-adminlte-input>
                    <a href="{{ route('antrian.console') }}" class="btn btn-danger btn-lg">Kembali</a>
                    {{-- <a href="{{ route('antrian.store_offline') }}" class="btn btn-success">Cek</a> --}}
                </x-adminlte-card>
            </div>
            <div class="col-md-4 dataPasien">
                <x-adminlte-card title="Data Pasien" theme="primary" icon="fas fa-qrcode">
                    <button class="btn btn-success col-md-12" id="keaktifan">XXXXX XXXXX</button> <br><br>
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Nama</dt>
                                <dd class="col-sm-8"><span id="nama_noform"></span></dd>
                                <dt class="col-sm-4">NIK</dt>
                                <dd class="col-sm-8"><span id="nik_noform"></span></dd>
                                <dt class="col-sm-4">Kelamin</dt>
                                <dd class="col-sm-8"><span id="kelamin"></dd>
                                <dt class="col-sm-4">Tgl Lahir</dt>
                                <dd class="col-sm-8"><span id="tgllahir"></span></dd>
                                <dt class="col-sm-4">Umur</dt>
                                <dd class="col-sm-8"><span id="umur"></span></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Kartu JKN</dt>
                                <dd class="col-sm-8"><span id="nomorkartu_noform"></span></dd>
                                <dt class="col-sm-4">Hak Kelas</dt>
                                <dd class="col-sm-8"><span id="hakkelas"></dd>
                                <dt class="col-sm-4">Jenis</dt>
                                <dd class="col-sm-8"><span id="jenispeserta"></dd>
                                <dt class="col-sm-4">Faskes 1</dt>
                                <dd class="col-sm-8"><span id="faskes1"> </dd>
                            </dl>
                        </div>
                    </div>

                    <form action="{{ route('antrian.store_offline') }}" method="post">
                        @csrf
                        <input type="hidden" id="nomorkartu" name="nomorkartu">
                        <input type="hidden" id="nik" name="nik">
                        <x-adminlte-input id="nohp" name="nohp" label="Nomor HP" placeholder="Masukan Nomor HP" />
                        <input type="hidden" id="kodepoli" name="kodepoli" value="{{ $jadwal->kodepoli }}">
                        <input type="hidden" id="norm" name="norm">
                        <input type="hidden" id="nama" name="nama">
                        <input type="hidden" id="tanggalperiksa" name="tanggalperiksa" value="{{ $tanggal }}">
                        <input type="hidden" id="kodedokter" name="kodedokter" value="{{ $jadwal->kodedokter }}">
                        <input type="hidden" id="jampraktek" name="jampraktek" value="{{ $jadwal->jadwal }}">
                        <input type="hidden" id="jeniskunjungan" name="jeniskunjungan">
                        <input type="hidden" id="nomorreferensi" name="nomorreferensi">
                        <button type="submit" class="btn btn-primary btn-lg withLoad">Daftar Antrian</button>
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
    </div>
    {{-- Pilih Dokter --}}
    <x-adminlte-modal id="modalDokter" size="lg" title="Pilih Dokter Poliklinik" theme="success"
        icon="fas fa-user-md">
        <div id="btnDokter">
        </div>
    </x-adminlte-modal>
    @include('sweetalert::alert')
@stop
{{-- @section('plugins.Sweetalert2', true); --}}
@section('adminlte_js')
    <script src="{{ asset('vendor/loading-overlay/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    {{-- cek nik --}}
    <script>
        $(function() {
            $('.dataPasien').hide();
            $('#btn_check_nik').click(function() {
                $('.dataPasien').hide();
                var nik = $('#nik_cek').val();
                $.LoadingOverlay("show", {
                    text: "Cek NIK Pasien..."
                });
                var url = "{{ route('api.cek_nik') }}";
                var formData = {
                    nik: nik,
                };
                $.get(url, formData, function(data) {
                    console.log(data);
                    if (data.metaData.code == 200) {
                        // pasien lama
                        if (data.response.peserta.mr.noMR) {
                            $('#nama_noform').html(data.response.peserta.nama);
                            $('#nik_noform').html(data.response.peserta.nik);
                            $('#nomorkartu_noform').html(data.response.peserta.noKartu);
                            $('#tgllahir').html(data.response.peserta.tglLahir);
                            $('#kelamin').html(data.response.peserta.sex);
                            $('#umur').html(data.response.peserta.umur.umurSaatPelayanan);
                            $('#hakkelas').html(data.response.peserta.hakKelas.keterangan);
                            $('#jenispeserta').html(data.response.peserta.jenisPeserta.keterangan);
                            $('#faskes1').html(data.response.peserta.provUmum.nmProvider);
                            $('#nohp').val(data.response.peserta.mr.noTelepon);
                            $('#keaktifan').html(data.response.peserta.statusPeserta.keterangan +
                                ' NO. RM ' + data.response.peserta.mr.noMR);
                            $('.dataPasien').show();
                            // form
                            $('#nomorkartu').val(data.response.peserta.noKartu);
                            $('#nik').val(data.response.peserta.nik);
                            $('#jeniskunjungan').val(3);
                            $('#norm').val(data.response.peserta.mr.noMR);
                            $('#nama').val(data.response.peserta.nama);

                            swal.fire(
                                'Success',
                                'Data NIK Pasien Ditemukan',
                                'success'
                            );
                        }
                        // pasien baru
                        else {
                            swal.fire(
                                'Error !',
                                'Anda Belum Terdaftar Di RSUD Waled. Silahkan Daftar Menggunakan Antrian Offline.',
                                'error'
                            );
                        }
                    }
                    // error
                    else {
                        swal.fire(
                            'Error !',
                            data.metaData.message,
                            'error'
                        );
                    }
                    $.LoadingOverlay("hide");
                });
            });
            $('#btn_check_rujukan').click(function() {
                $('.dataPasien').hide();
                var rujukan = $('#norujukan').val();
                $.LoadingOverlay("show", {
                    text: "Cek Rujukan Pasien..."
                });
                var formData = {
                    nomorreferensi: rujukan,
                    jenisrujukan: 1,
                };
                $.get("{{ route('api.rujukan_jumlah_sep') }}", formData, function(data) {
                    console.log(data);
                    if (data.metaData.code == 200) {
                        if (data.response.jumlahSEP < 1) {
                            $.get("{{ route('api.rujukan_nomor') }}", formData, function(data) {
                                console.log(data);
                                if (data.metaData.code == 200) {
                                    // pasien lama
                                    if (data.response.rujukan.peserta.mr.noMR) {
                                        $('#nama_noform').html(data.response.rujukan.peserta
                                            .nama);
                                        $('#nik_noform').html(data.response.rujukan.peserta
                                            .nik);
                                        $('#nomorkartu_noform').html(data.response.rujukan
                                            .peserta.noKartu);
                                        $('#tgllahir').html(data.response.rujukan.peserta
                                            .tglLahir);
                                        $('#kelamin').html(data.response.rujukan.peserta
                                            .sex);
                                        $('#umur').html(data.response.rujukan.peserta.umur
                                            .umurSaatPelayanan);
                                        $('#hakkelas').html(data.response.rujukan.peserta
                                            .hakKelas
                                            .keterangan);
                                        $('#jenispeserta').html(data.response.rujukan
                                            .peserta.jenisPeserta
                                            .keterangan);
                                        $('#faskes1').html(data.response.rujukan.peserta
                                            .provUmum
                                            .nmProvider);
                                        $('#nohp').val(data.response.rujukan.peserta.mr
                                            .noTelepon);
                                        $('#keaktifan').html(data.response.rujukan.peserta
                                            .statusPeserta
                                            .keterangan +
                                            ' NO. RM ' + data.response.rujukan.peserta
                                            .mr.noMR);
                                        $('.dataPasien').show();
                                        // form
                                        $('#nomorkartu').val(data.response.rujukan.peserta
                                            .noKartu);
                                        $('#nik').val(data.response.rujukan.peserta.nik);
                                        $('#nomorreferensi').val(data.response.rujukan
                                            .noKunjungan);
                                        $('#jeniskunjungan').val(1);
                                        $('#norm').val(data.response.rujukan.peserta.mr
                                            .noMR);
                                        $('#nama').val(data.response.rujukan.peserta.nama);
                                        swal.fire(
                                            'Success',
                                            'Data Rujukan Ditemukan',
                                            'success'
                                        );
                                        $.LoadingOverlay("hide");
                                    }
                                    // pasien baru
                                    else {
                                        swal.fire(
                                            'Error !',
                                            data.metaData.message,
                                            'error'
                                        );
                                    }
                                }
                                // error
                                else {
                                    swal.fire(
                                        'Error !',
                                        data.metaData.message,
                                        'error'
                                    );
                                }
                            });
                        } else {
                            swal.fire(
                                'Error !',
                                'Rujukan telah digunakan untuk membuat SEP sebelumnya. Silahkan daftar menggunakan Surat Kontrol.',
                                'error'
                            );
                            $.LoadingOverlay("hide");
                        }
                    }
                    // error
                    else {
                        swal.fire(
                            'Error !',
                            data.metaData.message,
                            'error'
                        );
                    }
                });
            });
            $('#btn_cek_suratkontrol').click(function() {
                $('.dataPasien').hide();
                var nomorreferensi = $('#suratkontrol_cek').val();
                alert(nomorreferensi);
                $.LoadingOverlay("show", {
                    text: "Cek Rujukan Pasien..."
                });
                var formData = {
                    nomorreferensi: nomorreferensi,
                };
                $.get("{{ route('api.surat_kontrol_nomor') }}", formData, function(suratkontrol) {
                    console.log(suratkontrol);
                    if (suratkontrol.metaData.code == 200) {
                        var formRujukan = {
                            nomorreferensi: suratkontrol.response.sep.provPerujuk.noRujukan,
                            jenisrujukan: 1,
                        };
                        $.get("{{ route('api.rujukan_nomor') }}", formRujukan, function(data) {
                            // console.log(data);
                            if (data.metaData.code == 200) {
                                // pasien lama
                                if (data.response.rujukan.peserta.mr.noMR) {
                                    $('#nama_noform').html(data.response.rujukan.peserta
                                        .nama);
                                    $('#nik_noform').html(data.response.rujukan.peserta
                                        .nik);
                                    $('#nomorkartu_noform').html(data.response.rujukan
                                        .peserta.noKartu);
                                    $('#tgllahir').html(data.response.rujukan.peserta
                                        .tglLahir);
                                    $('#kelamin').html(data.response.rujukan.peserta
                                        .sex);
                                    $('#umur').html(data.response.rujukan.peserta.umur
                                        .umurSaatPelayanan);
                                    $('#hakkelas').html(data.response.rujukan.peserta
                                        .hakKelas
                                        .keterangan);
                                    $('#jenispeserta').html(data.response.rujukan
                                        .peserta.jenisPeserta
                                        .keterangan);
                                    $('#faskes1').html(data.response.rujukan.peserta
                                        .provUmum
                                        .nmProvider);
                                    $('#nohp').val(data.response.rujukan.peserta.mr
                                        .noTelepon);
                                    $('#keaktifan').html(data.response.rujukan.peserta
                                        .statusPeserta
                                        .keterangan +
                                        ' NO. RM ' + data.response.rujukan.peserta
                                        .mr.noMR);
                                    $('.dataPasien').show();
                                    // form
                                    $('#nomorkartu').val(data.response.rujukan.peserta
                                        .noKartu);
                                    $('#nik').val(data.response.rujukan.peserta.nik);
                                    $('#nomorreferensi').val(suratkontrol.response
                                        .noSuratKontrol);
                                    $('#jeniskunjungan').val(3);
                                    $('#norm').val(data.response.rujukan.peserta.mr
                                        .noMR);
                                    $('#nama').val(data.response.rujukan.peserta.nama);
                                    swal.fire(
                                        'Success',
                                        'Data Rujukan Ditemukan',
                                        'success'
                                    );
                                    $.LoadingOverlay("hide");
                                }
                                // pasien baru
                                else {
                                    swal.fire(
                                        'Error !',
                                        data.metaData.message,
                                        'error'
                                    );
                                }
                            }
                            // error
                            else {
                                swal.fire(
                                    'Error !',
                                    data.metaData.message,
                                    'error'
                                );
                            }
                        });
                    }
                    // // error
                    else {
                        swal.fire(
                            'Error !',
                            suratkontrol.metaData.message,
                            'error'
                        );
                    }
                    $.LoadingOverlay("hide");
                });
            });
        });
    </script>
    {{-- withLoad --}}
    <script>
        $(function() {
            $(".withLoad").click(function() {
                $.LoadingOverlay("show");
            });
        })
    </script>
@stop
