@extends('admin.layouts.app')
@section('title', 'Chi tiết yêu cầu tham gia CLB')

@section('card-body')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">🔍 Chi tiết yêu cầu tham gia CLB</h3>
        <a href="{{ route('admin.club-join-requests.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <strong class="text-muted">👤 Người gửi:</strong>
                    <div class="fs-5 fw-semibold text-dark">
                        {{ $joinRequest->user->name ?? 'Không xác định' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong class="text-muted">🏛 CLB:</strong>
                    <div class="fs-5 fw-semibold text-primary">
                        {{ $joinRequest->club->name ?? 'Không xác định' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong class="text-muted">📅 Ngày gửi:</strong>
                    <div class="fs-6">
                        {{ $joinRequest->requested_at ? $joinRequest->requested_at->format('d/m/Y H:i') : '—' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong class="text-muted">📌 Trạng thái:</strong>
                    <div class="mt-1">
                        @if($joinRequest->status === 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2">⏳ Đang chờ duyệt</span>
                        @elseif($joinRequest->status === 'approved')
                            <span class="badge bg-success px-3 py-2">✅ Đã duyệt</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">❌ Từ chối</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-start gap-3 mt-4">
                <form action="{{ route('admin.club-join-requests.approve', $joinRequest->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success px-4"
                            onclick="return confirm('Bạn có chắc chắn muốn duyệt yêu cầu này?')">
                        <i class="bi bi-check-circle"></i> Duyệt
                    </button>
                </form>

                <form action="{{ route('admin.club-join-requests.reject', $joinRequest->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger px-4"
                            onclick="return confirm('Bạn chắc chắn muốn từ chối yêu cầu này?')">
                        <i class="bi bi-x-circle"></i> Từ chối
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
