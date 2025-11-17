<!-- Admin-style Sidebar (offcanvas) -->
<div class="admin-sidebar offcanvas__info">
    <div class="offcanvas__wrapper">
        <div class="offcanvas__content">
            <!-- Top Logo + Close Button -->
            <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                <div class="offcanvas__logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/img/logo/logo.svg') }}" alt="logo-img">
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
                <li><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>

                @auth
                    @php
                        $managedClubs = Auth::user()->getManagedClubs(); // CLB user là chủ nhiệm
                        $joinedClubs = Auth::user()->getJoinedClubs();   // CLB user tham gia
                        $memberClubs = $joinedClubs->filter(fn($club) => !$managedClubs->contains('id', $club->id));
                    @endphp

                    {{-- CLB quản lý --}}
                    @if($managedClubs->count() > 0)
                        <li class="menu-section">
                            <a href="#managedClubsSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle">
                                <i class="fas fa-cog"></i> Quản lý CLB của tôi
                            </a>
                            <ul class="collapse list-unstyled" id="managedClubsSubmenu">
                                @foreach($managedClubs as $club)
                                    <li>
                                        {{-- <a href="{{ route('club_manager.dashboard', ['club_id' => $club->id]) }}"> --}}
                                            <i class="fas fa-circle"></i> {{ $club->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    {{-- CLB thành viên --}}
                    @if($memberClubs->count() > 0)
                        <li class="menu-section">
                            <a href="#memberClubsSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle">
                                <i class="fas fa-users"></i> CLB của tôi
                            </a>
                            <ul class="collapse list-unstyled" id="memberClubsSubmenu">
                                @foreach($memberClubs as $club)
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
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
