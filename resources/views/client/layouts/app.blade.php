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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            if (typeof $.fn.select2 === 'undefined') {
                return;
            }
            $('select[data-select2="true"]').each(function () {
                const $el = $(this);
                const config = {
                    width: '100%',
                    placeholder: $el.data('placeholder') || $el.attr('placeholder') || '',
                    allowClear: $el.data('allow-clear') === true || $el.data('allow-clear') === 'true',
                    language: {
                        noResults: function() {
                            return "Không tìm thấy kết quả";
                        },
                        searching: function() {
                            return "Đang tìm kiếm...";
                        }
                    }
                };
                
                // Nếu có data-ajax-url thì dùng AJAX search
                if ($el.data('ajax-url')) {
                    config.ajax = {
                        url: $el.data('ajax-url'),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results || data.data || []
                            };
                        },
                        cache: true
                    };
                } else {
                    // Nếu không có AJAX, thêm search trong options
                    config.minimumResultsForSearch = 0; // Luôn hiển thị search box
                }
                
                $el.select2(config);
            });
        });
    </script>

    <script>
        // Toggle submenu cho từng CLB
        function toggleClubSubmenu(element) {
            const submenu = element.nextElementSibling;
            const icon = element.querySelector('.fa-chevron-down');
            
            if (submenu) {
                if (submenu.style.display === 'none' || submenu.style.display === '') {
                    submenu.style.display = 'block';
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                } else {
                    submenu.style.display = 'none';
                    if (icon) {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                }
            }
        }

        // Đảm bảo submenu không bị ẩn khi hover
        document.addEventListener('DOMContentLoaded', function() {
            const submenuItems = document.querySelectorAll('.submenu-item');
            submenuItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    const submenu = this.querySelector('.submenu');
                    if (submenu) {
                        submenu.style.display = 'block';
                    }
                });
                item.addEventListener('mouseleave', function() {
                    // Không ẩn khi rời chuột, chỉ ẩn khi click toggle
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
