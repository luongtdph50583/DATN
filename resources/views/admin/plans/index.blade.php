@extends('admin.layouts.app')

@section('title', 'Quản lý Kế hoạch')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Quản lý Kế hoạch</h1>
            <p class="text-muted small mb-0">Theo dõi và phê duyệt kế hoạch hoạt động CLB</p>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm kế hoạch
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->

            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">Chọn CLB</label>
                    <select name="club_id" class="form-select select2-club">
                        <option value="">Tất cả CLB</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary">Trạng thái</label>
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
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.plans.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CLB</label>
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
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        @if(request()->hasAny(['club_id', 'status']))
                            <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($plans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 6%;">ID</th>
                                <th style="width: 18%;">CLB</th>
                                <th style="width: 25%;">Tiêu đề</th>
                                <th style="width: 15%;">Thời gian</th>
                                <th style="width: 12%;">Ngân sách</th>
                                <th style="width: 12%;">Trạng thái</th>
                                <th style="width: 12%; text-align: center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($plans as $plan)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $plan->id }}</span></td>
                                    <td class="fw-bold">{{ $plan->club->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.plans.show', $plan) }}" class="text-decoration-none">
                                            {{ Str::limit($plan->title, 40) }}
                                        </a>
                                    </td>
                                    <td class="small">
                                        {{ $plan->start_date->format('d/m') }} → {{ $plan->end_date->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if($plan->budget)
                                            <span class="badge bg-success">{{ number_format($plan->budget) }}đ</span>
                                        @else
                                            <span class="badge bg-secondary">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($plan->status == 'pending') bg-warning text-dark
                                            @elseif($plan->status == 'approved') bg-success
                                            @else bg-danger @endif">
                                            {{ $plan->status_label }}
                                        </span>
                                    </td>
                                    <!-- NÚT HÀNH ĐỘNG CHUẨN -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-sm btn-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if($plan->status === 'pending')
                                                <form action="{{ route('admin.plans.approve', $plan) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.plans.reject', $plan) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Từ chối">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Xóa vĩnh viễn?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    {{ $plans->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có kế hoạch nào</h5>
                    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Thêm kế hoạch đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
