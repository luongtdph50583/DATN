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

    @forelse($trashedDocuments as $doc)
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
                <strong>{{ $doc->title }}</strong>
                <span class="badge bg-dark ms-2">{{ strtoupper($doc->file_type) }}</span>
            </div>
            <div class="card-body">
                <p><strong>CLB:</strong> {{ $doc->club->name ?? '-' }}</p>
                <p><strong>Người tải lên:</strong> {{ $doc->uploader->name ?? '-' }}</p>
                <p><strong>Tags:</strong> {{ $doc->tags ?? 'Không có tag' }}</p>
                <p><strong>Mô tả:</strong> {{ $doc->description ?? 'Không có mô tả' }}</p>

                <div class="d-flex gap-2">
                    <form action="{{ route('admin.documentclub.restore', $doc->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-info btn-sm">Khôi phục</button>
                    </form>

                    <form action="{{ route('admin.documentclub.forceDelete', $doc->id) }}" method="POST"
                        onsubmit="return confirm('Xóa vĩnh viễn tài liệu này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Thùng rác trống. Không có tài liệu nào đã bị xóa.</p>
    @endforelse
@endsection
