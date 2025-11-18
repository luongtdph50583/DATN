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
    <!-- CSS Stable Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <!-- Preloader, Back to Top, MouseCursor, Sidebar, Header, Notifications -->
    @include('client.layouts.sidebar')

    @php
        $clientNotifications = Auth::check()
            ? Auth::user()->unreadNotifications()->latest()->limit(5)->get()
            : collect();
    @endphp

    @include('client.layouts.header1', ['clientNotifications' => $clientNotifications])
    @if($clientNotifications->isNotEmpty())
        @include('client.layouts.notification-banner', ['notifications' => $clientNotifications])
    @endif

    @yield('content')

    @include('client.layouts.footer')

    <!-- JS Plugins -->
    <script src="{{ asset('assets1/js/jquery-3.7.1.min.js') }}"></script>
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

    <!-- JS Stable Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <!-- Init Select2 -->
    <script>
        $(function () {
            if (typeof $.fn.select2 === 'undefined') return;

            $('select[data-select2="true"]').each(function () {
                const $el = $(this);
                const config = {
                    width: '100%',
                    placeholder: $el.data('placeholder') || $el.attr('placeholder') || '',
                    allowClear: $el.data('allow-clear') === true || $el.data('allow-clear') === 'true',
                    language: {
                        noResults: () => "Không tìm thấy kết quả",
                        searching: () => "Đang tìm kiếm..."
                    }
                };

                if ($el.data('ajax-url')) {
                    config.ajax = {
                        url: $el.data('ajax-url'),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term || '', page: params.page || 1 };
                        },
                        processResults: function (data) {
                            return { results: data.results || data.data || [] };
                        },
                        cache: true
                    };
                } else {
                    config.minimumResultsForSearch = 0; // luôn hiển thị search box
                }

                $el.select2(config);
            });
        });
    </script>

    <!-- Submenu Toggle JS -->
    <script>
        function toggleClubSubmenu(element) {
            const submenu = element.nextElementSibling;
            const icon = element.querySelector('.fa-chevron-down');
            if (submenu) {
                if (submenu.style.display === '' || submenu.style.display === 'none') {
                    submenu.style.display = 'block';
                    if (icon) { icon.classList.replace('fa-chevron-down', 'fa-chevron-up'); }
                } else {
                    submenu.style.display = 'none';
                    if (icon) { icon.classList.replace('fa-chevron-up', 'fa-chevron-down'); }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.submenu-item').forEach(item => {
                item.addEventListener('mouseenter', () => {
                    const submenu = item.querySelector('.submenu');
                    if (submenu) submenu.style.display = 'block';
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>