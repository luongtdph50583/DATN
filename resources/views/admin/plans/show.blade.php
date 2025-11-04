@extends('admin.layouts.app')

@section('title', 'Chi tiết Kế hoạch')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Chi tiết Kế hoạch</h1>
            <p class="text-muted small mb-0">ID: #{{ $plan->id }} | {{ $plan->club->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Sửa
            </a>
            <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">
                <i class="fas fa-list me-2"></i>Danh sách
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-clipboard-list fa-2x me-3"></i>
            <h5 class="mb-0 d-inline">{{ $plan->title }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-8">
                    <h6 class="fw-bold text-primary mb-3">Thông tin cơ bản</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>CLB:</strong> {{ $plan->club->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>ID:</strong> #{{ $plan->id }}
                        </div>
                        <div class="col-12">
                            <strong>Thời gian:</strong> {{ $plan->start_date->format('d/m/Y') }} → {{ $plan->end_date->format('d/m/Y') }}
                        </div>
                        <div class="col-12">
                            <strong>Mô tả:</strong>
                            <p class="mb-0">{!! nl2br(e($plan->description)) !!}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold text-primary mb-3">Quản lý</h6>
                    <div class="mb-3">
                        <strong>Người tạo:</strong> {{ $plan->createdBy->name }}
                    </div>
                    @if($plan->approvedBy)
                        <div class="mb-3">
                            <strong>Người duyệt:</strong> {{ $plan->approvedBy->name }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <strong>Ngân sách:</strong>
                        @if($plan->budget)
                            <span class="badge bg-success">{{ number_format($plan->budget) }} VNĐ</span>
                        @else
                            <span class="badge bg-secondary">Chưa xác định</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Trạng thái:</strong>
                        <span class="badge 
                            @if($plan->status == 'pending') bg-warning text-dark
                            @elseif($plan->status == 'approved') bg-success
                            @else bg-danger @endif">
                            {{ $plan->status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($plan->status === 'pending')
            <div class="card-footer bg-light d-flex gap-2 justify-content-end">
                <form action="{{ route('admin.plans.approve', $plan) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Phê duyệt
                    </button>
                </form>
                <form action="{{ route('admin.plans.reject', $plan) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Từ chối
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection