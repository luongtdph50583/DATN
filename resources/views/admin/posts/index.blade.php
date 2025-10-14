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
                        @forelse($posts as $index => $post)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->created_at->format('d/m/Y') }}</td>
                                <td>
<a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-eye"></i> Xem</a>

                                    {{-- Nút Ẩn/Hiện --}}
                                    <form action="{{ route('admin.posts.toggle', $post->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-info btn-sm">
                                            {{ $post->status === 'visible' ? 'Ẩn' : 'Hiện' }}
                                        </button>
                                    </form>

                                    {{-- Nút Xóa --}}
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
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
        </div>
    </div>
@endsection
