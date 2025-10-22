@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Câu lạc bộ')

@section('card-body')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chỉnh sửa Câu lạc bộ</h1>

    <form action="{{ route('admin.clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Tên Câu lạc bộ</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $club->name) }}" required>
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description', $club->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Logo hiện tại:</label><br>
            @if($club->logo)
                <img src="{{ Storage::url($club->logo) }}" width="80" alt="Logo CLB">
            @else
                <em>Chưa có logo</em>
            @endif
        </div>

        <div class="form-group">
            <label>Thay logo mới (nếu có)</label>
            <input type="file" name="logo" class="form-control-file">
        </div>

        <div class="form-group">
            <label>Lĩnh vực</label>
            <input type="text" name="field" class="form-control" value="{{ old('field', $club->field) }}">
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="active" {{ $club->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="pending" {{ $club->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="inactive" {{ $club->status == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
