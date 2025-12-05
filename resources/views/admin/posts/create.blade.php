@extends('admin.layouts.app')

@section('title', 'Thêm bài viết')
@section('card-title', 'Thêm bài viết')
@section('card-header')
    <div class="d-flex justify-content-between align-items-center">
        <span>Nhập thông tin bài viết mới</span>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
@endsection

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="postForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Tiêu đề --}}
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
            @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Ảnh đại diện --}}
        <div class="mb-3">
            <label for="thumbnail" class="form-label">Ảnh đại diện</label>
            <input type="file" name="thumbnail" id="thumbnail" class="form-control">
            @error('thumbnail') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Loại bài viết --}}
        <div class="mb-3">
            <label for="type" class="form-label">Loại bài viết</label>
            <select name="type" id="type" class="form-select" required>
                <option value="">-- Chọn loại --</option>
                <option value="post" {{ old('type') === 'post' ? 'selected' : '' }}>Post</option>
                <option value="notice" {{ old('type') === 'notice' ? 'selected' : '' }}>Notice</option>
                <option value="document" {{ old('type') === 'document' ? 'selected' : '' }}>Document</option>
            </select>
            @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Câu lạc bộ --}}
        <div class="mb-3">
            <label for="club_id" class="form-label">Câu lạc bộ</label>
            <select name="club_id" id="club_id" class="form-select select2-club" required>
                <option value="">-- Chọn CLB --</option>
                @foreach($clubs ?? [] as $club)
                    <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                        {{ $club->name }}
                    </option>
                @endforeach
            </select>
            @error('club_id') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Hiển thị bài viết (ẩn/hiện) --}}
        <div class="mb-3">
            <label for="is_visible" class="form-label">Ẩn/Hiện bài viết</label>
            <select name="is_visible" id="is_visible" class="form-select">
                <option value="1" {{ old('is_visible', '1') == '1' ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ old('is_visible') == '0' ? 'selected' : '' }}>Ẩn</option>
            </select>
            @error('is_visible') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Gắn nổi bật --}}
        <div class="mb-3">
            <label for="is_featured" class="form-label">Gắn nổi bật</label>
            <select name="is_featured" id="is_featured" class="form-select">
                <option value="0" {{ old('is_featured') == '0' ? 'selected' : '' }}>Không</option>
                <option value="1" {{ old('is_featured') == '1' ? 'selected' : '' }}>Có</option>
            </select>
            @error('is_featured') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Hiển thị cho ai --}}
        <div class="mb-3">
            <label for="visibility" class="form-label">Hiển thị</label>
            <select name="visibility" id="visibility" class="form-select" required>
                <option value="">-- Chọn chế độ --</option>
                <option value="internal" {{ old('visibility') === 'internal' ? 'selected' : '' }}>Nội bộ CLB</option>
                <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Công khai</option>
            </select>
            @error('visibility') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Nội dung --}}
        <div class="mb-3">
            <label for="postContentEditor" class="form-label">Nội dung</label>
            <textarea name="content" id="postContentEditor" class="form-control" rows="10">{{ old('content') }}</textarea>
            @error('content') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- Upload file và chèn vào nội dung --}}
        <div class="mb-3">
            <label for="fileUpload" class="form-label">Đính kèm file (hỗ trợ: ảnh, video, audio, PDF, Word, Excel...)</label>
            <input type="file" id="fileUpload" class="form-control" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
            <button type="button" id="uploadBtn" class="btn btn-secondary mt-2">
                <i class="fas fa-upload me-1"></i> Tải lên & chèn vào nội dung
            </button>
            <div id="uploadStatus" class="text-muted small mt-1"></div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Thêm bài viết</button>
        </div>
    </form>

    <style>
        select.form-select {
            height: 42px;
            font-size: 15px;
            border-radius: 6px;
        }

        .mb-3 {
            position: relative;
        }

        /* 🎯 Style cho media trong CKEditor */
        .ck-content img {
            max-width: 100%;
            max-height: 400px;
            height: auto;
            display: block;
            margin: 1rem auto;
        }

        .ck-content video {
            max-width: 100%;
            max-height: 450px;
            display: block;
            margin: 1rem auto;
        }

        .ck-content audio {
            max-width: 500px;
            width: 100%;
            display: block;
            margin: 1rem auto;
        }
    </style>

    <!-- Include Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        $('.select2-club').select2({
            width: '100%',
            placeholder: "-- Chọn CLB --",
            allowClear: true
        });
    });
    </script>
@endsection

