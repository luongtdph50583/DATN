@extends('admin.layouts.app')

@section('title', 'Chi tiết Câu lạc bộ')

@section('card-body')
<div class="container py-5">

    {{-- Header trang --}}
    <div class="mb-4 d-flex align-items-center gap-2">
        <i class="fas fa-users fa-2x text-primary"></i>
        <h1 class="h3 text-gray-800 mb-0">Chi tiết Câu lạc bộ</h1>
    </div>

    {{-- Card thông tin CLB --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="row g-0">
            <div class="col-md-4 bg-light d-flex justify-content-center align-items-center p-4">
                @if ($club->logo)
                    <img src="{{ Storage::url($club->logo) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 180px; object-fit: contain;" alt="Logo CLB">
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-image fa-3x mb-2"></i>
                        <p>Chưa có logo</p>
                    </div>
                @endif
            </div>
            <div class="col-md-8 p-4">
                <h2 class="fw-bold text-primary mb-3">{{ $club->name }}</h2>
                <p><strong>Mô tả:</strong> {{ $club->description ?? 'Chưa có mô tả' }}</p>

                <div class="row mb-2">
                    <div class="col-sm-6 mb-2">
                        <strong>Lĩnh vực:</strong> {{ $club->field ?? 'Không rõ' }}
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong>Trạng thái:</strong>
                        <span class="badge-status {{ $club->status }}">
                            @switch($club->status)
                                @case('active') Hoạt động @break
                                @case('pending') Chờ duyệt @break
                                @default Không hoạt động
                            @endswitch
                        </span>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-6 mb-2">
                        <strong>Chủ nhiệm:</strong> {{ $club->manager->name ?? 'Chưa gán' }}
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong>Ngày tạo:</strong> {{ $club->created_at->format('d/m/Y') }}
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> Sửa
                    </a>
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Card danh sách thành viên CLB --}}
    <div class="card shadow-sm rounded-4">
        <div class="card-header club-members-header">
            <h5 class="mb-0"><i class="fas fa-user-friends me-2"></i>Thành viên CLB ({{ $club->members->count() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($club->members->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Avatar</th>
                            <th>Họ & Tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày tham gia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($club->members as $index => $member)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($member->avatar)
                                        <img src="{{ Storage::url($member->avatar) }}" class="rounded-circle" width="40" height="40" alt="Avatar">
                                    @else
                                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                    @endif
                                </td>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->pivot->role ?? 'Thành viên' }}</td>
                                <td>{{ $member->pivot->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="text-center text-white m-3">CLB chưa có thành viên nào.</p>
            @endif
        </div>
    </div>
</div>

{{-- CSS --}}
<style>
/* Badge trạng thái */
.badge-status {
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    min-width: 110px;
    text-align: center;
    box-shadow: 0 0 6px rgba(0,0,0,0.1);
    transition: 0.2s;
}
.badge-status.active { background-color: #28a745; color: #fff; }
.badge-status.pending { background-color: #ffc107; color: #212529; }
.badge-status.inactive { background-color: #6c757d; color: #fff; }
.badge-status:hover { transform: scale(1.05); }

/* Header danh sách thành viên */
.club-members-header {
    background: linear-gradient(90deg, #007bff, #0056b3);
    color: #fff !important;
    font-weight: 600;
    font-size: 1.1rem;
}
</style>
@endsection
