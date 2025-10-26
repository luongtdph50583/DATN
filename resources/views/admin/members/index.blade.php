@extends('admin.layouts.app')

@section('title', 'Quản lý Thành viên')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Quản lý Thành viên</h1>
                <p class="page-subtitle">Quản lý thông tin chi tiết thành viên hệ thống</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                    Thêm thành viên
                </a>
                <form action="{{ route('admin.members.export.excel') }}" method="GET" class="d-inline ms-2">
                    @csrf
                    <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                    <input type="hidden" name="search_email" value="{{ request('search_email') }}">
                    <input type="hidden" name="search_major" value="{{ request('search_major') }}">
                    <button type="submit" class="btn btn-success">
                        Xuất Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- FILTER SECTION -->
    <div class="filter-section mb-4">
        <form id="filterForm" action="{{ route('admin.members.index') }}" method="GET" class="row g-3 align-items-end">
            <!-- Tìm theo tên -->
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Tên</label>
                <input type="text" name="search_name" class="form-control" 
                       placeholder="Nhập tên..." value="{{ request('search_name') }}">
            </div>

            <!-- Tìm theo email -->
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Email</label>
                <input type="email" name="search_email" class="form-control" 
                       placeholder="Nhập email..." value="{{ request('search_email') }}">
            </div>

            <!-- Tìm theo chuyên ngành -->
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Chuyên ngành</label>
                <input type="text" name="search_major" class="form-control" 
                       placeholder="VD: CNTT..." value="{{ request('search_major') }}">
            </div>

            <!-- Nút tìm kiếm -->
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    Tìm kiếm
                </button>
            </div>
        </form>

        <!-- Nút xóa bộ lọc -->
        @if (request()->hasAny(['search_name', 'search_email', 'search_major']))
            <div class="mt-2">
                <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary btn-sm">
                    Xóa bộ lọc
                </a>
            </div>
        @endif
    </div>

    <!-- Table Section -->
    <div class="table-section">
        @if($members->count() > 0)
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>CCCD</th>
                            <th>Khóa</th>
                            <th>Chuyên ngành</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td><span class="id-badge">#{{ $member->id }}</span></td>
                                <td class="fw-bold">{{ $member->user->name ?? '—' }}</td>
                                <td>
                                    <a href="mailto:{{ $member->user->email ?? '' }}" class="text-primary">
                                        {{ $member->user->email ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $member->phone ?? '<span class="text-muted">—</span>' }}</td>
                                <td>{{ $member->citizen_id ?? '<span class="text-muted">—</span>' }}</td>
                                <td>{{ $member->course ?? '<span class="text-muted">—</span>' }}</td>
                                <td>{{ $member->major ?? '<span class="text-muted">—</span>' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $member->status }}">
                                        <span class="status-dot"></span>
                                        {{ $member->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-action btn-view" title="Xem">Xem</a>
                                        <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-action btn-edit" title="Sửa">Sửa</a>
                                        <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" style="display:inline;"
                                              onsubmit="return confirm('Xóa thành viên này?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-delete" title="Xóa">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($members->hasPages())
                <div class="pagination-wrapper">
                    {{ $members->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">Không có thành viên</div>
                <h3>Chưa có thành viên nào</h3>
                <p>Thêm thành viên đầu tiên để bắt đầu quản lý.</p>
                <a href="{{ route('admin.members.create') }}" class="btn btn-primary">Thêm thành viên</a>
            </div>
        @endif
    </div>
</div>

<style>
    /* === THÊM MỚI: FILTER STYLES === */
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
    }

    .filter-section .form-label {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .filter-section .form-control,
    .filter-section .form-select {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
    }

    .filter-section .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(91, 94, 255, 0.2);
    }

    .filter-section .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.75rem 1rem;
    }

    .filter-section .btn-outline-secondary {
        border-color: #d1d5db;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .filter-section .btn-outline-secondary:hover {
        background: #f3f4f6;
        color: var(--dark);
    }

    /* === GIỮ NGUYÊN CÁC STYLE CŨ === */
    :root { --primary: #5b5eff; --success: #10b981; --danger: #ef4444; --dark: #1f2937; --border: #e5e7eb; --shadow: 0 10px 25px -3px rgba(0,0,0,0.1); --radius: 14px; }
    .modern-container { background: linear-gradient(135deg, #f0f4ff 0%, #e0eaff 100%); padding: 2rem; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
    .header-section { background: white; padding: 1.8rem 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
    .page-title { font-size: 1.9rem; font-weight: 700; color: var(--dark); margin: 0; }
    .page-subtitle { color: #6b7280; font-size: 0.95rem; }
    .header-actions { display: flex; gap: 0.75rem; }
    .btn { border-radius: 10px; font-weight: 600; padding: 0.75rem 1.5rem; font-size: 0.95rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), #4a49d6); color: white; border: none; }
    .btn-success { background: linear-gradient(135deg, #34d399, var(--success)); color: white; border: none; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }
    .table-section { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.5rem; }
    .table-wrapper { overflow-x: auto; }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.95rem; }
    .modern-table th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; padding: 1rem; text-align: left; }
    .modern-table td { padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .modern-table tr:hover { background: #f8faff; }
    .id-badge { background: #e0e7ff; color: var(--primary); padding: 0.25rem 0.5rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; }
    .status-badge { padding: 0.35rem 0.75rem; border-radius: 1rem; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; }
    .status-active { background: #d1fae5; color: #065f46; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; }
    .action-buttons { display: flex; gap: 0.5rem; }
    .btn-action { padding: 0.35rem 0.75rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; border: 1px solid #d1d5db; background: white; color: #374151; transition: all 0.2s; }
    .btn-action:hover { background: #f3f4f6; transform: translateY(-1px); }
    .btn-view { background: #eff6ff; color: #2563eb; border-color: #93c5fd; }
    .btn-edit { background: #fef3c7; color: #d97706; border-color: #f59e0b; }
    .btn-delete { background: #fee2e2; color: #dc2626; border-color: #ef4444; }
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-icon { font-size: 4rem; color: #d1d5db; margin-bottom: 1rem; }
    .empty-state h3 { color: var(--dark); margin-bottom: 0.5rem; }
    .pagination-wrapper { margin-top: 2rem; text-align: center; }
    .alert { border-radius: 10px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
</style>
@endsection