@extends('admin.layouts.app')

@section('title', 'Chi tiết Sự kiện')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Chi tiết Sự kiện</h1>
            <p class="text-muted small mb-0">Thông tin chi tiết về sự kiện #{{ $event->id }}</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Detail Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-calendar-alt fa-2x me-3"></i>
            <div>
                <h5 class="mb-0">{{ $event->name }}</h5>
                <small><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</small>
            </div>
            <span class="badge 
                @if($event->status == 'pending') bg-warning text-dark
                @elseif($event->status == 'approved') bg-success
                @else bg-danger @endif ms-auto">
                {{ $event->status == 'pending' ? 'Chờ duyệt' : ($event->status == 'approved' ? 'Đã duyệt' : 'Từ chối') }}
            </span>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <!-- Cột 1 -->
                <div class="col-lg-6">
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle"></i> Thông tin cơ bản</h6>
                    
                    <table class="table table-borderless table-sm">
                        <tr><td class="fw-bold text-muted">ID</td><td>#{{ $event->id }}</td></tr>
                        <tr><td class="fw-bold text-muted">CLB</td><td>
                            @if($event->club)
                                <span class="badge bg-info text-dark">{{ $event->club->name }}</span>
                            @else —
                            @endif
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Bắt đầu</td><td>
                            {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') : '—' }}
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Kết thúc</td><td>
                            {{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('d/m/Y H:i') : '—' }}
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Giới hạn</td><td>
                            @if($event->max_participants)
                                <span class="badge bg-primary">{{ $event->max_participants }} người</span>
                            @else
                                <span class="badge bg-success">Không giới hạn</span>
                            @endif
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Công khai</td><td>
                            <span class="badge {{ $event->is_public ? 'bg-success' : 'bg-secondary' }}">
                                {{ $event->is_public ? 'Công khai' : 'Nội bộ' }}
                            </span>
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Mô tả</td><td>
                            {!! nl2br(e($event->description ?? '<em class="text-muted">Chưa có mô tả</em>')) !!}
                        </td></tr>
                    </table>
                </div>

                <!-- Cột 2 -->
                <div class="col-lg-6">
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-cog"></i> Quản lý & Ngân sách</h6>
                    
                    <table class="table table-borderless table-sm">
                        <tr><td class="fw-bold text-muted">Người tạo</td><td>
                            <strong>{{ $event->createdBy->name ?? '—' }}</strong><br>
                            <small class="text-muted">{{ $event->createdBy->email ?? '' }}</small>
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Người duyệt</td><td>
                            @if($event->approvalBy)
                                <strong class="text-success">{{ $event->approvalBy->name }}</strong><br>
                                <small class="text-muted">{{ $event->approvalBy->email }}</small>
                            @else
                                <span class="text-muted">Chưa duyệt</span>
                            @endif
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Ngân sách dự kiến</td><td>
                            <strong class="text-primary">{{ number_format($event->budget_estimated ?? 0) }} VNĐ</strong>
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Ngân sách hiện có</td><td>
                            <strong class="text-info">{{ number_format($event->budget_current ?? 0) }} VNĐ</strong>
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Ngân sách đã dùng</td><td>
                            <strong class="text-warning">{{ number_format($event->budget_used ?? 0) }} VNĐ</strong>
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Tạo lúc</td><td>
                            {{ $event->created_at->format('d/m/Y H:i') }}
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Cập nhật</td><td>
                            {{ $event->updated_at->diffForHumans() }}
                        </td></tr>
                    </table>
                </div>
            </div>

            <!-- Media -->
            @if($event->media_id)
                <div class="mt-4">
                    <h6 class="fw-bold text-primary"><i class="fas fa-images"></i> Media đính kèm</h6>
                    <img src="{{ Storage::url('media/' . $event->media_id . '.jpg') }}" 
                         class="img-fluid rounded shadow" style="max-height: 300px;">
                </div>
            @endif
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-light d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Sửa
            </a>
            <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                  onsubmit="return confirm('Xóa vĩnh viễn sự kiện này?');" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Xóa
                </button>
            </form>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Danh sách
            </a>
        </div>
    </div>
</div>
@endsection