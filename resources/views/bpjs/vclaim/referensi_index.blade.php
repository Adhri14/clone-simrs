@extends('adminlte::page')
@section('title', 'Referensi - Vclaim BPJS')
@section('content_header')
    <h1>Referensi - Vclaim BPJS </h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Klaim BPJS" theme="secondary" collapsible>
                <form action="" method="get">
                    <x-adminlte-select2 name="diagnosa" label="Diagnosa BPJS ICD-10">
                        <option selected disabled>Cari Diagnosa</option>
                    </x-adminlte-select2>
                    <x-adminlte-select name="jenisfaskes" label="Jenis Faskes BPJS">
                        <option value="1">Faskes Tingkat 1</option>
                        <option value="2">Faskes / RS</option>
                    </x-adminlte-select>
                    <x-adminlte-select2 name="faskes" label="Faskes BPJS">
                        <option selected disabled>Cari Faskes</option>
                    </x-adminlte-select2>
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tanggal" label="Tanggal Periksa" :config="$config"
                        value="{{ \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-primary">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                    <x-adminlte-select name="jenispelayanan" label="Jenis Pelayanan">
                        <option value="2">Rawat Jalan</option>
                        <option value="1">Rawat Inap</option>
                    </x-adminlte-select>
                    <x-adminlte-select2 name="poliklinik" label="Poliklinik BPJS">
                        <option selected disabled>Cari Poliklinik</option>
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="dokter" label="Dokter DPJP">
                        <option selected disabled>Cari Poliklinik</option>
                    </x-adminlte-select2>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Data Kunjungan" />
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.DateRangePicker', true)

@section('js')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $("#diagnosa").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('bpjs.vclaim.ref_diagnosa_api') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            diagnosa: params.term // search term
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
            $("#poliklinik").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('bpjs.vclaim.ref_poliklinik_api') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            poliklinik: params.term // search term
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
            $("#faskes").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('bpjs.vclaim.ref_faskes_api') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            jenisfaskes: $("#jenisfaskes option:selected").val(),
                            nama: params.term // search term
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
            $("#dokter").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('bpjs.vclaim.ref_dpjp_api') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            jenispelayanan: $("#jenispelayanan option:selected").val(),
                            kodespesialis: $("#poliklinik option:selected").val(),
                            tanggal: $("#tanggal").val(),
                            nama: params.term // search term
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