@push('scripts')
    <!-- Initialize Select2 (jQuery đã có sẵn trong layout) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Kiểm tra jQuery có tồn tại không
        if (typeof jQuery === 'undefined') {
            console.error('❌ jQuery chưa được load!');
            return;
        }

        // Initialize Select2
        jQuery(document).ready(function($) {
            $('.select2-club').select2({
                width: '100%',
                placeholder: "-- Chọn CLB --",
                allowClear: true
            });
        });
    });
    </script>

    <script>
    console.log('✅ Script loaded!');

    let postContentEditorInstance = null;

    // Khởi tạo CKEditor
    function initCKEditor() {
        console.log('🔧 Initializing CKEditor...');

        if (typeof ClassicEditor === 'undefined') {
            console.error('❌ CKEditor chưa được tải, thử lại sau 500ms...');
            setTimeout(initCKEditor, 500);
            return;
        }

        ClassicEditor.create(document.querySelector('#postContentEditor'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'link',
                    '|', 'bulletedList', 'numberedList',
                    '|', 'blockQuote', 'insertImage', 'insertTable',
                    '|', 'undo', 'redo'
                ]
            },
            simpleUpload: {
                uploadUrl: "{{ route('admin.posts.uploadImage') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                withCredentials: true
            },
            image: {
                toolbar: ['imageTextAlternative'],
                resizeOptions: [
                    {
                        name: 'resizeImage:original',
                        label: 'Original',
                        value: null
                    },
                    {
                        name: 'resizeImage:50',
                        label: '50%',
                        value: '50'
                    },
                    {
                        name: 'resizeImage:75',
                        label: '75%',
                        value: '75'
                    }
                ]
            }
        }).then(editor => {
            postContentEditorInstance = editor;
            console.log('✅ CKEditor initialized!', editor);

            const form = document.getElementById('postForm');
            form.addEventListener('submit', function () {
                editor.updateSourceElement();
            });
        }).catch(error => {
            console.error('❌ CKEditor init error:', error);
        });
    }

    // Khởi tạo khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCKEditor);
    } else {
        initCKEditor();
    }

    // Upload function
    async function uploadAndInsertFile() {
        console.log('🚀 uploadAndInsertFile called!');

        if (!postContentEditorInstance) {
            console.error('❌ Editor chưa sẵn sàng');
            alert('Trình soạn thảo chưa sẵn sàng. Vui lòng đợi một chút.');
            return;
        }

        console.log('✅ Editor ready:', postContentEditorInstance);

        const fileInput = document.getElementById('fileUpload');
        const file = fileInput.files[0];
        const status = document.getElementById('uploadStatus');

        if (!file) {
            status.textContent = '⚠️ Vui lòng chọn file trước.';
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('related_type', 'post');

        try {
            status.textContent = '⏳ Đang tải lên...';

            const res = await fetch("{{ route('admin.posts.uploadFile') }}", {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const data = await res.json();
            console.log('📦 Response data:', data);

            if (data.success && data.url) {
                console.log('✅ Upload thành công!');
                console.log('Type:', data.type);
                console.log('URL:', data.url);

                // ✅ Insert vào editor - cách đơn giản nhất
                if (data.type && data.type.includes('image')) {
                    console.log('🖼️ Inserting image...');

                    const currentData = postContentEditorInstance.getData();
                    const newData = currentData + `<p><img src="${data.url}" style="max-width: 100%; max-height: 400px; height: auto;"></p>`;
                    postContentEditorInstance.setData(newData);

                    status.textContent = '✅ Đã chèn ảnh vào nội dung.';
                    fileInput.value = '';
                    return;

                } else if (data.type && data.type.includes('video')) {
                    console.log('🎬 Inserting video link...');

                    const currentData = postContentEditorInstance.getData();
                    const newData = currentData + `<p>🎬 <a href="${data.url}" target="_blank">${data.name}</a> (Click để xem video)</p>`;
                    postContentEditorInstance.setData(newData);

                    status.textContent = '✅ Đã chèn link video vào nội dung.';
                    fileInput.value = '';
                    return;

                } else if (data.type && data.type.includes('audio')) {
                    console.log('🎵 Inserting audio link...');

                    const currentData = postContentEditorInstance.getData();
                    const newData = currentData + `<p>🎵 <a href="${data.url}" target="_blank">${data.name}</a> (Click để nghe audio)</p>`;
                    postContentEditorInstance.setData(newData);

                    status.textContent = '✅ Đã chèn link audio vào nội dung.';
                    fileInput.value = '';
                    return;

                } else {
                    console.log('📎 Inserting file link...');

                    const currentData = postContentEditorInstance.getData();
                    const newData = currentData + `<p>📎 <a href="${data.url}" target="_blank">${data.name}</a></p>`;
                    postContentEditorInstance.setData(newData);

                    status.textContent = '✅ Đã chèn link file vào nội dung.';
                    fileInput.value = '';
                    return;
                }
            } else {
                status.textContent = '❌ ' + (data.message || 'Không thể upload file.');
            }
        } catch (err) {
            console.error('❌ Upload error:', err);
            status.textContent = '⚠️ Lỗi khi tải lên file: ' + err.message;
        }
    }

    // Gắn sự kiện click cho nút upload khi DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        const uploadBtn = document.getElementById('uploadBtn');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', function() {
                console.log('🖱️ Button clicked!');
                uploadAndInsertFile();
            });
            console.log('✅ Upload button event attached!');
        } else {
            console.error('❌ Upload button not found!');
        }
    });
    </script>
@endpush
