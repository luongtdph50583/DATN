<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========== Meta Tags ========== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Gramentheme">
    <meta name="description" content="Nitro - Sports Multipurpose HTML Template">
    <!-- ======== Page title ============ -->
    <title>@yield('title', 'Nitro Template')</title>
    <!--<< Favicon >>-->
    <link rel="shortcut icon" href="{{ asset('assets1/img/favicon.svg') }}">
    <!--<< CSS >>-->
    <link rel="stylesheet" href="{{ asset('assets1/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/meanmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/main.css') }}">
</head>

<body>

    <!-- Preloader Start -->
    <div id="preloader" class="preloader">
        <div class="animation-preloader">
            <div class="spinner"></div>
            <div class="txt-loading">
                <span data-text-preloader="N" class="letters-loading">N</span>
                <span data-text-preloader="I" class="letters-loading">I</span>
                <span data-text-preloader="T" class="letters-loading">T</span>
                <span data-text-preloader="R" class="letters-loading">R</span>
                <span data-text-preloader="O" class="letters-loading">O</span>
            </div>
            <p class="text-center">Loading</p>
        </div>
        <div class="loader">
            <div class="row">
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back To Top Start -->
    <button id="back-top" class="back-to-top">
        <i class="fa-regular fa-arrow-up"></i>
    </button>

    <!-- MouseCursor Start -->
    <div class="mouseCursor cursor-outer"></div>
    <div class="mouseCursor cursor-inner"></div>

    <!-- Offcanvas Area Start -->
    <!-- End Right Bar -->
    @include('client.layouts.sidebar')

    @php
        $clientNotifications = Auth::check()
            ? Auth::user()->unreadNotifications()->latest()->limit(5)->get()
            : collect();
    @endphp

    <!-- Header -->
    @include('client.layouts.header1', ['clientNotifications' => $clientNotifications])

    <!-- Notification banner -->
    @if($clientNotifications->isNotEmpty())
        @include('client.layouts.notification-banner', ['notifications' => $clientNotifications])
    @endif

    @yield('content')

    <!-- Footer -->
    @include('client.layouts.footer')
    <!-- JS Plugins -->
    <script src="{{ asset('assets1/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets1/js/viewport.jquery.js') }}"></script>
    <script src="{{ asset('assets1/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets1/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('assets1/js/jquery.waypoints.js') }}"></script>
    <script src="{{ asset('assets1/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets1/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets1/js/jquery.meanmenu.min.js') }}"></script>
    <script src="{{ asset('assets1/js/parallaxie.js') }}"></script>
    <script src="{{ asset('assets1/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets1/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets1/js/gsap.min.js') }}"></script>
    <script src="{{ asset('assets1/js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('assets1/js/SplitText.min.js') }}"></script>
    <script src="{{ asset('assets1/js/splitType.js') }}"></script>
    <script src="{{ asset('assets1/js/main.js') }}"></script>

</body>

</html>
