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
                <img src="{{ asset('assets/images/logo-sm.png')}}" alt="" height="22">
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
            <!-- item-->
            <h6 class="dropdown-header">Welcome Anna!</h6>
            <a class="dropdown-item" href="pages-profile.html"><i
                    class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                    class="align-middle">Profile</span></a>
            <a class="dropdown-item" href="apps-chat.html"><i
                    class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span
                    class="align-middle">Messages</span></a>
            <a class="dropdown-item" href="apps-tasks-kanban.html"><i
                    class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span
                    class="align-middle">Taskboard</span></a>
            <a class="dropdown-item" href="pages-faqs.html"><i
                    class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span
                    class="align-middle">Help</span></a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="pages-profile.html"><i
                    class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance :
                    <b>$5971.67</b></span></a>
            <a class="dropdown-item" href="pages-profile-settings.html"><span
                    class="badge bg-success-subtle text-success mt-1 float-end">New</span><i
                    class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                    class="align-middle">Settings</span></a>
            <a class="dropdown-item" href="auth-lockscreen-basic.html"><i
                    class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock
                    screen</span></a>
            <a class="dropdown-item" href="auth-logout-basic.html"><i
                    class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle"
                    data-key="t-logout">Logout</span></a>
        </div>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">


            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                <!-- Quản lý tài khoản -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.users.index') }}">
                        <i class="ri-user-settings-line"></i> <span data-key="t-dashboards">Quản lí tài khoản</span>
                    </a>
                </li>

                <!-- Quản lý CLB -->
                 <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarclb" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="sidebarclb">
                        <i class="ri-team-line"></i> <span>Quản lý CLB</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarclb">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.clubs.index') }}" class="nav-link">
                                    <i class="ri-list-check-2"></i> Danh sách CLB
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.club_requests.index') }}" class="nav-link">
                                    <i class="ri-file-add-line"></i> Yêu cầu tạo CLB
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.club_join_requests.index') }}" class="nav-link">
                                    <i class="ri-user-add-line"></i> Yêu cầu tham gia CLB
                                </a>
                            </li>
                        </ul>
                    </div>
                </li> 

                <!-- Quản lý thành viên -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.members.index') }}">
                        <i class="ri-group-line"></i> <span data-key="t-dashboards">Quản lí thành viên</span>
                    </a>
                </li>
                     <!-- Quản lý sự kiện -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.events.index') }}">
                        <i class="ri-calendar-event-line"></i> <span data-key="t-dashboards">Quản lí sự kiện</span>
                    </a>
                </li>
              
                          <!-- Quản lý quỹ -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.funds.index') }}">
                                               <i class="ri-wallet-line"></i> <span data-key="t-funds">Quản lý quỹ</span>
                    </a>
                </li>

             

                <!-- Quản lý thông báo -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#notificationMenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="notificationMenu">
                        <i class="ri-notification-3-line"></i> <span>Quản lý thông báo</span>
                    </a>
                    <div class="collapse menu-dropdown" id="notificationMenu">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.notifications.create') }}" class="nav-link">
                                    <i class="ri-send-plane-line"></i> Gửi thông báo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.notifications.index') }}" class="nav-link">
                                    <i class="ri-list-check-2"></i> Thông báo đã gửi
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Quản lý tài liệu CLB -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#tailieu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="tailieu">
                        <i class="ri-folder-3-line"></i> <span>Quản lý tài liệu CLB</span>
                    </a>
                    <div class="collapse menu-dropdown" id="tailieu">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.documentclub.index') }}" class="nav-link">
                                    <i class="ri-file-text-line"></i> Danh sách
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.documentclub.create') }}" class="nav-link">
                                    <i class="ri-add-box-line"></i> Thêm mới
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.documentclub.trash') }}" class="nav-link">
                                    <i class="ri-add-box-line"></i> Tài liệu đã xóa
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Nội dung -->
                <li class="menu-title"><i class="ri-article-line"></i> <span data-key="t-components">Nội dung</span></li>

                <!-- Bài viết -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebartintuc" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebartintuc">
                        <i class="ri-newspaper-line"></i> <span data-key="t-advance-ui">Bài viết</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebartintuc">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.posts.index') }}" class="nav-link" data-key="t-nestable-list">
                                    <i class="ri-file-edit-line"></i> Danh sách
                                </a>
                            </li>
                          <li class="nav-item">
                                <a href="{{ route('admin.posts.trash') }}" class="nav-link" data-key="t-sweet-alerts">
                                    <i class="ri-file-copy-2-line"></i> Bài viết đã xóa
                                </a>   
                            </li>
                            
                        </ul>
                    </div>
                </li>

                <!-- Bình luận -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.comments.index') }}">
                        <i class="ri-chat-3-line"></i> <span data-key="t-widgets">Bình luận</span>
                    </a>
                </li>

                <!-- Quản lý Câu lạc bộ (tiêu đề nhóm) -->
                <li class="menu-title"><i class="ri-community-line"></i> <span>Chi tiết </span></li>

                   <!-- Thống kê -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.stats.index') }}">
                        <i class="ri-bar-chart-line"></i> <span data-key="t-widgets">Thống kê</span>
                    </a>
                </li>

                <!-- Kế hoạch -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.plans.index') }}">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Kế hoạch</span>
                    </a>
                </li>
            </ul>

        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
