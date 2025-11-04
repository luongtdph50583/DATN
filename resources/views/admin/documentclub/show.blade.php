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
                @if($document->description)
                    <p class="text-muted">{{ $document->description }}</p>
                @endif
            </div>

            <table class="table table-bordered">
                @if($document->club)
                    <tr>
                        <th>CLB</th>
                        <td>{{ $document->club->name }}</td>
                    </tr>
                @endif

                @if($document->uploader)
                    <tr>
                        <th>Người tải lên</th>
                        <td>{{ $document->uploader->name }}</td>
                    </tr>
                @endif

                @if($document->file_type)
                    <tr>
                        <th>Loại file</th>
                        <td>{{ strtoupper($document->file_type) }}</td>
                    </tr>
                @endif

                @if($document->file_name)
                    <tr>
                        <th>Tên file</th>
                        <td>{{ $document->file_name }}</td>
                    </tr>
                @endif

                @if($document->file_path)
                    <tr>
                        <th>Đường dẫn</th>
                        <td><code>{{ $document->file_path }}</code></td>
                    </tr>
                @endif

                @if($document->tags)
                    <tr>
                        <th>Tags</th>
                        <td>{{ $document->tags }}</td>
                    </tr>
                @endif

                @if($document->access_level)
                    <tr>
                        <th>Cấp độ truy cập</th>
                        <td>
                            @if(is_array($document->access_level))
                                {{ implode(', ', $document->access_level) }}
                            @else
                                {{ $document->access_level }}
                            @endif
                        </td>
                    </tr>
                @endif

                @if($document->status)
                    <tr>
                        <th>Trạng thái</th>
                        <td>{{ ucfirst($document->status) }}</td>
                    </tr>
                @endif

                @if($document->status === 'approved' && $document->approved_at)
                    <tr>
                        <th>Ngày duyệt</th>
                        <td>{{ $document->approved_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif

                @if($document->status === 'rejected' && $document->approved_at)
                    <tr>
                        <th>Ngày từ chối</th>
                        <td>{{ $document->approved_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif

                @if($document->approved_by)
                    <tr>
                        <th>Người duyệt</th>
                        <td>{{ $document->approver->name ?? '-' }}</td>
                    </tr>
                @endif

                @if($document->status === 'rejected' && $document->rejected_reason)
                    <tr>
                        <th>Lý do từ chối</th>
                        <td>{{ $document->rejected_reason }}</td>
                    </tr>
                @endif

                @if($document->created_at)
                    <tr>
                        <th>Ngày tạo</th>
                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif

                @if($document->updated_at)
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td>{{ $document->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif

                @if($document->deleted_at)
                    <tr>
                        <th>Đã xóa</th>
                        <td>{{ $document->deleted_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif
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
                @if($document->file_name)
                    <p><strong>Tên file:</strong> {{ $document->file_name }}</p>
                @endif
                @if($document->file_path)
                    <p><strong>Đường dẫn:</strong> <code>{{ $document->file_path }}</code></p>
                @endif
                @if($document->file_type)
                    <p><strong>Loại:</strong> {{ $document->file_type }}</p>
                @endif
                @if($document->access_level)
                    <p><strong>Cấp độ truy cập:</strong>
                        @if(is_array($document->access_level))
                            {{ implode(', ', $document->access_level) }}
                        @else
                            {{ $document->access_level }}
                        @endif
                    </p>
                @endif
                @if($document->status)
                    <p><strong>Trạng thái:</strong> {{ ucfirst($document->status) }}</p>
                @endif
                @if($document->status === 'approved' && $document->approved_at)
                    <p><strong>Ngày duyệt:</strong> {{ $document->approved_at->format('d/m/Y H:i') }}</p>
                @endif
                @if($document->status === 'rejected' && $document->approved_at)
                    <p><strong>Ngày từ chối:</strong> {{ $document->approved_at->format('d/m/Y H:i') }}</p>
                @endif
                @if($document->status === 'rejected' && $document->rejected_reason)
                    <p><strong>Lý do từ chối:</strong> {{ $document->rejected_reason }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection