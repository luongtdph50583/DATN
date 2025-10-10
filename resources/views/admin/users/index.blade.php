@extends('admin.layouts.app')

@section('content')
<div class="modern-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Quản lý Người dùng</h1>
                <p class="page-subtitle">Quản lý và giám sát tất cả người dùng trong hệ thống</p>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <span class="stat-label">Tổng người dùng</span>
                    <span class="stat-value">{{ $users->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="search-filter-section">
        <form method="GET" action="{{ route('admin.users.index') }}" class="advanced-filter">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="name" class="filter-label">
                        <i class="icon-search"></i>
                        Tên người dùng
                    </label>
                    <input type="text" id="name" name="name" class="filter-input" placeholder="Nhập tên..." value="{{ request('name') }}">
                </div>

                <div class="filter-group">
                    <label for="email" class="filter-label">
                        <i class="icon-email"></i>
                        Email
                    </label>
                    <input type="text" id="email" name="email" class="filter-input" placeholder="Nhập email..." value="{{ request('email') }}">
                </div>

                <div class="filter-group">
                    <label for="role" class="filter-label">
                        <i class="icon-role"></i>
                        Vai trò
                    </label>
                    <select id="role" name="role" class="filter-select">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>
                            👑 Admin
                        </option>
                        <option value="club_manager" {{ request('role') == 'club_manager' ? 'selected' : '' }}>
                            🏢 Quản lý CLB
                        </option>
                        <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>
                            👤 Thành viên
                        </option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-search">
                        <span>🔍 Tìm kiếm</span>
                    </button>
                    @if(request('name') || request('email') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-reset">
                            ↻ Xóa bộ lọc
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table Section -->
    <div class="table-section">
        @if($users->count() > 0)
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="user-row" data-user-id="{{ $user->id }}">
                                <td class="cell-id">
                                    <span class="id-badge">#{{ $user->id }}</span>
                                </td>
                                <td class="cell-name">
                                    <div class="user-info">
                                        <div class="user-avatar">{{ substr($user->name, 0, 1) }}</div>
                                        <span class="user-name">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="cell-email">{{ $user->email }}</td>
                                <td class="cell-role">
                                    <span class="role-badge role-{{ $user->role }}">
                                        @switch($user->role)
                                            @case('admin')
                                                👑 Admin
                                                @break
                                            @case('club_manager')
                                                🏢 Quản lý CLB
                                                @break
                                            @default
                                                👤 Thành viên
                                        @endswitch
                                    </span>
                                </td>
                                <td class="cell-status">
                                    <span class="status-badge status-{{ $user->status }}">
                                        <span class="status-dot"></span>
                                        {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                                    </span>
                                </td>
                                <td class="cell-actions">
                                    <div class="action-buttons">
                                        <button class="btn btn-action btn-toggle-status" data-id="{{ $user->id }}"
                                                data-status="{{ $user->status }}" title="{{ $user->status === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                            {{ $user->status === 'active' ? '🔒' : '🔓' }}
                                        </button>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-action btn-edit" title="Chỉnh sửa">
                                            ✏️
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-delete" title="Xóa">
                                                🗑️
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
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Không có dữ liệu</h3>
                <p>Không tìm thấy người dùng phù hợp với bộ lọc của bạn</p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Xóa bộ lọc</a>
            </div>
        @endif
    </div>
</div>

<style>
    :root {
        --primary: #5b5eff;
        --primary-dark: #4a49d6;
        --primary-light: #8a87ff;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --dark: #1f2937;
        --light: #f3f4f6;
        --border: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .modern-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    /* Header Section */
    .header-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
        border-left: 4px solid var(--primary);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 0.95rem;
        color: #6b7280;
    }

    .header-stats {
        display: flex;
        gap: 1rem;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 150px;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
    }

    /* Search & Filter Section */
    .search-filter-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
    }

    .advanced-filter {
        width: 100%;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .filter-input,
    .filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.95rem;
        transition: var(--transition);
        background-color: white;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(91, 94, 255, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-search {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        flex: 1;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-reset {
        background: var(--light);
        color: var(--dark);
        border: 1px solid var(--border);
    }

    .btn-reset:hover {
        background: var(--border);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    /* Table Section */
    .table-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table thead {
        background: linear-gradient(135deg, var(--dark), #374151);
        color: white;
    }

    .modern-table thead th {
        padding: 1.25rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
    }

    .modern-table tbody tr {
        border-top: 1px solid var(--border);
        transition: var(--transition);
    }

    .modern-table tbody tr:hover {
        background-color: var(--light);
        box-shadow: inset 0 0 0 1px rgba(91, 94, 255, 0.1);
    }

    .modern-table td {
        padding: 1.25rem;
        font-size: 0.95rem;
    }

    .id-badge {
        background: var(--primary);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
    }

    .user-name {
        font-weight: 600;
        color: var(--dark);
    }

    .role-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .role-admin {
        background: #fef3c7;
        color: #92400e;
    }

    .role-club_manager {
        background: #dbeafe;
        color: #1e40af;
    }

    .role-member {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        animation: pulse 2s infinite;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-active .status-dot {
        background: var(--success);
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-inactive .status-dot {
        background: var(--danger);
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .cell-actions {
        display: flex;
        justify-content: flex-end;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: white;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-toggle-status {
        border-color: #fbbf24;
        background: #fffbeb;
    }

    .btn-toggle-status:hover {
        background: #fef3c7;
    }

    .btn-edit {
        border-color: #60a5fa;
        background: #eff6ff;
    }

    .btn-edit:hover {
        background: #dbeafe;
    }

    .btn-delete {
        border-color: #f87171;
        background: #fef2f2;
    }

    .btn-delete:hover {
        background: #fee2e2;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 2rem;
        text-align: center;
        border-top: 1px solid var(--border);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 1rem;
        }

        .header-stats {
            width: 100%;
        }

        .stat-card {
            flex: 1;
        }

        .filter-row {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-search,
        .btn-reset {
            width: 100%;
        }

        .modern-table {
            font-size: 0.85rem;
        }

        .modern-table th,
        .modern-table td {
            padding: 0.75rem;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }
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