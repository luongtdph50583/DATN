@extends('admin.layouts.app')

@section('title', 'Chi tiết tài liệu đã xoá')

@section('card-header')
    <h4 class="mb-0 text-danger">🗑️ Chi tiết tài liệu đã xoá</h4>
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

                @php
                    $levels = [
                        'public' => 'Công khai',
                        'member' => 'Thành viên',
                        'communication' => 'Truyền thông',
                        'event_manager' => 'Quản lý sự kiện',
                        'secretary' => 'Thư ký',
                        'treasurer' => 'Thủ quỹ',
                        'deputy_manager' => 'Phó chủ nhiệm',
                        'club_manager' => 'Chủ nhiệm CLB',
                    ];
                    $accessLevels = $document->access_level;
                    if (is_string($accessLevels)) {
                        $decoded = json_decode($accessLevels, true);
                        $accessLevels = is_array($decoded) ? $decoded : [$accessLevels];
                    }
                    $translated = collect($accessLevels ?? [])
                        ->map(fn($v) => $levels[$v] ?? ucfirst($v))
                        ->implode(', ');
                @endphp

                @if(!empty($translated))
                    <tr>
                        <th>Cấp độ truy cập</th>
                        <td>{{ $translated }}</td>
                    </tr>
                @endif

                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $document->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>

                @if($document->updated_at)
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td>{{ $document->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif

                @if($document->deleted_at)
                    <tr>
                        <th>Ngày xoá</th>
                        <td>{{ $document->deleted_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif

                @if($document->rejected_reason)
                    <tr>
                        <th>Lý do xoá</th>
                        <td>{{ $document->rejected_reason }}</td>
                    </tr>
                @endif
            </table>

            <div class="mt-3 d-flex gap-2">
                <form action="{{ route('admin.documentclub.restore', $document->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-info">
                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                    </button>
                </form>

                <form action="{{ route('admin.documentclub.forceDelete', $document->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Xóa vĩnh viễn tài liệu này?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Xóa vĩnh viễn
                    </button>
                </form>

                <a href="{{ route('admin.documentclub.trash') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại thùng rác
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
            </div>
        </div>
    </div>
@endsection