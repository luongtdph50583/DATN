@extends('admin.layouts.app')

@section('title', 'Chi tiết tài liệu')

@section('card-header')
    <h4 class="mb-0">📄 Chi tiết tài liệu</h4>
@endsection

@section('card-body')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <h5 class="fw-bold">{{ $document->title }}</h5>
                <p class="text-muted">{{ $document->description ?? 'Không có mô tả' }}</p>
            </div>

            <table class="table table-bordered">
                <tr>
                    <th>CLB</th>
                    <td>{{ $document->club->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Người tải lên</th>
                    <td>{{ $document->uploader->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Loại file</th>
                    <td>{{ strtoupper($document->file_type) }}</td>
                </tr>
                <tr>
                    <th>Tags</th>
                    <td>{{ $document->tags ?? 'Không có' }}</td>
                </tr>
                <tr>
                    <th>Cấp độ truy cập</th>
                    <td>{{ ucfirst($document->access_level) }}</td>
                </tr>
                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('admin.documentclub.download', $document->id) }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Tải xuống
                </a>
                <a href="{{ route('admin.documentclub.edit', $document->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.documentclub.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="border rounded p-3 bg-light">
                <h6 class="fw-bold">📁 Thông tin file</h6>
                <p><strong>Tên file:</strong> {{ $document->file_name }}</p>
                <p><strong>Đường dẫn:</strong> <code>{{ $document->file_path }}</code></p>
                <p><strong>Loại:</strong> {{ $document->file_type }}</p>
            </div>
        </div>
    </div>
@endsection
