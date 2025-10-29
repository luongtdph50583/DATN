@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Câu lạc bộ')

@section('card-body')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chỉnh sửa Câu lạc bộ</h1>

    <div class="row g-4">
        <!-- Form chỉnh sửa bên trái -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h5 class="mb-3">Chỉnh sửa thông tin CLB</h5>
                <form action="{{ route('admin.clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tên Câu lạc bộ</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $club->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $club->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thay logo mới (nếu có)</label>
                        <input type="file" name="logo" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lĩnh vực</label>
                        <input type="text" name="field" class="form-control" value="{{ old('field', $club->field) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
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
        </div>

        <!-- Panel nội dung hiện tại bên phải -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h5 class="mb-3">Thông tin hiện tại</h5>
                <p><strong>Tên CLB:</strong> {{ $club->name }}</p>
                <p><strong>Mô tả:</strong> {{ $club->description ?? 'Chưa có mô tả' }}</p>
                <p><strong>Lĩnh vực:</strong> {{ $club->field ?? 'Chưa cập nhật' }}</p>
                <p><strong>Trạng thái:</strong> {{ ucfirst($club->status ?? 'Chưa cập nhật') }}</p>
                <p><strong>Số lượng thành viên:</strong> {{ $club->members()->count() }}</p>
                <p><strong>Logo:</strong><br>
                    @if($club->logo)
                        <img src="{{ Storage::url($club->logo) }}" alt="Logo CLB"
                             class="img-thumbnail" style="max-width:150px; max-height:150px;">
                    @else
                        <em>Chưa có logo</em>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    .card {
        border-radius: 10px;
    }
    .card p {
        margin-bottom: 0.75rem;
    }
    .card img {
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        border-radius: 8px;
    }
</style>
@endsection
