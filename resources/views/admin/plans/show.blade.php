@extends('admin.layouts.app')
@section('title', 'Chi tiết Kế hoạch ')

@section('card-body')
<div class="plans-container">
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Chi tiết Kế hoạch</h1>
                <p class="page-subtitle">ID: #{{ $plan->id }} | {{ $plan->club->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-warning">
                    Sửa
                </a>
                <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary ms-2">
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="detail-card">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="info-group">
                    <h5 class="info-label">Tiêu đề</h5>
                    <p class="info-value">{{ $plan->title }}</p>
                </div>

                <div class="info-group">
                    <h5 class="info-label">Mô tả</h5>
                    <p class="info-value">{!! nl2br(e($plan->description)) !!}</p>
                </div>

                <div class="info-group">
                    <h5 class="info-label">Thời gian</h5>
                    <p class="info-value">
                        <strong>{{ $plan->start_date->format('d/m/Y') }}</strong> → 
                        <strong>{{ $plan->end_date->format('d/m/Y') }}</strong>
                    </p>
                </div>

                <div class="info-group">
                    <h5 class="info-label">Ngân sách</h5>
                    <p class="info-value">
                        @if($plan->budget)
                            <strong class="text-success">{{ number_format($plan->budget) }} VNĐ</strong>
                        @else
                            <em class="text-muted">Chưa xác định</em>
                        @endif
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="status-card">
                    <h5>Trạng thái</h5>
                    <span class="status-badge status-{{ $plan->status }} large">
                        {{ $plan->status_label }}
                    </span>
                </div>

                <div class="info-group mt-4">
                    <h5 class="info-label">Người tạo</h5>
                    <p class="info-value">{{ $plan->createdBy->name }}</p>
                </div>

                @if($plan->approvedBy)
                <div class="info-group">
                    <h5 class="info-label">Người duyệt</h5>
                    <p class="info-value">{{ $plan->approvedBy->name }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        @if($plan->status === 'pending')
        <div class="action-section mt-5">
            <form action="{{ route('admin.plans.approve', $plan) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-lg px-5">
                    Phê duyệt
                </button>
            </form>
            <form action="{{ route('admin.plans.reject', $plan) }}" method="POST" class="d-inline ms-3">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg px-5">
                    Từ chối
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<style>
    :root { --primary: #6366f1; --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --dark: #1e293b; --border: #e2e8f0; --shadow: 0 10px 25px -3px rgba(0,0,0,0.1); --radius: 16px; }
    .plans-container { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 2rem; min-height: 100vh; }
    .header-section { background: white; padding: 1.8rem 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 1.5rem; border-left: 5px solid var(--success); }
    .detail-card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; }
    .info-group { margin-bottom: 1.5rem; }
    .info-label { font-weight: 600; color: var(--dark); margin-bottom: 0.5rem; font-size: 0.95rem; }
    .info-value { font-size: 1.1rem; color: #475569; margin: 0; line-height: 1.6; }
    .status-card { background: #f8fafc; padding: 1.5rem; border-radius: var(--radius); text-align: center; border: 2px solid var(--border); }
    .status-badge.large { font-size: 1.1rem; padding: 0.75rem 1.5rem; }
    .action-section { text-align: center; padding-top: 2rem; border-top: 1px solid var(--border); }
    .btn-lg { padding: 0.85rem 2rem; font-size: 1.1rem; }
</style>
@endsection