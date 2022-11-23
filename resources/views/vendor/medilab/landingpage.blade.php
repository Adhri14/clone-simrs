@extends('vendor.medilab.master')
@section('title', 'SIMRS Waled')
@section('content')
    <section id="why-us" class="why-us">
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
                                    <i class="bx bx-message-add"></i>
                                    <h4>Information</h4>
                                    <p>Menyajikan Informasi yang dibutuhkan untuk Internal dan Eksternal Rumah Sakit
                                    </p>
                                </div>
                            </div>
                            <div class="col-xl-4 d-flex align-items-stretch">
                                <div class="icon-box mt-4 mt-xl-0">
                                    <i class="bx bx-clinic"></i>
                                    <h4>Service</h4>
                                    <p>Pelayanan Kesehatan berbasis Digital Untuk Rumah Sakit</p>
                                </div>
                            </div>
                            <div class="col-xl-4 d-flex align-items-stretch">
                                <div class="icon-box mt-4 mt-xl-0">
                                    <i class="bx bx-line-chart"></i>
                                    <h4>Controlling</h4>
                                    <p>Memudahkan proses Pelaporan, Pemantauan, Pengendalian dan Evaluasi Rumah
                                        Sakit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="about">
        <div class="container-fluid">
            <div class="row">
                <div
                    class="col-xl-5 col-lg-6 video-box d-flex justify-content-center align-items-stretch position-relative">
                    <a href="https://www.youtube.com/watch?v=oRj04KcUmuU" class="glightbox play-btn mb-4"></a>

                </div>
                <div
                    class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5">
                    <h3>Pelayanan Online Pasien SIMRS Waled</h3>
                    <p>Esse voluptas cumque vel exercitationem. Reiciendis est hic accusamus. Non ipsam et sed
                        minima temporibus laudantium. Soluta voluptate sed facere corporis dolores excepturi. Libero
                        laboriosam sint et id nulla tenetur. Suscipit aut voluptate.</p>
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-fingerprint"></i></div>
                        <h4 class="title"><a href=""> Jadwal Dokter & Libur Rawat Jalan</a></h4>
                        <p class="description">PERATURAN SISTEM INFORMASI MANAJEMEN RUMAH SAKIT</p>
                    </div>
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-fingerprint"></i></div>
                        <h4 class="title"><a href="">Antrian Online Rawat Jalan</a></h4>
                        <p class="description">PERATURAN SISTEM INFORMASI MANAJEMEN RUMAH SAKIT</p>
                    </div>
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-fingerprint"></i></div>
                        <h4 class="title"><a href="">Administrasi Online Pasien</a></h4>
                        <p class="description">PERATURAN SISTEM INFORMASI MANAJEMEN RUMAH SAKIT</p>
                    </div>
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-gift"></i></div>
                        <h4 class="title"><a href="">Bed Monitoring Rawat Inap</a></h4>
                        <p class="description">At vero eos et accusamus et iusto odio dignissimos ducimus qui
                            blanditiis praesentium voluptatum deleniti atque</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="jadwalrawatjalan" class="faq section-bg">
        <div class="container">
            <div class="section-title">
                <h2>Jadwal Poliklinik Rawat Jalan</h2>
                <p>Dokter sewaktu waktu dapat membatalkan jadwal praktek dikarenakan cuti atau berhalangan. Silahkan
                    tetap dapatkan informasi terbaru jadwal dokter di web kami. <br>Terima kasih dan harap
                    maklum. Update terakhir {{ \Carbon\Carbon::now() }}</p>
            </div>
            <div class="faq-list">
                <ul>
                    @for ($i = 1; $i <= 6; $i++)
                        <li data-aos="fade-up" data-aos-delay="{{ $i * 100 }}"> <i
                                class="bx bx-calendar-exclamation icon-help"></i>
                            <a data-bs-toggle="collapse" data-bs-target="#faq-list-{{ $i }}" class="collapsed"
                                aria-expanded="false">
                                <b>
                                    {{ $jadwal->where('hari', $i)->first()->namahari }}
                                </b><i class="bx bx-chevron-down icon-show"></i><i
                                    class="bx bx-chevron-up icon-close"></i></a>
                            <div id="faq-list-{{ $i }}" class="collapse" data-bs-parent=".faq-list">
                                <table class="table table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th scope="col">POLIKLINIK</th>
                                            <th scope="col">DOKTER</th>
                                            <th scope="col">JADWAL</th>
                                            <th scope="col">KUOTA</th>
                                            <th scope="col">KET</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jadwal->where('hari', $i) as $item)
                                            <tr class="{{ $item->libur ? 'table-danger' : null }}">
                                                <td>{{ strtoupper($item->namasubspesialis) }}</td>
                                                <td>{{ strtoupper($item->namadokter) }}</td>
                                                <td>{{ $item->jadwal }}</td>
                                                <td>{{ $item->kapasitaspasien }}</td>
                                                <td>
                                                    @if ($item->libur)
                                                        Libur
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </li>
                    @endfor
                </ul>
            </div>
        </div>
    </section>
    <section id="services" class="services">
        <div class="container">
            <div class="section-title">
                <h2>Antrian Online Rawat Jalan</h2>
                <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit
                    sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias
                    ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div class="icon-box">
                        <div class="icon"><i class="fas fa-heartbeat"></i></div>
                        <h4><a href="">Pasien BPJS</a></h4>
                        <p>Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0">
                    <div class="icon-box">
                        <div class="icon"><i class="fas fa-pills"></i></div>
                        <h4><a href="">Pasien Umum</a></h4>
                        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0">
                    <div class="icon-box">
                        <div class="icon"><i class="fas fa-hospital-user"></i></div>
                        <h4><a href="">Pasien Asuransi Lainnya</a></h4>
                        <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="counts" class="counts">
        <div class="container">
            <div class="section-title">
                <h2>Bed Monitoring Rawat Inap</h2>
                <p>Cek.</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="count-box">
                        <i class="fas fa-user-md"></i>
                        <span data-purecounter-start="0" data-purecounter-end="85" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Doctors</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-5 mt-md-0">
                    <div class="count-box">
                        <i class="far fa-hospital"></i>
                        <span data-purecounter-start="0" data-purecounter-end="18" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Departments</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
                    <div class="count-box">
                        <i class="fas fa-flask"></i>
                        <span data-purecounter-start="0" data-purecounter-end="12" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Research Labs</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
                    <div class="count-box">
                        <i class="fas fa-award"></i>
                        <span data-purecounter-start="0" data-purecounter-end="150" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Awards</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-title">
                <h2>Contact</h2>
                <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit
                    sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias
                    ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
            </div>
        </div>

        <div>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2800.7142316722648!2d108.71889725135291!3d-6.913539570187568!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd5151423db71bc02!2sRSUD%20Waled%20Cirebon!5e0!3m2!1sen!2sid!4v1669169174657!5m2!1sen!2sid"
                style="border:0; width: 100%; height: 350px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-4">
                    <div class="info">
                        <div class="address">
                            <i class="bi bi-geo-alt"></i>
                            <h4>Location:</h4>
                            <p>Jl. Prabu Kiansantang No.4, Desa Waled Kota, Kec. Waled, Kabupaten Cirebon, Jawa Barat 45187
                            </p>
                        </div>
                        <div class="email">
                            <i class="bi bi-envelope"></i>
                            <h4>Email:</h4>
                            <p>brsud.waled@gmail.com</p>
                        </div>
                        <div class="phone">
                            <i class="bi bi-phone"></i>
                            <h4>Call:</h4>
                            <p>0898 3311 118 (Humas RSUD Waled)</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mt-5 mt-lg-0">
                    <form action="forms/contact.php" method="post" role="form" class="php-email-form">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <input type="text" name="name" class="form-control" id="name"
                                    placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6 form-group mt-3 mt-md-0">
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="Your Email" required>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="text" class="form-control" name="subject" id="subject"
                                placeholder="Subject" required>
                        </div>
                        <div class="form-group mt-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                        </div>
                        <div class="my-3">
                            <div class="loading">Loading</div>
                            <div class="error-message"></div>
                            <div class="sent-message">Your message has been sent. Thank you!</div>
                        </div>
                        <div class="text-center"><button type="submit">Send Message</button></div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')

@endsection

@section('css')
    <style>
        #hero {
            background: url("{{ asset('rs-bagus-min.png') }}") center no-repeat !important;
        }

        .about .video-box {
            background: url("{{ asset('rs-bagus-min.png') }}") no-repeat !important;
        }
    </style>
@endsection
