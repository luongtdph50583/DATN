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

{{-- Tìm kiếm bài viết --}}
<div class="mb-4">
    <label for="searchPost" class="form-label fw-semibold">Tìm kiếm bài viết</label>
    <input type="text" id="searchPost" class="form-control" placeholder="Nhập tiêu đề hoặc người đăng...">
</div>

{{-- Bảng danh sách bài viết --}}
<div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
        <tbody id="postTableBody">
            @forelse($posts as $index => $post)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->user->name ?? 'Không xác định' }}</td>
                <td>{{ $post->created_at->format('d/m/Y') }}</td>
                <td>
                    <span class="badge bg-{{ $post->status === 'approved' ? 'success' : ($post->status === 'rejected' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($post->status) }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $post->is_visible ? 'info' : 'dark' }}">
                        {{ $post->is_visible ? 'Hiển thị' : 'Ẩn' }}
                    </span>
                </td>
                <td>
                    {{-- Xem luôn có --}}
                    <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-warning btn-sm me-1">
                        <i class="fas fa-eye"></i> Xem
                    </a>

                    @if($post->status === 'pending')
                        {{-- Sửa --}}
                       
                        {{-- Duyệt --}}
                        <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST" class="d-inline me-1">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-circle me-1"></i> Duyệt
                            </button>
                        </form>
                        {{-- Từ chối --}}
                        <button type="button" class="btn btn-danger btn-sm" onclick="toggleRejectForm({{ $post->id }})">
                            <i class="bi bi-x-circle me-1"></i> Từ chối
                        </button>
                        <form id="rejectForm-{{ $post->id }}" action="{{ route('admin.posts.reject', $post->id) }}" method="POST" class="mt-2" style="display: none;">
                            @csrf
                            @method('PUT')
                            <div class="input-group" style="max-width: 400px;">
                                <input type="text" name="rejection_reason" class="form-control" placeholder="Lý do từ chối..." required>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-send me-1"></i> Xác nhận
                                </button>
                            </div>
                        </form>
                    @elseif($post->status === 'approved')
                        {{-- Chỉ Xem + Sửa + Xóa --}}
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary btn-sm me-1">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                    @endif

                    {{-- Xóa luôn có --}}
                    <form id="delete-form-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $post->id }})">
                            <i class="fas fa-trash"></i> Xóa
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
@endsection

@push('scripts')
    <script>
    function confirmDelete(postId) {
        const reason = prompt("Nhập lý do xóa bài viết:");
        if (reason && reason.trim() !== "") {
            const form = document.getElementById(`delete-form-${postId}`);
            if (!form) { alert("Không tìm thấy form xóa."); return; }
            const existingInput = form.querySelector('input[name="reason"]');
            if (existingInput) existingInput.remove();
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "reason";
            input.value = reason.trim();
            form.appendChild(input);
            form.submit();
        } else {
            alert("Bạn phải nhập lý do xóa.");
        }
    }

    function toggleRejectForm(postId) {
        const form = document.getElementById(`rejectForm-${postId}`);
        if (!form) return;
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    // Tìm kiếm bài viết động
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchPost');
            const tbody = document.getElementById('postTableBody');

            searchInput.addEventListener('input', () => {
                fetch('{{ route('admin.posts.filter') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ keyword: searchInput.value.trim() })
                })
                    .then(res => res.json())
                    .then(res => {
                        tbody.innerHTML = '';
                        if (!res.data.length) {
                            tbody.innerHTML = `<tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> Không có bài viết phù hợp.
                        </td>
                    </tr>`;
                            return;
                        }

                        res.data.forEach((post, index) => {
                            // Render trạng thái
                            const statusBadge = `<span class="badge bg-${post.status === 'approved' ? 'success' : (post.status === 'rejected' ? 'danger' : 'secondary')}">
                        ${post.status.charAt(0).toUpperCase() + post.status.slice(1)}</span>`;
                            // Render hiển thị
                            const visibleBadge = `<span class="badge bg-${post.is_visible ? 'info' : 'dark'}">
                        ${post.is_visible ? 'Hiển thị' : 'Ẩn'}</span>`;

                            // Actions
                            let actions = `<a href="/admin/posts/${post.id}" class="btn btn-warning btn-sm me-1">
                        <i class="fas fa-eye"></i> Xem</a>`;

                            if (post.status === 'pending') {
                                actions += `<a href="/admin/posts/${post.id}/edit" class="btn btn-primary btn-sm me-1">
                            <i class="fas fa-edit"></i> Sửa</a>
                            <form action="/admin/posts/${post.id}/approve" method="POST" class="d-inline me-1">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-circle me-1"></i> Duyệt
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm" onclick="toggleRejectForm(${post.id})">
                                <i class="bi bi-x-circle me-1"></i> Từ chối
                            </button>
                            <form id="rejectForm-${post.id}" action="/admin/posts/${post.id}/reject" method="POST" class="mt-2" style="display:none;">
                                @csrf
                                @method('PUT')
                                <div class="input-group" style="max-width:400px;">
                                    <input type="text" name="rejection_reason" class="form-control" placeholder="Lý do từ chối..." required>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-send me-1"></i> Xác nhận
                                    </button>
                                </div>
                            </form>`;
                            } else if (post.status === 'approved') {
                                actions += `<a href="/admin/posts/${post.id}/edit" class="btn btn-primary btn-sm me-1">
                            <i class="fas fa-edit"></i> Sửa</a>`;
                            }

                            // Xóa luôn có
                            actions += `<form id="delete-form-${post.id}" action="/admin/posts/${post.id}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(${post.id})">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </form>`;

                            tbody.innerHTML += `<tr>
                        <td>${index + 1}</td>
                        <td>${post.title}</td>
                        <td>${post.user_name || 'Không xác định'}</td>
                        <td>${new Date(post.created_at).toLocaleDateString('vi-VN')}</td>
                        <td>${statusBadge}</td>
                        <td>${visibleBadge}</td>
                        <td>${actions}</td>
                    </tr>`;
                        });
                    })
                    .catch(err => console.error(err));
            });
        });

    </script>
@endpush
