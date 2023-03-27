@extends('adminlte::page')

@section('title', 'Referensi Dokter')

@section('content_header')
    <h1>Referensi Dokter</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Data Dokter" theme="secondary" collapsible>
                @php
                    $heads = ['Kode SIMRS', 'Kode BPJS', 'Nama', 'SIP', 'Status', 'Action'];
                    $config['paging'] = false;
                    $config['info'] = false;
                    $config['scrollY'] = '400px';
                    $config['scrollCollapse'] = true;
                    $config['scrollX'] = true;
                @endphp
                <x-adminlte-datatable id="table1" :heads="$heads" :config="$config" hoverable bordered compressed>
                    @foreach ($paramedis as $item)
                        <tr>
                            <td>{{ $item->kode_paramedis }}</td>
                            <td>{{ $item->kode_dokter_jkn }}</td>
                            <td>{{ $item->nama_paramedis }}</td>
                            <td>{{ $item->sip_dr }}</td>
                            <td>
                                @if ($item->kode_dokter_jkn)
                                    @if ($dokter_bpjs->where('kodedokter', $item->kode_dokter_jkn)->first())
                                        <a href="#" class="btn btn-xs btn-success disabled"
                                            aria-disabled="true">Aktif</a>
                                    @else
                                        <a href="#" class="btn btn-xs btn-danger disabled" aria-disabled="true">Belum
                                            Aktif</a>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <x-adminlte-button class="btn-xs btnEdit" label="Edit" theme="warning" icon="fas fa-edit"
                                    data-toggle="tooltip" title="Edit Dokter {{ $item->nama_paramedis }}"
                                    data-id="{{ $item->kode_paramedis }}" />
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
            <x-adminlte-card title="Data Dokter BPJS" theme="info" icon="fas fa-info-circle" collapsible maximizable>
                @php
                    $heads = ['Kode Dokter', 'Nama Dokter', 'Status'];
                @endphp
                <x-adminlte-datatable id="table2" :heads="$heads" bordered hoverable compressed>
                    @foreach ($dokter_bpjs as $item)
                        <tr>
                            <td>{{ $item->kodedokter }}</td>
                            <td>{{ $item->namadokter }}</td>
                            <td>
                                @if ($paramedis->where('kode_dokter_jkn', $item->kodedokter)->first())
                                    <a href="#" class="btn btn-xs btn-secondary disabled" aria-disabled="true">Sudah
                                        Ada</a>
                                @else
                                    <a href="#" class="btn btn-xs btn-danger disabled" aria-disabled="true">Belum
                                        Ada</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <a href="{{ route('pelayananmedis.dokter.create') }}" class="btn btn-success">Refresh User Dokter</a>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modalEdit" title="Edit Dokter" theme="warning" icon="fas fa-user-plus">
        <form name="formInput" id="formInput" action="" method="post">
            @csrf
            <input type="hidden" name="antrianid" id="antrianid" value="">
            <x-adminlte-input name="kode_paramedis" placeholder="Kode Dokter" label="Kode Dokter" readonly />
            <x-adminlte-input name="kode_dokter_jkn" placeholder="Kode BPJS" label="Kode BPJS" />
            <x-adminlte-input name="nama_paramedis" placeholder="Nama Dokter" label="Nama Dokter" />
            <x-adminlte-input name="sip_dr" placeholder="SIP" label="SIP" />
            <x-slot name="footerSlot">
                {{-- <x-adminlte-button class="mr-auto btnSuratKontrol" label="Buat Surat Kontrol" theme="primary"
                    icon="fas fa-prescription-bottle-alt" />
                <a href="#" id="lanjutFarmasi" class="btn btn-success withLoad"> <i
                        class="fas fa-prescription-bottle-alt"></i>Farmasi Non-Racikan</a>
                <a href="#" id="lanjutFarmasiRacikan" class="btn btn-success withLoad"> <i
                        class="fas fa-prescription-bottle-alt"></i>Farmasi Racikan</a>
                <a href="#" id="selesaiPoliklinik" class="btn btn-warning withLoad"> <i class="fas fa-check"></i>
                    Selesai</a> --}}
                <x-adminlte-button theme="danger " label="Tutup" icon="fas fa-times" data-dismiss="modal" />
            </x-slot>
        </form>
    </x-adminlte-modal>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)

@section('js')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.btnEdit').click(function() {
                var id = $(this).data('id');
                $.LoadingOverlay("show");
                var url = "{{ route('pelayananmedis.dokter.index') }}/" + id;
                $.get(url, function(data) {
                    console.log(data);
                    $('#kode_paramedis').val(data.kode_paramedis);
                    $('#kode_dokter_jkn').val(data.kode_dokter_jkn);
                    $('#nama_paramedis').val(data.nama_paramedis);
                    $('#sip_dr').val(data.sip_dr);
                    $('#modalEdit').modal('show');
                })
                $.LoadingOverlay("hide", true);
            });
        });
    </script>
@endsection
