@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Tài liệu CLB')

@section('card-header')
    Chỉnh sửa Tài liệu
@endsection

@section('card-body')
    <form action="{{ route('admin.documentclub.update', $document->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $document->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="clb_id" class="form-label">CLB</label>
            <select name="clb_id" class="form-select" required>
                @foreach($clubs as $club)
                    <option value="{{ $club->id }}" {{ old('clb_id', $document->clb_id) == $club->id ? 'selected' : '' }}>
                        {{ $club->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">File hiện tại</label>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark">{{ strtoupper($document->file_type) }}</span>
                <a href="{{ route('admin.documentclub.download', $document->id) }}"
                    class="btn btn-outline-secondary btn-sm">Tải xuống</a>
            </div>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">Thay file mới (nếu cần)</label>
            <input type="file" name="file" class="form-control">
        </div>

        <div class="mb-3">
            <label for="access_level" class="form-label">Mức truy cập</label>
            <select name="access_level" class="form-select" required>
                <option value="public" {{ old('access_level', $document->access_level) == 'public' ? 'selected' : '' }}>Công
                    khai</option>
                <option value="member" {{ old('access_level', $document->access_level) == 'member' ? 'selected' : '' }}>Thành
                    viên</option>
                <option value="club_manager" {{ old('access_level', $document->access_level) == 'club_manager' ? 'selected' : '' }}>Quản lý CLB</option>
                <option value="admin" {{ old('access_level', $document->access_level) == 'admin' ? 'selected' : '' }}>Quản trị
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description', $document->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">Tags (phân cách bằng dấu phẩy)</label>
            <input type="text" name="tags" class="form-control" value="{{ old('tags', $document->tags) }}">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
@endsection
