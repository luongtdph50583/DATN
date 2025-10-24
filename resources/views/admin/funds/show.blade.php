@extends('admin.layouts.app')

@section('card-title', 'Chi tiết giao dịch quỹ')

@section('card-header', 'Xem chi tiết giao dịch')

@section('card-body')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Thông tin giao dịch #{{ $fund->id }}</h5>
    <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm">Quay lại</a>
 </div>

<!-- Summary strip -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="p-3 rounded border h-100">
            <div class="text-muted small">Số tiền</div>
            <div class="h5 mb-0">{{ $fund->formatted_amount }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 rounded border h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small">Loại</div>
                @if($fund->type === 'income')
                    <span class="badge bg-success">Thu</span>
                @else
                    <span class="badge bg-danger">Chi</span>
                @endif
            </div>
            <div class="text-end">
                <div class="text-muted small">Trạng thái</div>
                @if($fund->status === 'pending')
                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                @elseif($fund->status === 'approved')
                    <span class="badge bg-success">Đã duyệt</span>
                @else
                    <span class="badge bg-danger">Từ chối</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 rounded border h-100">
            <div class="text-muted small">Ngày tạo</div>
            <div class="h6 mb-0">{{ $fund->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>
 </div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="text-muted small">Câu lạc bộ</div>
                    <div class="font-weight-600">{{ $fund->club->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Danh mục</div>
                    <div>{{ $fund->category ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Người tạo</div>
                    <div>{{ $fund->creator->name }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="text-muted small">Mô tả</div>
                    <div>{{ $fund->description }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Người duyệt</div>
                    <div>{{ optional($fund->approver)->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.font-weight-600 { font-weight:600; }
.g-3 > [class^="col"], .g-4 > [class^="col"] { padding-left:.75rem; padding-right:.75rem; }
</style>
@endsection


