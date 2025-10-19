


@extends('admin.layouts.app')
@section('title')
    trang admin
@endsection

@section('card-title')
    Quản lý bai viet
@endsection

@section('card-header')
    chi tiet bai viet
@endsection

@section('card-body')
      <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td>{{ $post->id }}</td>
                </tr>
                <tr>
                    <th>Tiêu đề</th>
                    <td>{{ $post->title }}</td>
                </tr>
                <tr>
                    <th>CLB</th>
                    <td>{{ $post->club->name ?? 'Không xác định' }}</td>
                </tr>
                <tr>
                    <th>Người đăng</th>
                    <td>{{ $post->user->name ?? 'Không xác định' }}</td>
                </tr>
                <tr>
                    <th>Loại</th>
                    <td>{{ $post->type }}</td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>{{ $post->status === 'visible' ? 'Hiển thị' : 'Ẩn' }}</td>
                </tr>
                <tr>
                    <th>Ngày đăng</th>
                    <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Nội dung</th>
                    <td>{!! nl2br(e($post->content)) !!}</td>
                </tr>
            </table>
        </div>

<h6 class="font-weight-bold text-primary">Media đính kèm</h6>
<div class="row">
    @forelse($post->media as $media)
        @php
            $mime = strtolower($media->file_type);
            $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);
            $type = $extension ?: explode('/', $mime)[1] ?? '';
            $type = strtolower($type);

            // Do trong DB lưu kiểu "images/img1.jpg" => thêm "storage/"
            $path = asset('storage/' . $media->file_path);
        @endphp

        <div class="col-md-4 mb-4">
            <div class="border p-3 rounded shadow-sm text-center bg-light">
                <p class="mb-1 fw-bold text-dark">{{ $media->file_name }}</p>
                <p class="text-muted mb-1">Loại: {{ strtoupper($type) }}</p>
                <p class="text-muted mb-2">Tải lên: {{ $media->created_at->format('d/m/Y H:i') }}</p>

                {{-- 🖼️ Hình ảnh --}}
                @if(in_array($type, ['jpg','jpeg','png','gif','webp']))
                    <img src="{{ $path }}"
                         alt="{{ $media->file_name }}"
                         class="img-fluid rounded mb-2"
                         style="max-height:250px;object-fit:cover;">

                {{-- 🎬 Video --}}
                @elseif(in_array($type, ['mp4','mov','avi','mkv']))
                    <video controls class="w-100 rounded mb-2" style="max-height:250px;">
                        <source src="{{ $path }}" type="video/{{ $type }}">
                        Trình duyệt của bạn không hỗ trợ video.
                    </video>

                {{-- 🎧 Audio --}}
                @elseif(in_array($type, ['mp3','wav','ogg','m4a']))
                    <audio controls class="w-100 mb-2">
                        <source src="{{ $path }}" type="audio/{{ $type }}">
                        Trình duyệt của bạn không hỗ trợ audio.
                    </audio>

                {{-- 📄 PDF --}}
                @elseif($type === 'pdf')
                    <iframe src="{{ $path }}" width="100%" height="250px" class="rounded mb-2"></iframe>

                {{-- 🧾 File văn bản / Office --}}
                @elseif(in_array($type, ['doc','docx','xls','xlsx','ppt','pptx','txt']))
                    <div class="text-center my-3">
                        <i class="bi bi-file-earmark-text fs-1 text-secondary"></i>
                        <p class="mb-1">Tài liệu {{ strtoupper($type) }}</p>
                    </div>

                {{-- 📦 Các loại khác --}}
                @else
                    <div class="text-center my-3">
                        <i class="bi bi-file-earmark fs-1 text-muted"></i>
                        <p class="mb-1">Không thể hiển thị trực tiếp</p>
                    </div>
                @endif

                {{-- ⬇️ Nút tải xuống --}}
                <div class="text-center mt-2">
                    <a href="{{ $path }}" download class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i> Tải xuống
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center">
            <p class="text-muted">Không có media đính kèm.</p>
        </div>
    @endforelse
</div>


@endsection

