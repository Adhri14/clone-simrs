@extends('adminlte::page')
@section('title', 'Jadwal Dokter - Pelayanan Medis')
@section('content_header')
    <h1 class="m-0 text-dark">Jadwal Dokter - Pelayanan Medis</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-3">
            <x-adminlte-card title="Pencarian Jadwal Dokter HAFIS" theme="secondary" icon="fas fa-search" collapsible>
                <form action="">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tanggal" value="{{ $request->tanggal }}" placeholder="Silahkan Pilih Tanggal"
                        label="Tanggal Periksa" :config="$config" />
                    <x-adminlte-select2 name="kodePoli" id="kodePoli" label="Poliklinik">
                        @foreach ($polikliniks as $poli)
                            <option value="{{ $poli->kodeSubspesialis }}"
                                {{ $request->kodePoli == $poli->kodeSubspesialis ? 'selected' : null }}>
                                {{ $poli->namaSubspesialis }} ({{ $poli->kodeSubspesialis }})</option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-button label="Cari Jadwal Dokter" class="mr-auto withLoad" type="submit" theme="success"
                        icon="fas fa-search" />
                </form>
            </x-adminlte-card>
        </div>
        <div class="col-9">
            <x-adminlte-card title="Referensi Jadwal Dokter HAFIS" icon="fas fa-calendar-alt" theme="secondary" collapsible>
                @if ($errors->any())
                    <x-adminlte-alert theme="danger" title="Error Message!" dismissable>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </x-adminlte-alert>
                @endif

                @php
                    $heads = ['No.', 'Hari', 'Jadwal', 'Poliklinik', 'Subspesialis', 'Dokter', 'Kuota', 'Action'];
                @endphp
                <x-adminlte-datatable id="table2" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @isset($jadwals)
                        @foreach ($jadwals as $jadwal)
                            <tr class="{{ $jadwal->libur ? 'table-danger' : null }}  ">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $jadwal->namahari }} {{ $jadwal->libur ? 'LIBUR' : null }} </td>
                                <td>{{ $jadwal->jadwal }}</td>
                                <td>{{ $jadwal->namapoli }} ({{ $jadwal->kodepoli }})</td>
                                <td>{{ $jadwal->namasubspesialis }} ({{ $jadwal->kodesubspesialis }})</td>
                                <td>{{ $jadwal->namadokter }} ({{ $jadwal->kodedokter }})</td>
                                <td>{{ $jadwal->kapasitaspasien }}</td>
                                <td>
                                    @if ($jadwal_antrian->where('kodesubspesialis', $jadwal->kodesubspesialis)->where('kodedokter', $jadwal->kodedokter)->where('hari', $jadwal->hari)->first())
                                        <button class="btn btn-secondary btn-xs">Sudah Ada</button>
                                    @else
                                        <form action="{{ route('pelayanan-medis.jadwaldokter_add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="kodePoli" value="{{ $jadwal->kodepoli }}">
                                            <input type="hidden" name="namaPoli" value="{{ $jadwal->namapoli }}">
                                            <input type="hidden" name="kodeSubspesialis"
                                                value="{{ $jadwal->kodesubspesialis }}">
                                            <input type="hidden" name="namaSubspesialis"
                                                value="{{ $jadwal->namasubspesialis }}">
                                            <input type="hidden" name="kodeDokter" value="{{ $jadwal->kodedokter }}">
                                            <input type="hidden" name="namaDokter" value="{{ $jadwal->namadokter }}">
                                            <input type="hidden" name="hari" value="{{ $jadwal->hari }}">
                                            <input type="hidden" name="namaHari" value="{{ $jadwal->namahari }}">
                                            <input type="hidden" name="jadwal" value="{{ $jadwal->jadwal }}">
                                            <input type="hidden" name="kapasitasPasien"
                                                value="{{ $jadwal->kapasitaspasien }}">
                                            <input type="hidden" name="libur" value="{{ $jadwal->libur }}">
                                            <button type="submit" class="btn btn-success btn-xs">Tambah</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endisset
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
        <div class="col-12">
            <x-adminlte-card title="Data Jadwal Dokter SIMRS" theme="success" icon="fas fa-calendar-alt" collapsible>
                @php
                    $heads = ['Nama Poliklinik Subspesialis', 'Dokter', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $config['paging'] = false;
                @endphp
                <x-adminlte-datatable id="table1" class="nowrap text-xs" :heads="$heads" :config="$config" striped
                    bordered hoverable compressed>
                    @foreach ($jadwal_antrian->groupby('kodedokter') as $item)
                        <tr>
                            <td>
                                {{ strtoupper($item->first()->namasubspesialis) }}
                                ({{ $item->first()->kodesubspesialis }})
                            </td>
                            <td>{{ $item->first()->namadokter }} ({{ $item->first()->kodedokter }})</td>
                            @for ($i = 1; $i <= 6; $i++)
                                <td>
                                    @foreach ($item as $jadwal)
                                        @if ($jadwal->hari == $i)
                                            @if ($jadwal->libur == 1)
                                                <x-adminlte-button
                                                    label="{{ $jadwal->jadwal }} / {{ $jadwal->kapasitaspasien }}"
                                                    class="btn-xs mb-1 btnJadwal" theme="danger" data-toggle="tooltip"
                                                    title="Jadwal Dokter" data-id="{{ $jadwal->id }}" />
                                            @else
                                                <x-adminlte-button
                                                    label="{{ $jadwal->jadwal }} / {{ $jadwal->kapasitaspasien }}"
                                                    class="btn-xs mb-1 btnJadwal" theme="warning" data-toggle="tooltip"
                                                    title="Jadwal Dokter" data-id="{{ $jadwal->id }}" />
                                            @endif
                                        @endif
                                    @endforeach
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modalJadwal" title="Jadwal Praktek" theme="warning" icon="fas fa-calendar-alt">
        <form name="formUpdateJadwal" id="formUpdateJadwal" action="{{ route('pelayanan-medis.jadwaldokter_update') }}"
            method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="method" value="UPDATE">
            <input type="hidden" class="idjadwal" name="idjadwal" id="idjadwal">
            <label id="labeljadwal">Jadwal ID : 1</label>
            <x-adminlte-input name="namaSubspesialis" label="Poliklinik / Subspesialis"
                placeholder="Poliklinik / Subspesialis" readonly />
            <x-adminlte-input name="namaDokter" label="Dokter" placeholder="Dokter" readonly />
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="namaHari" label="Hari" placeholder="Hari" readonly />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input name="jadwal" label="Jadwal Praktek" placeholder="Jadwal Praktek" />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input name="kapasitasPasien" label="Kapasitas Pasien" placeholder="Kapasitas Pasien"
                        enable-old-support />
                </div>
                <div class="col-md-6">
                    <x-adminlte-input-switch name="libur" label="Libur" data-on-text="YES" data-off-text="NO"
                        data-on-color="primary" />
                </div>
            </div>
        </form>
        <form name="formDeleteJadwal" id="formDeleteJadwal" action="{{ route('pelayanan-medis.jadwaldokter_delete') }}"
            method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" class="idjadwal" name="idjadwal" id="idjadwal">
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button label="Update" form="formUpdateJadwal" class="mr-auto withLoad" type="submit"
                theme="success" icon="fas fa-edit" />
            <x-adminlte-button label="Hapus" form="formDeleteJadwal" class="withLoad" type="submit" theme="danger"
                icon="fas fa-trash-alt" />
            <x-adminlte-button theme="danger" icon="fas fa-times" label="Close" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Datatables', true)
@section('plugins.BootstrapSwitch', true)
@section('js')
    <script>
        $(function() {
            $('.btnJadwal').click(function() {
                var jadwalid = $(this).data('id');
                $.LoadingOverlay("show");
                $.get("{{ route('pelayanan-medis.jadwaldokter.index') }}" + '/' + jadwalid + '/get',
                    function(data) {
                        console.log(data);
                        $('#namaSubspesialis').val(data.namaSubspesialis);
                        $('#namaDokter').val(data.namaDokter);
                        $('#namaHari').val(data.namaHari);
                        $('#kapasitasPasien').val(data.kapasitasPasien);
                        $('#jadwal').val(data.jadwal);
                        $('#labeljadwal').html("Jadwal ID : " + data.id);
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
