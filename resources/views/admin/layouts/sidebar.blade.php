<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-dark.png')}}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-light.png')}}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <!-- User dropdown -->
    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user" src="assets/images/users/avatar-1.jpg" alt="Header Avatar">
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">Anna Adame</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text"><i
                            class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span
                            class="align-middle">Online</span></span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">Welcome Anna!</h6>
            <a class="dropdown-item" href="pages-profile.html"><i
                    class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> Profile</a>
            <a class="dropdown-item" href="apps-chat.html"><i
                    class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> Messages</a>
            <a class="dropdown-item" href="apps-tasks-kanban.html"><i
                    class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> Taskboard</a>
            <a class="dropdown-item" href="pages-faqs.html"><i
                    class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> Help</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="pages-profile.html"><i
                    class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> Balance: <b>$5971.67</b></a>
            <a class="dropdown-item" href="pages-profile-settings.html"><span
                    class="badge bg-success-subtle text-success mt-1 float-end">New</span><i
                    class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> Settings</a>
            <a class="dropdown-item" href="auth-lockscreen-basic.html"><i
                    class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> Lock screen</a>
            <a class="dropdown-item" href="auth-logout-basic.html"><i
                    class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> Logout</a>
        </div>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>Menu</span></li>

                <!-- Quản lý tài khoản -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.users.index') }}">
                        <i class="ri-user-settings-line"></i> Quản lí tài khoản
                    </a>
                </li>

                <!-- Quản lý CLB -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCLB" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarCLB">
                        <i class="ri-team-line"></i> Quản lý CLB
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCLB">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.clubs.index') }}" class="nav-link">Danh sách CLB</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.club_requests.index') }}" class="nav-link">Yêu cầu tạo CLB</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.club_join_requests.index') }}" class="nav-link">Yêu cầu tham gia CLB</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Quản lý thành viên -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.members.index') }}">
                        <i class="ri-group-line"></i> Quản lí thành viên
                    </a>
                </li>

                <!-- Quản lý sự kiện -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.events.index') }}">
                        <i class="ri-calendar-event-line"></i> Quản lí sự kiện
                    </a>
                </li>

                <!-- Quản lý quỹ -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.funds.index') }}">
                        <i class="ri-wallet-line"></i> Quản lý quỹ
                    </a>
                </li>

                <!-- Quản lý thông báo -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarNotification" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarNotification">
                        <i class="ri-notification-3-line"></i> Quản lý thông báo
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarNotification">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.notifications.create') }}" class="nav-link">Gửi thông báo</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.notifications.index') }}" class="nav-link">Thông báo đã gửi</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Quản lý tài liệu CLB -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDocuments" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarDocuments">
                        <i class="ri-folder-3-line"></i> Quản lý tài liệu CLB
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDocuments">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.documentclub.index') }}" class="nav-link">Danh sách</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.documentclub.create') }}" class="nav-link">Thêm mới</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.documentclub.trash') }}" class="nav-link">Tài liệu đã xóa</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Nội dung -->
                <li class="menu-title"><i class="ri-article-line"></i> Nội dung</li>

                <!-- Bài viết -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPosts" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarPosts">
                        <i class="ri-newspaper-line"></i> Bài viết
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPosts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.posts.index') }}" class="nav-link">Danh sách</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.posts.trash') }}" class="nav-link">Bài viết đã xóa</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Bình luận -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarComments" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarComments">
                        <i class="ri-chat-3-line"></i> Bình luận
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarComments">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.comments.index') }}" class="nav-link">Danh sách bình luận</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.comments.trashed') }}" class="nav-link">Bình luận đã xóa</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Chi tiết / Thống kê -->
                <li class="menu-title"><i class="ri-community-line"></i> Chi tiết</li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.stats.index') }}">
                        <i class="ri-bar-chart-line"></i> Thống kê
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidebar-background"></div>
    </div>
</div>
