@extends('admin.layouts.app')

@section('title', 'Chi tiết log cập nhật tài liệu')

@section('card-header')
    Chi tiết log cập nhật
@endsection

@section('card-body')

                    {{-- Card thông tin chung --}}
                {{-- Card thông tin chung --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin log #{{ $log->id }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                        <tr>
                            <th width="25%">Tài liệu</th>
                            <td>
                                @if($log->document && !$log->document->trashed())
                                    <a href="{{ route('admin.documentclub.show', $log->document->id) }}" class="btn btn-sm btn-primary"
                                        target="_blank">
                                        <i class="fas fa-eye"></i> {{ $log->document->title }}
                                    </a>
                                @else
                                    <span class="text-muted">Tài liệu đã bị xoá</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>CLB</th>
                            <td>
                                @if($log->document && $log->document->club && !$log->document->club->trashed())
                                    <a href="{{ route('admin.clubs.show', $log->document->club->id) }}" class="btn btn-sm btn-outline-secondary"
                                        target="_blank">
                                        <i class="fas fa-users"></i> {{ $log->document->club->name }}
                                    </a>
                                @else
                                    <span class="text-muted">CLB đã bị xoá</span>
                                @endif
                            </td>
                        </tr>

                            <tr>
                                <th>Người cập nhật</th>
                                <td>{{ $log->changedBy->name ?? 'Không rõ' }}</td>
                            </tr>
                            <tr>
                                <th>Vai trò</th>
                                <td>{{ $roleDisplay }}</td>
                            </tr>
                            <tr>
                                <th>Thời gian</th>
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                    @php
    $changes = (array) $log->changes;
    $labels = [
        'title' => 'Tiêu đề',
        'description' => 'Mô tả',
        'tags' => 'Tags',
        'access_level' => 'Quyền truy cập',
        'file' => 'File',
        'deleted' => 'Xóa',
        'delete_reason' => 'Lý do xóa',
    ];
                    @endphp

                    {{-- Card các thay đổi --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Các thay đổi</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($changes['deleted']) && $changes['deleted'])
                                <p><strong class="text-danger">Đã xóa tài liệu</strong></p>
                                <p>Lý do: {{ $changes['delete_reason'] ?? '—' }}</p>
                            @else
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="25%">Trường</th>
                                            <th width="37.5%">Giá trị cũ</th>
                                            <th width="37.5%">Giá trị mới</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($changes as $field => $value)
                                            @if(is_array($value) && isset($value['old']) && isset($value['new']))
                                                <tr>
                                                    <td><strong>{{ $labels[$field] ?? ucfirst($field) }}</strong></td>
                                                    <td class="text-muted">
                                                        {{ is_array($value['old']) ? implode(', ', $value['old']) : ($value['old'] ?? '—') }}
                                                    </td>
                                                    <td class="text-success fw-bold">
                                                        {{ is_array($value['new']) ? implode(', ', $value['new']) : ($value['new'] ?? '—') }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>

                    {{-- Card riêng cho mô tả dài --}}
                    @if(isset($changes['description']) && is_array($changes['description']))
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Mô tả</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong class="text-danger">Mô tả cũ:</strong>
                                        <div class="border p-2 bg-white content-html">
                                            {!! $changes['description']['old'] !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong class="text-success">Mô tả mới:</strong>
                                        <div class="border p-2 bg-white content-html">
                                            {!! $changes['description']['new'] !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Flow chuẩn: nút quay lại, duyệt/từ chối nếu cần --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.document_update_logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại danh sách
                        </a>

                        @if($log->status === 'pending')
                            <div>
                                <form action="{{ route('admin.document_update_logs.approve', $log->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Phê duyệt
                                    </button>
                                </form>
                                <form action="{{ route('admin.document_update_logs.reject', $log->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Bạn chắc chắn muốn từ chối?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

@endsection