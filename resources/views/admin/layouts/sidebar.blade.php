 <div class="app-menu navbar-menu" >
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
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div class="dropdown sidebar-user m-1 rounded">
                <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-flex align-items-center gap-2">
                        <img class="rounded header-profile-user" src="assets/images/users/avatar-1.jpg" alt="Header Avatar">
                        <span class="text-start">
                            <span class="d-block fw-medium sidebar-user-name-text">Anna Adame</span>
                            <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">Online</span></span>
                        </span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <h6 class="dropdown-header">Welcome Anna!</h6>
                    <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                    <a class="dropdown-item" href="apps-chat.html"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Messages</span></a>
                    <a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>
                    <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>$5971.67</b></span></a>
                    <a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                    <a class="dropdown-item" href="auth-lockscreen-basic.html"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a>
                    <a class="dropdown-item" href="auth-logout-basic.html"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                </div>
            </div>
            <div id="scrollbar">
                <div class="container-fluid">


                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                     <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.users.index') }}">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Quản lí tài khoản</span>
                            </a>

                        </li>
                      <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.events.index') }}">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Quản lí sự kiện </span>
                            </a>

                        </li> <!-- end Dashboard Menu -->
                        {{-- quản lí clb --}}
                        <li class="nav-item">
    <a class="nav-link menu-link" href="#sidebarclb" data-bs-toggle="collapse" role="button"
        aria-expanded="false" aria-controls="sidebarclb">
        <i class="ri-team-line"></i> <span>Quản lý CLB</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarclb">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.clubs.index') }}" class="nav-link">
                    Danh sách CLB
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.club-requests.index') }}" class="nav-link">
                    Yêu cầu tạo CLB
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.club-join-requests.index') }}" class="nav-link">
                    Yêu cầu tham gia CLB
                </a>
            </li>
        </ul>
    </div>
</li>
{{-- end quản lí clb --}}
                       <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.members.index') }}">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Quản lí thành viên</span>
                            </a>

                        </li> <!-- end Dashboard Menu -->
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#quanliquy" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="quanliquy">
                                <i class="ri-wallet-line"></i> <span data-key="t-funds">Quản lý quỹ</span>
                            </a>
                            <div class="collapse menu-dropdown" id="quanliquy">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.funds.index') }}" class="nav-link" data-key="t-fund-list">Danh sách giao dịch</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.funds.create') }}" class="nav-link" data-key="t-fund-create">Thêm giao dịch</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Fund Management Menu -->
                       <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.stats.index') }}">
                                <i class="ri-honour-line"></i> <span data-key="t-widgets">Thống kê</span>
                            </a>
                        </li>
                        {{--Plans club --}}
                        <li>
                            <a class="nav-link menu-link" href="{{ route('admin.plans.index') }}">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Kế hoạch</span>
                            </a>
                        </li>
                        {{-- End Plans club --}}

                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-components">Nội dung</span></li>



                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebartintuc" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebartintuc">
                                <i class="ri-stack-line"></i> <span data-key="t-advance-ui">Bài viết và Tài liệu</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebartintuc">
                                <ul class="nav nav-sm flex-column">
                                     <li class="nav-item">
                                        <a href="{{ route('admin.posts.index') }}" class="nav-link" data-key="t-nestable-list">Bài Viết</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.documents.index') }}" class="nav-link" data-key="t-sweet-alerts">Tài Liệu </a>
                                    </li>


                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.comments.index') }}">
                                <i class="ri-honour-line"></i> <span data-key="t-widgets">Bình luận</span>
                            </a>
                        </li>


                        <li class="menu-title"><i class="ri-community-line"></i> <span>Quản lý Câu lạc bộ</span></li>





                        {{-- <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarLayouts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                                <i class="ri-layout-3-line"></i> <span data-key="t-layouts">Layouts</span> <span class="badge badge-pill bg-danger" data-key="t-hot">Hot</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarLayouts">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="layouts-horizontal.html" target="_blank" class="nav-link" data-key="t-horizontal">Horizontal</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="layouts-detached.html" target="_blank" class="nav-link" data-key="t-detached">Detached</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="layouts-two-column.html" target="_blank" class="nav-link" data-key="t-two-column">Two Column</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="layouts-vertical-hovered.html" target="_blank" class="nav-link" data-key="t-hovered">Hovered</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->

                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pages</span></li>


                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarPages" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPages">
                                <i class="ri-pages-line"></i> <span data-key="t-pages">Pages</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarPages">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="pages-starter.html" class="nav-link" data-key="t-starter"> Starter </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#sidebarProfile" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProfile" data-key="t-profile"> Profile
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarProfile">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="pages-profile.html" class="nav-link" data-key="t-simple-page">
                                                        Simple Page </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="pages-profile-settings.html" class="nav-link" data-key="t-settings"> Settings </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="nav-item">
                                        <a href="pages-privacy-policy.html" class="nav-link" data-key="t-privacy-policy">Privacy Policy</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pages-term-conditions.html" class="nav-link" data-key="t-term-conditions">Term & Conditions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#sidebarBlogs" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarBlogs">
                                            <span data-key="t-blogs">Blogs</span> <span class="badge badge-pill bg-success" data-key="t-new">New</span>
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarBlogs">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="pages-blog-list.html" class="nav-link" data-key="t-list-view">List View</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="pages-blog-grid.html" class="nav-link" data-key="t-grid-view">Grid View</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="pages-blog-overview.html" class="nav-link" data-key="t-overview">Overview</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarLanding" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLanding">
                                <i class="ri-rocket-line"></i> <span data-key="t-landing">Landing</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarLanding">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="landing.html" class="nav-link" data-key="t-one-page"> One Page </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="nft-landing.html" class="nav-link" data-key="t-nft-landing"> NFT Landing </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="job-landing.html" class="nav-link" data-key="t-job">Job</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-components">Components</span></li>



                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAdvanceUI" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAdvanceUI">
                                <i class="ri-stack-line"></i> <span data-key="t-advance-ui">Advance UI</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAdvanceUI">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="advance-ui-sweetalerts.html" class="nav-link" data-key="t-sweet-alerts">Sweet
                                            Alerts</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="advance-ui-nestable.html" class="nav-link" data-key="t-nestable-list">Nestable
                                            List</a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="widgets.html">
                                <i class="ri-honour-line"></i> <span data-key="t-widgets">Widgets</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarForms" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarForms">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Forms</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarForms">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="forms-elements.html" class="nav-link" data-key="t-basic-elements">Basic
                                            Elements</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="forms-select.html" class="nav-link" data-key="t-form-select"> Form Select </a>
                                    </li>

                                </ul>
                            </div>
                        </li> --}}


                    </ul>

                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
