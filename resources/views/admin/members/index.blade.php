@extends('admin.layouts.app')

@section('title', 'Quản lý Thành viên')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Quản lý Thành viên</h1>
            <p class="text-muted small mb-0">Quản lý thông tin chi tiết thành viên hệ thống</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm thành viên
            </a>
            <form action="{{ route('admin.members.export.excel') }}" method="GET" class="d-inline">
                @csrf
                <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                <input type="hidden" name="search_email" value="{{ request('search_email') }}">
                <input type="hidden" name="search_major" value="{{ request('search_major') }}">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Xuất Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.members.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tên</label>
                    <input type="text" name="search_name" class="form-control" placeholder="Nhập tên..." value="{{ request('search_name') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="search_email" class="form-control" placeholder="Nhập email..." value="{{ request('search_email') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Chuyên ngành</label>
                    <input type="text" name="search_major" class="form-control" placeholder="VD: CNTT..." value="{{ request('search_major') }}">
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    @if(request()->hasAny(['search_name', 'search_email', 'search_major']))
                        <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($members->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 6%;">ID</th>
                                <th style="width: 18%;">Họ tên</th>
                                <th style="width: 12%;">MSSV</th>
                                <th style="width: 18%;">Email</th>
                                <th style="width: 10%;">SĐT</th>
                                <th style="width: 8%;">Khóa</th>
                                <th style="width: 12%;">Chuyên ngành</th>
                                <th style="width: 10%;">Trạng thái</th>
                                <th style="width: 6%; text-align: center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $member->id }}</span></td>
                                    <td class="fw-bold">{{ $member->user->name ?? '—' }}</td>
                                    <td><code class="small">{{ $member->student_code ?? '—' }}</code></td>
                                    <td>
                                        <a href="mailto:{{ $member->user->email ?? '' }}" class="text-decoration-none">
                                            {{ Str::limit($member->user->email ?? '—', 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $member->phone ?? '—' }}</td>
                                    <td>{{ $member->course ?? '—' }}</td>
                                    <td>{{ Str::limit($member->major ?? '—', 20) }}</td>
                                    <td>
                                        <span class="badge {{ $member->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $member->status == 'active' ? 'Hoạt động' : 'Khóa' }}
                                        </span>
                                    </td>
                                    <!-- NÚT HÀNH ĐỘNG CHUẨN -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.members.destroy', $member) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Xóa thành viên này?');">
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
                    {{ $members->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có thành viên nào</h5>
                    <a href="{{ route('admin.members.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Thêm thành viên đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection