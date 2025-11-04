@extends('admin.layouts.app')

@section('title', 'Chi tiết CLB')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            @if($club->logo)
                <img src="{{ Storage::url($club->logo) }}" class="rounded me-3" style="width:80px;height:80px;object-fit:cover;">
            @else
                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center me-3" style="width:80px;height:80px;font-size:32px;">
                    {{ strtoupper(substr($club->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="h4 mb-1">{{ $club->name }}</h1>
                <p class="text-muted small mb-0">{{ $club->field }}</p>
            </div>
        </div>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Detail Card -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Thông tin CLB</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr><td class="fw-bold text-muted">ID</td><td>#{{ $club->id }}</td></tr>
                        <tr><td class="fw-bold text-muted">Tên</td><td>{{ $club->name }}</td></tr>
                        <tr><td class="fw-bold text-muted">Lĩnh vực</td><td>{{ $club->field }}</td></tr>
                        <tr><td class="fw-bold text-muted">Email</td><td>{{ $club->email ?? '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Điện thoại</td><td>{{ $club->phone ?? '—' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Giới hạn</td><td>{{ $club->member_limit ?? 'Không giới hạn' }}</td></tr>
                        <tr><td class="fw-bold text-muted">Thành viên</td><td>{{ $members->count() }}</td></tr>
                        <tr><td class="fw-bold text-muted">Chủ nhiệm</td><td>
                            {{ $club->manager->name ?? '—' }}<br>
                            <small class="text-muted">{{ $club->manager->email ?? '' }}</small>
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Trạng thái</td><td>
                            <span class="badge {{ $club->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $club->status == 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                            </span>
                        </td></tr>
                        <tr><td class="fw-bold text-muted">Mô tả</td><td>
                            {!! nl2br(e($club->description ?? '<em class="text-muted">Chưa có</em>')) !!}
                        </td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Hành động</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Sửa CLB
                    </a>
                    <a href="{{ route('admin.clubs.assign', $club) }}" class="btn btn-primary">
                        <i class="fas fa-user-tie"></i> Gán chủ nhiệm
                    </a>
                    <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST"
                          onsubmit="return confirm('Xóa vĩnh viễn CLB này?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Xóa CLB
                        </button>
                    </form>
                </div>
            </div>

            @if($club->logo)
                <div class="card shadow-sm mt-3 text-center">
                    <div class="card-body">
                        <img src="{{ Storage::url($club->logo) }}" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection