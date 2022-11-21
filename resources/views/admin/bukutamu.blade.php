<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Buku Tamu - SIM RSUD Waled</title>
    <meta content="" name="Aplikasi pengisian buku tamu">
    <meta content="" name="buku tamu">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/logo rs waled ico.png') }}" rel="icon">
    <link href="{{ asset('assets/img/logo rs waled ico.png') }}" rel="apple-touch-icon">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('signature/dist/signature-style.css') }}">


    <style>
        #hero {
            /* background-size: cover; */
            /* height: 75ex; */
            background: url("{{ asset('rs-bagus-min.png') }}") center no-repeat !important;
        }

        .about .video-box {
            /* background-size: cover; */
            /* height: 75ex; */
            background: url("{{ asset('rs-bagus-min.png') }}") no-repeat !important;
        }

        #footer {
            background: white !important;
        }
    </style>
</head>

<body>
    <!-- ======= Top Bar ======= -->
    <div id="topbar" class="d-flex align-items-center fixed-top">
        <div class="container d-flex justify-content-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-envelope"></i> <a href="mailto:contact@example.com">it.support@rsudwaled.id</a>
                <i class="bi bi-phone"></i> 0895 2990 9036
            </div>
            <div class="d-none d-lg-flex social-links align-items-center">
                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></i></a>
            </div>
        </div>
    </div>
    <header id="header" class="fixed-top">
        <div class="container d-flex align-items-center">
            <a href="/" class="logo m-2"><img src="{{ asset('assets/img/logo rs waled ico.png') }}"
                    alt="" class="img-fluid"></a>
            <h1 class="logo me-auto"><a href="/">RSUD Waled</a></h1>
            <nav id="navbar" class="navbar order-last order-lg-0">
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
            <a href="#about" class="appointment-btn scrollto"><span class="d-none d-md-inline"></span>
                Buku Tamu
            </a>
        </div>
    </header>
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <h1><b>RSUD Waled</b></h1>
            <h2><b>Melayani Sepenuh Hati</b></h2>
            <h2><b>Rumah Sakit Umum Daerah<br>Kecamatan Waled Kabupaten Cirebon</b></h2>
            <a href="#about" class="btn-get-started scrollto">Isi Buku Tamu</a>
        </div>
    </section><!-- End Hero -->
    <main id="main">
        <!-- ======= Why Us Section ======= -->
        {{-- <section id="why-us" class="why-us">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 d-flex align-items-stretch">
                        <div class="content">
                            <h3>Apa sih SIM RSUD Waled ?</h3>
                            <p>
                                SIMRS adalah suatu sistem teknologi informasi komunikasi yang
                                memproses dan mengintegrasikan seluruh alur proses pelayanan
                                Rumah Sakit dalam bentuk jaringan koordinasi, pelaporan dan
                                prosedur administrasi untuk memperoleh informasi secara tepat dan
                                akurat, dan merupakan bagian dari Sistem Informasi Kesehatan.
                            </p>
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="more-btn">
                                    @guest
                                        Login
                                    @else
                                        Dashboard
                                    @endguest
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 d-flex align-items-stretch">
                        <div class="icon-boxes d-flex flex-column justify-content-center">
                            <div class="row">
                                <div class="col-xl-4 d-flex align-items-stretch">
                                    <div class="icon-box mt-4 mt-xl-0">
                                        <i class="bx bx-receipt"></i>
                                        <h4>Information</h4>
                                        <p>Menyajikan Informasi yang dibutuhkan untuk Internal dan Eksternal Rumah Sakit
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xl-4 d-flex align-items-stretch">
                                    <div class="icon-box mt-4 mt-xl-0">
                                        <i class="bx bx-cube-alt"></i>
                                        <h4>Service</h4>
                                        <p>Pelayanan Kesehatan berbasis Digital Untuk Rumah Sakit</p>
                                    </div>
                                </div>
                                <div class="col-xl-4 d-flex align-items-stretch">
                                    <div class="icon-box mt-4 mt-xl-0">
                                        <i class="bx bx-images"></i>
                                        <h4>Controlling</h4>
                                        <p>Memudahkan proses Pelaporan, Pemantauan, Pengendalian dan Evaluasi Rumah
                                            Sakit</p>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End .content-->
                    </div>
                </div>

            </div>
        </section> --}}
        <!-- End Why Us Section -->
        <!-- ======= About Section ======= -->
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
                        <h3 id="about"> Buku Tamu RSUD Waled</h3>
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
    </main><!-- End #main -->
    <footer id="footer">
        {{-- <div class="footer-top">
            <div class="container">
                <div class="row">

                    <div class="col-lg-3 col-md-6 footer-contact">
                        <h3>RSUD Waled</h3>
                        <p>
                            Jl. Prabu Kiansantang No.4 <br>
                            Kecamatan Waled Kabupaten Cirebon<br>
                            Jawa Barat 45187 <br><br>
                            <strong>Phone:</strong> +1 5589 55488 55<br>
                            <strong>Email:</strong> info@example.com<br>
                        </p>
                    </div>

                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>Services Integration</h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Vclaim & Antrian BPJS</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Satu Sehat Kemenkes</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-6 footer-newsletter">
                        <h4>Join Our Newsletter</h4>
                        <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
                        <form action="" method="post">
                            <input type="email" name="email"><input type="submit" value="Subscribe">
                        </form>
                    </div>

                </div>
            </div>
        </div> --}}

        <div class="container d-md-flex py-4">
            <div class="me-md-auto text-center text-md-start">
                <div class="copyright">
                    &copy; Copyright <strong><span>SIM RSUD Waled</span></strong>. All Rights Reserved
                </div>
                <div class="cblackits">
                    <!-- All the links in the footer should remain intact. -->
                    <!-- You can delete the links only if you purchased the pro version. -->
                    <!-- Licensing information: https://bootstrapmade.com/license/ -->
                    <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/medilab-free-medical-bootstrap-theme/ -->
                    Designed by <a href="#">TIM IT RSUD Waled</a>
                </div>
            </div>
            <div class="social-links text-center text-md-right pt-3 pt-md-0">
                <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
                <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
                <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
            </div>
        </div>
    </footer>
    <div id="preloader"></div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor/purecounter/purecounter.js') }}"></script> --}}
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('signature/dist/underscore-min.js') }}"></script>
    <script src="{{ asset('signature/dist/signature-script.js') }}"></script>
    @include('sweetalert::alert')
</body>

</html>
