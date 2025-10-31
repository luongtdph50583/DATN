@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu tạo CLB')

@section('card-body')
<div class="container py-4">
    <h1 class="mb-4">📄 Chi tiết yêu cầu tạo CLB</h1>

    {{-- 🟢 Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm p-4">
        <div class="row">
            {{-- Thông tin CLB --}}
            <div class="col-md-8">
                <h4 class="fw-bold">{{ $clubRequest->name }}</h4>

                <p><strong>Người gửi yêu cầu:</strong> {{ $clubRequest->user->name ?? 'Không xác định' }}</p>

                <p><strong>Chủ nhiệm (nếu CLB đã tạo):</strong> 
                    {{ $manager->name ?? 'Chưa có' }}
                </p>

                <p><strong>Email:</strong> {{ $clubRequest->email ?? 'Không có' }}</p>
                <p><strong>SĐT:</strong> {{ $clubRequest->phone ?? 'Không có' }}</p>
                <p><strong>Lĩnh vực:</strong> {{ $clubRequest->field ?? 'Chưa cập nhật' }}</p>
                <p><strong>Mô tả:</strong> {{ $clubRequest->description ?? 'Không có mô tả' }}</p>

                <p><strong>Số lượng thành viên tối đa:</strong> {{ $memberLimit }}</p>

                <p><strong>Trạng thái hiện tại:</strong>
                    @if ($clubRequest->status === 'pending')
                        <span class="badge bg-warning text-dark">Đang chờ duyệt</span>
                    @elseif ($clubRequest->status === 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                    @else
                        <span class="badge bg-danger">Đã từ chối</span>
                    @endif
                </p>

                <p><strong>Ngày gửi:</strong> {{ $clubRequest->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Ngày cập nhật:</strong> {{ $clubRequest->updated_at->format('d/m/Y H:i') }}</p>
            </div>

            {{-- Logo CLB --}}
            <div class="col-md-4 text-center">
                @if($clubRequest->logo)
                    <img src="{{ asset('storage/' . $clubRequest->logo) }}" 
                         alt="Logo CLB" 
                         class="img-thumbnail mb-2" 
                         style="max-width: 200px;">
                @else
                    <p class="text-muted fst-italic">Chưa có logo</p>
                @endif

                @if($club)
                    <p class="text-success fw-bold mt-2">
                        ✅ CLB này đã được tạo (ID: {{ $club->id }})
                    </p>
                @endif
            </div>
        </div>

        {{-- 🔘 Nút hành động --}}
        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('admin.club-requests.index') }}" class="btn btn-secondary">
                ⬅ Quay lại
            </a>

            @if($clubRequest->status === 'pending')
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.club-requests.approve', $clubRequest->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success">✅ Duyệt</button>
                    </form>

                    <form method="POST" action="{{ route('admin.club-requests.reject', $clubRequest->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">❌ Từ chối</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
