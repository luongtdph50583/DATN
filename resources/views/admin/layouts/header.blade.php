<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>LBTH</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
    :root {
        --blue: #4e73df;
        --indigo: #6610f2;
        --purple: #6f42c1;
        --pink: #e83e8c;
        --red: #e74a3b;
        --orange: #fd7e14;
        --yellow: #f6c23e;
        --green: #1cc88a;
        --teal: #20c9a6;
        --cyan: #36b9cc;
        --white: #fff;
        --gray: #858796;
        --gray-dark: #5a5c69;
        --primary: #4e73df;
        --secondary: #858796;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --light: #f8f9fc;
        --dark: #5a5c69;
        --breakpoint-xs: 0;
        --breakpoint-sm: 576px;
        --breakpoint-md: 768px;
        --breakpoint-lg: 992px;
        --breakpoint-xl: 1200px;
        --font-family-sans-serif: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--font-family-sans-serif);
        height: 100%;
        background-color: #f8f9fc;
    }

    #wrapper {
        display: flex;
        width: 100%;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background-color: var(--primary);
        transition: all 0.3s ease-in-out;
    }

    .sidebar .sidebar-brand {
        padding: 1.5rem;
        text-decoration: none;
        color: var(--white);
        font-weight: 800;
        transition: transform 0.5s ease;
    }

    .sidebar .sidebar-brand:hover {
        transform: scale(1.05);
        animation: bounce 0.5s;
    }

    .sidebar .sidebar-brand .sidebar-brand-icon {
        margin-right: 0.5rem;
    }

    .sidebar .sidebar-brand .sidebar-brand-text {
        display: flex;
        align-items: center;
    }

    .sidebar hr.sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin: 0.5rem 0;
        opacity: 0;
        animation: fadeIn 1s forwards 0.5s;
    }

    .sidebar .nav-item {
        padding: 0.5rem 1rem;
    }

    .sidebar .nav-item .nav-link {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.8);
        padding: 0.75rem;
        border-radius: 0.35rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .sidebar .nav-item .nav-link i {
        margin-right: 0.5rem;
        width: 1.25rem;
        text-align: center;
    }

    .sidebar .nav-item .nav-link:hover {
        color: var(--white);
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateX(5px);
        animation: slideRight 0.3s ease;
    }

    .sidebar .nav-item .nav-link.active {
        color: var(--white);
        background-color: rgba(255, 255, 255, 0.2);
    }

    .sidebar .sidebar-heading {
        padding: 0.75rem 1rem;
        font-size: 0.65rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        animation: fadeInUp 0.5s ease;
    }

    .sidebar .sidebar-card {
        padding: 1rem;
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 0.35rem;
        margin: 1rem;
        text-align: center;
        animation: zoomIn 0.5s ease;
    }

    .sidebar .sidebar-card img {
        max-width: 100%;
    }

    .sidebar .sidebar-card .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    #sidebarToggle {
        width: 2.5rem;
        height: 2.5rem;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    #sidebarToggle:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
        animation: spin 0.5s ease;
    }

    /* Topbar */
    .topbar {
        height: 4rem;
        padding: 0 1rem;
        background-color: var(--white);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .topbar #sidebarToggleTop {
        color: var(--gray-dark);
        padding: 0.5rem;
        transition: all 0.3s ease;
    }

    .topbar #sidebarToggleTop:hover {
        color: var(--primary);
        animation: pulse 0.5s;
    }

    .topbar .navbar-search {
        width: 25rem;
    }

    .topbar .navbar-search .form-control {
        border: 1px solid var(--gray-200);
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .topbar .navbar-search .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        animation: glow 0.5s ease;
    }

    .topbar .navbar-nav .nav-item .nav-link {
        padding: 0.5rem 1rem;
        color: var(--gray-600);
        transition: all 0.3s ease;
    }

    .topbar .navbar-nav .nav-item .nav-link:hover {
        color: var(--primary);
        animation: fadeIn 0.3s ease;
    }

    .topbar .navbar-nav .nav-item .dropdown-menu {
        min-width: 12rem;
        padding: 0.5rem;
        animation: slideDown 0.3s ease;
    }

    .topbar .navbar-nav .nav-item .dropdown-menu .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .topbar .navbar-nav .nav-item .dropdown-menu .dropdown-item:hover {
        background-color: var(--gray-100);
        animation: bounce 0.3s ease;
    }

    .topbar .navbar-nav .nav-item .img-profile {
        width: 2rem;
        height: 2rem;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideRight {
        from { transform: translateX(0); }
        to { transform: translateX(5px); }
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes zoomIn {
        from { opacity: 0; transform: scale(0.5); }
        to { opacity: 1; transform: scale(1); }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    @keyframes glow {
        0% { box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.25); }
        50% { box-shadow: 0 0 0 0.4rem rgba(78, 115, 223, 0.25); }
        100% { box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.25); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            margin-left: -250px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.active {
            margin-left: 0;
        }

        #content-wrapper {
            margin-left: 0;
        }
    }
</style>


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">LBTH <sup>2</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">


            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
              
            </div>

           <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Quản lý tài khoản</span>
                </a>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.events.index') }}">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Quản lý sự kiện</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.members.index') }}" 
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Quản lý thành viên</span>
                </a>
            </li>

             <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.clubs.index') }}" 
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Quản lý CLB</span>
                </a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Heading -->
                <div class="sidebar-heading">
                    Nội dung
            <!-- Nav Item - Tin tức và bài viết -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.posts.index') }}">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Tin tức & Bài viết</span></a>
            </li>

            <!-- Nav Item - Comment -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.comments.index') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Bình luận</span></a>
            </li>

             <!-- Nav Item - Lịch sử tham gia -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.history.index') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Lịch sử tham gia</span></a>
            </li>

             <!-- Nav Item - Tài liệu -->
            <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.documents.index') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tài liệu</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->
            <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="{{asset('img/undraw_rocket.svg')}}" alt="...">
                <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components, and more!</p>
                <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Upgrade to Pro!</a>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{{asset('img/undraw_profile_1.svg')}}"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{{asset('img/undraw_profile_2.svg')}}"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{{ asset('img/undraw_profile_3.svg')}}"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{{ asset(' https://source.unsplash.com/Mv9hjnEUHR4/60x60')}}"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->