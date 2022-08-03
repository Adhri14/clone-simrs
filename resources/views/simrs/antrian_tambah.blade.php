@extends('adminlte::page')

@section('title', 'Tambah Antrian Online')

@section('content_header')
    <h1>Tambah Antrian Online</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Isilah Data Berikut Untuk Pendaftaran Antrian Online" theme="info"
                icon="fas fa-info-circle" collapsible maximizable>
                <form action="{{ route('antrian.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <x-adminlte-input name="nik" label="NIK" placeholder="Nomor Induk Kependudukan"
                                enable-old-support />
                        </div>
                        <div class="col-md-4">
                            <x-adminlte-input name="nohp" label="Nomor HP" placeholder="Nomor HP Yang Dapat Dihubungi"
                                enable-old-support />
                        </div>
                        <div class="col-md-4">
                            <x-adminlte-select id="jeniskunjungan" name="jeniskunjungan" label="Jenis Kunjungan"
                                enable-old-support>
                                <option disabled selected>PILIH JENIS KUNJUNGAN</option>
                                <option value="1">RUJUKAN FKTP</option>
                                <option value="3">KONTROL</option>
                                <option value="2">RUJUKAN INTERNAL</option>
                                <option value="4">RUJUKAN ANTAR RS</option>
                            </x-adminlte-select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="nomorkartu" label="Nomor Kartu BPJS" placeholder="Nomor Kartu BPJS"
                                enable-old-support>
                                <x-slot name="bottomSlot">
                                    <span class="text-sm text-danger">
                                        Masukan jika kunjungan anda menggunakan BPJS/JKN
                                    </span>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="nomorreferensi" label="Nomor Rujukan" placeholder="Nomor Rujukan"
                                enable-old-support>
                                <x-slot name="bottomSlot">
                                    <span class="text-sm text-danger">
                                        Masukan jika kunjungan anda menggunakan BPJS/JKN
                                    </span>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @php
                                $config = ['format' => 'YYYY-MM-DD'];
                            @endphp
                            <x-adminlte-input-date name="tanggalperiksa"
                                value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" label="Tanggal Periksa"
                                :config="$config" />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-select2 name="kodepoli" id="kodepoli" label="Poliklinik">
                                <option value="" disabled selected>PILIH POLIKLINIK</option>
                                @foreach ($poli as $item)
                                    @if ($item->kodepoli == $item->kodesubspesialis)
                                        <option value="{{ $item->kodepoli }}">{{ $item->kodepoli }} -
                                            {{ $item->namapoli }}
                                        </option>
                                    @endif
                                @endforeach
                                @foreach ($poli as $item)
                                    @if ($item->kodepoli != $item->kodesubspesialis)
                                        <option value="{{ $item->kodesubspesialis }}">{{ $item->kodesubspesialis }}
                                            -
                                            {{ $item->namasubspesialis }}
                                        </option>
                                    @endif
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                    <x-adminlte-select2 id="kodedokter" name="kodedokter" label="Jadwal Dokter" enable-old-support>
                    </x-adminlte-select2>
                    <x-adminlte-button label="Tambah" type="submit" theme="success" icon="fas fa-plus" />
                    <x-adminlte-button label="Reset" theme="danger" icon="fas fa-ban" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)

@section('js')
    <script>
        $(function() {
            $("#kodepoli").change(function() {
                var url = 'http://127.0.0.1:8000/api/antrian/ref/jadwal';
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        kodepoli: $("#kodepoli").val(),
                        tanggalperiksa: $("#tanggalperiksa").val(),
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.metadata.code != 200) {
                            $("#kodedokter").empty();
                            alert(
                                "Jadwal Dokter Poliklinik pada tanggal tersebut tidak tersedia"
                            );
                            return false;
                        } else {
                            $("#kodedokter").empty();
                            $.each(data.response, function(item) {
                                $('#kodedokter').append($('<option>', {
                                    value: data.response[item]
                                        .kodedokter,
                                    text: data.response[item].jadwal +
                                        ' - ' + data.response[item]
                                        .namadokter
                                }));
                            })
                        }
                    }
                });
            });
        });
    </script>
@endsection
