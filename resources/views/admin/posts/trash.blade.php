@extends('admin.layouts.app')

@section('title', 'Thùng rác bài viết')

@section('card-title')
    <div class="d-flex justify-content-between align-items-center">
        <span>🗑️ Thùng rác bài viết</span>
        
    </div>
@endsection
@section('card-header')
    Bài viết đã xóa
@endsection

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($posts->isEmpty())
        <div class="alert alert-info">Không có bài viết nào trong thùng rác.</div>
    @else
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tiêu đề</th>
                    <th>Người đăng</th>
                    <th>Ngày xóa</th>
                    <th>CLB</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $index => $post)
                    <tr>
                        <td>{{ $index + 1}}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->user->name ?? '—' }}</td>
                        <td>{{ $post->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $post->club->name ?? '—' }}</td>
                        <td class="d-flex gap-2">
                            {{-- Khôi phục --}}
                            <form action="{{ route('admin.posts.restore', $post->id) }}" method="POST"
                                onsubmit="return confirm('Khôi phục bài viết này?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>

                            {{-- Xóa vĩnh viễn --}}
                            <form action="{{ route('admin.posts.forceDelete', $post->id) }}" method="POST"
                                onsubmit="return confirm('Xóa vĩnh viễn bài viết này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash3"></i> Xóa vĩnh viễn
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection