@extends('admin.layouts.app')

@section('title', 'Quản lý Câu lạc bộ')

@section('card-body')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">📋 Quản lý Câu lạc bộ</h1>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Nút thêm mới + Tìm kiếm --}}
    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary mb-2">➕ Thêm CLB mới</a>
        <form method="GET" action="{{ route('admin.clubs.index') }}" class="d-flex mb-2">
            <input type="text" name="search" class="form-control me-2" placeholder="🔍 Tìm tên CLB..."
                   value="{{ request('search') }}">
            <button class="btn btn-outline-primary">Tìm</button>
            @if(request('search'))
                <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>
            @endif
        </form>
    </div>

    {{-- Bảng dữ liệu --}}
    <div class="card shadow-lg rounded-3">
        <div class="card-body table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-bordered align-middle text-center" style="min-width: 1100px;">
                <thead class="table-primary">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 200px;">Tên CLB</th>
                        <th style="width: 150px;">Lĩnh vực</th>
                        <th style="width: 160px;">Chủ nhiệm</th>
                        <th style="width: 140px;">Trạng thái</th>
                        <th style="width: 140px;">Ngày tạo</th>
                        <th style="width: 260px;">Mô tả</th>
                        <th style="width: 220px;" class="sticky-col bg-white shadow-sm">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clubs as $club)
                        <tr>
                            <td>{{ $club->id }}</td>
                            <td class="fw-bold">{{ $club->name }}</td>
                            <td>{{ $club->field ?? '—' }}</td>

                            {{-- Chủ nhiệm --}}
                            <td>
                                @if ($club->leader)
                                    👤 {{ $club->leader->name }}
                                @else
                                    <span class="text-muted fst-italic">Chưa gán</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td>
                                @switch($club->status)
                                    @case('active')
                                        <span class="badge bg-success">Đang hoạt động</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                        @break
                                    @case('inactive')
                                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">Không xác định</span>
                                @endswitch
                            </td>

                            {{-- Ngày tạo --}}
                            <td>{{ $club->created_at->format('d/m/Y') }}</td>

                            {{-- Mô tả --}}
                            <td class="text-start">{{ Str::limit($club->description, 60) }}</td>

                            {{-- Hành động (CỐ ĐỊNH) --}}
                            <td class="sticky-col bg-white text-nowrap" style="right: 0;">
                                <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-info btn-sm mb-1">👁️</a>
                                <a href="{{ route('admin.clubs.edit', $club->id) }}" class="btn btn-warning btn-sm mb-1">✏️</a>
                                <a href="{{ route('admin.clubs.assign', $club->id) }}" class="btn btn-primary btn-sm mb-1">👤</a>
                                <form action="{{ route('admin.clubs.destroy', $club->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa CLB này?')">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted py-4">Không có dữ liệu câu lạc bộ nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- CSS giữ cột Hành động cố định --}}
<style>
    .sticky-col {
        position: sticky;
        right: 0;
        z-index: 5;
    }

    /* Giữ nền trắng để không bị che */
    .table .sticky-col {
        background: #fff;
    }

    /* Hiệu ứng bóng nhẹ */
    .shadow-sm {
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
    }
</style>
@endsection
