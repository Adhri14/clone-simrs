@extends('adminlte::page')

@section('title', 'Laporan Kunjungan Poliklinik')

@section('content_header')
    <h1>Laporan Kunjungan Poliklinik</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Kunjungan" theme="secondary" collapsible>
                <form action="" method="get">
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
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->KDPOLI }}"
                                            {{ $item->KDPOLI == $request->kodepoli ? 'selected' : null }}>
                                            {{ $item->KDPOLI }}
                                            -
                                            {{ $item->nama_unit }}
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
                    </div>
                    <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Submit Antrian" />
                </form>
            </x-adminlte-card>
            @if (isset($kunjungans))
                <div class="row">
                    <div class="col-md-12">
                        <x-adminlte-card title="Kunjungan Poliklinik ({{ $kunjungans->count() }} Orang)" theme="primary"
                            icon="fas fa-info-circle" collapsible>
                            @php
                                \Carbon\Carbon::setlocale(LC_ALL, 'IND');
                                $message =
                                    'Poliklinik : ' .
                                    $request->kodepoli .
                                    '-' .
                                    $unit->firstWhere('KDPOLI', $request->kodepoli)->nama_unit .
                                    '<br>Tanggal : ' .
                                    \Carbon\Carbon::parse($request->tanggal)
                                        ->locale('id')
                                        ->isoFormat('dddd D MMMM YYYY') .
                                    '<br>User : ' .
                                    Auth::user()->name;
                                $heads = ['No', 'No RM ', ' Nama', 'L', 'P', 'Umur', 'Alamat', 'Baru', 'Lama', 'Umum', 'JKN', 'Cara ', 'Diagnosa', 'Tindakan'];
                                $config = [
                                    // 'order' => ['2', 'desc'],
                                    'paging' => false,
                                    'buttons' => [
                                        [
                                            'extend' => 'colvis',
                                            'className' => 'btn-info',
                                        ],
                                        [
                                            'extend' => 'print',
                                            'className' => 'btn-info',
                                            'messageTop' => $message,
                                            'orientation' => 'landscape',
                                            'columns' => ':not(.select-checkbox)',
                                            'footer' => true,
                                        ],
                                        [
                                            'extend' => 'pdf',
                                            'className' => 'btn-info',
                                        ],
                                    ],
                                ];
                            @endphp
                            <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" :config="$config"
                                striped bordered hoverable compressed with-buttons>
                                @foreach ($kunjungans as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->no_rm }}</td>
                                        <td>{{ $item->pasien->nama_px }}</td>
                                        <td>{{ $item->pasien->jenis_kelamin == 'L' ? 'L' : '-' }}</td>
                                        <td>{{ $item->pasien->jenis_kelamin == 'P' ? 'P' : '-' }}</td>
                                        <td></td>
                                        <td>{{ $item->pasien->kecamatans->nama_kecamatan }}</td>
                                        <td>{{ $item->counter == 1 ? 'Baru' : '-' }}</td>
                                        <td>{{ $item->counter == 1 ? '-' : 'Lama' }}</td>
                                        <td>{{ $item->no_sep ? '-' : 'UMUM' }}</td>
                                        <td>{{ $item->no_sep ? 'JKN' : '-' }}</td>
                                        <td>{{ $item->penjamin ? $item->penjamin->nama_penjamin_bpjs : 'MANDIRI' }}</td>
                                        <td>{{ $item->diagnosapoli->diag_00 }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th colspan="4"> Total</th>
                                        <th></th>
                                        <th></th>
                                        <th>{{ $kunjungans->where('counter', 1)->count() }}</th>
                                        <th>{{ $kunjungans->where('counter', '!=', 1)->count() }}</th>
                                        <th>{{ $kunjungans->where('no_sep', '==', null)->count() }}</th>
                                        <th>{{ $kunjungans->where('no_sep', '!=', null)->count() }}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </x-adminlte-datatable>
                            Warna teks hijau adalah kunjungan yang telah dibuatkan surat kontrol.
                        </x-adminlte-card>

                        <x-adminlte-card title="Kunjungan Poliklinik ({{ $kunjungans->count() }} Orang)" theme="primary"
                            icon="fas fa-info-circle" collapsible>
                            @php
                                \Carbon\Carbon::setlocale(LC_ALL, 'IND');
                                $message =
                                    'Poliklinik : ' .
                                    $request->kodepoli .
                                    '-' .
                                    $unit->firstWhere('KDPOLI', $request->kodepoli)->nama_unit .
                                    '<br>Tanggal : ' .
                                    \Carbon\Carbon::parse($request->tanggal)
                                        ->locale('id')
                                        ->isoFormat('dddd D MMMM YYYY') .
                                    '<br>User : ' .
                                    Auth::user()->name;
                                $heads = ['No', 'No RM ', ' Nama', 'L', 'P', 'Umur', 'Alamat', 'Baru', 'Lama', 'Umum', 'JKN', 'Cara ', 'Diagnosa', 'Tindakan'];
                                $config = [
                                    // 'order' => ['2', 'desc'],
                                    'paging' => false,
                                    'buttons' => [
                                        [
                                            'extend' => 'colvis',
                                            'className' => 'btn-info',
                                        ],
                                        [
                                            'extend' => 'print',
                                            'className' => 'btn-info',
                                            'messageTop' => $message,
                                            'orientation' => 'landscape',
                                            'columns' => ':not(.select-checkbox)',
                                            'footer' => true,
                                        ],
                                        [
                                            'extend' => 'pdf',
                                            'className' => 'btn-info',
                                        ],
                                    ],
                                ];
                            @endphp
                            <x-adminlte-datatable id="table1" class="nowrap text-xs" :heads="$heads" :config="$config"
                                striped bordered hoverable compressed with-buttons>
                                @foreach ($kunjungans as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->no_rm }}</td>
                                        <td>{{ $item->pasien->nama_px }}</td>
                                        <td>{{ $item->pasien->jenis_kelamin == 'L' ? 'L' : '-' }}</td>
                                        <td>{{ $item->pasien->jenis_kelamin == 'P' ? 'P' : '-' }}</td>
                                        <td></td>
                                        <td>{{ $item->pasien->kecamatans->nama_kecamatan }}</td>
                                        <td>{{ $item->counter == 1 ? 'Baru' : '-' }}</td>
                                        <td>{{ $item->counter == 1 ? '-' : 'Lama' }}</td>
                                        <td>{{ $item->no_sep ? '-' : 'UMUM' }}</td>
                                        <td>{{ $item->no_sep ? 'JKN' : '-' }}</td>
                                        <td>{{ $item->penjamin ? $item->penjamin->nama_penjamin_bpjs : 'MANDIRI' }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th colspan="4"> Total</th>
                                        <th></th>
                                        <th></th>
                                        <th>{{ $kunjungans->where('counter', 1)->count() }}</th>
                                        <th>{{ $kunjungans->where('counter', '!=', 1)->count() }}</th>
                                        <th>{{ $kunjungans->where('no_sep', '==', null)->count() }}</th>
                                        <th>{{ $kunjungans->where('no_sep', '!=', null)->count() }}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </x-adminlte-datatable>
                            Warna teks hijau adalah kunjungan yang telah dibuatkan surat kontrol.
                        </x-adminlte-card>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.TempusDominusBs4', true)
