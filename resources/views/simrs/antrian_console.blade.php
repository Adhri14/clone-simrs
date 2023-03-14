@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('title', 'Antrian QR Code')
@section('body')
    <div class="wrapper">
        <div class="row p-1">
            {{-- checkin --}}
            <div class="col-md-5">
                <x-adminlte-card title="Anjungan Checkin Antrian RSUD Waled" theme="primary" icon="fas fa-qrcode">
                    <div class="text-center">
                        <x-adminlte-input name="kodebooking" label="Silahkan scan QR Code Antrian atau masukan Kode Antrian"
                            placeholder="Masukan Kode Antrian untuk Checkin" igroup-size="lg">
                            <x-slot name="appendSlot">
                                <x-adminlte-button name="btn_checkin" id="btn_checkin" theme="success" label="Checkin!" />
                            </x-slot>
                            <x-slot name="prependSlot">
                                <div class="input-group-text text-success">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                        <i class="fas fa-qrcode fa-3x"></i>
                        <br>
                        <label>Status = <span id="status">-</span></label>
                    </div>
                </x-adminlte-card>
                <x-adminlte-card title="Informasi Antrian" theme="primary" icon="fas fa-user-injured">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>
                                    Antrian Lt 1
                                </h6>
                                <h4>
                                    {{ $antrian_terakhir1->angkaantrean }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>
                                    Antrian Lt 2
                                </h6>
                                <h4>
                                    {{ $antrian_terakhir2->angkaantrean }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>
                                    Antrian Lt Online
                                </h6>
                                <h4>
                                    {{ $antrian_terakhir3->angkaantrean }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>
                                    Antrian Total
                                </h6>
                                <h4>
                                    {{ $antrian_terakhir3->angkaantrean }}
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">

                        <div class="row">
                            <div class="col-md-6">
                                <x-adminlte-info-box class="btnDaftarBPJS" text="Daftar Pasien BPJS" theme="success" />
                            </div>
                            <div class="col-md-6">
                                <x-adminlte-info-box class="btnDaftarUmum" text="Daftar Pasien Umum" theme="success" />
                            </div>

                        </div>
                    </div>
                </x-adminlte-card>
                <div class="col-md-6">
                    <x-adminlte-button icon="fas fa-sync" class="withLoad reload" theme="warning" label="Reload" />
                    <a href="{{ route('antrian.cek_printer') }}" class="btn btn-warning"><i class="fas fa-print"></i> Test
                        Printer</a>
                </div>
            </div>
            {{-- ambil antrian offline --}}
            <div class="col-md-7">
                <p hidden>{{ setlocale(LC_ALL, 'IND') }}</p>
                <x-adminlte-card
                    title="Jadwal Dokter Poliklinik {{ \Carbon\Carbon::now()->formatLocalized('%A, %d %B %Y') }}"
                    theme="primary" icon="fas fa-calendar-alt">
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $heads = ['Poliklinik', 'Dokter', 'Jadwal', 'Lokasi', 'Daftar', 'Kuota', 'Antrian'];
                                $config['order'] = ['5', 'asc'];
                            @endphp
                            <x-adminlte-datatable id="table1" class="nowrap text-xs" :heads="$heads" :config="$config"
                                striped bordered hoverable compressed>
                                @foreach ($poliklinik as $poli)
                                    @foreach ($poli->jadwals->where('hari', \Carbon\Carbon::now()->dayOfWeek)->where('kodesubspesialis', $poli->kodesubspesialis) as $jadwal)
                                        <tr
                                            class="text-left
                                            {{ $jadwal->libur == 1 ||$poli->antrians->where('tanggalperiksa', \Carbon\Carbon::now()->format('Y-m-d'))->where('taskid', '!=', 99)->where('kodedokter', $jadwal->kodedokter)->count() >= $jadwal->kapasitaspasien? ' text-danger': ' text-black' }}
                                           ">
                                            <td> {{ strtoupper($jadwal->namasubspesialis) }}</td>
                                            <td> {{ $jadwal->namadokter }} {{ $jadwal->libur ? '(TUTUP)' : '' }}</td>
                                            <td> {{ $jadwal->jadwal }}</td>
                                            <td>Lantai {{ $poli->lokasi }} </td>
                                            <td>Lantai {{ $poli->lantaipendaftaran }} </td>
                                            <td> {{ $jadwal->kapasitaspasien }}</td>
                                            <td> {{ $poli->antrians->where('tanggalperiksa', \Carbon\Carbon::now()->format('Y-m-d'))->where('kodedokter', $jadwal->kodedokter)->where('taskid', '!=', 99)->count() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </x-adminlte-datatable>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
    {{-- Daftar Pasien BPJS --}}
    <x-adminlte-modal id="modalBPJS" size="xl" title="Daftar Antrian Pasien" theme="success" icon="fas fa-user-plus">
        {{-- <div id="inputKartu">
            <x-adminlte-input name="nomorkartu" id="nomorkartu" label="Masukan Nomor BPJS Pasien"
                placeholder="Masukan Nomor BPJS Peserta" igroup-size="lg">
                <x-slot name="prependSlot">
                    <div class="input-group-text text-success">
                        <i class="fas fa-user"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
        </div>
        <div id="inputNIK">
            <x-adminlte-input name="nik" id="nik" label="Masukan NIK Pasien" placeholder="Masukan NIK Peserta"
                igroup-size="lg">
                <x-slot name="prependSlot">
                    <div class="input-group-text text-success">
                        <i class="fas fa-user"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
        </div> --}}
        {{-- <br> --}}
        <div class="form-group">
            <label>Silahkan pilih poliklinik BPJS di bawah ini</label>
            <div class="row">
                @foreach ($poliklinik as $item)
                    <div class="col-md-4">
                        <div class="custom-control custom-radio " style="scale: 100%">
                            <input class="custom-control-input btnPoliBPJS" type="radio"
                                data-id="{{ $item->kodesubspesialis }}" id="{{ $item->namasubspesialis }}"
                                value="{{ $item->kodesubspesialis }}" name="kodesubspesialis">
                            <label for="{{ $item->namasubspesialis }}" class="custom-control-label"
                                data-id="{{ $item->kodesubspesialis }}">{{ $item->namasubspesialis }} </label>
                        </div>
                    </div>
                    {{-- <x-adminlte-button class="btnPoliBPJS btn-lg m-2" theme="warning" label="{{ $item->namasubspesialis }}"
                    data-id="{{ $item->kodesubspesialis }}" /> --}}
                    {{-- <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault"
                        id="{{ $item->namasubspesialis }}">
                    <label class="form-check-label" for="{{ $item->namasubspesialis }}">
                        Default radio
                    </label>
                </div> --}}
                @endforeach
            </div>
        </div>
        <br>
        <div class="form-group" id="daftarDokter">
            <label>Silahkan pilih dokter poliklinik di bawah ini</label>
            <div id="rowDokter"></div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button class="mr-auto withLoad" type="submit" theme="success" id="btnDaftarPoliBPJS"
                icon="fas fa-user-plus" label="Daftar BPJS" />
            <x-adminlte-button class="mr-auto withLoad" type="submit" theme="success" id="btnDaftarPoliUmum"
                icon="fas fa-user-plus" label="Daftar Umum" />
            <x-adminlte-button theme="secondary" icon="fas fa-times" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop
@section('plugins.Sweetalert2', true)*


@include('sweetalert::alert')
@section('adminlte_css')
    {{-- <script src="{{ asset('vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}"></script> --}}
@endsection
@section('adminlte_js')
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/loading-overlay/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('vendor/onscan.js/onscan.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    {{-- scan --}}
    {{-- <script>
        $(function() {
            onScan.attachTo(document, {
                onScan: function(sCode, iQty) {
                    $.LoadingOverlay("show", {
                        text: "Printing..."
                    });
                    var url = "{{ route('antrian.checkin_update') }}";
                    var formData = {
                        kodebooking: sCode,
                        waktu: "{{ \Carbon\Carbon::now()->timestamp * 1000 }}",
                    };
                    $('#kodebooking').val(sCode);
                    $.get(url, formData, function(data) {
                        console.log(data);
                        $.LoadingOverlay("hide");
                        if (data.metadata.code == 200) {
                            $('#status').html(data.metadata.message);
                            swal.fire(
                                'Sukses...',
                                data.metadata.message,
                                'success'
                            ).then(okay => {
                                if (okay) {
                                    $.LoadingOverlay("show", {
                                        text: "Reload..."
                                    });
                                    $('#status').html('-');
                                    location.reload();
                                }
                            });
                        } else {
                            $('#status').html(data.metadata.message);
                            swal.fire(
                                'Opss Error...',
                                data.metadata.message,
                                'error'
                            ).then(okay => {
                                if (okay) {
                                    $.LoadingOverlay("show", {
                                        text: "Reload..."
                                    });
                                    $('#status').html('-');
                                    location.reload();
                                }
                            });
                        }
                    });
                },
            });
        });
    </script> --}}
    {{-- btn chekin --}}
    <script>
        $(function() {
            $('#btn_checkin').click(function() {
                var kodebooking = $('#kodebooking').val();
                $.LoadingOverlay("show", {
                    text: "Printing..."
                });
                var url = "{{ route('antrian.checkin_update') }}";
                var formData = {
                    kodebooking: kodebooking,
                    waktu: "{{ \Carbon\Carbon::now()->timestamp * 1000 }}",
                };
                $('#kodebooking').val(kodebooking);
                $.get(url, formData, function(data) {
                    console.log(data);
                    $.LoadingOverlay("hide");
                    if (data.metadata.code == 200) {
                        $('#status').html(data.metadata.message);
                        swal.fire(
                            'Sukses...',
                            data.metadata.message,
                            'success'
                        ).then(okay => {
                            if (okay) {
                                $.LoadingOverlay("show", {
                                    text: "Reload..."
                                });
                                $('#status').html('-');
                                location.reload();
                            }
                        });
                    } else {
                        $('#status').html(data.metadata.message);
                        swal.fire(
                            'Opss Error...',
                            data.metadata.message,
                            'error'
                        ).then(okay => {
                            if (okay) {
                                $.LoadingOverlay("show", {
                                    text: "Reload..."
                                });
                                $('#status').html('-');
                                location.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>
    {{-- btn daftar --}}
    <script>
        $(function() {
            $(document).ready(function() {
                $('#daftarDokter').hide();
                setTimeout(function() {
                    $('#kodebooking').focus();
                }, 500);
            });
            $('.btnDaftarBPJS').click(function() {
                $('#modalBPJS').modal('show');
                $('#inputNIK').show();
                $('#btnDaftarPoliUmum').hide();
                $('#btnDaftarPoliBPJS').show();
                $('#inputKartu').show();
                setTimeout(function() {
                    $('#nomorkartu').focus();
                }, 500);

            });
            $('.btnDaftarUmum').click(function() {
                $('#modalBPJS').modal('show');
                $('#inputNIK').show();
                $('#inputKartu').hide();
                $('#btnDaftarPoliUmum').show();
                $('#btnDaftarPoliBPJS').hide();
                setTimeout(function() {
                    $('#nik').focus();
                }, 500);

            });
            $('.btnPoliBPJS').click(function() {
                $('div#rowDokter').children().remove();
                var id = $(this).data('id');
                var hari = "{{ now()->dayOfWeek }}"
                $.LoadingOverlay("show");
                var url = "{{ route('antrian.jadwaldokter_poli') }}/?kodesubspesialis=" + id + "&hari=" +
                    hari;
                $.get(url, function(data) {
                    $('#daftarDokter').show();
                    $.each(data, function(i, item) {
                        console.log(item.kodedokter);
                        var bigString = [
                            '<div class="custom-control custom-radio " >',
                            '<input class="custom-control-input btnPoliBPJS" type="radio"',
                            'data-id="' + item.kodedokter + '" id="' + item.kodedokter +
                            '"',
                            'value="' + item.kodedokter + '" name="kodedokter">',
                            '<label for="' + item.kodedokter +
                            '" class="custom-control-label"',
                            'data-id="' + item.kodedokter + '">' + item.namadokter +
                            ' </label>',
                            ' </div>',
                        ];
                        $('#rowDokter').append(bigString.join(''));
                    });
                    $.LoadingOverlay("hide", true);
                });
            });
            $('#btnDaftarPoliBPJS').click(function() {
                var kodesubspesialis = $("input[name=kodesubspesialis]:checked").val();
                var kodedokter = $("input[name=kodedokter]:checked").val();
                var url = "{{ route('antrian.daftar_pasien_bpjs_offline') }}" + "?kodesubspesialis=" +
                    kodesubspesialis + "&kodedokter=" + kodedokter;
                window.location.href = url;
            });
            $('#btnDaftarPoliUmum').click(function() {
                var kodesubspesialis = $("input[name=kodesubspesialis]:checked").val();
                var kodedokter = $("input[name=kodedokter]:checked").val();
                var url = "{{ route('antrian.daftar_pasien_umum_offline') }}" + "?kodesubspesialis=" +
                    kodesubspesialis + "&kodedokter=" + kodedokter;
                window.location.href = url;
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
        $('.reload').click(function() {
            location.reload();
        });
    </script>
@stop
