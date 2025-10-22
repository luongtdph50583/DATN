@extends('admin.layouts.app')

@section('title', 'Thêm Câu lạc bộ mới')

@section('card-body')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thêm Câu lạc bộ</h1>

    <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Tên Câu lạc bộ</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label>Logo</label>
            <input type="file" name="logo" class="form-control-file">
        </div>

        <div class="form-group">
            <label>Lĩnh vực</label>
            <input type="text" name="field" class="form-control" value="{{ old('field') }}">
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="active">Hoạt động</option>
                <option value="pending">Chờ duyệt</option>
                <option value="inactive">Không hoạt động</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
