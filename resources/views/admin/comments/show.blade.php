@extends('admin.layouts.app')

@section('title', 'Chi tiết bình luận')

@section('card-body')
<div class="container mt-4">

    <!-- Bài viết liên quan -->
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="related-post-title">Bài viết liên quan</h3>
    <a href="#" class="btn btn-outline-primary btn-sm">Xem bài viết <i class="fas fa-arrow-right ms-1"></i></a>
</div>

<!-- Card bài viết -->
<div class="card card-post mb-4">
    <div class="row g-0 align-items-center">

        <div class="col-md-8">
            <div class="card-body">
                <h5 class="fw-bold mb-2">{{ $comment->post->title ?? 'Không xác định' }}</h5>
                <p class="text-muted mb-3">{{ $comment->post->content ?? 'Nội dung không khả dụng' }}</p>

              

                <p class="mb-1"><strong>Trạng thái bài viết:</strong> 
                    @if($comment->post->status === 'visible')
                        <span class="badge badge-bg-gradient" title="Đang hiển thị">Đang hiển thị</span>
                    @else
                        <span class="badge badge-bg-gradient" title="Đã ẩn">Đã ẩn</span>
                    @endif
                </p>

                <p class="mb-0"><strong>Loại bài viết:</strong> 
                    <span class="badge badge-bg-gradient">{{ ucfirst($comment->post->type) }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Thông tin bình luận -->
    <div class="card shadow mb-4">
        <div class="card-header card-header-modern">
            <i class="fas fa-comments"></i>
            <h4 class="mb-0">Thông tin bình luận</h4>
        </div>

        <div class="card-body">
            <table class="table table-bordered align-middle table-custom">
                <tbody>
                    <tr>
                        <th>ID bình luận</th>
                        <td>{{ $comment->id }}</td>
                    </tr>
                    <tr>
                        <th>Người bình luận</th>
                        <td>{{ $comment->user->name ?? 'Ẩn danh' }}</td>
                    </tr>
                    <tr>
                        <th>Nội dung</th>
                        <td class="text-break">{{ $comment->content }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            @if($comment->status === 'visible')
                                <span class="badge badge-bg-gradient">Hiển thị</span>
                            @elseif($comment->status === 'hidden')
                                <span class="badge badge-bg-gradient">Đã ẩn</span>
                            @else
                                <span class="badge badge-bg-gradient">Chờ duyệt</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Số lượt thích</th>
                        <td>{{ $comment->likes_count }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                 
                </tbody>
            </table>

            <div class="mt-3">
                <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Card bài viết hiện đại */
.card-post {
    display: flex;
    flex-direction: row;
    gap: 1.5rem;
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}
.card-post:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

/* Header hiện đại cho bình luận */
.card-header-modern {
    background: linear-gradient(135deg, #4e54c8, #8f94fb);
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1rem 1.25rem;
    border-radius: .5rem .5rem 0 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Bảng hiện đại */
.table-custom {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    background-color: #fff;
}
.table-custom th {
    background-color: #f3f4f6;
    font-weight: 600;
    color: #333;
}
.table-custom td, .table-custom th {
    padding: 0.75rem 1rem;
    vertical-align: middle;
}
.table-custom tr:hover {
    background-color: #eef2ff;
    transition: background-color 0.2s;
}

/* Badge gradient */
.badge-bg-gradient {
    background: linear-gradient(90deg, #667eea, #764ba2);
    color: #fff;
    font-weight: 500;
}

/* Ảnh bài viết */
.post-image {
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    transition: transform 0.3s, box-shadow 0.3s;
}
.post-image:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 25px rgba(0,0,0,0.18);
}

/* Responsive mobile */
@media(max-width: 768px) {
    .card-post {
        flex-direction: column;
        text-align: center;
    }
    .card-post .col-md-8 {
        margin-top: 1rem;
    }
}
</style>
@endsection


