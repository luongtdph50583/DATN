@extends('admin.layouts.app')

@section('title', 'Chi tiết bài viết')

@section('card-body')

    {{-- Tiêu đề bài viết --}}
    <h3 class="text-primary fw-bold mb-3 text-center">
        <i class="bi bi-file-text me-2"></i> {{ $post->title }}
    </h3>

    {{-- Thumbnail nếu có --}}
    @if($post->thumbnail)
        <div class="text-center mb-4">
            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="Thumbnail" class="img-fluid rounded shadow-sm"
                style="max-height: 300px; object-fit: cover;">
        </div>
    @endif

    {{-- Thông tin bài viết --}}
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped align-middle">
            <tbody>
                <tr>
                    <th style="width: 200px;">ID bài viết</th>
                    <td>{{ $post->id }}</td>
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
                    <th>Loại bài viết</th>
                    <td><span class="badge bg-primary">{{ strtoupper($post->type) }}</span></td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        <span class="badge bg-{{ $post->status === 'visible' ? 'success' : 'secondary' }}">
                            {{ $post->status === 'visible' ? 'Hiển thị' : 'Ẩn' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Hiển thị</th>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($post->visibility) }}</span></td>
                </tr>
                <tr>
                    <th>Ngày đăng</th>
                    <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Nội dung bài viết --}}
    <div class="mb-5">
        <h5 class="text-muted mb-2">Nội dung bài viết</h5>
        <div class="border rounded p-3 bg-white">
            {!! $post->content !!}
        </div>
    </div>

    {{-- Media đính kèm --}}
    <h5 class="fw-bold text-primary mb-3">Media đính kèm</h5>
<div class="row">
    @forelse($post->media as $media)
        @php
            $path = asset('storage/' . $media->file_path);
            $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
            $mime = strtolower($media->file_type);
            $type = $extension ?: (Str::contains($mime, '/') ? explode('/', $mime)[1] : 'other');

            $isImage = in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $isVideo = in_array($type, ['mp4', 'mov', 'avi', 'mkv']);
            $isAudio = in_array($type, ['mp3', 'wav', 'ogg', 'm4a']);
            $isDocument = in_array($type, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
        @endphp

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-truncate" title="{{ $media->file_name }}">
                        <i class="bi bi-file-earmark me-1"></i> {{ $media->file_name }}
                    </h6>
                    <p class="text-muted small mb-2">Loại: {{ strtoupper($type) }}</p>

                    {{-- Hiển thị nội dung media --}}
                    @if($isImage)
                        <img src="{{ $path }}" alt="{{ $media->file_name }}" class="img-fluid rounded mb-2"
                            style="max-height:180px; object-fit:cover;">
                    @elseif($isVideo)
                        <div class="ratio ratio-16x9 mb-2">
                            <video controls class="rounded">
                                <source src="{{ $path }}" type="{{ $mime }}">
                                Trình duyệt không hỗ trợ video.
                            </video>
                        </div>
                    @elseif($isAudio)
                        <audio controls class="w-100 mb-2">
                            <source src="{{ $path }}" type="{{ $mime }}">
                            Trình duyệt không hỗ trợ audio.
                        </audio>
                    @elseif($isDocument)
                        <div class="text-center my-3">
                            <i class="bi bi-file-earmark-text fs-1 text-secondary"></i>
                            <p class="mb-1">Tài liệu {{ strtoupper($type) }}</p>
                        </div>
                    @else
                        <div class="text-center my-3">
                            <i class="bi bi-file-earmark fs-1 text-muted"></i>
                            <p class="mb-1">Không thể hiển thị trực tiếp</p>
                        </div>
                    @endif

                    <a href="{{ $path }}" download class="btn btn-sm btn-outline-primary mt-2">
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