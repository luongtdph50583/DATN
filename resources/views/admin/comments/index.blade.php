@extends('admin.layouts.app')

@section('title', 'Quản lý Bình luận')

@section('card-body')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Tiêu đề trang -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Danh sách Bình luận</h1>
        </div>

        <!-- Bảng bình luận -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Người bình luận</th>
                                <th>Bài viết</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Lượt thích</th>
                                <th>Ngày tạo</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>{{ $comment->user->name ?? 'Không rõ' }}</td>
                                    <td>{{ Str::limit($comment->post->title ?? 'Không rõ', 40) }}</td>
                                    <td>{{ Str::limit($comment->content, 60) }}</td>
                                    <td>
                                        @if ($comment->status === 'visible')
                                            <span class="badge bg-success">Hiển thị</span>
                                        @elseif ($comment->status === 'hidden')
                                            <span class="badge bg-secondary">Đã ẩn</span>
                                        @else
                                            <span class="badge bg-warning">Đang chờ</span>
                                        @endif
                                    </td>
                                    <td>{{ $comment->likes_count }}</td>
                                    <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                 <td>
    <div class="d-flex flex-wrap align-items-center gap-1 justify-content-center">
        <!-- Nút chuyển trạng thái (Ẩn / Hiện) -->
        <form action="{{ route('admin.comments.toggleStatus', $comment->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-warning" title="{{ $comment->status === 'visible' ? 'Ẩn bình luận' : 'Hiện bình luận' }}">
                <i class="fas {{ $comment->status === 'visible' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
            </button>
        </form>

        <!-- Nút xem chi tiết -->
        <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
            <i class="fas fa-eye"></i>
        </a>

        <!-- Nút xóa mở modal -->
        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $comment->id }}" title="Xóa">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>
</td>



                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Chưa có bình luận nào.</td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table> 
                    @foreach ($comments as $comment)
                        <div class="modal fade" id="deleteModal{{ $comment->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $comment->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $comment->id }}">Xác nhận xóa
                                                Comment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Bạn có chắc muốn xóa comment này không?</p>
                                            <p><strong>Nội dung:</strong> {{ Str::limit($comment->content, 100) }}</p>

                                            <div class="mb-3">
                                                <label for="deleted_reason_{{ $comment->id }}" class="form-label">Lý do
                                                    xóa</label>
                                                <textarea name="deleted_reason" id="deleted_reason_{{ $comment->id }}" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>
        </div>
    </div>
    <style>
        .btn-toggle {
            min-width: 70px;
            /* bạn có thể chỉnh 60-80px tùy ý */
        }
    </style>
@endsection
