@extends('admin.layouts.app')

@section('title', 'Thùng rác Tài liệu CLB')

@section('card-header')
    Thùng rác Tài liệu
@endsection

@section('card-body')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h4>Danh sách tài liệu đã xóa</h4>
        <a href="{{ route('admin.documentclub.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
    </div>

    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.documentclub.trash') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                    placeholder="Tìm theo tên tài liệu...">
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
                <input type="text" name="uploader" value="{{ request('uploader') }}" class="form-control"
                    placeholder="Người tải lên...">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="{{ route('admin.documentclub.trash') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Tiêu đề</th>
                    <th>Loại file</th>
                    <th>CLB</th>
                    <th>Người tải lên</th>
                    <th>Tags</th>
                    <th>Mô tả</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trashedDocuments as $index => $doc)
                            <tr>
                                <td>{{ ($trashedDocuments->currentPage() - 1) * $trashedDocuments->perPage() + $index + 1 }}</td>
                                <td>{{ $doc->title }}</td>
                                <td><span class="badge bg-dark">{{ strtoupper($doc->file_type) }}</span></td>
                                <td>{{ $doc->club->name ?? '-' }}</td>
                                <td>{{ $doc->uploader->name ?? '-' }}</td>
                                <td>{{ $doc->tags ?? 'Không có tag' }}</td>
                                <td>{{ $doc->description ?? 'Không có mô tả' }}</td>
                    <td>
                        {{-- Xem chi tiết --}}
                        <a href="{{ route('admin.documentclub.showTrash', $doc->id) }}" class="btn btn-warning btn-sm me-1" title="Xem">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Khôi phục --}}
                        <form action="{{ route('admin.documentclub.restore', $doc->id) }}" method="POST" class="d-inline me-1">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-info btn-sm" title="Khôi phục">
                                <i class="fas fa-undo"></i>
                            </button>
                        </form>

                        {{-- Xoá vĩnh viễn --}}
                        <form action="{{ route('admin.documentclub.forceDelete', $doc->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Xóa vĩnh viễn tài liệu này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa vĩnh viễn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>


                            </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Thùng rác trống. Không có tài liệu nào đã bị xóa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $trashedDocuments->appends(request()->query())->links() }}
    </div>
@endsection