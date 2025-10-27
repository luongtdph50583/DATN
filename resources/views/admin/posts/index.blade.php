@extends('admin.layouts.app')

@section('title', 'Trang quản trị')

@section('card-title', 'Quản lý bài viết')

@section('card-header', 'Tin tức & Bài viết')

@section('card-body')
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
    <div class="table-responsive shadow-sm rounded">
        <table class="table align-middle table-hover">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th width="5%">#</th>
                    <th>Tiêu đề</th>
                    <th>Ngày đăng</th>
                    <th>Trạng thái</th>
                    <th>Hiển thị</th>
                    <th width="22%">Hành động</th>
                </tr>
            </thead>
            <tbody id="postTableBody">
                @forelse($posts as $index => $post)
                    <tr>
                        <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                        <td>{{ $post->title }}</td>
                        <td class="text-center">{{ $post->created_at->format('d/m/Y') }}</td>

                        {{-- Cột trạng thái --}}
                        <td class="text-center">
                            @if ($post->status === 'visible' || $post->status === 'active')
                                <span class="badge bg-success px-3 py-2">Đang hiển thị</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">Đã ẩn</span>
                            @endif
                        </td>

                        {{-- Cột hiển thị (visibility) --}}
                        <td class="text-center">
                            @if ($post->visibility === 'public')
                                <span class="badge bg-primary px-3 py-2">Công khai</span>
                            @else
                                <span class="badge bg-info text-dark px-3 py-2">Nội bộ CLB</span>
                            @endif
                        </td>

                        {{-- Hành động --}}
                        <td class="text-center">
                            <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-outline-warning btn-sm me-1">
                                <i class="fas fa-eye"></i> Xem
                            </a>

                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary btn-sm me-1">
                                <i class="fas fa-edit"></i> Sửa
                            </a>

                            <form action="{{ route('admin.posts.toggle', $post->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-info btn-sm me-1">
                                    {{ $post->status === 'visible' ? 'Ẩn' : 'Hiện' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> Không có bài viết nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

{{-- Tìm kiếm bài viết động --}}
<script>
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
                        tbody.innerHTML = `
                            <tr><td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> Không có bài viết phù hợp.
                            </td></tr>`;
                        return;
                    }

                    res.data.forEach((post, index) => {
                        tbody.innerHTML += `
                            <tr>
                                <td class="text-center fw-semibold">${index + 1}</td>
                                <td>${post.title}</td>
                                <td class="text-center">${new Date(post.created_at).toLocaleDateString('vi-VN')}</td>
                                <td class="text-center">
                                    ${post.status === 'visible' ? 
                                        '<span class="badge bg-success px-3 py-2">Đang hiển thị</span>' :
                                        '<span class="badge bg-secondary px-3 py-2">Đã ẩn</span>'
                                    }
                                </td>
                                <td class="text-center">
                                    ${post.visibility === 'public' ? 
                                        '<span class="badge bg-primary px-3 py-2">Công khai</span>' :
                                        '<span class="badge bg-info text-dark px-3 py-2">Nội bộ CLB</span>'
                                    }
                                </td>
                                <td class="text-center">
                                    <a href="/admin/posts/${post.id}" class="btn btn-outline-warning btn-sm me-1">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="/admin/posts/${post.id}/edit" class="btn btn-outline-primary btn-sm me-1">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="/admin/posts/${post.id}/toggle" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-info btn-sm me-1">
                                            ${post.status === 'visible' ? 'Ẩn' : 'Hiện'}
                                        </button>
                                    </form>
                                    <form action="/admin/posts/${post.id}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>`;
                    });
                })
                .catch(err => console.error('Lỗi:', err));
        });
    });
</script>
