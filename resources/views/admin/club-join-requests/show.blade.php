@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu tạo Câu lạc bộ')

@section('card-body')
<div class="container py-4">
    <h1 class="h3 mb-4 text-gray-800">Chi tiết yêu cầu tạo Câu lạc bộ</h1>

    <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
        <div class="card-body bg-white">
            {{-- Thông tin người gửi --}}
            <div class="mb-4 border-bottom pb-3">
                <h5 class="fw-bold mb-2 text-primary">
                    <i class="fas fa-user me-2"></i>Người gửi yêu cầu
                </h5>
                <p class="mb-1"><strong>Họ tên:</strong> {{ $clubRequest->user->name ?? 'Không rõ' }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $clubRequest->user->email ?? 'Ẩn danh' }}</p>
                <p class="mb-1"><strong>Ngày gửi:</strong> {{ $clubRequest->created_at->format('d/m/Y H:i') }}</p>
            </div>

            {{-- Thông tin câu lạc bộ đề xuất --}}
            <div class="mb-4">
                <h5 class="fw-bold mb-2 text-success">
                    <i class="fas fa-building me-2"></i>Thông tin Câu lạc bộ được đề xuất
                </h5>
                <p class="mb-1"><strong>Tên CLB:</strong> {{ $clubRequest->name }}</p>
                <p class="mb-1"><strong>Lĩnh vực:</strong> {{ $clubRequest->field ?? 'Không rõ' }}</p>
                <p class="mb-1"><strong>Mô tả:</strong></p>
                <div class="border rounded p-3 bg-light text-secondary">
                    {{ $clubRequest->description ?? 'Chưa có mô tả chi tiết' }}
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="mb-4">
                <p><strong>Trạng thái hiện tại:</strong> 
                    <span class="badge 
                        @if($clubRequest->status === 'approved') bg-success
                        @elseif($clubRequest->status === 'rejected') bg-danger
                        @else bg-warning text-dark
                        @endif">
                        {{ ucfirst($clubRequest->status) }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Nút xử lý --}}
        <div class="card-footer d-flex justify-content-between align-items-center bg-light">
            <form action="{{ route('admin.club-requests.handle', $clubRequest->id) }}" method="POST" class="d-flex gap-2 m-0">
                @csrf
                <button type="submit" name="action" value="approve" class="btn btn-success">
                    <i class="fas fa-check me-1"></i>Duyệt
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-danger">
                    <i class="fas fa-times me-1"></i>Từ chối
                </button>
            </form>
            <a href="{{ route('admin.club-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>
    </div>
</div>

{{-- CSS nhẹ --}}
<style>
    .card {
        border-radius: 1rem;
    }
    .fw-bold { font-weight: 600 !important; }
</style>
@endsection
