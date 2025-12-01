@extends('admin.layouts.app')

@section('title', 'Chi tiết lịch sử chỉnh sửa')

@section('card-header')
    Chi tiết chỉnh sửa bài viết
@endsection

@section('card-body')

    <style>
        .content-html img,
        .field-image img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
        }
        table.change-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        table.change-table th,
        table.change-table td {
            border: 1px solid #dee2e6;
            padding: .5rem;
            vertical-align: top;
        }
        table.change-table th {
            background-color: #f8f9fa;
            text-align: left;
        }
    </style>

    {{-- Card thông tin chung --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-3">Thông tin chung</h5>
            <table class="table table-sm">
                <tr>
                    <th>CLB</th>
                    <td>{{ $log->post->club->name }}</td>
                </tr>
                <tr>
                    <th>Người cập nhật</th>
                    <td>{{ $log->updatedBy->name }}</td>
                </tr>
                <tr>
                    <th>Role</th>
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
        $changes = $log->changes ?? [];

        function fieldLabel($field) {
            return match ($field) {
                'title' => 'Tiêu đề',
                'content' => 'Nội dung',
                'type' => 'Loại',
                'visibility' => 'Chế độ hiển thị',
                'is_visible' => 'Trạng thái hiển thị',
                'is_featured' => 'Đánh dấu nổi bật',
                'thumbnail' => 'Ảnh đại diện',
                'status' => 'Trạng thái duyệt',
                default => strtoupper($field),
            };
        }

        function displayValue($val, $field) {
            if (is_null($val)) return '<i>Không có</i>';
            if ($field === 'is_visible') return $val ? 'Hiện' : 'Ẩn';
            if ($field === 'is_featured') return $val ? 'Có' : 'Không';
            if ($field === 'status') {
                return match ($val) {
                    'pending' => 'Đang chờ duyệt',
                    'approved' => 'Đã duyệt',
                    'rejected' => 'Bị từ chối',
                    default => $val,
                };
            }
            if ($field === 'visibility') return $val === 'internal' ? 'Nội bộ' : 'Công khai';
            if ($field === 'type') {
                return match ($val) {
                    'post' => 'Bài viết',
                    'notice' => 'Thông báo',
                    'document' => 'Tài liệu',
                    default => $val,
                };
            }
            return e($val);
        }
    @endphp

    {{-- Card bảng thay đổi --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-3">Các thay đổi</h5>
            <table class="change-table">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Cũ</th>
                        <th>Mới</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($changes as $field => $vals)
                        @if($field !== 'content')
                            <tr>
                                <td class="fw-bold">{{ fieldLabel($field) }}</td>
                                <td>
                                    @if(str_contains($field, 'image') || str_contains($field, 'thumbnail'))
                                        @if(!empty($vals[0]))
                                            @php
                                                $oldImage = Str::startsWith($vals[0], ['http://', 'https://'])
                                                    ? $vals[0]
                                                    : Storage::url($vals[0]);
                                            @endphp
                                            <img src="{{ $oldImage }}" style="max-width:150px" class="border">
                                        @else
                                            <i>Không có</i>
                                        @endif
                                    @else
                                        {!! displayValue($vals[0] ?? null, $field) !!}
                                    @endif
                                </td>
                                <td>
                                    @if(str_contains($field, 'image') || str_contains($field, 'thumbnail'))
                                        @if(!empty($vals[1]))
                                            @php
                                                $newImage = Str::startsWith($vals[1], ['http://', 'https://'])
                                                    ? $vals[1]
                                                    : Storage::url($vals[1]);
                                            @endphp
                                            <img src="{{ $newImage }}" style="max-width:150px" class="border">
                                        @else
                                            <i>Không có</i>
                                        @endif
                                    @else
                                        {!! displayValue($vals[1] ?? null, $field) !!}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Card riêng cho content --}}
    @if(isset($changes['content']) && is_array($changes['content']))
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-3">{{ fieldLabel('content') }}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-danger">Nội dung cũ:</strong>
                        <div class="border p-2 bg-white content-html">
                            {!! $changes['content'][0] !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-success">Nội dung mới:</strong>
                        <div class="border p-2 bg-white content-html">
                            {!! $changes['content'][1] !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('css')
    <style>
        .content-html img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endpush
