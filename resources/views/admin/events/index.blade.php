@extends('admin.layouts.app')

@section('title', 'Quản lý Sự kiện')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Quản lý Sự kiện</h1>
                <p class="page-subtitle">Theo dõi, duyệt và quản lý toàn bộ sự kiện của các CLB</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    Thêm sự kiện
                </a>
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
        <form id="filterForm" action="{{ route('admin.events.index') }}" method="GET" class="row g-3 align-items-end">
            <!-- Tìm theo tên sự kiện -->
            <div class="col-md-4">
                <label class="form-label fw-bold text-muted">Tên sự kiện</label>
                <input type="text" name="search_name" class="form-control" 
                       placeholder="Nhập tên sự kiện..." value="{{ request('search_name') }}">
            </div>

            <!-- Tìm theo CLB -->
            <div class="col-md-4">
                <label class="form-label fw-bold text-muted">Câu lạc bộ</label>
                <select name="club_id" class="form-select">
                    <option value="">-- Tất cả CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nút tìm kiếm -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    Tìm kiếm
                </button>
            </div>

            <!-- Nút xóa bộ lọc -->
            @if (request()->hasAny(['search_name', 'club_id']))
                <div class="col-md-2">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary w-100">
                        Xóa bộ lọc
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Table Section -->
    <div class="table-section">
        @if($events->count() > 0)
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sự kiện</th>
                            <th>Thời gian</th>
                            <th>Địa điểm</th>
                            <th>Số người</th>
                            <th>Trạng thái</th>
                            <th>Người tạo</th>
                            <th>CLB</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <!-- ID -->
                                <td><span class="id-badge">#{{ $event->id }}</span></td>

                                <!-- Tên sự kiện -->
                                <td class="fw-bold">
                                    <a href="{{ route('admin.events.show', $event->id) }}" class="text-primary text-decoration-none">
                                        {{ $event->name }}
                                    </a>
                                </td>

                                <!-- Thời gian -->
                                <td>
                                    <div class="text-sm">
                                        <div><strong>Bắt đầu:</strong> {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') : '—' }}</div>
                                        <div><strong>Kết thúc:</strong> {{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('d/m/Y H:i') : '—' }}</div>
                                    </div>
                                </td>

                                <!-- Địa điểm -->
                                <td>{{ $event->location }}</td>

                                <!-- Số người tham gia -->
                                <td>
                                    @if($event->max_participants)
                                        <span class="text-primary">{{ $event->max_participants }} người</span>
                                    @else
                                        <span class="text-muted">Không giới hạn</span>
                                    @endif
                                </td>

                                <!-- Trạng thái -->
                                <td>
                                    <span class="status-badge status-{{ $event->status }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>

                                <!-- Người tạo -->
                                <td>
                                    <div class="text-sm">
                                        {{ $event->createdBy->name ?? '—' }}
                                    </div>
                                </td>

                                <!-- CLB -->
                                <td>
                                    @if($event->club)
                                        <span class="badge bg-info text-dark">{{ $event->club->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <!-- HÀNH ĐỘNG – ĐỒNG BỘ 100% -->
                                <td>
                                    <div class="action-buttons">
                                        @if($event->status === 'pending')
                                            <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-action btn-approve" 
                                                        onclick="return confirm('Duyệt sự kiện này?')">
                                                    Duyệt
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.events.reject', $event) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-action btn-reject" 
                                                        onclick="return confirm('Từ chối sự kiện này?')">
                                                    Từ chối
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.events.show', $event->id) }}" class="btn-action btn-view">
                                            Xem
                                        </a>

                                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn-action btn-edit">
                                            Sửa
                                        </a>

                                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Xóa vĩnh viễn sự kiện này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete">
                                                Xóa
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
            @if ($events->hasPages())
                <div class="pagination-wrapper">
                    {{ $events->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">No events</div>
                <h3>Chưa có sự kiện nào</h3>
                <p>Thêm sự kiện đầu tiên để bắt đầu quản lý.</p>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Thêm sự kiện</a>
            </div>
        @endif
    </div>
</div>

<style>
    :root {
        --primary: #5b5eff;
        --primary-dark: #4a49d6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #0ea5e9;
        --dark: #1f2937;
        --light: #f9fafb;
        --border: #e5e7eb;
        --shadow: 0 10px 25px -3px rgba(0,0,0,0.1);
        --radius: 14px;
        --transition: all 0.25s ease;
    }

    .modern-container {
        background: linear-gradient(135deg, #f0f4ff 0%, #e0eaff 100%);
        padding: 2rem;
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    .header-section {
        background: white;
        padding: 1.8rem 2rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        border-left: 5px solid var(--primary);
    }

    .page-title { font-size: 1.9rem; font-weight: 700; color: var(--dark); margin: 0; }
    .page-subtitle { color: #6b7280; margin: 0.5rem 0 0; font-size: 0.95rem; }

    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    /* === FILTER === */
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
        font-weight: 600;
    }

    .filter-section .form-control,
    .filter-section .form-select {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
    }

    .filter-section .form-control:focus,
    .filter-section .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(91, 94, 255, 0.2);
    }

    .filter-section .btn-outline-secondary {
        border-color: #d1d5db;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .filter-section .btn-outline-secondary:hover {
        background: #f3f4f6;
        color: var(--dark);
    }

    /* === TABLE === */
    .table-section {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1.5rem;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.95rem;
    }

    .modern-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        text-align: left;
    }

    .modern-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .modern-table tr:hover {
        background: #f8faff;
    }

    .id-badge {
        background: #e0e7ff;
        color: var(--primary);
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .status-pending { background: #fef3c7; color: #d97706; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    /* === NÚT HÀNH ĐỘNG – ĐỒNG BỘ 100% === */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn-action {
        min-width: 60px;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 1.5rem;
        border: 1.8px solid transparent;
        transition: all 0.25s ease;
        cursor: pointer;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Xem */
    .btn-view { background: #dbeafe; color: #2563eb; border-color: #93c5fd; }
    .btn-view:hover { background: #bfdbfe; border-color: #60a5fa; color: #1d4ed8; }

    /* Sửa */
    .btn-edit { background: #fef3c7; color: #d97706; border-color: #fbbf24; }
    .btn-edit:hover { background: #fde68a; border-color: #f59e0b; color: #b45309; }

    /* Xóa */
    .btn-delete { background: #fee2e2; color: #dc2626; border-color: #f87171; }
    .btn-delete:hover { background: #fecaca; border-color: #ef4444; color: #b91c1c; }

    /* Duyệt */
    .btn-approve { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
    .btn-approve:hover { background: #a7f3d0; border-color: #34d399; color: #064e3b; }

    /* Từ chối */
    .btn-reject { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
    .btn-reject:hover { background: #fecaca; border-color: #ef4444; color: #7f1d1d; }

    /* === EMPTY STATE === */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .pagination-wrapper {
        margin-top: 2rem;
        text-align: center;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .text-sm { font-size: 0.875rem; line-height: 1.4; }
    .badge { font-size: 0.8rem; padding: 0.35rem 0.75rem; }

    @media (max-width: 768px) {
        .header-content { flex-direction: column; text-align: center; gap: 1rem; }
        .filter-section .row { flex-direction: column; }
        .filter-section .col-md-4, .filter-section .col-md-2 { width: 100%; }
        .btn-action { min-width: 50px; font-size: 0.75rem; padding: 0.3rem 0.6rem; }
    }
</style>
@endsection