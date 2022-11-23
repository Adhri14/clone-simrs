@include('vendor.medilab.partials.head')

<body>
    @include('vendor.medilab.partials.navbar')
    <main id="main">
        @yield('content')
    </main>
    @include('vendor.medilab.partials.footer')
    @include('vendor.medilab.partials.js')
</body>

</html>
