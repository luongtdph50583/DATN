@extends('admin.layouts.app')
@section('title', 'Danh sách CLB')

@section('card-body')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Danh sách Câu lạc bộ</h3>
    <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary">+ Tạo CLB mới</a>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="GET" class="mb-3">
    <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm CLB..." value="{{ request('keyword') }}">
</form>

<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Tên CLB</th>
            <th>Lĩnh vực</th>
            <th>Chủ nhiệm</th>
            <th>Trạng thái</th>
            <th width="180">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($clubs as $club)
        <tr>
            <td>{{ $club->id }}</td>
            <td>{{ $club->name }}</td>
            <td>{{ $club->field }}</td>
            <td>{{ $club->manager->name ?? '—' }}</td>
            <td>
                <span class="badge bg-{{ $club->status == 'active' ? 'success' : 'secondary' }}">
                    {{ $club->status }}
                </span>
            </td>
            <td>
                <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-info btn-sm">
    👁 Xem
</a>
                <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-sm btn-warning">Sửa</a>
                 <a href="{{ route('admin.clubs.assign', $club->id) }}" class="btn btn-sm btn-primary">
        Gán chủ nhiệm
    </a>
                <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa CLB này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $clubs->links() }}
@endsection
