@extends('admin.layouts.app')
@section('title')
    trang admin
@endsection

@section('card-title')
    Quản lý bài viết
@endsection

@section('card-header')
    Tin tức & Bài viết
@endsection

@section('card-body')
    {{-- Nút thêm bài viết --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.posts.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm bài viết
        </a>
    </div>

    {{-- Tìm kiếm bài viết --}}
    <div class="mb-4">
        <label for="searchPost" class="form-label">Tìm kiếm bài viết</label>
        <input type="text" id="searchPost" class="form-control" placeholder="Nhập tiêu đề hoặc tên người đăng...">
    </div>

    {{-- Bảng danh sách bài viết --}}
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Ngày đăng</th>
                    <th>Trạng thái</th>
                    <th>Hiển thị</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $index => $post)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->created_at->format('d/m/Y') }}</td>
                        <td>{{ $post->status }}</td>
                        <td>{{ $post->visibility }}</td>
                        <td>
                            {{-- Nút Xem --}}
                            <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-eye"></i> Xem
                            </a>

                            {{-- Nút Sửa --}}
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>

                            {{-- Nút Ẩn/Hiện --}}
                            <form action="{{ route('admin.posts.toggle', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info btn-sm">
                                    {{ $post->status === 'visible' ? 'Ẩn' : 'Hiện' }}
                                </button>
                            </form>

                            {{-- Nút Xóa --}}
                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Không có bài viết nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchPost');
        const tbody = document.querySelector('#dataTable tbody');

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
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Không có bài viết phù hợp.</td></tr>`;
                        return;
                    }
                    res.data.forEach((post, index) => {
                        tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${post.title}</td>
                        <td>${new Date(post.created_at).toLocaleDateString('vi-VN')}</td>
                        <td>${post.status}</td>
                        <td>${post.visibility}</td>
                        <td>
                            <a href="/admin/posts/${post.id}" class="btn btn-warning btn-sm">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                            <form action="/admin/posts/${post.id}/toggle" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info btn-sm">
                                    ${post.status === 'visible' ? 'Ẩn' : 'Hiện'}
                                </button>
                            </form>
                            <form action="/admin/posts/${post.id}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
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
