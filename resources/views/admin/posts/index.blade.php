
@extends('admin.layouts.app')
@section('title')
    trang admin
@endsection

@section('card-title')
    Quản lý bai viet
@endsection

@section('card-header')
    Tin tức & Bài viết
@endsection

@section('card-body')
    <div class="mb-4">
        <label for="searchPost" class="form-label">Tìm kiếm bài viết</label>
        <input type="text" id="searchPost" class="form-control" placeholder="Nhập tiêu đề hoặc tên người đăng...">
    </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Ngày đăng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $index => $post)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fas fa-eye"></i> Xem</a>

                                    {{-- Nút Ẩn/Hiện --}}
                                    <form action="{{ route('admin.posts.toggle', $post->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-info btn-sm">
                                            {{ $post->status === 'visible' ? 'Ẩn' : 'Hiện' }}
                                        </button>
                                    </form>

                                    {{-- Nút Xóa --}}
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Không có bài viết nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
@endsection
{{-- <div class="mb-4">
    <label for="searchPost" class="form-label">Tìm kiếm bài viết</label>
    <input type="text" id="searchPost" class="form-control" placeholder="Nhập tiêu đề hoặc tên người đăng...">
</div> --}}

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
                            tbody.innerHTML = `<tr><td colspan="4" class="text-center">Không có bài viết phù hợp.</td></tr>`;
                            return;
                        }
                        res.data.forEach((post, index) => {
                            tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${post.title}</td>
                        <td>${new Date(post.created_at).toLocaleDateString('vi-VN')}</td>
                        <td>
                            <a href="/admin/posts/${post.id}" class="btn btn-warning btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        </td>
                    </tr>`;
                        });
                    })
                    .catch(err => console.error('Lỗi:', err));
            });
        });

</script>

@extends('admin.layouts.app')

     @section('content')
         <div class="card shadow mb-4">
             <div class="card-header py-3">
                 <h6 class="m-0 font-weight-bold text-primary">Tin tức & Bài viết</h6>
                 <a href="#" class="btn btn-primary btn-sm">Thêm mới</a>
             </div>
             <div class="card-body">
                 <div class="table-responsive">
                     <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                         <thead>
                             <tr>
                                 <th>ID</th>
                                 <th>Tiêu đề</th>
                                 <th>Ngày đăng</th>
                                 <th>Hành động</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>1</td>
                                 <td>Tin tức tháng 10</td>
                                 <td>12/10/2025</td>
                                 <td>
                                     <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                     <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                 </td>
                             </tr>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     @endsection
