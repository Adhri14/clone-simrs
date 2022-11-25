<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>
        @yield('title')
    </title>

    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="{{ asset('assets/img/logo rs waled ico.png') }}" rel="icon">
    <link href="{{ asset('assets/img/logo rs waled ico.png') }}" rel="apple-touch-icon">

    <link href="{{ asset('medilab/assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('medilab/assets/css/style.css') }}" rel="stylesheet">
    <style>
        #topbar {
            background: green !important;
        }

        #topbar .contact-info a {
            color: white;

        }

        #topbar .contact-info i {
            color: white;

        }

        #topbar .social-links a {
            color: white;

        }

        body #topbar {
            color: white;
        }
    </style>

    @yield('css')

</head>

<body class="section-bg">
    <div id="topbar" class="d-flex align-items-center fixed-top">
        <div class="container d-flex justify-content-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-envelope"></i> <a href="mailto:contact@example.com">brsud.waled@gmai.com</a>
                <i class="bi bi-phone"></i>0898 3311 118
            </div>
            <div class="d-none d-lg-flex social-links align-items-center">
                <a href="#" class="facebook"><i class="bi bi-whatsapp"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="youtube"><i class="bi bi-youtube"></i></i></a>
            </div>
        </div>
    </div>
    <main id="main" class="mh-100">
        @yield('content')
    </main>
    <div id="topbar" class="d-flex align-items-center fixed-bottom">
        <div class="container d-flex justify-content-between">
            <div class="contact-info d-flex align-items-center">
                <div class="copyright">
                    &copy; Copyright <strong><span>SIMRS Waled</span></strong>. All Rights Reserved. Designed by <a
                        href="https://github.com/marwandhiaurrahman/" target="_blank">IT RSUD Waled</a>
                </div>
            </div>
            <div class="d-none d-lg-flex social-links align-items-center">
                <a href="#" class="facebook"><i class="bi bi-whatsapp"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="youtube"><i class="bi bi-youtube"></i></i></a>
            </div>
        </div>
    </div>
    <div id="preloader"></div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <script src="{{ asset('medilab/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('medilab/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('medilab/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('medilab/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('medilab/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('medilab/assets/js/main.js') }}"></script>
    @include('sweetalert::alert')

    @yield('js')

</body>

</html>
