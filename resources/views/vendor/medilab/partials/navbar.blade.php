<div id="topbar" class="d-flex align-items-center fixed-top">
    <div class="container d-flex justify-content-between">
        <div class="contact-info d-flex align-items-center">
            <i class="bi bi-envelope"></i> <a href="mailto:contact@example.com">admin.rsud@lamaddukelleng.com</a>
            <i class="bi bi-phone"></i>0812 3456 7890
        </div>
        <div class="d-none d-lg-flex social-links align-items-center">
            <a href="#" class="facebook"><i class="bi bi-whatsapp"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="youtube"><i class="bi bi-youtube"></i></i></a>
        </div>
    </div>
</div>
<header id="header" class="fixed-top">
    <div class="container d-flex align-items-center">
        <h1 class="logo me-auto">
            <a href="{{ route('landingpage') }}">
                <img src="{{ asset('vendor/adminlte/dist/img/rswaledico.png') }}" alt="" class="img-fluid">
                RSUD Lamaddukelleng</a>
        </h1>
        <nav id="navbar" class="navbar order-last order-lg-0">
            <ul>
                <li><a class="nav-link scrollto active" href="{{ route('landingpage') }}">Home</a></li>
                <li><a class="nav-link scrollto" href="{{ route('landingpage') }}#about">Pelayanan</a></li>
                <li><a class="nav-link scrollto" href="{{ route('landingpage') }}#blog">Blog</a></li>
                <li><a class="nav-link scrollto" href="{{ route('landingpage') }}#contact">Kontak</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>
        <a href="{{ route('daftar_online') }}" class="appointment-btn scrollto">
            Daftar
        </a>
    </div>
</header>
