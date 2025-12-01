@extends('client.layouts.app')
@section('title', 'Quản lý bài viết - ' . $club->name)

@section('content')

    <div class="container mt-4 mb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Quản lý bài viết - {{ $club->name }}</h2>
            <a href="{{ route('club_manager.posts.create', ['club_id' => $club->id]) }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Thêm bài viết
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        <th>Người đăng</th>
                        <th>Ngày đăng</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $index => $post)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->user->name ?? 'N/A' }}</td>
                            <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($post->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @elseif($post->status === 'rejected')
                                    <span class="badge bg-danger">Từ chối</span>
                                @else
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('club_manager.posts.show', ['club_id' => $club->id, 'post' => $post->id]) }}"
                                   class="btn btn-sm btn-info me-1" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                    <a href="{{ route('club_manager.posts.edit', ['club_id' => $club->id, 'post' => $post->id]) }}"
                                       class="btn btn-sm btn-primary me-1" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                            <form action="{{ route('club_manager.posts.destroy', ['club_id' => $club->id, 'post' => $post->id]) }}" method="POST"
                                class="d-inline" onsubmit="return confirmDeleteWithReason(this);">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="delete_reason" value="">
                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có bài viết nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $posts->links() }}
    </div>
@endsection
@push('scripts')
    <script>
        function confirmDeleteWithReason(form) {
            const reason = prompt("Nhập lý do xóa bài viết:");
            if (reason === null) {
                // Người dùng bấm Cancel
                return false;
            }
            if (reason.trim() === "") {
                alert("Bạn phải nhập lý do xóa.");
                return false;
            }
            form.querySelector('input[name="delete_reason"]').value = reason;
            return true;
        }
    </script>
@endpush

