<!-- <div class="header-top-section">
            <div class="container">
                <div class="header-top-wrapper">
                    <p>
                        🔵 🔴 Secure your ticket to Gamper!" <a href="contact.html">BUY YOUR TICKET</a>
                    </p>
                    <div class="form-clt mt-0">
        <img src="{{ asset('assets1/img/global.png') }}" alt="global">
                        <div class="form">
                            <select class="single-select w-100">
                                <option>English</option>
                                <option>Bangla</option>
                                <option>Hindi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

<!-- Header Section Start -->
<header id="header-sticky" class="header-2">
    <div class="container">
        <div class="mega-menu-wrapper">
            <div class="header-main">
                <a href="{{ route('client.home') }}" class="logo">
                    <img src="{{ asset('assets1/img/logo/theme-logo.svg') }}" alt="{{ config('app.name') }}">
                </a>
                <div class="header-left">
                    <div class="mean__menu-wrapper">
                        <div class="main-menu">
                            <nav id="mobile-menu">
                                <ul>
                                    <li class="has-dropdown active menu-thumb">
                                        <a href="index.html">
                                            Home
                                        </a>
                                        <ul class="submenu has-homemenu">
                                            <li>
                                                <div class="homemenu-items">
                                                    <div class="homemenu">
                                                        <div class="homemenu-thumb">
                                                            <img src="{{ asset('assets1/img/header/home-1.jpg') }}" alt="img">
                                                            <div class="demo-button">
                                                                <a href="index.html" class="gt-theme-btn">Demo page</a>
                                                            </div>
                                                        </div>
                                                        <div class="homemenu-content text-center">
                                                            <h4 class="homemenu-title">
                                                                Football
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="homemenu">
                                                        <div class="homemenu-thumb mb-15">
                                                            <img src="{{ asset('assets1/img/header/home-2.jpg') }}" alt="img">
                                                            <div class="demo-button">
                                                                <a href="index-2.html" class="gt-theme-btn">Demo
                                                                    page</a>
                                                            </div>
                                                        </div>
                                                        <div class="homemenu-content text-center">
                                                            <h4 class="homemenu-title">
                                                                Basketball
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="homemenu">
                                                        <div class="homemenu-thumb mb-15">
                                                            <img src="{{ asset('assets1/img/header/home-3.jpg') }}" alt="img">
                                                            <div class="demo-button">
                                                                <a href="index-3.html" class="gt-theme-btn">Demo
                                                                    page</a>
                                                            </div>
                                                        </div>
                                                        <div class="homemenu-content text-center">
                                                            <h4 class="homemenu-title">
                                                                Rugby Club
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-dropdown active d-xl-none">
                                        <a href="index.html" class="border-none">
                                            Home
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="index.html">Football</a></li>
                                            <li><a href="index-2.html">Basketball</a></li>
                                            <li><a href="index-3.html">Rugby Club</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="about.html">
                                            About Us
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="about.html">About Us</a></li>
                                            <li><a href="about-2.html">About Us 02</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-dropdown">
                                        <a href="news.html">
                                            Pages
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="gallery.html">About Us</a></li>
                                            <li><a href="club-ranking.html">club rankings</a></li>
                                            <li><a href="club-line.html">club LineUp</a></li>
                                            <li><a href="ticket.html">Ticket</a></li>
                                            <li><a href="404.html">404 Page</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="{{ route('client.clubs.list') }}">Các Câu Lạc Bộ </a>
                                    </li>
                              <li>
    <a href="{{ route('client.postpublic.index') }}">
        Blog
    </a>
    @php
        $clubs = \App\Models\Club::all();
    @endphp
    <ul class="submenu {{ $clubs->count() > 7 ? 'submenu-horizontal' : '' }}">
        @foreach($clubs as $club)
            <li>
                <a href="{{ route('client.postpublic.by_club', $club->id) }}">
                    {{ $club->name }}
                </a>
            </li>
        @endforeach
    </ul>
</li>


                                    <li>
                                        <a href="contact.html">Contact Us</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="header-right d-flex justify-content-end align-items-center gap-3">
                    {{-- <div class="header-btn">
                        <a href="ticket.html" class="theme-btn border-btn">
                            get tickets <i class="fa-solid fa-arrow-up-right"></i>
                        </a>
                        <a href="club-ranking.html" class="theme-btn d-none d-xxl-block">
                            JOIN NOW <i class="fa-solid fa-arrow-up-right"></i>
                        </a>
                    </div> --}}


                    @auth
                        <div class="dropdown header-notification">
                            <button class="theme-btn border-btn d-flex align-items-center gap-2 position-relative"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                @if(isset($clientNotifications) && $clientNotifications->count())
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $clientNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm notification-dropdown">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold">{{ __('Thông báo') }}</span>
                                    <form action="{{ route('notifications.read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0">
                                            {{ __('Đánh dấu đã đọc') }}
                                        </button>
                                    </form>
                                </div>
                                @if(isset($clientNotifications) && $clientNotifications->isNotEmpty())
                                    <ul class="list-unstyled mb-0 notification-list">
                                        @foreach($clientNotifications as $notification)
                                            <li class="mb-2">
                                                <div class="fw-semibold">
                                                    {{ data_get($notification->data, 'title', __('Thông báo')) }}
                                                </div>
                                                @php($messageHtml = data_get($notification->data, 'message_html'))
                                                <div class="text-muted small">
                                                    @if($messageHtml)
                                                        {!! $messageHtml !!}
                                                    @else
                                                        {{ data_get($notification->data, 'message', '') }}
                                                    @endif
                                                </div>
                                                <span class="text-muted fst-italic small">
                                                    {{ optional($notification->created_at)->diffForHumans() }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted small mb-0">{{ __('Bạn không có thông báo mới.') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="theme-btn border-btn" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ \Illuminate\Support\Str::limit(Auth::user()->name, 12) }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <span class="dropdown-item-text text-muted">
                                    {{ __('Vai trò:') }} {{ __(Auth::user()->role ?? 'member') }}
                                </span>
                              <a class="dropdown-item" href="{{ route('profile.show') }}">{{ __('Trang cá nhân') }}</a>
                              <a class="dropdown-item" href="{{ route('formation-request.index') }}">{{ __('Yêu cầu của bạn ') }}</a>

                                @php($managedClub = Auth::user()->getManagedClubs()->first())
                                @if($managedClub)
                                    <a class="dropdown-item" href="{{ route('club_manager.posts.index', ['club_id' => $managedClub->id]) }}">
                                        {{ __('Quản lý CLB') }}
                                    </a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">
                                    {{ __('Đăng xuất') }}
                                </a>
                            </div>
                        </div>
                        <form id="header-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="theme-btn border-btn">
                            {{ __('Đăng nhập') }}
                        </a>
                    @endauth

                    <div class="header__hamburger d-xl-block my-auto">
                        <div class="sidebar__toggle">
                            <div class="header-bar">
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
