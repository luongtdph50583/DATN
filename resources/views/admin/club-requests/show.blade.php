@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu tạo CLB')

@section('card-body')
<div class="container mt-4">
    <h2>📄 Chi tiết yêu cầu tạo Câu lạc bộ</h2>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <h4 class="card-title">{{ $clubRequest->name }}</h4>
            <p><strong>Lĩnh vực:</strong> {{ $clubRequest->field }}</p>
            <p><strong>Mô tả:</strong> {{ $clubRequest->description }}</p>
            <p><strong>Trạng thái:</strong>
                @if($clubRequest->status === 'pending')
                    <span class="badge bg-warning text-dark">Đang chờ</span>
                @elseif($clubRequest->status === 'approved')
                    <span class="badge bg-success">Đã duyệt</span>
                @elseif($clubRequest->status === 'rejected')
                    <span class="badge bg-danger">Từ chối</span>
                @endif
            </p>
            <p><strong>Ngày gửi:</strong> {{ $clubRequest->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.club-requests.index') }}" class="btn btn-secondary">⬅ Quay lại</a>

        @if($clubRequest->status === 'pending')
            <form action="{{ route('admin.club-requests.handle', $clubRequest->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-success">✅ Duyệt</button>
            </form>

            <form action="{{ route('admin.club-requests.handle', $clubRequest->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn btn-danger">❌ Từ chối</button>
            </form>
        @endif
    </div>
</div>
@endsection
