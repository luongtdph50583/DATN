@extends('admin.layouts.app')

@section('title', 'Quản lý Câu lạc bộ')

@section('card-body')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý Câu lạc bộ</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary">+ Thêm CLB mới</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Câu lạc bộ</h6>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Logo</th>
                        <th>Lĩnh vực</th>
                        <th>Trạng thái</th>
                        <th>Chủ nhiệm</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clubs as $club)
                        <tr>
                            <td>{{ $club->id }}</td>
                            <td>{{ $club->name }}</td>
                            <td>{{ $club->description }}</td>
                            <td>
                                @if($club->logo)
                                    <img src="{{ Storage::url($club->logo) }}" width="50" alt="Logo CLB">
                                @endif
                            </td>
                            <td>{{ $club->field }}</td>
                            <td>
                                <span class="badge-status {{ $club->status }}">
                                    @switch($club->status)
                                        @case('active')
                                            Hoạt động
                                            @break
                                        @case('pending')
                                            Chờ duyệt
                                            @break
                                        @default
                                            Không hoạt động
                                    @endswitch
                                </span>
                            </td>
                            <td>{{ $club->manager->name ?? 'Chưa gán' }}</td>
                            <td>
                                <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-sm btn-info">Xem chi tiết</a>

                                <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa CLB này?')">Xóa</button>
                                </form>

                                <a href="{{ route('admin.clubs.assign', $club->id) }}" class="btn btn-sm btn-info">
                                    Gán chủ nhiệm
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- === CSS cho badge trạng thái === --}}
<style>
.badge-status {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
    min-width: 110px;
    text-align: center;
    transition: 0.2s ease;
}

.badge-status.active {
    background-color: #28a745;
    color: #fff;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.4);
}

.badge-status.pending {
    background-color: #ffc107;
    color: #212529;
    box-shadow: 0 0 5px rgba(255, 193, 7, 0.4);
}

.badge-status.inactive {
    background-color: #6c757d;
    color: #fff;
    box-shadow: 0 0 5px rgba(108, 117, 125, 0.4);
}

/* Hiệu ứng hover nhẹ */
.badge-status:hover {
    transform: scale(1.05);
}
</style>
@endsection
