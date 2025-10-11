<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLB Sinh viên - Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--dark);
        }

        @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        .navbar-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            padding: 1.2rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            animation: slideDown 0.5s ease;
        }

        .navbar-custom .navbar-brand {
            font-size: 1.6rem;
            font-weight: 800;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
        }

        .navbar-custom .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-custom i {
            animation: float 3s ease-in-out infinite;
        }

        .main-container {
            padding: 2.5rem 0;
        }

        .sidebar-menu {
            list-style: none;
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            height: fit-content;
            position: sticky;
            top: 120px;
            animation: slideUp 0.6s ease;
        }

        .sidebar-menu li {
            border-bottom: 1px solid var(--border);
        }

        .sidebar-menu .menu-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 1.1rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 550;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu .menu-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--primary);
            transform: scaleY(0);
            transition: transform 0.3s ease;
            transform-origin: center;
        }

        .sidebar-menu .menu-link:hover::before,
        .sidebar-menu .menu-link.active::before {
            transform: scaleY(1);
        }

        .sidebar-menu .menu-link:hover {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.1) 0%, transparent 100%);
            padding-left: 2rem;
        }

        .sidebar-menu .menu-link.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding-left: 2rem;
            font-weight: 700;
        }

        .sidebar-menu i {
            width: 22px;
            text-align: center;
            font-size: 1.15rem;
            animation: fadeIn 0.5s ease;
        }

        .content-area {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-xl);
            padding: 2.5rem;
            animation: slideUp 0.7s ease;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .tab-content.active {
            display: block;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 3px solid var(--primary);
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideUp 0.6s ease;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(129, 140, 248, 0.05) 100%);
            border-radius: 14px;
            padding: 1.8rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--primary);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.7s ease backwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2);
            border-left-color: var(--secondary);
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--primary);
            animation: fadeIn 1s ease;
        }

        .article-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
            animation: slideUp 0.6s ease backwards;
        }

        .article-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.2);
            border-color: var(--primary);
        }

        .article-card:nth-child(1) { animation-delay: 0.2s; }
        .article-card:nth-child(2) { animation-delay: 0.3s; }
        .article-card:nth-child(3) { animation-delay: 0.4s; }
        .article-card:nth-child(4) { animation-delay: 0.5s; }

        .article-image {
            width: 100%;
            height: 240px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4.5rem;
            position: relative;
            overflow: hidden;
        }

        .article-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.2), transparent 50%);
            animation: rotate 15s linear infinite;
        }

        .article-content {
            padding: 1.8rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .article-category {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 24px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
            width: fit-content;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
            animation: slideUp 0.5s ease;
        }

        .article-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.8rem;
            line-height: 1.4;
            animation: slideUp 0.6s ease;
        }

        .article-excerpt {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.2rem;
            flex-grow: 1;
            animation: slideUp 0.7s ease;
        }

        .article-meta {
            display: flex;
            justify-content: space-between;
            color: #9ca3af;
            font-size: 0.85rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-top: auto;
        }

        .article-meta-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .article-meta-item:hover {
            color: var(--primary);
            transform: translateX(2px);
        }

        .article-meta-item i {
            color: var(--primary);
        }

        .btn-new {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.8rem 1.8rem;
            border-radius: 10px;
            font-weight: 700;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }

        .btn-new::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-new:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-new:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--dark);
            position: relative;
            overflow: hidden;
        }

        .btn-icon::after {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--primary);
            transform: scale(0);
            transition: transform 0.3s ease;
            z-index: -1;
            border-radius: 10px;
        }

        .btn-icon:hover::after {
            transform: scale(1);
        }

        .btn-icon:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

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
                position: static;
                margin-bottom: 2rem;
            }

            .content-area {
                padding: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .article-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-lg">
            <a class="navbar-brand" href="#">
                <i class="fas fa-users-crown"></i>
                CLB Sinh viên
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-white fw-bold">👤 Xin chào, Bạn</span>
                <button class="btn btn-outline-light fw-bold">Đăng xuất</button>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="container-lg">
            <div class="row g-4">
                <div class="col-lg-3">
                    <ul class="sidebar-menu">
                        <li><a href="#" class="menu-link active" data-tab="news">
                            <i class="fas fa-newspaper"></i>
                            <span>Tin tức & Bài viết</span>
                        </a></li>
                        <li><a href="#" class="menu-link" data-tab="documents">
                            <i class="fas fa-file-pdf"></i>
                            <span>Tài liệu</span>
                        </a></li>
                        <li><a href="#" class="menu-link" data-tab="history">
                            <i class="fas fa-history"></i>
                            <span>Lịch sử tham gia</span>
                        </a></li>
                        <li><a href="#" class="menu-link" data-tab="comments">
                            <i class="fas fa-comments"></i>
                            <span>Bình luận</span>
                        </a></li>
                        <li><a href="#" class="menu-link" data-tab="events">
                            <i class="fas fa-calendar"></i>
                            <span>Quản lý sự kiện</span>
                        </a></li>
                        <li><a href="#" class="menu-link" data-tab="members">
                            <i class="fas fa-users"></i>
                            <span>Quản lý thành viên</span>
                        </a></li>
                    </ul>
                </div>

                <div class="col-lg-9">
                    <div id="news" class="tab-content content-area active">
                        <div class="section-header">
                            <h2><i class="fas fa-newspaper"></i> Tin tức & Bài viết</h2>
                            <button class="btn-new"><i class="fas fa-plus"></i> Bài viết mới</button>
                        </div>

                        <div class="stats-container">
                            <div class="stat-card">
                                <div class="stat-label">📰 Tổng bài viết</div>
                                <div class="stat-value">47</div>
                            </div>
                            <div class="stat-card" style="border-left-color: var(--secondary);">
                                <div class="stat-label">👀 Lượt xem</div>
                                <div class="stat-value" style="color: var(--secondary);">5.8K</div>
                            </div>
                            <div class="stat-card" style="border-left-color: var(--success);">
                                <div class="stat-label">❤️ Yêu thích</div>
                                <div class="stat-value" style="color: var(--success);">1.2K</div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6"><div class="article-card"><div class="article-image">📸</div><div class="article-content"><span class="article-category">CẬP NHẬT</span><h3 class="article-title">Hội thao CLB 2024 thành công</h3><p class="article-excerpt">Hơn 200 thành viên tham gia với các trò chơi vui nhộn và ý nghĩa...</p><div class="article-meta"><div class="article-meta-item"><i class="fas fa-calendar"></i> 15/10</div><div class="article-meta-item"><i class="fas fa-eye"></i> 450</div></div></div></div></div>
                            <div class="col-md-6"><div class="article-card"><div class="article-image">🎓</div><div class="article-content"><span class="article-category" style="background: var(--success);">THÔNG BÁO</span><h3 class="article-title">Tuyển dụng thành viên mới</h3><p class="article-excerpt">Câu lạc bộ tuyển những bạn đam mê để bổ sung vào đội ngũ...</p><div class="article-meta"><div class="article-meta-item"><i class="fas fa-calendar"></i> 20/09</div><div class="article-meta-item"><i class="fas fa-eye"></i> 832</div></div></div></div></div>
                            <div class="col-md-6"><div class="article-card"><div class="article-image">🏆</div><div class="article-content"><span class="article-category" style="background: var(--warning);">GIẢI THƯỞNG</span><h3 class="article-title">CLB đạt giải thưởng nổi bật</h3><p class="article-excerpt">Công nhận những hoạt động bổ ích suốt năm của CLB...</p><div class="article-meta"><div class="article-meta-item"><i class="fas fa-calendar"></i> 10/10</div><div class="article-meta-item"><i class="fas fa-eye"></i> 1.2K</div></div></div></div></div>
                            <div class="col-md-6"><div class="article-card"><div class="article-image">📚</div><div class="article-content"><span class="article-category" style="background: var(--danger);">SỰ KIỆN</span><h3 class="article-title">Khóa đào tạo lãnh đạo</h3><p class="article-excerpt">Chuyên gia từ công ty hàng đầu chia sẻ kinh nghiệm quý báu...</p><div class="article-meta"><div class="article-meta-item"><i class="fas fa-calendar"></i> 05/10</div><div class="article-meta-item"><i class="fas fa-eye"></i> 567</div></div></div></div></div>
                        </div>
                    </div>

                    <div id="documents" class="tab-content content-area">
                        <div class="section-header">
                            <h2><i class="fas fa-file-pdf"></i> Tài liệu</h2>
                            <button class="btn-new"><i class="fas fa-plus"></i> Tải lên</button>
                        </div>
                        <p class="text-center text-muted">Chưa có tài liệu nào. Hãy tải lên tài liệu đầu tiên!</p>
                    </div>

                    <div id="history" class="tab-content content-area">
                        <div class="section-header">
                            <h2><i class="fas fa-history"></i> Lịch sử tham gia</h2>
                        </div>
                        <p class="text-center text-muted">Chưa có dữ liệu lịch sử tham gia.</p>
                    </div>

                    <div id="comments" class="tab-content content-area">
                        <div class="section-header">
                            <h2><i class="fas fa-comments"></i> Bình luận & Thảo luận</h2>
                        </div>
                        <p class="text-center text-muted">Chưa có bình luận nào. Hãy bình luận đầu tiên!</p>
                    </div>

                    <!-- Events -->
                    <div id="events" class="tab-content content-area">
                        <div class="section-header">
                            <h2><i class="fas fa-calendar"></i> Quản lý Sự kiện</h2>
                            <button class="btn-new"><i class="fas fa-plus"></i> Thêm sự kiện</button>
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="table table-hover">
                                <thead style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tiêu đề</th>
                                        <th>Ngày diễn ra</th>
                                        <th>Địa điểm</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="animation: slideUp 0.6s ease backwards; animation-delay: 0.1s;">
                                        <td><strong>#1</strong></td>
                                        <td>Hội thảo kỹ năng giao tiếp</td>
                                        <td>28/10/2024</td>
                                        <td>Phòng 101</td>
                                        <td>
                                            <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-edit"></i></button>
                                            <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr style="animation: slideUp 0.6s ease backwards; animation-delay: 0.2s;">
                                        <td><strong>#2</strong></td>
                                        <td>Workshop lập trình web</td>
                                        <td>05/11/2024</td>
                                        <td>Phòng 205</td>
                                        <td>
                                            <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-edit"></i></button>
                                            <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr style="animation: slideUp 0.6s ease backwards; animation-delay: 0.3s;">
                                        <td><strong>#3</strong></td>
                                        <td>Picnic team building</td>
                                        <td>15/11/2024</td>
                                        <td>Công viên sinh thái</td>
                                        <td>
                                            <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-edit"></i></button>
                                            <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Members -->
                    <div id="members" class="tab-content content-area">
                        <div class="section-header">
                            <h2><i class="fas fa-users"></i> Quản lý Thành viên</h2>
                            <button class="btn-new"><i class="fas fa-plus"></i> Thêm thành viên</button>
                        </div>

                        <!-- Filter -->
                        <div style="background: var(--light); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; animation: slideUp 0.6s ease;">
                            <form action="{{ route('admin.members.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                <input type="hidden" name="activeTab" value="members">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tên thành viên</label>
                                    <input type="text" name="search_name" value="{{ $searchName ?? '' }}" placeholder="Nhập tên..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">CLB</label>
                                    <select name="club_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px;">
                                        <option value="">Tất cả CLB</option>
                                        @foreach (\App\Models\Club::all() as $club)
                                            <option value="{{ $club->id }}" {{ ($clubId ?? '') == $club->id ? 'selected' : '' }}>
                                                {{ $club->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Vai trò</label>
                                    <select name="role" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px;">
                                        <option value="">Tất cả</option>
                                        <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="member" {{ ($role ?? '') == 'member' ? 'selected' : '' }}>Member</option>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: flex-end;">
                                    <button class="btn-new" style="width: 100%;"><i class="fas fa-search"></i> Tìm kiếm</button>
                                </div>
                            </form>
                        </div>

                        <!-- Members Table -->
                        <div style="overflow-x: auto;">
                            <table class="table table-hover">
                                <thead style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>CLB</th>
                                        <th>Ngày tham gia</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clubs as $club)
                                        @forelse ($club->members as $member)
                                            <tr style="animation: slideUp 0.6s ease backwards;" :style="'animation-delay: ' . ($loop->index * 0.1) . 's'">
                                                <td><strong>#{{ $member->user->id }}</strong></td>
                                                <td>{{ $member->user->name }}</td>
                                                <td>{{ $member->user->email }}</td>
                                                <td>
                                                    <span style="background: {{ $member->role == 'admin' ? 'var(--primary)' : 'var(--success)' }}; color: white; padding: 0.35rem 0.75rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                                        {{ ucfirst($member->role) }}
                                                    </span>
                                                </td>
                                                <td>{{ $club->name }}</td>
                                                <td>
                                                    @if ($member->joined_at instanceof \Carbon\Carbon)
                                                        {{ $member->joined_at->format('d/m/Y') }}
                                                    @else
                                                        {{ $member->joined_at ?: 'Chưa xác định' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-edit"></i></button>
                                                    <button class="btn-icon" style="width: 32px; height: 32px;"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Không có thành viên trong CLB này.</td>
                                            </tr>
                                        @endforelse
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Không có câu lạc bộ nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('activeTab') || 'news';
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.querySelectorAll('.menu-link').forEach(link => link.classList.remove('active'));
            const tabElement = document.getElementById(activeTab);
            if (tabElement) {
                tabElement.classList.add('active');
            }
            const menuLink = document.querySelector(`.menu-link[data-tab="${activeTab}"]`);
            if (menuLink) {
                menuLink.classList.add('active');
            }
        });
    </script>
</body>
</html>