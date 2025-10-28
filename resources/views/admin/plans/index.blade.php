@extends('admin.layouts.app')
@section('title', 'Quản lý Kế hoạch')

@section('card-body')
<div class="plans-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Quản lý Kế hoạch</h1>
                <p class="page-subtitle">Theo dõi và phê duyệt kế hoạch hoạt động CLB</p>
            </div>
            <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-lg">
                Thêm kế hoạch
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.plans.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <select name="club_id" class="form-select">
                        <option value="">Tất cả CLB</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
                @if(request()->hasAny(['club_id', 'status']))
                    <div class="col-md-3">
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary w-100">Xóa lọc</a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CLB</th>
                    <th>Tiêu đề</th>
                    <th>Thời gian</th>
                    <th>Ngân sách</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                <tr>
                    <td><span class="id-badge">#{{ $plan->id }}</span></td>
                    <td class="fw-bold">{{ $plan->club->name }}</td>
                    <td>{{ Str::limit($plan->title, 40) }}</td>
                    <td>
                        <small>{{ $plan->start_date->format('d/m') }} → {{ $plan->end_date->format('d/m/Y') }}</small>
                    </td>
                    <td>
                        @if($plan->budget)
                            <strong class="text-success">{{ number_format($plan->budget) }}đ</strong>
                        @else
                            <em class="text-muted">—</em>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $plan->status }}">
                            {{ $plan->status_label }}
                        </span>
                    </td>
                    <td class="action-buttons">
                        <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                            Xem
                        </a>
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                            Sửa
                        </a>

                        @if($plan->status === 'pending')
                            <form action="{{ route('admin.plans.approve', $plan) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Phê duyệt">
                                    Duyệt
                                </button>
                            </form>
                            <form action="{{ route('admin.plans.reject', $plan) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" title="Từ chối">
                                    Từ chối
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Xóa kế hoạch này? Không thể khôi phục!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Chưa có kế hoạch nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $plans->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    :root {
        --primary: #6366f1; --primary-dark: #4f46e5;
        --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --info: #06b6d4;
        --dark: #1e293b; --light: #f8fafc; --border: #e2e8f0;
        --shadow: 0 10px 25px -3px rgba(0,0,0,0.1);
        --radius: 16px;
        --transition: all 0.3s ease;
    }

    .plans-container {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 2rem;
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    .header-section {
        background: white;
        padding: 1.8rem 2rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border-left: 5px solid var(--primary);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-size: 1.9rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .page-subtitle {
        color: #64748b;
        margin: 0.5rem 0 0;
        font-size: 0.95rem;
    }

    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
    }

    .table-wrapper {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .modern-table thead {
        background: linear-gradient(135deg, var(--dark), #334155);
        color: white;
    }

    .modern-table th {
        padding: 1rem 0.75rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modern-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: var(--transition);
    }

    .modern-table tbody tr:hover {
        background: #f8fafc;
        box-shadow: inset 4px 0 0 var(--primary);
    }

    .modern-table td {
        padding: 1rem 0.75rem;
        font-size: 0.925rem;
        color: #475569;
    }

    .id-badge {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 0.35rem 0.7rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
    .status-approved { background: #d1fae5; color: #065f46; border: 1px solid var(--success); }
    .status-rejected { background: #fee2e2; color: #991b1b; border: 1px solid var(--danger); }

    .action-buttons {
        display: flex;
        gap: 0.4rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.5rem 0.8rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 600;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .btn-sm { min-width: 70px; }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    }

    .btn-success { background: var(--success); color: white; }
    .btn-danger { background: var(--danger); color: white; }
    .btn-warning { background: var(--warning); color: white; }
    .btn-info { background: var(--info); color: white; }
    .btn-outline-danger { border: 1.5px solid var(--danger); color: var(--danger); background: transparent; }
    .btn-outline-danger:hover { background: var(--danger); color: white; }

    .pagination-wrapper {
        padding: 1.5rem;
        background: white;
        text-align: center;
        border-top: 1px solid var(--border);
    }

    .alert {
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border: none;
    }

    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        border-left: 5px solid var(--success);
    }

    @media (max-width: 768px) {
        .header-content { flex-direction: column; text-align: center; }
        .action-buttons { flex-direction: column; }
        .btn { width: 100%; }
        .modern-table { font-size: 0.85rem; }
    }
</style>
@endsection