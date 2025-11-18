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
                        <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-sm btn-info">Xem</a>
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-warning">Sửa</a>

                        @if($plan->status === 'pending')
                            <form action="{{ route('admin.plans.approve', $plan) }}" method="POST" class="d-inline">@csrf
                                <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                            </form>
                            <form action="{{ route('admin.plans.reject', $plan) }}" method="POST" class="d-inline">@csrf
                                <button type="submit" class="btn btn-sm btn-danger">Từ chối</button>
                            </form>
                        @endif

                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Xóa kế hoạch này? Không thể khôi phục!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
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

{{-- ✅ Thêm Select2 JS + CSS --}}
@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2-club').select2({
        theme: 'bootstrap-5',
        placeholder: 'Chọn CLB...',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush

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
