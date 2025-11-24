@extends('client.layouts.app')

@section('title', 'Chi tiết yêu cầu: ' . $request->name)

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Chi tiết yêu cầu thành lập CLB</h3>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $request->name }}</h5>
            <p><strong>Khẩu hiệu:</strong> {{ $request->slogan ?? '-' }}</p>
            <p><strong>Mô tả:</strong> {{ $request->description ?? '-' }}</p>
            <p><strong>Mục đích:</strong> {{ $request->purpose ?? '-' }}</p>
            <p><strong>Lĩnh vực:</strong> {{ $request->field ?? '-' }}</p>
            <p><strong>Kế hoạch 3 tháng đầu:</strong> {{ $request->plan ?? '-' }}</p>
            <p><strong>Email liên hệ:</strong> {{ $request->email ?? '-' }}</p>
            <p><strong>Số điện thoại:</strong> {{ $request->phone ?? '-' }}</p>
            @if($request->logo)
                <p><strong>Logo:</strong><br>
                    <img src="{{ asset('storage/'.$request->logo) }}" alt="Logo CLB" style="max-width:200px;">
                </p>
            @endif
            <p><strong>Giảng viên đỡ đầu:</strong> {{ $request->advisor?->name ?? '-' }} 
                ({{ ucfirst($request->advisor_status) }})</p>
            <p><strong>Trạng thái duyệt:</strong> 
                <span class="badge 
                        {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                    {{ ucfirst($request->status) }}
                </span>
            </p>
            @if($request->note)
                <p><strong>Ghi chú của người duyệt:</strong> {{ $request->note }}</p>
            @endif
        </div>
    </div>

    <a href="{{ route('formation-request.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
</div>
@endsection
