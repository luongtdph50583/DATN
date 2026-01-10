<!-- Sidebar Quản Trị (offcanvas) -->
<div class="admin-sidebar offcanvas__info">
    <div class="offcanvas__wrapper">
        <div class="offcanvas__content">

            <!-- Logo + nút đóng -->
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

            <!-- Menu Chính -->
            <ul class="admin-menu list-unstyled">
                <li><a href="{{ url('admin/dashboard') }}"><i class="fas fa-tachometer-alt"></i> Trang quản trị</a></li>
              

                @auth
                    @php
                        $managedClubs = Auth::user()->getManagedClubs();
                        $joinedClubs = Auth::user()->getJoinedClubs();
                        $memberClubs = $joinedClubs->filter(fn($club) => !$managedClubs->contains('id', $club->id));
                    @endphp

                    {{-- CLB mà bạn quản lý --}}
                    @if ($managedClubs->count() > 0)
                        <li class="menu-section">
                            <a href="#managedClubsSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle">
                                <i class="fas fa-cog"></i> Quản lý CLB của tôi
                            </a>
                            <ul class="collapse list-unstyled" id="managedClubsSubmenu">

                                @foreach ($managedClubs as $club)
                                    <li class="submenu-item">
                                        <a href="javascript:void(0)" class="club-toggle"
                                            onclick="event.preventDefault(); toggleClubSubmenu(this);">
                                            <i class="fas fa-circle"></i> {{ $club->name }}
                                            <i class="fas fa-chevron-down ms-auto"></i>
                                        </a>

                                        <ul class="submenu list-unstyled ms-3" style="display:none;">
                                      <li>
    <a href="{{ route('club_manager.showmember', ['club' => $club->id]) }}">
        <i class="fas fa-file-alt"></i> Quản lý thành viên 
    </a>
</li>

                                            <li>
                                                <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-file-alt"></i> Quản lý bài viết
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('club_manager.member_requests.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-user-plus"></i> Yêu cầu tham gia
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('club_manager.edit_request.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-edit"></i> Đề xuất chỉnh sửa CLB
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('club_manager.recruit_forms.list', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-users"></i> Form tuyển thành viên
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('club_manager.events.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-calendar-alt"></i> Quản lý sự kiện
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('club_manager.fund.index', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-coins"></i> Quỹ CLB
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('club_manager.notifications.create', ['club_id' => $club->id]) }}">
                                                    <i class="fas fa-bell"></i> Gửi thông báo
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('club_manager.club.documents.index', [$club->id]) }}">
                                                    <i class="fas fa-folder-open"></i> Quản lý tài liệu
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif


                    {{-- CLB mà bạn là thành viên --}}
                    @if ($memberClubs->count() > 0)
                        <li class="menu-section">
                            <a href="#memberClubsSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle">
                                <i class="fas fa-users"></i> CLB tôi tham gia
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

                    {{-- Đăng xuất --}}
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                            @csrf
                        </form>
                    </li>
                @endauth


                {{-- Nếu chưa đăng nhập --}}
                @guest
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                    </li>
                @endguest
            </ul>

            <!-- Thông tin liên hệ -->
            <div class="offcanvas__contact mt-4">
                <h4>Thông tin liên hệ</h4>
                <ul>
                    <li><i class="fal fa-map-marker-alt"></i> <span>Trường CĐ FPT Polytechnic — Hà Nội</span></li>
                    <li><i class="fal fa-envelope"></i> <a href="mailto:info@example.com">club@gmail.com</a></li>
                    <li><i class="fal fa-clock"></i> Thứ 2 — Thứ 6, 8:00–17:00</li>
                    <li><i class="far fa-phone"></i> <a href="tel:+11002345909">+84 000 000 000 </a></li>
                </ul>
            </div>

        </div>
    </div>
</div>

<div class="offcanvas__overlay"></div>
