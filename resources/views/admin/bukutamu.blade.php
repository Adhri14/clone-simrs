@extends('vendor.medilab.master')

@section('title','Buku Tamu - SIMRS Waled')

@section('content')
<main id="main">
    <section class="about section-bg">
        <div class="container-fluid">
            <div class="section-title">
                <h2>Profil RSUD Waled</h2>
            </div>
            <div class="row">
                <div
                    class="col-xl-5 col-lg-6 video-box d-flex justify-content-center align-items-stretch position-relative">
                    <a href="https://www.youtube.com/watch?v=oRj04KcUmuU" class="glightbox play-btn mb-4"></a>
                </div>
                <div
                    class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5">
                    <h3 id="bukutamu"> Buku Tamu RSUD Waled</h3>
                    <p>Silahkan isi data berikut.</p>
                    @if ($errors->any())
                        <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-adminlte-alert>
                    @endif
                    <form action="" id="daftarTamu" method="post">
                        @csrf
                        <div class="col-md-10 form-group">
                            <label for="name"><b>Nama Tamu</b></label>
                            <input type="text" name="name" class="form-control" id="name"
                                placeholder="Masukan Nama Anda" value="{{ old('name') }}">
                            <div class="validate"></div>
                        </div>
                        <div class="col-md-10 form-group mt-3">
                            <label for="organisasi"><b>Organisasi</b></label>
                            <input type="text" class="form-control" name="organisasi" id="organisasi"
                                placeholder="Masukan Organisasi Anda" value="{{ old('organisasi') }}">
                            <div class="validate"></div>
                        </div>
                        <div class="col-md-10 form-group mt-3">
                            <label for="phone"><b>Nomor HP / Telepon</b></label>
                            <input type="tel" class="form-control" name="phone" id="phone"
                                placeholder="Masukan No HP / Telepon Anda" value="{{ old('phone') }}">
                            <div class="validate"></div>
                        </div>
                        <div class="col-md-10 form-group mt-3">
                            <label for="alamat"><b>Alamat</b></label>
                            <input type="text" class="form-control" name="alamat" id="alamat"
                                placeholder="Masukan Alamat Anda" value="{{ old('alamat') }}">
                            {{-- <select name="kabupaten" id="kabupaten" class="form-select">
                                <option value="">Select Department</option>
                                <option value="Department 1">Department 1</option>
                                <option value="Department 2">Department 2</option>
                                <option value="Department 3">Department 3</option>
                            </select> --}}
                            <div class="validate"></div>
                        </div>
                        <div class="col-md-10 form-group mt-3">
                            <label for="tujuan"><b>Tujuan Kunjungan</b></label>
                            <input type="text" class="form-control" name="tujuan" id="tujuan"
                                placeholder="Masukan Tujuan Kunjungan Anda" value="{{ old('tujuan') }}">
                            <div class="validate"></div>
                        </div>
                        <div class="mb-3 mt-3">
                            <div class="sent-message">
                                Dengan ini saya menyatakan bahwa data yang saya isi ini benar dan kedatangan saya
                                sesuai dengan maksud dan tujuan yang tercantum.
                                <br>
                                Cirebon, {{ \Carbon\Carbon::now()->format('d M Y') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name"><b>Tanda Tangan</b></label>
                            <br>
                            <!-- partial:index.partial.html -->
                            <div class="signature-component">
                                <canvas id="signature-pad" width="400" height="200"></canvas>
                                <div>
                                    <button id="save" type="submit" form="daftarTamu"
                                        class="btn btn-success mt-1">Submit</button>
                                    <span class="btn btn-danger mt-1" id="clear">Clear</span>
                                    <span id="showPointsToggle"></span>

                                </div>
                            </div>
                            <div class="validate"></div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section><!-- End About Section -->
</main>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('signature/dist/signature-style.css') }}">
    <style>
        #hero {
            background: url("{{ asset('rs-bagus-min.png') }}")  no-repeat !important;
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

