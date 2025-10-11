<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLB Sinh viên</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
        }
        .navbar-custom {
            background-color: #343a40;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu .menu-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
        }
        .sidebar-menu .menu-link.active, .sidebar-menu .menu-link:hover {
            background-color: #495057;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .stat-card {
            border-left: 4px solid var(--primary);
            padding: 15px;
            background: #fff;
            border-radius: 5px;
        }
        .article-card, .document-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
        .btn-new {
            background-color: var(--primary);
            color: #fff;
            border: none;
        }
        .btn-icon {
            background: none;
            border: none;
            color: #495057;
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
                                        @forelse ($events as $event)
                                            <tr>
                                                <td>{{ $event->id }}</td>
                                                <td>{{ $event->title }}</td>
                                                <td>{{ $event->event_date }}</td>
                                                <td>{{ $event->location }}</td>
                                                <td>
                                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-warning">Sửa</a>
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
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Hủy</a>
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
                } else if (tab === 'events-edit' && '{{ Auth::check() && Auth::user()->role === 'admin' ? 'true' : 'false' }}' === 'true' && '{{ $event->id ?? '' }}') {
                    window.location.href = '{{ route('admin.events.edit', $event->id ?? '') }}?activeTab=events-edit';
                }
            });
        });

        // Kích hoạt tab dựa trên URL
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('activeTab') || 'news';
        document.getElementById(activeTab).classList.add('active');
        document.querySelector(`.menu-link[data-tab="${activeTab}"]`).classList.add('active');
    </script>
</body>
</html>