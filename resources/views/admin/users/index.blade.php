@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Quản lý Người dùng</h1>
                <p class="page-subtitle">Quản lý tài khoản hệ thống</p>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <span class="stat-label">Tổng người dùng</span>
                    <span class="stat-value">{{ $users->total() }}</span>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-filter-section">
        <form method="GET" action="{{ route('admin.users.index') }}" class="advanced-filter">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="name" class="filter-label">Tên</label>
                    <input type="text" id="name" name="name" class="filter-input" placeholder="Nhập tên..." value="{{ request('name') }}">
                </div>

                <div class="filter-group">
                    <label for="email" class="filter-label">Email</label>
                    <input type="text" id="email" name="email" class="filter-input" placeholder="Nhập email..." value="{{ request('email') }}">
                </div>

                <div class="filter-group">
                    <label for="role" class="filter-label">Vai trò</label>
                    <select id="role" name="role" class="filter-select">
                        <option value="">Tất cả</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="club_manager" {{ request('role') == 'club_manager' ? 'selected' : '' }}>Quản lý CLB</option>
                        <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Thành viên</option>
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-search">Tìm kiếm</button>
                    @if(request()->filled(['name', 'email', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-reset">Xóa bộ lọc</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-section">
        @if($users->count() > 0)
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td><span class="id-badge">#{{ $user->id }}</span></td>

                                <!-- Avatar -->
                                <td class="text-center">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="user-avatar-img">
                                    @else
                                        <div class="user-avatar-fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    @endif
                                </td>

                                <!-- Tên -->
                                <td class="fw-bold">{{ $user->name }}</td>

                                <!-- Email -->
                                <td><a href="mailto:{{ $user->email }}" class="text-primary">{{ $user->email }}</a></td>

                                <!-- Vai trò -->
                                <td>
                                    <span class="role-badge role-{{ $user->role }}">
                                        @switch($user->role)
                                            @case('admin') Admin @break
                                            @case('club_manager') Quản lý CLB @break
                                            @default Thành viên
                                        @endswitch
                                    </span>
                                </td>

                                <!-- Trạng thái -->
                                <td>
                                    <span class="status-badge status-{{ $user->status }}">
                                        <span class="status-dot"></span>
                                        {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                                    </span>
                                </td>

                                <!-- Ngày tạo -->
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>

                                <!-- Hành động -->
<td>
    <div class="action-buttons">
        <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning" 
                    title="{{ $user->status === 'active' ? '🔒 Khóa' : '🔓 Mở' }}">
                <i class="fas fa-{{ $user->status === 'active' ? 'lock' : 'lock-open' }}">🔒</i>
            </button>
        </form>

        <a href="{{ route('admin.users.show', $user) }}" 
           class="btn btn-sm btn-info" 
           title="Xem chi tiết">
            <i class="fas fa-eye">👁️</i>
        </a>

        <a href="{{ route('admin.users.edit', $user) }}" 
           class="btn btn-sm btn-warning" 
           title="Chỉnh sửa">
            <i class="fas fa-edit">✏️</i>
        </a>

        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
              style="display:inline;"
              onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" title="Xóa tài khoản">
                <i class="fas fa-trash-alt">🗑️</i>
            </button>
        </form>
    </div>
</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="pagination-wrapper">
                    {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">Không có người dùng</div>
                <h3>Không tìm thấy dữ liệu</h3>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Xóa bộ lọc</a>
            </div>
        @endif
    </div>
</div>

<style>
:root {
    --primary: #6366f1; --primary-dark: #4f46e5; --secondary: #8b5cf6;
    --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --info: #06b6d4;
    --dark: #0f172a; --gray-100: #f1f5f9; --gray-200: #e2e8f0; --gray-600: #475569;
    --shadow: 0 4px 6px -1px rgba(0,0,0,0.1); --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
    --transition: all 0.3s ease;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

/* Layout */
.modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 1.5rem; }
.header-section, .search-filter-section, .table-section { background: white; border-radius: 12px; box-shadow: var(--shadow-lg); margin-bottom: 1.5rem; }

/* Header */
.header-section { padding: 1.5rem; border-left: 4px solid var(--primary); }
.header-content { display: flex; justify-content: space-between; align-items: center; }
.page-title { font-size: 1.75rem; font-weight: 700; color: var(--dark); margin-bottom: 0.25rem; }
.page-subtitle { font-size: 0.9rem; color: var(--gray-600); }
.header-stats { display: flex; align-items: center; gap: 1rem; }

.stat-card {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white; padding: 0.875rem 1.5rem; border-radius: 10px;
    display: flex; flex-direction: column; align-items: center; min-width: 140px;
    box-shadow: var(--shadow); transition: var(--transition);
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
.stat-label { font-size: 0.8rem; opacity: 0.95; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-value { font-size: 2rem; font-weight: 800; }

/* Filter */
.search-filter-section { padding: 1.25rem; }
.filter-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end; }
.filter-group { display: flex; flex-direction: column; }
.filter-label { font-size: 0.85rem; font-weight: 600; color: var(--dark); margin-bottom: 0.4rem; }

.filter-input, .filter-select {
    padding: 0.7rem 0.9rem; border: 2px solid var(--gray-200); border-radius: 8px;
    font-size: 0.9rem; transition: var(--transition); background: var(--gray-100);
}
.filter-input:focus, .filter-select:focus {
    outline: none; border-color: var(--primary); background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.filter-actions { display: flex; gap: 0.5rem; }

/* Buttons */
.btn {
    padding: 0.7rem 1.25rem; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600;
    cursor: pointer; transition: var(--transition); display: inline-flex;
    align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none;
}
.btn-search { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; flex: 1; }
.btn-search:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.btn-reset { background: white; color: var(--gray-600); border: 2px solid var(--gray-200); }
.btn-reset:hover { background: var(--gray-100); }
.btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; box-shadow: var(--shadow); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }

/* Table */
.table-section { overflow: hidden; }
.table-wrapper { overflow-x: auto; width: 100%; }
.modern-table { width: 100%; border-collapse: collapse; table-layout: auto; }

.modern-table thead {
    background: linear-gradient(135deg, var(--dark), #334155); color: white;
}
.modern-table thead th {
    padding: 1rem 0.75rem; text-align: left; font-weight: 700; font-size: 0.8rem;
    letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap;
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--gray-200); transition: var(--transition);
    animation: fadeIn 0.3s ease-out backwards;
}
.modern-table tbody tr:nth-child(1) { animation-delay: 0.05s; }
.modern-table tbody tr:nth-child(2) { animation-delay: 0.1s; }
.modern-table tbody tr:nth-child(3) { animation-delay: 0.15s; }
.modern-table tbody tr:nth-child(4) { animation-delay: 0.2s; }
.modern-table tbody tr:nth-child(5) { animation-delay: 0.25s; }

.modern-table tbody tr:hover {
    background: var(--gray-100); box-shadow: inset 3px 0 0 var(--primary);
}

.modern-table td { padding: 0.875rem 0.75rem; font-size: 0.875rem; color: var(--gray-600); }

/* Column Widths */
.modern-table th:nth-child(1), .modern-table td:nth-child(1) { width: 5%; }
.modern-table th:nth-child(2), .modern-table td:nth-child(2) { width: 6%; text-align: center; }
.modern-table th:nth-child(3), .modern-table td:nth-child(3) { width: 15%; }
.modern-table th:nth-child(4), .modern-table td:nth-child(4) { width: 20%; }
.modern-table th:nth-child(5), .modern-table td:nth-child(5) { width: 12%; }
.modern-table th:nth-child(6), .modern-table td:nth-child(6) { width: 12%; }
.modern-table th:nth-child(7), .modern-table td:nth-child(7) { width: 10%; }
.modern-table th:nth-child(8), .modern-table td:nth-child(8) { width: 20%; }

.id-badge {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white; padding: 0.35rem 0.7rem; border-radius: 6px;
    font-weight: 700; font-size: 0.8rem; display: inline-block;
}

/* Avatar */
.user-avatar-img, .user-avatar-fallback {
    width: 36px; height: 36px; border-radius: 10px; object-fit: cover;
    border: 2px solid var(--gray-200); transition: var(--transition);
}
.user-avatar-fallback {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white; font-weight: 700; display: flex;
    align-items: center; justify-content: center; font-size: 0.9rem;
}
.user-avatar-img:hover, .user-avatar-fallback:hover {
    transform: scale(1.15) rotate(5deg); box-shadow: var(--shadow);
}

.fw-bold { font-weight: 700; color: var(--dark); }
.text-primary { color: var(--primary); text-decoration: none; font-weight: 600; }
.text-primary:hover { color: var(--primary-dark); text-decoration: underline; }

/* Badges */
.role-badge, .status-badge {
    padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 0.35rem;
    text-transform: uppercase; letter-spacing: 0.3px; transition: var(--transition);
}
.role-badge:hover, .status-badge:hover { transform: translateY(-2px); }

.role-admin { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; border: 1px solid #fbbf24; }
.role-club_manager { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; border: 1px solid #60a5fa; }
.role-member { background: linear-gradient(135deg, #f3f4f6, #e5e7eb); color: #374151; border: 1px solid #d1d5db; }

.status-active { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; border: 1px solid var(--success); }
.status-inactive { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; border: 1px solid var(--danger); }

.status-dot {
    width: 8px; height: 8px; border-radius: 50%; animation: pulse 2s infinite;
}
.status-active .status-dot { background: var(--success); box-shadow: 0 0 5px var(--success); }
.status-inactive .status-dot { background: var(--danger); box-shadow: 0 0 5px var(--danger); }

/* Action Buttons */
.action-buttons { display: flex; gap: 0.4rem; justify-content: center; }

.btn-sm {
    width: 36px; height: 36px; padding: 0; font-size: 0.95rem; border-radius: 8px;
    border: 2px solid transparent; cursor: pointer; transition: var(--transition);
    display: flex; align-items: center; justify-content: center; position: relative;
    overflow: hidden;
}
.btn-sm i { font-size: 1rem; position: relative; z-index: 1; }

.btn-sm::before {
    content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0;
    border-radius: 50%; background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%); transition: width 0.4s, height 0.4s;
}
.btn-sm:hover::before { width: 100px; height: 100px; }
.btn-sm:active { transform: scale(0.95); }

.btn-outline-warning { background: #fffbeb; color: #92400e; border-color: #fbbf24; }
.btn-outline-warning:hover { background: linear-gradient(135deg, #fef3c7, #fde68a); transform: translateY(-2px) rotate(5deg); }

.btn-info { background: linear-gradient(135deg, var(--info), #0891b2); color: white; }
.btn-info:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 6px 15px rgba(6, 182, 212, 0.4); }

.btn-warning { background: linear-gradient(135deg, var(--warning), #d97706); color: white; }
.btn-warning:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 6px 15px rgba(245, 158, 11, 0.4); }

.btn-danger { background: linear-gradient(135deg, var(--danger), #dc2626); color: white; }
.btn-danger:hover { transform: translateY(-2px) rotate(-5deg); box-shadow: 0 6px 15px rgba(239, 68, 68, 0.4); }

/* Empty State */
.empty-state { text-align: center; padding: 3rem 2rem; }
.empty-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-state h3 { font-size: 1.5rem; font-weight: 700; color: var(--dark); margin-bottom: 0.5rem; }

/* Pagination */
.pagination-wrapper { padding: 1.5rem; text-align: center; border-top: 2px solid var(--gray-200); background: var(--gray-100); }

/* Animations */
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(1.1); } }

/* Responsive */
@media (max-width: 1200px) {
    .modern-table { font-size: 0.8rem; }
    .modern-table th, .modern-table td { padding: 0.7rem 0.5rem; }
    .user-avatar-img, .user-avatar-fallback { width: 32px; height: 32px; }
    .btn-sm { width: 32px; height: 32px; font-size: 0.85rem; }
}

@media (max-width: 768px) {
    .header-content { flex-direction: column; gap: 1rem; text-align: center; }
    .header-stats { width: 100%; justify-content: center; }
    .filter-row { grid-template-columns: 1fr; }
    .table-wrapper { overflow-x: scroll; }
    .modern-table { min-width: 900px; }
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-toggle-status').forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                const currentStatus = this.getAttribute('data-status');

                fetch(`{{ route('admin.users.toggleStatus', ['user' => ':id']) }}`.replace(':id', userId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status: currentStatus }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.querySelector(`[data-user-id="${userId}"]`);
                        const statusBadge = row.querySelector('.status-badge');
                        const statusDot = statusBadge.querySelector('.status-dot');
                        const toggleBtn = row.querySelector('.btn-toggle-status');

                        statusBadge.textContent = data.status === 'active' ? '🟢 Hoạt động' : '🔴 Khóa';
                        statusBadge.className = `status-badge status-${data.status}`;
                        statusBadge.innerHTML = `<span class="status-dot"></span>${data.status === 'active' ? '🟢 Hoạt động' : '🔴 Khóa'}`;
                        toggleBtn.textContent = data.status === 'active' ? '🔒' : '🔓';
                        toggleBtn.setAttribute('data-status', data.status);

                        alert(data.message);
                    } else {
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi gửi yêu cầu.');
                });
            });
        });
    });
</script>

@endsection