{{-- resources/views/trash/media/index.blade.php --}}

@extends('admin.layouts.app')
@section('title', 'Thùng rác Media')

@section('card-title', 'Thùng rác Media') {{-- Tiêu đề card --}}
@section('card-header', 'Danh sách media đã xóa') {{-- Header card nếu layout dùng --}}

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($mediaList->isEmpty())
        <p>Chưa có media nào bị xóa tạm.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên file</th>
                    <th>Loại</th>
                    <th>Đường dẫn</th>
                    <th>Người tải lên</th>
                    <th>Ngày xóa</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mediaList as $media)
                    <tr>
                        <td>{{ $media->id }}</td>
                        <td>{{ $media->file_name }}</td>
                        <td>{{ $media->file_type }}</td>
                        <td>{{ $media->file_path }}</td>
                        <td>{{ $media->uploader?->name ?? 'N/A' }}</td>
                        <td>{{ $media->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.trash.media.restore', $media->id) }}" method="POST"
                                style="display:inline-block">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-success"
                                    onclick="return confirm('Bạn có chắc muốn khôi phục media này?')">Restore</button>
                            </form>

                            <form action="{{ route('admin.trash.media.forceDelete', $media->id) }}" method="POST"
                                style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Xóa vĩnh viễn media này? Hành động không thể hoàn tác!')">
                                    Force Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection