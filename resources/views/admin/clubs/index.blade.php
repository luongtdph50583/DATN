@extends('admin.layouts.app')

@section('title', 'Danh sách CLB')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Danh sách Câu lạc bộ</h1>
            <p class="text-muted small mb-0">Quản lý tất cả các CLB trong hệ thống</p>
        </div>
        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo CLB mới
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm CLB..." value="{{ request('keyword') }}">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="fas fa-search"></i>
            </button>
            @if(request('keyword'))
                <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($clubs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 6%;">ID</th>
                                <th style="width: 25%;">Tên CLB</th>
                                <th style="width: 20%;">Lĩnh vực</th>
                                <th style="width: 20%;">Chủ nhiệm</th>
                                <th style="width: 12%;">Trạng thái</th>
                                <th style="width: 17%; text-align: center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clubs as $club)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $club->id }}</span></td>
                                    <td class="fw-bold">
                                        <a href="{{ route('admin.clubs.show', $club) }}" class="text-decoration-none">
                                            {{ Str::limit($club->name, 40) }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($club->field, 30) }}</td>
                                    <td class="small">
                                        {{ $club->manager->name ?? '<span class="text-muted">—</span>' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $club->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $club->status == 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                                        </span>
                                    </td>
                                    <!-- NÚT HÀNH ĐỘNG CHUẨN -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-sm btn-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.clubs.assign', $club) }}" class="btn btn-sm btn-primary" title="Gán chủ nhiệm">
                                                <i class="fas fa-user-tie"></i>
                                            </a>
                                            <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Xóa CLB này?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    {{ $clubs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có CLB nào</h5>
                    <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Tạo CLB đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection