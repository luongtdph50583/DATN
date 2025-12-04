<!-- Admin-style Sidebar (offcanvas) -->
<div class="admin-sidebar offcanvas__info">
    <div class="offcanvas__wrapper">
        <div class="offcanvas__content">
            <!-- Top Logo + Close Button -->
            <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                <div class="offcanvas__logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets1/img/logo/logo.svg') }}" alt="logo-img">
                    </a>
                </div>
                <div class="offcanvas__close">
                    <button>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Sidebar Menu -->
      <ul class="admin-menu list-unstyled">
    <li><a href="{{ url('admin/dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="{{ url('admin/posts') }}"><i class="fas fa-newspaper"></i> Posts</a></li>
    <li><a href="{{ url('admin/users') }}"><i class="fas fa-users"></i> Users</a></li>
    <li><a href="{{ url('admin/settings') }}"><i class="fas fa-cogs"></i> Settings</a></li>
  

                @auth
                    @php
                        $managedClubs = Auth::user()->getManagedClubs(); // CLB user là chủ nhiệm
                        $joinedClubs = Auth::user()->getJoinedClubs(); // CLB user tham gia
                        $memberClubs = $joinedClubs->filter(fn($club) => !$managedClubs->contains('id', $club->id));
                    @endphp

                    {{-- CLB quản lý --}}
                    @if ($managedClubs->count() > 0)
                        <li class="menu-section">
                            <a href="#managedClubsSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle" onclick="event.preventDefault(); this.classList.toggle('active');">
                                <i class="fas fa-cog"></i> Quản lý CLB của tôi
                            </a>
                            <ul class="collapse list-unstyled" id="managedClubsSubmenu">
                                @foreach ($managedClubs as $club)
                                    <li class="submenu-item" style="position: relative;">
                                        <a href="javascript:void(0)" class="club-toggle" onclick="event.preventDefault(); toggleClubSubmenu(this);">
                                            <i class="fas fa-circle"></i> {{ $club->name }}
                                            <i class="fas fa-chevron-down ms-auto" style="float: right;"></i>
                                        </a>
                                        <ul class="submenu list-unstyled ms-3" style="display: none;">
                                            <li><a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-file-alt"></i> Bài viết
                                                </a></li>
                                            <li><a
                                                    href="{{ route('club_manager.member_requests.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-user-plus"></i> Yêu cầu tham gia
                                                </a></li>
                                            <li><a
                                                    href="{{ route('club_manager.edit_request.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-edit"></i> Đề xuất sửa CLB
                                                </a></li>
                                            <li><a
                                                    href="{{ route('club_manager.interviews.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-calendar-check"></i> Phỏng vấn
                                                </a></li>
                                            <li><a
                                                    href="{{ route('club_manager.recruit.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-users"></i> Tuyển thành viên
                                                </a></li>
                                                <li>
                <a href="{{ route('club_manager.events.index',['club_id' => $club->id]) }}">
                    <i class="fas fa-calendar-alt"></i>
                   Quản lý sự kiện 
                </a>
            </li>
                                            <li>
                                                <a href="{{ route('club_manager.fund.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-coins"></i> Quỹ CLB
                                                </a>
                                            </li>
                                            <li><a
                                                    href="{{ route('club_manager.notifications.create', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-bell"></i> Thông báo CLB
                                                </a></li>
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    {{-- CLB thành viên --}}
                    @if ($memberClubs->count() > 0)
                        <li class="menu-section">
                            <a href="#memberClubsSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle">
                                <i class="fas fa-users"></i> CLB của tôi
                            </a>
                            <ul class="collapse list-unstyled" id="memberClubsSubmenu">
                                @foreach ($memberClubs as $club)
                                    <li>
                                        <a href="{{ route('club.member.view', ['club_id' => $club->id]) }}">
                                            <i class="fas fa-circle"></i> {{ $club->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    {{-- Logout --}}
                    <li>
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                            @csrf
                        </form>
                    </li>
                @endauth

                @guest
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                @endguest
            </ul>


            <!-- Optional Sidebar Info -->
            <div class="offcanvas__contact mt-4">
                <h4>Contact Info</h4>
                <ul>
                    <li><i class="fal fa-map-marker-alt"></i> <span>Main Street, Melbourne, Australia</span></li>
                    <li><i class="fal fa-envelope"></i> <a href="mailto:info@example.com">info@example.com</a></li>
                    <li><i class="fal fa-clock"></i> Mon-Fri, 09am -05pm</li>
                    <li><i class="far fa-phone"></i> <a href="tel:+11002345909">+11002345909</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas__overlay"></div>

<style>
    /* Sửa CSS để submenu không bị ẩn khi hover */
    .admin-menu .submenu-item {
        position: relative;
    }
    
    .admin-menu .submenu-item .submenu {
        display: none;
        position: relative;
        left: 0;
        top: 0;
        background: transparent;
        padding: 0;
        margin: 0;
        box-shadow: none;
        border: none;
        visibility: visible;
        opacity: 1;
        transform: none;
        transition: none;
    }
    
    .admin-menu .submenu-item:hover .submenu,
    .admin-menu .submenu-item .submenu.show {
        display: block !important;
    }
    
    .admin-menu .submenu-item .club-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .admin-menu .submenu-item .submenu li a {
        padding-left: 20px;
        font-size: 14px;
    }
</style>
