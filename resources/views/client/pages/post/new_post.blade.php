@extends('client.layouts.app')
@php($isEdit = isset($post))
@section('title', ($isEdit ? 'Chỉnh sửa' : 'Tạo') . ' bài viết - ' . ($club->name ?? 'CLB'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="mb-1">{{ $isEdit ? 'Chỉnh sửa bài viết' : 'Tạo bài viết mới' }}</h2>
                        <p class="text-muted mb-0">CLB: {{ $club->name }}</p>
                    </div>
                    <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ $isEdit ? route('club_manager.posts.update', ['club_id' => $club->id, 'post' => $post->id]) : route('club_manager.posts.store', ['club_id' => $club->id]) }}"
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($isEdit)
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                       value="{{ old('title', $post->title ?? '') }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Loại bài viết <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="post" {{ old('type', $post->type ?? '') === 'post' ? 'selected' : '' }}>Bài viết</option>
                                        <option value="notice" {{ old('type', $post->type ?? '') === 'notice' ? 'selected' : '' }}>Thông báo</option>
                                        <option value="document" {{ old('type', $post->type ?? '') === 'document' ? 'selected' : '' }}>Tài liệu</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Chế độ hiển thị <span class="text-danger">*</span></label>
                                    <select name="visibility" class="form-select" required>
                                        <option value="">-- Chọn chế độ --</option>
                                        <option value="internal" {{ old('visibility', $post->visibility ?? '') === 'internal' ? 'selected' : '' }}>Nội bộ CLB</option>
                                        <option value="public" {{ old('visibility', $post->visibility ?? '') === 'public' ? 'selected' : '' }}>Công khai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mt-0">
                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái hiển thị</label>
                                    <select name="is_visible" class="form-select">
                                        <option value="1" {{ (string)old('is_visible', $post->is_visible ?? '1') === '1' ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ (string)old('is_visible', $post->is_visible ?? '1') === '0' ? 'selected' : '' }}>Ẩn</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Đánh dấu nổi bật</label>
                                    <select name="is_featured" class="form-select">
                                        <option value="0" {{ (string)old('is_featured', $post->is_featured ?? '0') === '0' ? 'selected' : '' }}>Không</option>
                                        <option value="1" {{ (string)old('is_featured', $post->is_featured ?? '0') === '1' ? 'selected' : '' }}>Có</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                @if(!empty($post->thumbnail))
                                    <p class="text-muted small mt-2">Ảnh hiện tại:</p>
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="thumbnail" class="img-fluid rounded" style="max-height:180px">
                                @endif
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="content" rows="10" class="form-control" placeholder="Nhập nội dung chi tiết" required>{{ old('content', $post->content ?? '') }}</textarea>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="theme-btn">
                                    <i class="fa-solid fa-paper-plane me-1"></i>
                                    {{ $isEdit ? 'Cập nhật bài viết' : 'Đăng bài viết' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
