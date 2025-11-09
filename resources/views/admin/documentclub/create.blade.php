@extends('admin.layouts.app')

@section('title', 'Thêm Tài liệu CLB')

@section('card-header')
    Thêm Tài liệu
@endsection

@section('card-body')
            <form action="{{ route('admin.documentclub.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
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
                    <label for="file" class="form-label">File</label>
                    <input type="file" name="file" class="form-control" required>
                </div>

        <div class="mb-3">
            <label for="access_level" class="form-label">Mức truy cập (có thể chọn nhiều)</label>
            <select name="access_level[]" id="access_level" class="form-select select2" multiple="multiple" required>
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
$oldLevels = old('access_level', []);
                @endphp

                @foreach($levels as $value => $label)
                    <option value="{{ $value }}" {{ in_array($value, $oldLevels) ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>



                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label">Tags (phân cách bằng dấu phẩy)</label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags') }}"
                        placeholder="ví dụ: tài liệu,quy chế,hướng dẫn">
                </div>

                <button type="submit" class="btn btn-primary">Tải lên</button>
            </form>
@endsection
@push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = $('.select2');

            select.select2({
                placeholder: "Chọn mức truy cập",
                allowClear: true,
                width: '100%'
            });

            const managerRoles = ['communication', 'event_manager', 'secretary', 'treasurer', 'deputy_manager', 'club_manager'];

            function updateAccessLogic() {
                let selected = select.val() || [];
                const options = select.find('option');
                options.prop('disabled', false);

                // === 1️⃣ Nếu chọn "Công khai"
                if (selected.includes('public')) {
                    // disable toàn bộ quyền khác
                    options.each(function () {
                        if ($(this).val() !== 'public') $(this).prop('disabled', true);
                    });
                }

                // === 2️⃣ Nếu chọn "Thành viên"
                else if (selected.includes('member')) {
                    const allExceptPublic = options.map(function () {
                        return $(this).val() !== 'public' ? $(this).val() : null;
                    }).get().filter(v => v);
                    selected = allExceptPublic; // chọn tất cả trừ công khai
                    select.val(selected).trigger('change.select2');
                }

                // === 3️⃣ Nếu chọn quyền quản lý khác → tự động thêm "Chủ nhiệm CLB"
                else {
                    const hasManagerRole = selected.some(v => managerRoles.includes(v) && v !== 'club_manager');
                    if (hasManagerRole && !selected.includes('club_manager')) {
                        selected.push('club_manager');
                        select.val(selected).trigger('change.select2');
                    }
                }

                // === 4️⃣ Nếu BỎ chọn "Thành viên" → bỏ hết quyền quản lý
                if (!selected.includes('member')) {
                    selected = selected.filter(v => !managerRoles.includes(v));
                    select.val(selected).trigger('change.select2');
                }

                // === Disable hợp lý
                if (selected.includes('public')) {
                    options.each(function () {
                        if ($(this).val() !== 'public') $(this).prop('disabled', true);
                    });
                } else if (selected.length > 0) {
                    options.each(function () {
                        if ($(this).val() === 'public') $(this).prop('disabled', true);
                    });
                }

                select.trigger('change.select2');
            }

            // Gọi lần đầu khi load
            updateAccessLogic();
            // Lắng nghe thay đổi
            select.on('change', updateAccessLogic);
        });
    </script>





@endpush
