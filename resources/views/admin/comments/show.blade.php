@extends('admin.layouts.app')

@section('title', 'Chi tiết bình luận')

@section('card-body')
<div class="container mt-4">

    <!-- Bài viết -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $comment->post->title ?? 'Không xác định' }}</h5>
            <p class="text-muted">{!! $comment->post->content ?? 'Nội dung không khả dụng' !!}</p>
            <p>Loại bài viết: <span class="badge bg-primary">{{ ucfirst($comment->post->type) }}</span></p>
            <p>Trạng thái bài viết: 
                @if($comment->post->status === 'approved')
                    <span class="badge bg-success">Hiển thị</span>
                @else
                    <span class="badge bg-secondary">Đã ẩn</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Chi tiết bình luận -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Thông tin bình luận</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>ID</th><td>{{ $comment->id }}</td></tr>
                <tr><th>Người bình luận</th><td>{{ $comment->user->name ?? 'Ẩn danh' }}</td></tr>
                <tr><th>Nội dung</th><td>{{ $comment->content }}</td></tr>
                <tr><th>Trạng thái</th>
                    <td>
                        @if($comment->status === 'visible')
                            <span class="badge bg-success">Hiển thị</span>
                        @elseif($comment->status === 'hidden')
                            <span class="badge bg-secondary">Đã ẩn</span>
                        @else
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @endif
                    </td>
                </tr>
                <tr><th>Lượt thích</th><td>{{ $comment->likes_count }}</td></tr>
                <tr><th>Ngày tạo</th><td>{{ $comment->created_at->format('d/m/Y H:i') }}</td></tr>
                @if($comment->deleted_at)
                    <tr><th>Đã xóa</th><td>{{ $comment->deleted_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Lý do xóa</th><td>{{ $comment->deleted_reason ?? '-' }}</td></tr>
                @endif
            </table>

            <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </div>
</div>
@endsection
