@extends('admin.layouts.app')

@section('title', 'Bình luận đã xóa')

@section('card-body')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Danh sách Bình luận đã xóa</h1>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Người bình luận</th>
                        <th>Bài viết</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Lượt thích</th>
                        <th>Ngày xóa</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->user->name ?? 'Ẩn danh' }}</td>
                            <td>{{ Str::limit($comment->post->title ?? 'Không rõ', 40) }}</td>
                            <td>{{ Str::limit($comment->content, 60) }}</td>
                            <td>
                                @if ($comment->status === 'visible')
                                    <span class="badge bg-success">Hiển thị</span>
                                @elseif ($comment->status === 'hidden')
                                    <span class="badge bg-secondary">Đã ẩn</span>
                                @else
                                    <span class="badge bg-warning text-dark">Đang chờ</span>
                                @endif
                            </td>
                            <td>{{ $comment->likes_count }}</td>
                            <td>{{ $comment->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                    <!-- Restore -->
                                    <form action="{{ route('admin.comments.restore', $comment->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-recycle"></i> Khôi phục
                                        </button>
                                    </form>

                                    <!-- Force Delete -->
                                    <form action="{{ route('admin.comments.forceDelete', $comment->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn bình luận này?')">
                                            <i class="fas fa-trash-alt"></i> Xóa vĩnh viễn
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có bình luận nào bị xóa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
