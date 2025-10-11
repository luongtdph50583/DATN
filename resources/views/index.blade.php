<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLB Sinh viên</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #ec4899;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #ffc107;
            --dark: #1f2937;
            --light: #f9fafb;
            --border: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--dark);
        }

        /* Navbar Custom */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: var(--shadow-lg);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-custom .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }

        .navbar-custom .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-custom .text-white {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .navbar-custom .btn-outline-light {
            border: 2px solid white;
            transition: all 0.3s ease;
        }

        .navbar-custom .btn-outline-light:hover {
            background: white;
            color: var(--primary) !important;
        }

        /* Main Container */
        .main-container {
            padding: 2rem 0;
            min-height: 100vh;
        }

        /* Sidebar Menu */
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .sidebar-menu li {
            border-bottom: 1px solid var(--border);
        }

        .sidebar-menu li:last-child {
            border-bottom: none;
        }

        .sidebar-menu .menu-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            cursor: pointer;
        }

        .sidebar-menu .menu-link:hover {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.1) 0%, rgba(129, 140, 248, 0.05) 100%);
            color: var(--primary);
            padding-left: 2rem;
            border-left: 3px solid var(--primary);
        }

        .sidebar-menu .menu-link.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding-left: 2rem;
            border-left: 3px solid white;
        }

        .sidebar-menu i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* Content Area */
        .content-area {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            padding: 2rem;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        /* Stats Container */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* Article Cards */
        .article-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .article-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }

        .article-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }

        .article-content {
            padding: 1.5rem;
        }

        .article-category {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            letter-spacing: 0.5px;
        }

        .article-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .article-excerpt {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .article-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #9ca3af;
            font-size: 0.85rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .article-meta-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .article-meta-item i {
            color: var(--primary);
        }

        /* Document Cards */
        .document-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s ease;
        }

        .document-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateX(6px);
            border-color: var(--primary);
        }

        .document-icon {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .document-info {
            flex: 1;
            min-width: 0;
        }

        .document-name {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            word-break: break-word;
            font-size: 1rem;
        }

        .document-meta {
            display: flex;
            gap: 1.5rem;
            font-size: 0.85rem;
            color: #9ca3af;
            flex-wrap: wrap;
        }

        .document-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-new {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .btn-new:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
            text-decoration: none;
        }

        .btn-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--dark);
        }

        .btn-icon:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding: 2rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .timeline-item {
            padding-left: 80px;
            padding-bottom: 2rem;
            position: relative;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 5px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .timeline-content {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(129, 140, 248, 0.05) 100%);
            border-left: 3px solid var(--primary);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
        }

        .timeline-date {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.35rem;
            font-size: 1rem;
        }

        .timeline-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Comments Section */
        .comments-section {
            margin-top: 2rem;
        }

        .comment-form {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(129, 140, 248, 0.05) 100%);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
        }

        .comment-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            resize: vertical;
            transition: all 0.3s ease;
            min-height: 100px;
            font-size: 0.95rem;
        }

        .comment-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-submit-comment {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit-comment:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .comment-item {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
        }

        .comment-item:hover {
            box-shadow: var(--shadow-md);
        }

        .comment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .comment-author {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }

        .comment-author-name {
            font-weight: 700;
            color: var(--dark);
        }

        .comment-time {
            color: #9ca3af;
            font-size: 0.85rem;
        }

        .comment-text {
            color: #374151;
            line-height: 1.6;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .comment-actions {
            display: flex;
            gap: 1.5rem;
            font-size: 0.85rem;
        }

        .comment-action-btn {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 500;
        }

        .comment-action-btn:hover {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-new {
                width: 100%;
                justify-content: center;
            }

            .sidebar-menu {
                margin-bottom: 2rem;
                position: static;
            }

            .timeline::before {
                left: 12px;
            }

            .timeline-item {
                padding-left: 50px;
            }

            .timeline-marker {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .document-card {
                flex-direction: column;
                text-align: center;
            }

            .document-actions {
                justify-content: center;
                width: 100%;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .article-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .content-area {
                padding: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-lg">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-users-crown"></i>
                CLB Sinh viên
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center gap-3">
                    @auth
                        <span class="text-white">👤 Xin chào, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-light">Đăng xuất</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light">Đăng nhập</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-light">Đăng ký</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <div class="container-lg">
            <div class="row g-4">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <ul class="sidebar-menu">
                        <li><a href="#" class="menu-link {{ request()->query('activeTab') == 'news' || !request()->query('activeTab') ? 'active' : '' }}" data-tab="news">
                            <i class="fas fa-newspaper"></i>
                            <span>Tin tức & Bài viết</span>
                        </a></li>
                        <li><a href="#" class="menu-link {{ request()->query('activeTab') == 'documents' ? 'active' : '' }}" data-tab="documents">
                            <i class="fas fa-file-pdf"></i>
                            <span>Tài liệu</span>
                        </a></li>
                        <li><a href="#" class="menu-link {{ request()->query('activeTab') == 'history' ? 'active' : '' }}" data-tab="history">
                            <i class="fas fa-history"></i>
                            <span>Lịch sử tham gia</span>
                        </a></li>
                        <li><a href="#" class="menu-link {{ request()->query('activeTab') == 'comments' ? 'active' : '' }}" data-tab="comments">
                            <i class="fas fa-comments"></i>
                            <span>Bình luận & Thảo luận</span>
                        </a></li>
                        @auth
                            @if (Auth::check() && Auth::user()->role === 'admin')
                                <li><a href="{{ route('admin.events.index') }}" class="menu-link {{ request()->query('activeTab') == 'events' ? 'active' : '' }}" data-tab="events">
                                    <i class="fas fa-wrench"></i>
                                    <span>Events</span>
                                </a></li>
                            @endif
                        @endauth
                    </ul>
                </div>

                <!-- Content Area -->
                <div class="col-lg-9 content-area">
                    <!-- Tin tức & Bài viết -->
                    <div id="news" class="tab-content {{ request()->query('activeTab') == 'news' || !request()->query('activeTab') ? 'active' : '' }}">
                        <div class="section-header">
                            <h2><i class="fas fa-newspaper"></i> Tin tức & Bài viết</h2>
                            <button class="btn-new"><i class="fas fa-plus"></i> Bài viết mới</button>
                        </div>
                        <div class="stats-container">
                            <div class="stat-card">
                                <div class="stat-label">📰 Tổng bài viết</div>
                                <div class="stat-value">24</div>
                            </div>
                            <div class="stat-card" style="border-left-color: var(--secondary);">
                                <div class="stat-label">👀 Lượt xem</div>
                                <div class="stat-value" style="color: var(--secondary);">1.2K</div>
                            </div>
                            <div class="stat-card" style="border-left-color: var(--success);">
                                <div class="stat-label">❤️ Lượt yêu thích</div>
                                <div class="stat-value" style="color: var(--success);">342</div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="article-card">
                                    <div class="article-image">📸</div>
                                    <div class="article-content">
                                        <span class="article-category">CẬP NHẬT</span>
                                        <h3 class="article-title">Tổ chức thành công hội thao CLB 2024</h3>
                                        <p class="article-excerpt">Hội thao lần này quy tụ hơn 200 thành viên tham gia với nhiều trò chơi đầy vui nhộn và ý nghĩa...</p>
                                        <div class="article-meta">
                                            <div class="article-meta-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>15/10/2024</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-user"></i>
                                                <span>Admin</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-eye"></i>
                                                <span>450</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="article-card">
                                    <div class="article-image">🎓</div>
                                    <div class="article-content">
                                        <span class="article-category" style="background: var(--success);">THÔNG BÁO</span>
                                        <h3 class="article-title">Tuyển dụng thành viên mới cho năm học mới</h3>
                                        <p class="article-excerpt">Câu lạc bộ đang tuyển dụng những bạn đam mê, năng động để bổ sung vào đội ngũ...</p>
                                        <div class="article-meta">
                                            <div class="article-meta-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>20/09/2024</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-user"></i>
                                                <span>Quản lý</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-eye"></i>
                                                <span>832</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="article-card">
                                    <div class="article-image">🏆</div>
                                    <div class="article-content">
                                        <span class="article-category" style="background: var(--warning);">GIẢI THƯỞNG</span>
                                        <h3 class="article-title">CLB đạt giải thưởng Hoạt động nổi bật</h3>
                                        <p class="article-excerpt">Với những hoạt động bổ ích và sáng tạo suốt năm, CLB đã nhận được công nhận từ nhà trường...</p>
                                        <div class="article-meta">
                                            <div class="article-meta-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>10/10/2024</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-user"></i>
                                                <span>Admin</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-eye"></i>
                                                <span>1.2K</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="article-card">
                                    <div class="article-image">📚</div>
                                    <div class="article-content">
                                        <span class="article-category" style="background: var(--danger);">SỰ KIỆN</span>
                                        <h3 class="article-title">Khóa đào tạo kỹ năng lãnh đạo</h3>
                                        <p class="article-excerpt">Buổi đào tạo sẽ diễn ra vào cuối tuần với các chuyên gia từ công ty hàng đầu...</p>
                                        <div class="article-meta">
                                            <div class="article-meta-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>05/10/2024</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-user"></i>
                                                <span>Quản lý</span>
                                            </div>
                                            <div class="article-meta-item">
                                                <i class="fas fa-eye"></i>
                                                <span>567</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tài liệu -->
                    <div id="documents" class="tab-content {{ request()->query('activeTab') == 'documents' ? 'active' : '' }}">
                        <div class="section-header">
                            <h2><i class="fas fa-file-pdf"></i> Tài liệu</h2>
                            <button class="btn-new"><i class="fas fa-plus"></i> Tải lên tài liệu</button>
                        </div>
                        <div class="document-card">
                            <div class="document-icon">📄</div>
                            <div class="document-info">
                                <div class="document-name">Quy chế hoạt động CLB 2024.pdf</div>
                                <div class="document-meta">
                                    <span><i class="fas fa-file-pdf"></i> PDF • 2.5 MB</span>
                                    <span><i class="fas fa-calendar"></i> 15/09/2024</span>
                                    <span><i class="fas fa-download"></i> 234 lượt tải</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="btn-icon" title="Tải xuống"><i class="fas fa-download"></i></button>
                                <button class="btn-icon" title="Xem trước"><i class="fas fa-eye"></i></button>
                                <button class="btn-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="document-card">
                            <div class="document-icon">📊</div>
                            <div class="document-info">
                                <div class="document-name">Báo cáo tài chính năm học 2023-2024.xlsx</div>
                                <div class="document-meta">
                                    <span><i class="fas fa-file"></i> EXCEL • 1.8 MB</span>
                                    <span><i class="fas fa-calendar"></i> 20/09/2024</span>
                                    <span><i class="fas fa-download"></i> 156 lượt tải</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="btn-icon" title="Tải xuống"><i class="fas fa-download"></i></button>
                                <button class="btn-icon" title="Xem trước"><i class="fas fa-eye"></i></button>
                                <button class="btn-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="document-card">
                            <div class="document-icon">🖼️</div>
                            <div class="document-info">
                                <div class="document-name">Kế hoạch hoạt động Q4 2024.docx</div>
                                <div class="document-meta">
                                    <span><i class="fas fa-file"></i> WORD • 890 KB</span>
                                    <span><i class="fas fa-calendar"></i> 10/10/2024</span>
                                    <span><i class="fas fa-download"></i> 89 lượt tải</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="btn-icon" title="Tải xuống"><i class="fas fa-download"></i></button>
                                <button class="btn-icon" title="Xem trước"><i class="fas fa-eye"></i></button>
                                <button class="btn-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="document-card">
                            <div class="document-icon">📷</div>
                            <div class="document-info">
                                <div class="document-name">Album ảnh hoạt động tháng 10.zip</div>
                                <div class="document-meta">
                                    <span><i class="fas fa-file"></i> ZIP • 15.2 MB</span>
                                    <span><i class="fas fa-calendar"></i> 12/10/2024</span>
                                    <span><i class="fas fa-download"></i> 45 lượt tải</span>
                                </div>
                            </div>
                            <div class="document-actions">
                                <button class="btn-icon" title="Tải xuống"><i class="fas fa-download"></i></button>
                                <button class="btn-icon" title="Xem trước"><i class="fas fa-eye"></i></button>
                                <button class="btn-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Lịch sử tham gia -->
                    <div id="history" class="tab-content {{ request()->query('activeTab') == 'history' ? 'active' : '' }}">
                        <div class="section-header">
                            <h2><i class="fas fa-history"></i> Lịch sử tham gia</h2>
                        </div>
                        <p>Chưa có dữ liệu lịch sử tham gia.</p>
                    </div>

                    <!-- Bình luận & Thảo luận -->
                    <div id="comments" class="tab-content {{ request()->query('activeTab') == 'comments' ? 'active' : '' }}">
                        <div class="section-header">
                            <h2><i class="fas fa-comments"></i> Bình luận & Thảo luận</h2>
                        </div>
                        <p>Chưa có bình luận hoặc thảo luận nào.</p>
                    </div>

                    <!-- Events (Chỉ hiển thị cho Admin) -->
                    <div id="events" class="tab-content {{ request()->query('activeTab') == 'events' ? 'active' : '' }}">
                        @auth
                            @if (Auth::check() && Auth::user()->role === 'admin')
                                <div class="section-header">
                                    <h2><i class="fas fa-wrench"></i> Quản lý Sự kiện</h2>
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif
                                    @if (session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
                                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary mb-3">Thêm Sự kiện mới</a>
                                </div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tiêu đề</th>
                                            <th>Ngày diễn ra</th>
                                            <th>Địa điểm</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $events = $events ?? [];
                                        @endphp
                                        @forelse ($events as $event)
                                            <tr>
                                                <td>{{ $event->id }}</td>
                                                <td>{{ $event->title }}</td>
                                                <td>{{ $event->event_date }}</td>
                                                <td>{{ $event->location }}</td>
                                                <td>
                                                    <a href="{{ route('admin.events.edit', $event->id) }}?activeTab=events-edit" class="btn btn-sm btn-warning">Sửa</a>
                                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">Không có sự kiện nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            @else
                                <p>Bạn không có quyền truy cập vào phần quản lý sự kiện.</p>
                            @endif
                        @else
                            <p>Vui lòng đăng nhập để xem phần quản lý sự kiện.</p>
                        @endauth
                    </div>

                    <!-- Form tạo mới sự kiện -->
                    <div id="events-create" class="tab-content {{ request()->query('activeTab') == 'events-create' ? 'active' : '' }}">
                        @auth
                            @if (Auth::check() && Auth::user()->role === 'admin')
                                <h1>Thêm Sự kiện mới</h1>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form action="{{ route('admin.events.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Tiêu đề</label>
                                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" class="form-control" id="description">{{ old('description') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="event_date" class="form-label">Ngày diễn ra</label>
                                        <input type="date" name="event_date" class="form-control" id="event_date" value="{{ old('event_date') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Địa điểm</label>
                                        <input type="text" name="location" class="form-control" id="location" value="{{ old('location') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Lưu</button>
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Hủy</a>
                                </form>
                            @else
                                <p>Bạn không có quyền truy cập vào phần quản lý sự kiện.</p>
                            @endif
                        @else
                            <p>Vui lòng đăng nhập để xem phần quản lý sự kiện.</p>
                        @endauth
                    </div>

                    <!-- Form chỉnh sửa sự kiện -->
                    <div id="events-edit" class="tab-content {{ request()->query('activeTab') == 'events-edit' ? 'active' : '' }}">
                        @auth
                            @if (Auth::check() && Auth::user()->role === 'admin')
                                <h1>Sửa Sự kiện</h1>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form action="{{ route('admin.events.update', $event->id ?? '') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Tiêu đề</label>
                                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $event->title ?? '') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" class="form-control" id="description">{{ old('description', $event->description ?? '') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="event_date" class="form-label">Ngày diễn ra</label>
                                        <input type="date" name="event_date" class="form-control" id="event_date" value="{{ old('event_date', $event->event_date ?? '') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Địa điểm</label>
                                        <input type="text" name="location" class="form-control" id="location" value="{{ old('location', $event->location ?? '') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    <a href="{{ route('admin.events.index') }}?activeTab=events" class="btn btn-secondary">Hủy</a>
                                </form>
                            @else
                                <p>Bạn không có quyền truy cập vào phần quản lý sự kiện.</p>
                            @endif
                        @else
                            <p>Vui lòng đăng nhập để xem phần quản lý sự kiện.</p>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.menu-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tab = this.getAttribute('data-tab');
                const url = new URL(window.location);
                url.searchParams.set('activeTab', tab);
                window.history.pushState({}, '', url);

                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                document.querySelectorAll('.menu-link').forEach(link => link.classList.remove('active'));
                document.getElementById(tab).classList.add('active');
                this.classList.add('active');

                if (tab === 'events' && '{{ Auth::check() && Auth::user()->role === 'admin' ? 'true' : 'false' }}' === 'true') {
                    window.location.href = '{{ route('admin.events.index') }}?activeTab=events';
                } else if (tab === 'events-create' && '{{ Auth::check() && Auth::user()->role === 'admin' ? 'true' : 'false' }}' === 'true') {
                    window.location.href = '{{ route('admin.events.create') }}?activeTab=events-create';
                }
            });
        });

        // Kích hoạt tab dựa trên URL khi load trang
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('activeTab') || 'news';
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.querySelectorAll('.menu-link').forEach(link => link.classList.remove('active'));
            document.getElementById(activeTab).classList.add('active');
            document.querySelector(`.menu-link[data-tab="${activeTab}"]`)?.classList.add('active');

            // Xử lý riêng cho events-edit
            if (activeTab === 'events-edit' && '{{ Auth::check() && Auth::user()->role === 'admin' ? 'true' : 'false' }}' === 'true' && '{{ $event->id ?? '' }}') {
                document.getElementById('events-edit').classList.add('active');
                // Không đổi URL vì đã được controller xử lý
            }
        });
    </script>
</body>

</html>