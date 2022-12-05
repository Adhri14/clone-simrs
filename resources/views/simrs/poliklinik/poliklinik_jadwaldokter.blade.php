@extends('adminlte::page')

@section('title', 'Jadwal Dokter Poliklinik')
@section('content_header')
    <h1>Jadwal Dokter Poliklinik</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Filter Data Kunjungan" theme="secondary" collapsible>
                <form action="" method="get">
                    <x-adminlte-select2 name="kodepoli" label="Poliklinik">
                        @foreach ($unit as $item)
                            <option value="{{ $item->KDPOLI }}"
                                {{ $item->KDPOLI == $request->kodepoli ? 'selected' : null }}>
                                {{ $item->nama_unit }} ({{ $item->KDPOLI }})
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                </form>
            </x-adminlte-card>
        </div>
        @if (isset($jadwaldokter))
            <div class="col-md-12">
                <x-adminlte-card title="Jadwal Dokter Poliklinik" theme="primary" icon="fas fa-info-circle" collapsible>
                    @php
                        $heads = ['Hari', 'Dokter', 'Jam Praktek', 'Kapasitas', 'Action'];
                        $config['paging'] = false;
                        $config['info'] = false;
                        $config['scrollY'] = '400px';
                        $config['scrollX'] = true;
                        $config['scrollCollapse'] = true;
                    @endphp
                    <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" :config="$config" bordered
                        hoverable compressed>
                        @foreach ($jadwaldokter as $item)
                            <tr class="{{ $item->libur ? 'table-danger' : null }} ">
                                <td>{{ $item->hari }}. {{ $item->namahari }}</td>
                                <td>{{ $item->namadokter }} ({{ $item->kodedokter }})</td>
                                <td>{{ $item->jadwal }} </td>
                                <td>{{ $item->kapasitaspasien }} </td>
                                <td>
                                    <x-adminlte-button label="Edit" class="btn-xs mb-1 btnEdit" icon='fas fa-edit'
                                        theme="warning" data-toggle="tooltip" title="Edit Jadwal Dokter"
                                        data-id="{{ $item->id }}" />
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                    Catatan : <br>
                    *Kolom berwarna merah menandakan jadwal diliburkan
                </x-adminlte-card>
            </div>
        @endif
    </div>
    <x-adminlte-modal id="modalJadwal" title="Jadwal Praktek" theme="warning" icon="fas fa-calendar-alt">
        <form name="formUpdateJadwal" id="formUpdateJadwal" action="{{ route('jadwaldokter.store') }}" method="POST">
            @csrf
            <input type="hidden" name="method" value="UPDATE">
            <input type="hidden" class="idjadwal" name="idjadwal" id="idjadwal">
            <label id="labeljadwal">Jadwal ID : 1</label>
            <x-adminlte-select2 name="kodedokter" id="kodedokter" label="Dokter">
                @foreach ($dokters as $item)
                    <option value="{{ $item->kodedokter }}">{{ $item->kodedokter }} {{ $item->namadokter }}
                    </option>
                @endforeach
            </x-adminlte-select2>
            <x-adminlte-select2 name="kodesubspesialis" id="kodesubspesialis" label="Poliklinik">
                @foreach ($unit as $item)
                    <option value="{{ $item->KDPOLI }}">
                        {{ $item->nama_unit }} ({{ $item->KDPOLI }})
                    </option>
                @endforeach
            </x-adminlte-select2>
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select2 name="hari" id="hari" label="Hari">
                        <option value="1">SENIN</option>
                        <option value="2">SELASA</option>
                        <option value="3">RABU</option>
                        <option value="4">KAMIS</option>
                        <option value="5">JUMAT</option>
                        <option value="6">SABTU</option>
                        <option value="0">MINGGU</option>
                    </x-adminlte-select2>
                </div>
                <div class="col-md-6">
                    <x-adminlte-input name="jadwal" label="Jadwal Praktek" placeholder="Jadwal Praktek"
                        enable-old-support />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input name="kapasitaspasien" label="Kapasitas Pasien" placeholder="Kapasitas Pasien"
                        enable-old-support />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input-switch name="libur" label="Libur" data-on-text="YES" data-off-text="NO"
                        data-on-color="primary" />
                </div>
            </div>

        </form>
        <form name="formDeleteJadwal" id="formDeleteJadwal" action="{{ route('jadwaldokter.store') }}" method="POST">
            @csrf
            <input type="hidden" name="method" value="DELETE">
            <input type="hidden" class="idjadwal" name="idjadwal" id="idjadwal">
        </form>

        <x-slot name="footerSlot">
            <x-adminlte-button label="Update" form="formUpdateJadwal" class="mr-auto withLoad" type="submit"
                theme="success" icon="fas fa-edit" />
            <x-adminlte-button theme="danger" icon="fas fa-times" label="Close" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop
@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.BootstrapSwitch', true)

@section('js')
    <script>
        $(function() {
            $('.btnEdit').click(function() {
                var jadwalid = $(this).data('id');
                $.LoadingOverlay("show");
                $.get("{{ route('jadwaldokter.index') }}" + '/' + jadwalid + '/edit', function(data) {
                    console.log(data);
                    $('#kodesubspesialis').val(data.kodesubspesialis).trigger('change');
                    $('#kodedokter').val(data.kodedokter).trigger('change');
                    $('#hari').val(data.hari).trigger('change');
                    $('#kapasitaspasien').val(data.kapasitaspasien);
                    $('#labeljadwal').html("Jadwal ID : " + data.id);
                    $('#jadwal').val(data.jadwal);
                    $('.idjadwal').val(data.id);
                    if (data.libur == 1) {
                        $('#libur').prop('checked', true).trigger('change');
                    }
                    $.LoadingOverlay("hide", true);
                    $('#modalJadwal').modal('show');
                })

            });
        });
    </script>
@endsection
