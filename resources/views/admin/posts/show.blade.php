@extends('dashboard')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Chi tiết bài viết</h6>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">← Quay lại danh sách</a>
    </div>
    <div class="card-body">
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
        <div class="col-md-4 mb-4">
            <div class="border p-2 rounded">
                <p class="mb-1"><strong>{{ $media->file_name }}</strong></p>
                <p class="text-muted">Loại: {{ $media->file_type }}</p>
                <p class="text-muted">Ngày tải lên: {{ $media->created_at->format('d/m/Y H:i') }}</p>

                @php
                    $type = strtolower($media->file_type);
                @endphp

                @if(Str::startsWith($type, 'image'))
                    <img src="{{ asset($media->file_path) }}" alt="{{ $media->file_name }}" class="img-fluid rounded">
                @elseif(Str::startsWith($type, 'video'))
                    <video controls class="w-100">
                        <source src="{{ asset($media->file_path) }}" type="{{ $media->file_type }}">
                        Trình duyệt của bạn không hỗ trợ video.
                    </video>
                @elseif(Str::endsWith($type, 'pdf'))
                    <iframe src="{{ asset($media->file_path) }}" width="100%" height="400px"></iframe>
                @elseif(Str::endsWith($type, 'doc') || Str::endsWith($type, 'docx') || Str::endsWith($type, 'xls') || Str::endsWith($type, 'xlsx') || Str::endsWith($type, 'cgi'))
                    <a href="{{ asset($media->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">Tải về</a>
                @else
                    <p class="text-muted">Không thể hiển thị trực tiếp. <a href="{{ asset($media->file_path) }}" target="_blank">Tải về</a></p>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12">
            <p>Không có media đính kèm.</p>
        </div>
    @endforelse
</div>
<pre>{{ print_r($post->media->toArray(), true) }}</pre>

        </div>
    </div>
</div>
@endsection
