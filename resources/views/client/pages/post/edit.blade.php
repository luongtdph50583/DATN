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

                            {{-- Upload file và chèn vào nội dung --}}
                            <div class="mt-3">
                                <label for="fileUpload" class="form-label">Đính kèm file (hỗ trợ: ảnh, video, audio, PDF,
                                    Word, Excel...)</label>
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

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    let clubPostEditorInstance = null;

    document.addEventListener('DOMContentLoaded', function () {

        ClassicEditor.create(document.querySelector('#clubPostEditor'), {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link',
                '|', 'bulletedList', 'numberedList',
                '|', 'blockQuote', 'insertTable',
                '|', 'undo', 'redo'
            ],
            simpleUpload: {
                uploadUrl: "{{ route('club_manager.posts.upload_image', ['club_id' => $club->id]) }}",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }
        })
            .then(editor => {
                clubPostEditorInstance = editor;

                // AUTO HEIGHT
                editor.editing.view.document.on('change:data', () => {
                    const editable = editor.ui.view.editable.element;
                    editable.style.height = "auto";
                    editable.style.height = editable.scrollHeight + "px";
                });

                // VALIDATE submit
                document.getElementById('clubPostForm').addEventListener('submit', function (e) {
                    clubPostEditorInstance.updateSourceElement();
                    const content = document.getElementById('clubPostEditor').value.trim();
                    if (!content) {
                        e.preventDefault();
                        alert('Nội dung không được để trống.');
                        clubPostEditorInstance.editing.view.focus();
                    }
                });
            })
            .catch(error => console.error(error));


        // SELECT2
        if (typeof $.fn.select2 !== 'undefined') {
            $('select[data-select2="true"]').each(function () {
                $(this).select2({
                    width: '100%',
                    placeholder: $(this).data('placeholder') || '',
                    allowClear: true,
                    language: {
                        noResults: () => "Không tìm thấy kết quả",
                        searching: () => "Đang tìm kiếm..."
                    }
                });
            });
        }

    });



// === Upload file & chèn vào editor ===
async function uploadAndInsertFile() {
    if (!clubPostEditorInstance) {
        alert('Editor chưa sẵn sàng.');
        return;
    }

    const fileInput = document.getElementById('fileUpload');
    const file = fileInput.files[0];
    const status = document.getElementById('uploadStatus');
    const postId = document.getElementById('postId')?.value;

    if (!file) {
        status.textContent = '⚠️ Vui lòng chọn file trước.';
        return;
    }

    status.textContent = '⏳ Đang tải lên...';

    const formData = new FormData();
    formData.append('file', file);
    if (postId) formData.append('post_id', postId);
    formData.append('related_type', 'post');

    try {
        let response = await fetch("{{ route('club_manager.posts.upload_file', ['club_id' => $club->id]) }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        });

        const data = await response.json();

        if (!data.url) {
            status.textContent = '❌ Upload thất bại.';
            return;
        }

        const url = data.url;
        const name = data.name || file.name;
        const type = data.type || file.type;

        let htmlToInsert = '';

        if (type.includes('image')) {
            htmlToInsert = `<p><img src="${url}" alt="${name}" style="max-width:100%;height:auto;"></p>`;
        } else if (type.includes('video')) {
            htmlToInsert = `<p>🎬 <a href="${url}" target="_blank">${name}</a></p>`;
        } else if (type.includes('audio')) {
            htmlToInsert = `<p>🎵 <a href="${url}" target="_blank">${name}</a></p>`;
        } else {
            htmlToInsert = `<p>📎 <a href="${url}" target="_blank">${name}</a></p>`;
        }

        // DÙNG model.insertContent để chèn an toàn + nhúng hợp lệ
        clubPostEditorInstance.model.change( writer => {
            const viewFragment = clubPostEditorInstance.data.processor.toView( htmlToInsert );
            const modelFragment = clubPostEditorInstance.data.toModel( viewFragment );

            clubPostEditorInstance.model.insertContent( modelFragment );

            // Sau khi chèn, thêm 1 đoạn paragraph trống để xuống dòng / không dính ảnh
            writer.insertElement( 'paragraph', clubPostEditorInstance.model.document.selection.getFirstPosition() );
        });

        status.textContent = '✅ Upload & chèn thành công!';
        fileInput.value = '';

    } catch (err) {
        status.textContent = '❌ Lỗi: ' + err.message;
    }
}

</script>


