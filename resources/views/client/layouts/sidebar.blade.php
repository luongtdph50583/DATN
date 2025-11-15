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
    <li><a href="{{ url('admin/dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="{{ url('admin/posts') }}"><i class="fas fa-newspaper"></i> Posts</a></li>
    <li><a href="{{ url('admin/users') }}"><i class="fas fa-users"></i> Users</a></li>
    <li><a href="{{ url('admin/settings') }}"><i class="fas fa-cogs"></i> Settings</a></li>

    @auth
        <!-- Logout chỉ hiển thị khi đã đăng nhập -->
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
        <!-- Login chỉ hiển thị khi chưa đăng nhập -->
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
