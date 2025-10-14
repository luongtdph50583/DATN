@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Chi tiết Bình luận</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Bình luận #{{ $comment->id }}</h6>
            </div>
            <div class="card-body">
                <p><strong>Bài viết:</strong> {{ $comment->post->title ?? 'N/A' }}</p>
                <p><strong>Người dùng:</strong> {{ $comment->user->name ?? 'N/A' }}</p>
                <p><strong>Nội dung:</strong> {{ $comment->content }}</p>
                <p><strong>Trạng thái:</strong> {{ ucfirst($comment->status) }}</p>
                <p><strong>Ngày tạo:</strong> {{ $comment->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Ngày cập nhật:</strong> {{ $comment->updated_at->format('d/m/Y H:i') }}</p>
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
            </div>
        </div>
    </div>
@endsection