@extends('adminlte::page')
@section('title', 'Daftar Antrian Online')
@section('content_header')
    <h1>Daftar Antrian Online</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Keterangan Pendaftaran" theme="secondary" collapsible>
                <form action="" method="get">

                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <Label>Jenis Pasien</Label>
                    <br>
                    <a href="?jenispasien=JKN"
                        class="btn {{ $request->jenispasien == 'JKN' ? 'btn-success' : 'btn-secondary' }} ">Pasien BPJS </a>
                    <a href="?jenispasien=NON-JKN"
                        class="btn {{ $request->jenispasien == 'NON-JKN' ? 'btn-success' : 'btn-secondary' }} ">Pasien UMUM
                    </a>
                    <br>
                    <br>
                    <x-adminlte-input-date name="idDisabled" label="Tanggal Periksa" value="{{ now()->format('Y-m-d') }}"
                        :config="$config" />


                    @if ($request->jenispasien == 'JKN')
                        <x-adminlte-select name="jeniskunjungan" label="Jenis Kunjungan">
                            <option selected disabled>Pilih Jenis Kunjungan</option>
                            <option value="1">Rujukan FKTP</option>
                            <option value="4">Rujukan Antar RS</option>
                            <option value="3">Surat Kontrol</option>
                        </x-adminlte-select>

                        <x-adminlte-input name="nomorreferensi" placeholder="Masukan Nomor Surat" label="Nomor Surat">
                            <x-slot name="appendSlot">
                                <div class="input-group">
                                    <a href="#" class="btn btn-warning">
                                        <i class="fas fa-search"></i> Cek Nomor
                                    </a>
                                </div>
                            </x-slot>


                        </x-adminlte-input>
                    @else
                    @endif


                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)

{{-- @section('js')
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

                    var urlLanjutFarmasiRacikan = "{{ route('landingpage') }}" +
                        "/poliklinik/lanjut_farmasi_racikan/" + data
                        .kodebooking;
                    $("#lanjutFarmasiRacikan").attr("href", urlLanjutFarmasiRacikan);

                    var urlSelesaiPoliklinik = "{{ route('landingpage') }}" +
                        "/poliklinik/selesai_poliklinik/" + data
                        .kodebooking;
                    $("#selesaiPoliklinik").attr("href", urlSelesaiPoliklinik);
                    $('#modalPelayanan').modal('show');
                    $.LoadingOverlay("hide", true);
                })
            });
            $('.btnSuratKontrol').click(function() {
                $('#modalSuratKontrol').modal('show');
            });
        });
    </script>
@endsection --}}
