@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Quản lý Bình luận</h1>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách bình luận</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bài viết</th>
                                <th>Người dùng</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>{{ $comment->post->title ?? 'N/A' }}</td>
                                    <td>{{ $comment->user->name ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($comment->content, 50) }}</td>
                                    <td>{{ ucfirst($comment->status) }}</td>
                                    <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                        <form action="{{ route('admin.comments.toggleStatus', $comment->id) }}" method="POST" style="display:inline-block;"
                                              onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái?')">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="fas fa-eye{{ $comment->status === 'visible' ? '-slash' : '' }}"></i> {{ $comment->status === 'visible' ? 'Ẩn' : 'Hiện' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display:inline-block;"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa bình luận?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có bình luận nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
@endpush