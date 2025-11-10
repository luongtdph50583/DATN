@extends('admin.layouts.app')

@section('title', 'Chi tiết Thành viên')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:70px;height:70px;font-size:28px;font-weight:bold;">
                {{ strtoupper(substr($member->user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h1 class="h4 mb-1">{{ $member->user->name ?? 'Chưa có tên' }}</h1>
                <p class="text-muted small mb-0">
                    <i class="fas fa-envelope me-1"></i> {{ $member->user->email ?? '—' }}
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge {{ $member->status == 'active' ? 'bg-success' : 'bg-secondary' }} fs-6">
                {{ $member->status == 'active' ? 'Hoạt động' : 'Khóa' }}
            </span>
            <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Danh sách
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Thông tin chi tiết</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><td class="fw-bold text-muted">ID</td><td>#{{ $member->id }}</td></tr>
                        <tr><td class="fw-bold text-muted">MSSV</td><td><code>{{ $member->student_code ?? '—' }}</code></td></tr>
                        <tr><td class="fw-bold text-muted">Giới tính</td><td>{{ $member->gender ? ucfirst($member->gender == 'male' ? 'Nam' : ($member->gender == 'female' ? 'Nữ' : 'Khác')) : '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Ngày sinh</td><td>{{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Địa chỉ</td><td>{{ $member->address ?? '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Khóa</td><td><strong>{{ $member->course ?? '—' }}</strong></td></tr>
                        <tr><td class="fw-bold text-muted">Chuyên ngành</td><td>{{ $member->major ?? '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><td class="fw-bold text-muted">CCCD</td><td><code>{{ $member->citizen_id ?? '—' }}</code></td></tr>
                        <tr><td class="fw-bold text-muted">Ngày cấp</td><td>{{ $member->issued_date ? \Carbon\Carbon::parse($member->issued_date)->format('d/m/Y') : '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Nơi cấp</td><td>{{ $member->issued_place ?? '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Dân tộc</td><td>{{ $member->ethnicity ?? '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">SĐT</td><td>
                            @if($member->phone)
                                <a href="tel:{{ $member->phone }}" class="text-decoration-none">{{ $member->phone }}</a>
                            @else
                                —
                            @endif
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Tạo lúc</td><td>{{ $member->created_at->format('d/m/Y H:i') }}</td></tr>
                        <tr><td class="fw-bold text-muted">Cập nhật</td><td>{{ $member->updated_at->diffForHumans() }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- Hoạt động và Tham gia -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Hoạt động & Tham gia</h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <!-- CLB -->
            <div class="col-md-6">
                <h6>CLB đã tham gia</h6>
                @if($member->clubs->count() > 0)
                    @foreach($member->clubs as $club)
                        <span class="badge bg-info mb-1" title="Role: {{ $club->pivot->role }}">
                            {{ $club->name }} ({{ $club->pivot->role }})
                        </span>
                    @endforeach
                @else
                    <p class="text-muted">Chưa tham gia CLB nào.</p>
                @endif
            </div>

          
        </div>

        <hr>

        <!-- Bài viết và Comment -->
        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <h6>Bài viết đã đăng</h6>
                <p>{{ $member->posts->count() }} bài viết</p>
            </div>
            <div class="col-md-6">
                <h6>Bình luận đã đăng</h6>
                <p>{{ $member->comments->count() }} bình luận</p>
            </div>
        </div>
    </div>
</div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2">
            {{-- <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Sửa thông tin
            </a> --}}
            <form action="{{ route('admin.members.destroy', $member) }}" method="POST"
                  onsubmit="return confirm('Xóa vĩnh viễn thành viên này?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-2"></i>Xóa thành viên
                </button>
            </form>
        </div>
    </div>
</div>
@endsection