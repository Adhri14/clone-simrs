@extends('adminlte::page')
@section('title', 'Capaian Antrian')
@section('content_header')
    <h1>Capaian Antrian</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Capaian Antrain" theme="primary" icon="fas fa-info-circle" collapsible>

                @php
                    $heads = ['Bulan', 'Kunjungan', 'Antrol', 'Anrol Selesai (QR)', 'Whatsapp', 'MJKN', 'Offline'];
                    // $config['order'] = ['2', 'asc'];
                @endphp
                <x-adminlte-datatable id="table2" class="nowrap text-xs" :heads="$heads" bordered hoverable compressed with-buttons >
                    @foreach ($antrian_nobatal as $item)
                        <tr>
                            <td>{{ $item->bulan }}</td>
                            <td>
                                {{ $kunjungans->where('bulan', $item->bulan)->first() ? $kunjungans->where('bulan', $item->bulan)->first()->total : '0' }}
                            </td>
                            <td>{{ $item->total }}
                                ({{ $kunjungans->where('bulan', $item->bulan)->first() ? number_format(($item->total / $kunjungans->where('bulan', $item->bulan)->first()->total) * 100, 2) . ' %' : '0' }})
                            </td>
                            <td>
                                {{ $antrian_selesai->where('bulan', $item->bulan)->first() ? $antrian_selesai->where('bulan', $item->bulan)->first()->total : '0' }}
                                ({{ $antrian_selesai->where('bulan', $item->bulan)->first() ? number_format(($antrian_selesai->where('bulan', $item->bulan)->first()->total / $item->total) * 100, 2) . ' %' : '0' }})
                            </td>
                            <td>
                                {{ $antrian_whatsapp->where('bulan', $item->bulan)->first() ? $antrian_whatsapp->where('bulan', $item->bulan)->first()->total : '0' }}
                                ({{ $antrian_whatsapp->where('bulan', $item->bulan)->first() ? number_format(($antrian_whatsapp->where('bulan', $item->bulan)->first()->total / $item->total) * 100, 2) . ' %' : '0' }})
                            </td>
                            <td>
                                {{ $antrian_jkn->where('bulan', $item->bulan)->first() ? $antrian_jkn->where('bulan', $item->bulan)->first()->total : '0' }}
                                ({{ $antrian_jkn->where('bulan', $item->bulan)->first() ? number_format(($antrian_jkn->where('bulan', $item->bulan)->first()->total / $item->total) * 100, 2) . ' %' : '0' }})
                            </td>
                            <td>
                                {{ $antrian_lainnya->where('bulan', $item->bulan)->first() ? $antrian_lainnya->where('bulan', $item->bulan)->first()->total : '0' }}
                            </td>

                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                {{-- <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Bulan</th>
                            <th scope="col">Total Kunjungan</th>
                            <th scope="col">Total Anrol</th>
                            <th scope="col">Total Selesai</th>
                            <th scope="col">Total Whatsapp</th>
                            <th scope="col">Total MJKN</th>
                            <th scope="col">Total Offline</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table> --}}
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)
@section('plugins.TempusDominusBs4', true)
