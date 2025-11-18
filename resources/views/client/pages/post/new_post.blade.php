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
                    <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}"
                        class="btn btn-outline-secondary">
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
                        <form id="clubPostForm"
                            action="{{ $isEdit ? route('club_manager.posts.update', ['club_id' => $club->id, 'post' => $post->id]) : route('club_manager.posts.store', ['club_id' => $club->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($isEdit) @method('PUT') @endif

                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $post->title ?? '') }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Loại bài viết <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select" required data-select2="true"
                                        data-placeholder="Chọn loại bài viết">
                                        <option value="">-- Chọn loại --</option>
                                        <option value="post" {{ old('type', $post->type ?? '') === 'post' ? 'selected' : '' }}>Bài viết</option>
                                        <option value="notice" {{ old('type', $post->type ?? '') === 'notice' ? 'selected' : '' }}>Thông báo</option>
                                        <option value="document" {{ old('type', $post->type ?? '') === 'document' ? 'selected' : '' }}>Tài liệu</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Chế độ hiển thị <span class="text-danger">*</span></label>
                                    <select name="visibility" class="form-select" required data-select2="true">
                                        <option value="">-- Chọn chế độ --</option>
                                        <option value="internal" {{ old('visibility', $post->visibility ?? '') === 'internal' ? 'selected' : '' }}>Nội bộ CLB</option>
                                        <option value="public" {{ old('visibility', $post->visibility ?? '') === 'public' ? 'selected' : '' }}>Công khai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mt-0">
                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái hiển thị</label>
                                    <select name="is_visible" class="form-select" data-select2="true">
                                        <option value="1" {{ (string) old('is_visible', $post->is_visible ?? '1') === '1' ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ (string) old('is_visible', $post->is_visible ?? '1') === '0' ? 'selected' : '' }}>Ẩn</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Đánh dấu nổi bật</label>
                                    <select name="is_featured" class="form-select" data-select2="true">
                                        <option value="0" {{ (string) old('is_featured', $post->is_featured ?? '0') === '0' ? 'selected' : '' }}>Không</option>
                                        <option value="1" {{ (string) old('is_featured', $post->is_featured ?? '0') === '1' ? 'selected' : '' }}>Có</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                @if(!empty($post->thumbnail))
                                    <p class="text-muted small mt-2">Ảnh hiện tại:</p>
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="thumbnail"
                                        class="img-fluid rounded" style="max-height:180px">
                                @endif
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="content" id="clubPostEditor" rows="10" class="form-control"
                                    placeholder="Nhập nội dung chi tiết">{{ old('content', $post->content ?? '') }}</textarea>
                            </div>

                            <div class="mt-3">
                                <label for="fileUpload" class="form-label">Đính kèm file (ảnh, video, audio, PDF,
                                    Word...)</label>
                                <input type="file" id="fileUpload" class="form-control"
                                    accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                                <button type="button" class="btn btn-secondary mt-2" onclick="uploadAndInsertFile()">
                                    <i class="fas fa-upload me-1"></i> Tải lên & chèn vào nội dung
                                </button>
                                <div id="uploadStatus" class="text-muted small mt-1"></div>
                            </div>

                            <input type="hidden" name="post_id" id="postId" value="{{ $post->id ?? 0 }}">

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

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script>
    <script>
        let clubPostEditorInstance = null;

        document.addEventListener('DOMContentLoaded', function () {
            ClassicEditor.create(document.querySelector('#clubPostEditor'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link',
                    '|', 'bulletedList', 'numberedList',
                    '|', 'blockQuote', 'undo', 'redo'
                ],
                simpleUpload: {
                    uploadUrl: "{{ route('club_manager.posts.upload_image', ['club_id' => $club->id]) }}",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    withCredentials: true
                }
            }).then(editor => {
                clubPostEditorInstance = editor;

                document.getElementById('clubPostForm').addEventListener('submit', function (e) {
                    const content = editor.getData().trim();
                    if (!content) {
                        e.preventDefault();
                        alert('⚠️ Vui lòng nhập nội dung!');
                        return false;
                    }
                    editor.updateSourceElement();
                });
            }).catch(error => console.error(error));
        });

        async function uploadAndInsertFile() {
            if (!clubPostEditorInstance) { alert('Trình soạn thảo chưa sẵn sàng.'); return; }

            const fileInput = document.getElementById('fileUpload');
            const file = fileInput.files[0];
            const status = document.getElementById('uploadStatus');
            const postId = document.getElementById('postId').value;

            if (!file) { status.textContent = '⚠️ Vui lòng chọn file trước.'; return; }
            status.textContent = '⏳ Đang tải lên...';

            const formData = new FormData();
            formData.append('upload', file);
            formData.append('post_id', postId);

            try {
                const response = await fetch("{{ route('club_manager.posts.upload_file', ['club_id' => $club->id]) }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();

                if (data.url) {
                    let insertHtml = '';
                    const type = file.type.split('/')[0];
                    if (type === 'image') insertHtml = `<img src="${data.url}" alt="${data.name}" style="max-width:100%;">`;
                    else if (type === 'video') insertHtml = `<video controls style="max-width:100%;"><source src="${data.url}" type="${file.type}"></video>`;
                    else if (type === 'audio') insertHtml = `<audio controls><source src="${data.url}" type="${file.type}"></audio>`;
                    else insertHtml = `<a href="${data.url}" download>📎 ${data.name}</a>`;

                    clubPostEditorInstance.model.change(writer => {
                        const pos = clubPostEditorInstance.model.document.selection.getFirstPosition();
                        const viewFragment = clubPostEditorInstance.data.processor.toView(insertHtml);
                        const modelFragment = clubPostEditorInstance.data.toModel(viewFragment);
                        writer.insert(modelFragment, pos);
                    });

                    status.textContent = '✅ Đã tải lên và chèn vào nội dung!';
                    fileInput.value = '';
                } else {
                    status.textContent = '❌ Lỗi: ' + (data.error?.message || 'Không thể tải lên file.');
                }
            } catch (err) {
                status.textContent = '❌ Lỗi: ' + err.message;
            }
        }
    </script>
@endpush