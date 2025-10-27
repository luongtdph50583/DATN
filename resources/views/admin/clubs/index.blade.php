@extends('admin.layouts.app')

@section('title', 'Danh sách CLB')

@section('card-body')
<h1 class="mb-4">Danh sách CLB</h1>

{{-- Thông báo success --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Form tìm kiếm --}}
<form method="GET" action="{{ route('admin.clubs.index') }}" class="mb-3 row g-2 align-items-end">
    <div class="col-md-3">
        <label for="name" class="form-label">Tên CLB</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}">
    </div>
    <div class="col-md-3">
        <label for="field" class="form-label">Lĩnh vực</label>
        <input type="text" name="field" id="field" class="form-control" value="{{ request('field') }}">
    </div>
    <div class="col-md-3">
        <label for="status" class="form-label">Trạng thái</label>
        <select name="status" id="status" class="form-control">
            <option value="">-- Tất cả --</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>

<a href="{{ route('admin.clubs.create') }}" class="btn btn-success mb-3">Thêm mới CLB</a>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Logo</th>
            <th>Tên CLB</th>
            <th>Lĩnh vực</th>
            <th>Trạng thái</th>
            <th>Chủ nhiệm</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clubs as $club)
        <tr>
            <td>{{ $club->id }}</td>
            <td>
                @if($club->logo)
                    <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo" style="height:50px;">
                @else
                    <span class="text-muted">Chưa có</span>
                @endif
            </td>
            <td>{{ $club->name }}</td>
            <td>{{ $club->field ?? 'Chưa cập nhật' }}</td>
            <td>{{ ucfirst($club->status ?? 'Chưa cập nhật') }}</td>
            <td>{{ $club->manager->name ?? 'Chưa có' }}</td>
            <td>
                <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-info btn-sm">Xem</a>
                <a href="{{ route('admin.clubs.edit', $club->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <a href="{{ route('admin.clubs.assign', $club->id) }}" class="btn btn-success btn-sm">Gán chủ nhiệm</a>
                <form action="{{ route('admin.clubs.destroy', $club->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Chưa có CLB nào</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
