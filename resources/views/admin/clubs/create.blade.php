@extends('admin.layouts.app')

@section('title', 'Tạo CLB')

@section('card-body')
<div class="container-fluid">
    <h1 class="mb-4">Tạo Câu lạc bộ mới</h1>

    <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    {{-- Logo bên trái --}}
                    <div class="col-md-4 text-center">
                        <div class="border p-4 text-muted">
                            Chưa có logo
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Upload logo</label>
                            <input type="file" name="logo" class="form-control">
                        </div>
                    </div>

                    {{-- Thông tin CLB bên phải --}}
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Tên CLB</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lĩnh vực</label>
                            <input type="text" name="field" class="form-control" value="{{ old('field') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Tạo CLB</button>
                        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
