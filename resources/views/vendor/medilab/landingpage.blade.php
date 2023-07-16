@extends('vendor.medilab.master')

@section('title', 'SIRAMAH-RS Waled')

@section('content')
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <h1>RSUD Lamaddukelleng</h1>
            <h2>WEB Company Profile RSUD Lamaddukelleng</h2>
            <a href="#about" class="btn-get-started scrollto">Daftar Online</a>
        </div>
    </section>
    <section id="why-us" class="why-us">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 d-flex align-items-stretch">
                    <div class="content">
                        <h3>Apa sih RSUD Lamaddukelleng ?</h3>
                        <p>
                            RSUD Lamaddukelleng adalah suatu sistem teknologi informasi komunikasi yang
                            memproses dan mengintegrasikan seluruh alur proses pelayanan
                            Rumah Sakit dalam bentuk jaringan koordinasi, pelaporan dan
                            prosedur administrasi untuk memperoleh informasi secara tepat dan
                            akurat, dan merupakan bagian dari Sistem Informasi Kesehatan.
                        </p>
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
        </div>
    </section>
    <section id="daftaronline" class="services">
        <div class="container">
            <div class="section-title">
                <h2>Antrian Online Rawat Jalan</h2>
                <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit
                    sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias
                    ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
            </div>
            <div class="row ">
                <div class="col-lg-6 col-md-6 d-flex align-items-stretch">
                    <div class="icon-box">
                        <div class="icon"><i class="fas fa-heartbeat"></i></div>
                        <h4><a href="">Pasien BPJS</a></h4>
                        <p>Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 d-flex align-items-stretch mt-4 mt-md-0">
                    <div class="icon-box">
                        <div class="icon"><i class="fas fa-pills"></i></div>
                        <h4><a href="">Pasien Umum</a></h4>
                        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>
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
    <section id="blog" class="blog">
        <div class="container">
            <div class="section-title">
                <h2>Blog</h2>
                <p>Informasi tentang Rumah Sakit Umum Daerah Lamaddukelleng akan sering kita update diblog ini.</p>
            </div>
            <div class="row justify-content-center">
                @foreach ($blogs as $item)
                    <div class="col-lg-4 mb-3">
                        <div class="card">
                            <img src="https://source.unsplash.com/1200x400?{{ $item->Category->name }}" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->title }}</h5>
                                <p class="card-text">{{ $item->excerpt }}</p>
                                <a href="{{ route('blog.show', $item->id) }}">Baca selengkapnya</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row mt-4 justify-content-center">
                <div class="col-lg-6 d-flex justify-content-center">
                    {{ $blogs->links() }}
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

        {{-- <div>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2800.7142316722648!2d108.71889725135291!3d-6.913539570187568!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd5151423db71bc02!2sRSUD%20Waled%20Cirebon!5e0!3m2!1sen!2sid!4v1669169174657!5m2!1sen!2sid"
                style="border:0; width: 100%; height: 350px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div> --}}
        {{-- <div>
            <iframe
                src="https://maps.google.com/maps?width=1000&amp;height=400&amp;hl=en&amp;q=rsud lamaddukeleng&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                style="border:0; width: 100%; height: 350px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div> --}}

        {{-- <div class="mapouter"><div class="gmap_canvas"><iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=1000&amp;height=400&amp;hl=en&amp;q=rsud lamaddukeleng&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a href="https://connectionsgame.org/">Connections Game</a></div><style>.mapouter{position:relative;text-align:right;width:600px;height:400px;}.gmap_canvas {overflow:hidden;background:none!important;width:100p%;height:400px;}.gmap_iframe {width:100%!important;height:400px!important;}</style></div> --}}

        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-4">
                    <div class="info">
                        <div class="address">
                            <i class="bi bi-geo-alt"></i>
                            <h4>Location:</h4>
                            <p>Jalan Kartika Candra Kirana No.9, Tempe, Wajo, Maddukelleng, Sengkang, Kabupaten Wajo, Sulawesi Selatan 90918
                            </p>
                        </div>
                        <div class="email">
                            <i class="bi bi-envelope"></i>
                            <h4>Email:</h4>
                            <p>admin.rsud@lamaddukelleng.com</p>
                        </div>
                        <div class="phone">
                            <i class="bi bi-phone"></i>
                            <h4>Call:</h4>
                            <p>0812 3456 7890 (Humas RSUD Lamaddukelleng)</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mt-5 mt-lg-0">
                    <div>
            <iframe
                src="https://maps.google.com/maps?width=1000&amp;height=400&amp;hl=en&amp;q=rsud lamaddukeleng&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                style="border:0; width: 100%; height: 350px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
                    {{-- <form action="forms/contact.php" method="post" role="form" class="php-email-form">
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
                    </form> --}}
                </div>
            </div>
        </div>
    </section>
@endsection
