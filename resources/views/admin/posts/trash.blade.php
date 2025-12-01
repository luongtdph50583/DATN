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

    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.posts.trash') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                    placeholder="Tìm theo tiêu đề...">
            </div>
            <div class="col-md-3">
                <select name="club_id" class="form-control">
                    <option value="">-- Chọn CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="author" value="{{ request('author') }}" class="form-control"
                    placeholder="Người đăng...">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="{{ route('admin.posts.trash') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

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
                        <td>{{ ($posts->currentPage() - 1) * $posts->perPage() + $index + 1 }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->user->name ?? '—' }}</td>
                        <td>{{ $post->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $post->club->name ?? '—' }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.posts.showTrash', $post->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Xem
                            </a>
                            <form action="{{ route('admin.posts.restore', $post->id) }}" method="POST"
                                onsubmit="return confirm('Khôi phục bài viết này?')" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form action="{{ route('admin.posts.forceDelete', $post->id) }}" method="POST"
                                onsubmit="return confirm('Xóa vĩnh viễn bài viết này?')" class="d-inline">
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

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    @endif
@endsection