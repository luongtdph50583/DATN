@extends('admin.layouts.app')
@section('title', 'Sửa bài viết')

@section('card-title', 'Sửa bài viết')
@section('card-header')
    <div class="d-flex justify-content-between align-items-center">
        <span> Thông tin bài viết</span>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
@endsection
@section('card-body')
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form id="postForm" action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
    {{-- Ảnh đại diện --}}
    <div class="mb-3">
        <label for="thumbnail" class="form-label">Ảnh đại diện</label>
        @if($post->thumbnail)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="Thumbnail" class="img-thumbnail" width="150">
            </div>
        @endif
        <input type="file" name="thumbnail" id="thumbnail" class="form-control">
        @error('thumbnail') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

            {{-- Trạng thái --}}
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-select">
                    <option value="visible" {{ old('status', $post->status) === 'visible' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="hidden" {{ old('status', $post->status) === 'hidden' ? 'selected' : '' }}>Ẩn</option>
                </select>
                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Tiêu đề --}}
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $post->title) }}">
                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Loại bài viết --}}
            <div class="mb-3">
                <label for="type" class="form-label">Loại bài viết</label>
                <select name="type" id="type" class="form-select">
                    <option value="post" {{ $post->type === 'post' ? 'selected' : '' }}>Post</option>
                    <option value="notice" {{ $post->type === 'notice' ? 'selected' : '' }}>Notice</option>
                    <option value="document" {{ $post->type === 'document' ? 'selected' : '' }}>Document</option>
                </select>
                @error('type') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Hiển thị --}}
            <div class="mb-3">
                <label for="visibility" class="form-label">Hiển thị</label>
                <select name="visibility" id="visibility" class="form-select">
                    <option value="internal" {{ $post->visibility === 'internal' ? 'selected' : '' }}>Nội bộ CLB</option>
                    <option value="public" {{ $post->visibility === 'public' ? 'selected' : '' }}>Công khai</option>
                </select>
                @error('visibility') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Quill Editor --}}
            <div class="mb-3">
                <label for="editor" class="form-label">Nội dung</label>
                <div id="editor"
                    style="min-height: 300px; max-height: 600px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 6px; padding: 10px; background-color: #fff;">
                    {!! old('content', $post->content) !!}
                </div>
                <input type="hidden" name="content" id="contentInput">
                @error('content') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Upload file và chèn vào nội dung --}}
            <div class="mb-3">
                <label for="fileUpload" class="form-label">Đính kèm file</label>
                <input type="file" id="fileUpload" class="form-control">
                <button type="button" class="btn btn-secondary mt-2" onclick="uploadAndInsertFile()">Tải lên & chèn vào nội
                    dung</button>
                <div id="uploadStatus" class="text-muted small mt-1"></div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Cập nhật bài viết</button>
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

            #editor {
                position: relative;
            }
        </style>
@endsection







@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ImageResize = window.ImageResize?.default || window.ImageResize;

            window.quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Nhập nội dung bài viết...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['link', 'image', 'code-block'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'header': [1, 2, 3, false] }],
                        [{ 'align': [] }],
                        ['clean']
                    ],
                    imageResize: {
                        modules: ['Resize', 'DisplaySize', 'Toolbar']
                    }
                }
            });

            quill.getModule('toolbar').addHandler('image', () => {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.click();
                input.onchange = () => {
                    const file = input.files[0];
                    if (file && /^image\//.test(file.type)) {
                        uploadImage(file);
                    } else {
                        alert('Vui lòng chọn file ảnh hợp lệ.');
                    }
                };
            });

            quill.root.addEventListener('paste', function (e) {
                const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        e.preventDefault();
                        const file = items[i].getAsFile();
                        uploadImage(file);
                    }
                }
            });

            document.getElementById('postForm').addEventListener('submit', function (e) {
                const html = quill.root.innerHTML.trim();
                if (html === '' || html === '<p><br></p>') {
                    e.preventDefault();
                    alert('Vui lòng nhập nội dung bài viết!');
                    return;
                }
                document.getElementById('contentInput').value = html;
            });
        });

        async function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);

            try {
                const res = await fetch("{{ route('admin.posts.uploadImage') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await res.json();
                if (data.url) {
                    const range = quill.getSelection(true);
                    const imgTag = `<img src="${data.url}" style="width:150px; height:auto; display:inline-block; margin-right:10px;" />`;
                    quill.clipboard.dangerouslyPasteHTML(range.index, imgTag);
                    quill.setSelection(range.index + 1);
                } else {
                    throw new Error('Không nhận được URL ảnh');
                }
            } catch (err) {
                console.error(err);
                alert('Không thể upload ảnh. Vui lòng thử lại.');
            }
        }


                async function uploadAndInsertFile() {
                        const fileInput = document.getElementById('fileUpload');
                        const file = fileInput.files[0];
                        const status = document.getElementById('uploadStatus');

                        if (!file) {
                            status.textContent = '⚠️ Vui lòng chọn file trước.';
                            return;
                        }

                        const formData = new FormData();
                        formData.append('file', file);

                        // 👇 Nếu đang chỉnh sửa bài viết thì thêm ID vào
                        const postIdInput = document.getElementById('post_id');
                        if (postIdInput && postIdInput.value) {
                            formData.append('related_id', postIdInput.value);
                        }

                        // 👇 Thêm luôn type = post để không phải hardcode trong Controller
                        formData.append('related_type', 'post');

                        try {
                            const res = await fetch("{{ route('admin.posts.uploadFile') }}", {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: formData
                            });

                            const data = await res.json();

                            if (data.success && data.url) {
                                const range = quill.getSelection(true);
                                const fileType = data.type.startsWith('image')
                                    ? `<img src="${data.url}" alt="${data.name}" class="rounded shadow mb-2" style="max-width: 100%;">`
                                    : `<p><a href="${data.url}" target="_blank">📎 ${data.name}</a></p>`;

                                quill.clipboard.dangerouslyPasteHTML(range.index, fileType);
                                quill.setSelection(range.index + 1);
                                status.textContent = '✅ Đã chèn file vào nội dung.';
                                fileInput.value = '';
                            } else {
                                status.textContent = '❌ ' + (data.message || 'Không thể upload file.');
                            }
                        } catch (err) {
                            console.error(err);
                            status.textContent = '⚠️ Lỗi khi tải lên file.';
                        }
                    }
                </script>



    </script>
@endpush

