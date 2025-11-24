@extends('client.layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <!-- Cột trái: logo / avatar -->
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            @if($club->logo)
                                <img src="{{ asset('storage/'.$club->logo) }}" 
                                     class="rounded shadow-sm" 
                                     alt="{{ $club->name }}" 
                                     style="width:120px; height:120px; object-fit:cover;">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" 
                                     class="rounded shadow-sm" 
                                     alt="Logo mặc định" 
                                     style="width:120px; height:120px; object-fit:cover;">
                            @endif
                            <h5 class="mt-3">{{ $club->name }}</h5>
                            <p class="text-muted">{{ $club->slogan ?? '-' }}</p>
                        </div>

                        <!-- Cột phải: thông tin chi tiết -->
                        <div class="col-md-8">
                            <h6>Thông tin cơ bản</h6>
                            <p><strong>Lĩnh vực hoạt động:</strong> {{ $club->field }}</p>
                            <p><strong>Mô tả:</strong> {{ $club->description ?? '-' }}</p>

                            <hr>
                            <h6>Thông tin liên hệ & quản lý</h6>
                            <p><strong>Người quản lý hành chính:</strong> {{ $club->manager?->name ?? '-' }}</p>
                            <p><strong>Giảng viên đỡ đầu:</strong> {{ $club->advisor?->name ?? '-' }} 
                                (Trạng thái: {{ ucfirst($club->advisor_status) }})</p>
                            <p><strong>Email:</strong> {{ $club->email ?? '-' }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $club->phone ?? '-' }}</p>
                            <p><strong>Giới hạn thành viên:</strong> {{ $club->member_limit ?? '-' }}</p>

                            <hr>
                            <h6>Thông tin khác</h6>
                            <p><strong>Ngày thành lập:</strong> {{ $club->founded_at?->format('d/m/Y') ?? '-' }}</p>
                            <p><strong>Địa điểm hoạt động:</strong> {{ $club->location ?? '-' }}</p>
                            <p><strong>Nội quy CLB:</strong></p>
                            <p>{{ $club->rules ?? '-' }}</p>

                            <div class="mt-4 d-flex gap-2">
                                <a href="{{ route('client.clubs.list') }}" class="btn btn-secondary">
                                    Quay lại danh sách CLB
                                </a>
               @php
    $hasActiveForm = \App\Models\ClubJoinFormQuestion::where('club_id', $club->id)
                        ->where('is_active', true)
                        ->exists();
@endphp

@if($hasActiveForm)
    <a href="{{ route('clubs.join.form', $club->id) }}" class="btn btn-primary">
        Đăng ký tham gia CLB
    </a>
@else
    <button class="btn btn-secondary" disabled>
        Hiện tại chưa có form đăng ký
    </button>
@endif

                            </div>
                        </div>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div>
    </div>
</div>
@endsection
