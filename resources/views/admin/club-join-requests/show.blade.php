@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu tham gia CLB')

@section('card-body')
<div class="container py-4">
    <h1 class="h4 mb-4 text-gray-800">Chi tiết yêu cầu #{{ $joinRequest->id }}</h1>

    {{-- Thông tin người gửi --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">Thông tin người gửi</div>
        <div class="card-body">
            <p><strong>Họ tên:</strong> {{ $joinRequest->user->name ?? 'Không rõ' }}</p>
            <p><strong>Email:</strong> {{ $joinRequest->user->email ?? '—' }}</p>
            <p><strong>Ngày gửi:</strong> {{ $joinRequest->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Trạng thái:</strong>
                <span class="badge-status {{ $joinRequest->status }}">
                    @switch($joinRequest->status)
                        @case('approved') Đã duyệt @break
                        @case('rejected') Từ chối @break
                        @default Chờ duyệt
                    @endswitch
                </span>
            </p>
        </div>
    </div>

    {{-- Thông tin CLB --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">Thông tin Câu lạc bộ</div>
        <div class="card-body">
            @if ($joinRequest->club)
                <p><strong>Tên CLB:</strong> {{ $joinRequest->club->name }}</p>
                <p><strong>Lĩnh vực:</strong> {{ $joinRequest->club->field ?? '—' }}</p>
                <p><strong>Người phụ trách:</strong> {{ $joinRequest->club->leader->name ?? 'Không rõ' }}</p>
                <p><strong>Mô tả:</strong></p>
                <div class="border p-3 bg-light rounded">
                    {{ $joinRequest->club->description ?? 'Không có mô tả' }}
                </div>
            @else
                <p class="text-muted">Thông tin CLB không khả dụng.</p>
            @endif
        </div>
    </div>

    {{-- Ghi chú của người gửi --}}
    @if (!empty($joinRequest->message))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">Lời nhắn / Ghi chú</div>
            <div class="card-body">
                {{ $joinRequest->message }}
            </div>
        </div>
    @endif

    {{-- Nút hành động --}}
    @if ($joinRequest->status === 'pending')
        <form action="{{ route('admin.club-join-requests.handle', $joinRequest->id) }}" method="POST" class="mb-3">
            @csrf
            <button type="submit" name="action" value="approve" class="btn btn-success">
                ✅ Duyệt yêu cầu
            </button>
            <button type="submit" name="action" value="reject" class="btn btn-danger"
                onclick="return confirm('Bạn có chắc muốn từ chối yêu cầu này?')">
                ❌ Từ chối
            </button>
        </form>
    @endif

    <a href="{{ route('admin.club-join-requests.index') }}" class="btn btn-secondary">← Quay lại</a>
</div>

{{-- CSS --}}
<style>
.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
    min-width: 100px;
}
.badge-status.pending { background-color: #ffc107; color: #212529; }
.badge-status.approved { background-color: #28a745; color: #fff; }
.badge-status.rejected { background-color: #dc3545; color: #fff; }
</style>
@endsection
