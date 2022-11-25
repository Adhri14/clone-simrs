@extends('vendor.medilab.display')
@section('title', 'Buku Tamu - SIMRS Waled')

@section('content')
    <section id="jadwalrawatjalan" class="faq section-bg">
        <div class="container">
            <div>
                <div class="row">
                    <div class="col-md-4">
                        <iframe width="100%" height="315"
                            src="https://www.youtube.com/embed/oRj04KcUmuU?autoplay=1&loop=1&mute=1&controls=0"
                            frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div class="col-md-8">
                        <div class="card ">
                            <div class="card-header ">Jadwal Operasi {{ now()->format('d M Y') }}</div>
                            <div class="card-body">
                                <table id="jadwaloperasiTable"
                                    class="table table-hover table-sm table-responsive table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">TANGGAL</th>
                                            <th scope="col">No Booking</th>
                                            <th scope="col">KAMAR</th>
                                            <th scope="col">RM</th>
                                            <th scope="col">PASIEN</th>
                                            <th scope="col">DOKTER</th>
                                            <th scope="col">KET</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jadwals as $item)
                                            <tr class="text-xs ">
                                                <td>{{ Carbon\Carbon::parse( $item->tanggal)->format('d M Y') }}</td>
                                                <td>{{ $item->no_book }}</td>
                                                <td>{{ $item->ruangan }}</td>
                                                <td>{{ strlen($item->nomor_rm) == 6 ? $item->nomor_rm : substr($item->nomor_rm, -6) }}
                                                </td>
                                                <td>{{ strtoupper($item->nama_pasien) }}</td>
                                                <td>{{ strtoupper($item->nama_dokter) }}</td>
                                                <td>Belum</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <a href="{{ route('jadwaloperasi_info') }}" class="btn btn-primary btn-sm mt-2">Info
                            Jadwal Operasi</a>
                        <a href="{{ route('jadwaloperasi_display') }}" class="btn btn-warning btn-sm mt-2">Reload</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
    <style>
        table {
            font-size: 11px !important;
        }
    </style>
@endsection
@section('js')
    @include('sweetalert::alert')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $('#jadwaloperasiTable').DataTable({
            paging: false,
            info: false,
            searching: false,
            order: [
                1, 'asc'
            ],
        });
    </script>
@endsection
