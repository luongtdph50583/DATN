@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu thành lập CLB')

@section('card-body')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chi tiết yêu cầu thành lập CLB</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Ảnh Logo -->
                <div class="col-md-4 text-center">
                    @if($clubRequest->logo)
                        <img src="{{ asset('storage/' . $clubRequest->logo) }}" 
                             alt="Logo CLB" 
                             class="img-fluid rounded border p-2">
                    @else
                        <div class="border p-4 rounded bg-light">
                            <em>Không có logo</em>
                        </div>
                    @endif
                </div>

                <!-- Thông tin -->
                <div class="col-md-8">
                    <p><strong>Tên CLB:</strong> {{ $clubRequest->name }}</p>
                    <p><strong>Người tạo yêu cầu:</strong> 
                        {{ optional($clubRequest->user)->name ?? 'Không xác định' }}
                    </p>
                    @php
    $class = [
        'pending' => 'badge-custom badge-pending',
        'approved' => 'badge-custom badge-approved',
        'rejected' => 'badge-custom badge-rejected'
    ][$clubRequest->status] ?? 'badge-custom';

    $label = [
        'pending' => '⏳ Chờ duyệt',
        'approved' => '✅ Đã duyệt',
        'rejected' => '❌ Bị từ chối'
    ][$clubRequest->status] ?? 'Không xác định';
@endphp

<p><strong>Trạng thái:</strong> 
    <span class="{{ $class }}">{{ $label }}</span>
</p>
                    <p><strong>Lĩnh vực:</strong> {{ $clubRequest->field ?? '-' }}</p>
                    <p><strong>Ghi chú:</strong> {{ $clubRequest->note ?? '-' }}</p>
                </div>
            </div>

            <!-- Mô tả -->
            <div class="mt-4">
                <h5>Mô tả CLB</h5>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($clubRequest->description)) !!}
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="mt-4 d-flex gap-2">
                <form action="{{ route('admin.club-requests.updateStatus', $clubRequest->id) }}"
                      method="POST" class="mr-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn btn-success">
                        ✅ Duyệt và tạo CLB
                    </button>
                </form>

                <form action="{{ route('admin.club-requests.updateStatus', $clubRequest->id) }}"
                      method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn btn-danger">
                        ❌ Từ chối
                    </button>
                </form>

                <a href="{{ route('admin.club-requests.index') }}" class="btn btn-secondary ml-3">
                    Quay lại danh sách
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
<style>
    .badge-custom {
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 8px;
    text-transform: capitalize;
}

/* Chờ duyệt */
.badge-pending {
    background-color: #ffd08a;
    color: #7a4600;
    border: 1px solid #ffb44d;
}

/* Đã duyệt */
.badge-approved {
    background-color: #b4f0d0;
    color: #085c34;
    border: 1px solid #2ecc71;
}

/* Bị từ chối */
.badge-rejected {
    background-color: #ffb3b8;
    color: #7a1a1a;
    border: 1px solid #e74c3c;
}



</style>