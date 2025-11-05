@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Tài liệu CLB')

@section('card-header')
    Chỉnh sửa Tài liệu
@endsection

@section('card-body')
    <form action="{{ route('admin.documentclub.update', $document->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $document->title) }}" required>
        </div>

     <div class="mb-3">
        <label for="clb_id" class="form-label">CLB</label>
        <select name="clb_id" class="form-select select2-club" required>
            <option value="">Chọn CLB</option>
            @foreach($clubs as $club)
                <option value="{{ $club->id }}" {{ old('clb_id') == $club->id ? 'selected' : '' }}>
                    {{ $club->name }}
                </option>
            @endforeach
        </select>
    </div>

        <div class="mb-3">
            <label class="form-label">File hiện tại</label>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark">{{ strtoupper($document->file_type) }}</span>
                <a href="{{ route('admin.documentclub.download', $document->id) }}"
                    class="btn btn-outline-secondary btn-sm">Tải xuống</a>
            </div>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">Thay file mới (nếu cần)</label>
            <input type="file" name="file" class="form-control">
        </div>

        <div class="mb-3">
            <label for="access_level" class="form-label">Mức truy cập</label>

            @php
                $levels = [
                    'public' => 'Công khai',
                    'guest' => 'Khách tạm thời',
                    'member' => 'Thành viên',
                    'communication' => 'Truyền thông',
                    'event_manager' => 'Quản lý sự kiện',
                    'secretary' => 'Thư ký',
                    'treasurer' => 'Thủ quỹ',
                    'deputy_manager' => 'Phó chủ nhiệm',
                    'club_manager' => 'Chỉ chủ nhiệm',
                    'admin' => 'Quản trị hệ thống',
                ];

                // Lấy giá trị cũ, hỗ trợ JSON array trong DB
                $oldLevels = old('access_level');
                if (!$oldLevels) {
                    if (is_string($document->access_level)) {
                        $oldLevels = json_decode($document->access_level, true) ?? [];
                    } elseif (is_array($document->access_level)) {
                        $oldLevels = $document->access_level;
                    } else {
                        $oldLevels = [];
                    }
                }
            @endphp

            <select name="access_level[]" class="form-select select2" multiple required>
                @foreach($levels as $value => $label)
                    <option value="{{ $value }}" {{ in_array($value, $oldLevels) ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description', $document->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">Tags (phân cách bằng dấu phẩy)</label>
            <input type="text" name="tags" class="form-control" value="{{ old('tags', $document->tags) }}">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.select2').select2({
                placeholder: "Chọn mức truy cập",
                allowClear: true,
                width: '100%'
            });
        });
          document.addEventListener('DOMContentLoaded', function () {
    // Select2 cho CLB
    $('.select2-club').select2({
        placeholder: "Chọn CLB",
        allowClear: true,
        width: '100%'
    });
    });
    </script>
@endpush