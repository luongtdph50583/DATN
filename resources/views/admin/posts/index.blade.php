@extends('admin.layouts.app')

@section('title', 'Quản lý bài viết')

@section('card-header')
    Tin tức & Bài viết
@endsection

@section('card-body')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Nút thêm bài viết --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.posts.create') }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus me-1"></i> Thêm bài viết
        </a>
    </div>

    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.posts.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                    placeholder="Tìm theo tiêu đề hoặc người đăng...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">-- Trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="is_visible" class="form-control">
                    <option value="">-- Hiển thị --</option>
                    <option value="1" {{ request('is_visible') == '1' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ request('is_visible') == '0' ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    {{-- Bảng danh sách bài viết --}}
    <div class="table-responsive">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tiêu đề</th>
                    <th>Người đăng</th>
                    <th>Ngày đăng</th>
                    <th>Trạng thái</th>
                    <th>Hiển thị</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $index => $post)
                    <tr>
                        <td>{{ ($posts->currentPage() - 1) * $posts->perPage() + $index + 1 }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->user->name ?? 'Không xác định' }}</td>
                        <td>{{ $post->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span
                                class="badge bg-{{ $post->status === 'approved' ? 'success' : ($post->status === 'rejected' ? 'danger' : 'secondary') }}">
                                @if($post->status === 'approved')
                                    Đã duyệt
                                @elseif($post->status === 'rejected')
                                    Từ chối
                                @else
                                    Chờ duyệt
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $post->is_visible ? 'info' : 'dark' }}">
                                {{ $post->is_visible ? 'Hiển thị' : 'Ẩn' }}
                            </span>
                        </td>
                        <td>
                            {{-- Xem --}}
                            <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-warning btn-sm me-1"
                                title="Xem">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Nếu đang chờ duyệt --}}
                            @if ($post->status === 'pending')
                                {{-- Duyệt --}}
                                <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST" class="d-inline me-1">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm" title="Duyệt">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>

                                {{-- Từ chối --}}
                                <button type="button" class="btn btn-danger btn-sm me-1" onclick="toggleRejectForm({{ $post->id }})"
                                    title="Từ chối">
                                    <i class="fas fa-times-circle"></i>
                                </button>

                                <form id="rejectForm-{{ $post->id }}" action="{{ route('admin.posts.reject', $post->id) }}"
                                    method="POST" class="mt-2" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group" style="max-width: 400px;">
                                        <input type="text" name="rejection_reason" class="form-control"
                                            placeholder="Lý do từ chối..." required>
                                        <button type="submit" class="btn btn-danger" title="Xác nhận từ chối">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </div>
                                </form>
                            @endif

                          

                            {{-- Xóa --}}
                        <form id="delete-form-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $post->id }})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> Không có bài viết nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $posts->appends(request()->query())->links() }}
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(postId) {
            // Bước 1: xác nhận xoá
            if (!confirm("Bạn có chắc chắn muốn xóa bài viết này?")) {
                return;
            }

            // Bước 2: prompt nhập lý do xoá
            const reason = prompt("Nhập lý do xóa bài viết:");
            if (!reason || reason.trim() === "") {
                alert("Bạn phải nhập lý do xóa.");
                return;
            }

            // Bước 3: gắn lý do vào form và submit
            const form = document.getElementById(`delete-form-${postId}`);
            if (!form) {
                alert("Không tìm thấy form xóa.");
                return;
            }

            // Xóa input cũ nếu có
            let input = form.querySelector('input[name="reason"]');
            if (input) input.remove();

            // Tạo input ẩn mới
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'reason';
            input.value = reason.trim();
            form.appendChild(input);

            form.submit();
        }

        // Toggle hiển thị form từ chối
        function toggleRejectForm(postId) {
            const form = document.getElementById(`rejectForm-${postId}`);
            if (!form) return;
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
@endpush