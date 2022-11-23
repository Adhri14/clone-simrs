@extends('vendor.medilab.master')

@section('title', 'Buku Tamu - SIMRS Waled')

@section('content')
    <main id="main">
        <section id="jadwalrawatjalan" class="faq section-bg">
            <div class="container">
                <div class="section-title">
                    <h2>Jadwal Operasi</h2>
                    <p>Dokter sewaktu waktu dapat membatalkan jadwal praktek dikarenakan cuti atau berhalangan. Silahkan
                        tetap dapatkan informasi terbaru jadwal dokter di web kami. <br>Terima kasih dan harap
                        maklum. Update terakhir {{ \Carbon\Carbon::now() }}</p>
                </div>
                <div class="faq-list">
                    <table class="table table-hover table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">NO RM</th>
                                <th scope="col">PASIEN</th>
                                <th scope="col">DOKTER</th>
                                <th scope="col">RUANGAN</th>
                                <th scope="col">TANGGAL / WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwals as $item)
                                <tr>
                                    <td>{{ $item->nomor_rm }}</td>
                                    <td>{{ strtoupper($item->nama_pasien) }}</td>
                                    <td>{{ strtoupper($item->nama_dokter) }}</td>
                                    <td>{{ $item->ruangan }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </section>
    </main>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('signature/dist/signature-style.css') }}">
    <style>
        #hero {
            background: url("{{ asset('rs-bagus-min.png') }}") no-repeat !important;
        }

        .about .video-box {
            background: url("{{ asset('rs-bagus-min.png') }}") no-repeat !important;
        }

        #footer {
            background: white !important;
        }
    </style>

@endsection

@section('js')
    <script src="{{ asset('signature/dist/underscore-min.js') }}"></script>
    <script src="{{ asset('signature/dist/signature-script.js') }}"></script>
    @include('sweetalert::alert')

@endsection
