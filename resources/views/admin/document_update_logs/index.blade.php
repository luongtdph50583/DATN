@extends('admin.layouts.app')

@section('title', 'Lịch sử chỉnh sửa tài liệu')

@section('card-header')
    Lịch sử thay đổi tài liệu
@endsection

@section('card-body')

    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.document_update_logs.index') }}" class="mb-3">
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
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="{{ route('admin.document_update_logs.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tài liệu</th>
                    <th>CLB</th>
                    <th>Người cập nhật</th>
                    <th>Role</th>
                    <th>Trường thay đổi</th>
                    <th>Thời gian</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $index => $log)
                                    <tr>
                                        {{-- STT tính theo trang hiện tại --}}
                                        <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}</td>
                                        <td>{{ $log->document->title ?? '—' }}</td>
                                        <td>{{ $log->document->club->name ?? '—' }}</td>
                                        <td>{{ $log->changedBy->name ?? 'Không rõ' }}</td>
                                        <td>{{ $logRoles[$log->id] ?? 'Không rõ' }}</td>

                                        {{-- Trường thay đổi --}}
                                        <td>
                                            @php
                    $fields = array_keys((array) $log->changes);
                    $fieldLabels = [
                        'title' => 'Tiêu đề',
                        'description' => 'Mô tả',
                        'tags' => 'Tags',
                        'access_level' => 'Quyền truy cập',
                        'file' => 'File',
                        'deleted' => 'Xóa',
                        'delete_reason' => 'Lý do xóa',
                    ];
                                            @endphp

                                            @if(in_array('deleted', $fields))
                                                Xóa
                                            @elseif(count($fields))
                                                {{ implode(', ', array_map(fn($f) => $fieldLabels[$f] ?? $f, $fields)) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>

                                    <td>
                                        {{-- Xem chi tiết log tài liệu --}}
                                        <a href="{{ route('admin.document_update_logs.show', $log->id) }}" class="btn btn-warning btn-sm me-1" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>

                                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $logs->appends(request()->query())->links() }}
    </div>

@endsection