@extends('client.layouts.app')

@section('title', 'Chỉnh sửa Tài liệu CLB')

@section('content')
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">📄 Chỉnh sửa Tài liệu - {{ $club->name }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('club_manager.club.documents.update', [$club->id, $document->id]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $document->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">File tài liệu</label>
                                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                                    <small class="text-muted">Để trống nếu không muốn thay đổi file.</small>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if($document->file_path)
                                        <div class="mt-2">
                                            <span class="text-muted">File hiện tại:</span>
                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">
                                                {{ basename($document->file_path) }}
                                            </a>
                                        </div>
                                    @endif
                                </div>


                                <div class="mb-3">
                                    <label for="access_level" class="form-label">Ai có thể xem? <span
                                            class="text-danger">*</span></label>
                                    @php
    $levels = [
        'public' => '🌍 Công khai (Mọi người)',
        'member' => '👥 Tất cả thành viên CLB',
        'communication' => '📢 Ban Truyền thông',
        'event_manager' => '🎉 Ban Tổ chức sự kiện',
        'secretary' => '📝 Thư ký',
        'treasurer' => '💰 Thủ quỹ',
        'deputy_manager' => '👔 Phó chủ nhiệm',
        'club_manager' => '⭐ Chủ nhiệm CLB',
    ];
    $selectedLevels = old('access_level', $document->access_level ?? []);
                                    @endphp
                                    <select name="access_level[]" id="access_level"
                                        class="form-select select2 @error('access_level') is-invalid @enderror"
                                        multiple="multiple" required>
                                        @foreach($levels as $value => $label)
                                            <option value="{{ $value }}" {{ in_array($value, $selectedLevels) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Chọn một hoặc nhiều nhóm có quyền truy cập</small>
                                    @error('access_level')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="4">{{ old('description', $document->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" name="tags" class="form-control @error('tags') is-invalid @enderror"
                                        value="{{ old('tags', $document->tags) }}">
                                    <small class="text-muted">Phân cách bằng dấu phẩy để dễ tìm kiếm sau này</small>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Lưu thay đổi
                                    </button>
                                    <a href="{{ route('club_manager.club.documents.index', $club->id) }}"
                                        class="btn btn-secondary">
                                        Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Hướng dẫn sử dụng -->
                    <div class="card mt-3 border-info">
                        <div class="card-body">
                            <h6 class="text-info">💡 Hướng dẫn phân quyền:</h6>
                            <ul class="small mb-0">
                                <li><strong>Công khai:</strong> Mọi người đều xem được (kể cả người ngoài CLB)</li>
                                <li><strong>Tất cả thành viên:</strong> Tất cả thành viên CLB đều xem được</li>
                                <li><strong>Chọn role cụ thể:</strong> Chỉ những role đó + role cao hơn mới xem được</li>
                                <li><em>VD: Chọn "Thư ký" → Chỉ có Thư ký, Phó chủ nhiệm, Chủ nhiệm mới xem được</em></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            const select = $('#access_level');
            if (!select.length) return;

            // Khởi tạo Select2
            select.select2({
                width: '100%',
                theme: 'bootstrap-5',
                placeholder: "Chọn nhóm có quyền xem",
                allowClear: false
            });

            function updateAccessLogic() {
                let selected = select.val() || [];
                const options = select.find('option');

                // Reset tất cả về enabled
                options.prop('disabled', false);

                // ============================================
                // LOGIC CƠ BẢN:
                // 1. "Công khai" = ai cũng xem được (cả người ngoài CLB) → không chọn gì thêm
                // 2. "Tất cả thành viên" = tất cả member trong CLB (kể cả các role) → không chọn gì thêm
                // 3. Chọn role cụ thể = CHỈ những role đó + Phó chủ nhiệm + Chủ nhiệm xem được (member thường KHÔNG xem được)
                // ============================================

                if (selected.includes('public')) {
                    // CASE 1: Chọn "Công khai" → xóa hết, chỉ giữ public
                    selected = ['public'];
                    select.val(selected).trigger('change.select2');

                    // Disable tất cả option khác
                    options.each(function () {
                        if ($(this).val() !== 'public') {
                            $(this).prop('disabled', true);
                        }
                    });
                }
                else if (selected.includes('member')) {
                    // CASE 2: Chọn "Tất cả thành viên" 
                    // → TẤT CẢ member (bao gồm cả các role) đều xem được
                    // → Không cần chọn gì thêm
                    selected = ['member'];
                    select.val(selected).trigger('change.select2');

                    // Disable tất cả option khác
                    options.each(function () {
                        if ($(this).val() !== 'member') {
                            $(this).prop('disabled', true);
                        }
                    });
                }
                else if (selected.length > 0) {
                    // CASE 3: Chỉ chọn các role cụ thể (KHÔNG có member)
                    // → Nghĩa là CHỈ những role này + Phó chủ nhiệm + Chủ nhiệm mới xem được
                    // → Member thường KHÔNG xem được

                    // Disable "Công khai" và "Tất cả thành viên"
                    options.each(function () {
                        const val = $(this).val();
                        if (val === 'public' || val === 'member') {
                            $(this).prop('disabled', true);
                        }
                    });

                    // Tự động thêm "Phó chủ nhiệm" và "Chủ nhiệm" vì họ luôn có quyền xem mọi tài liệu
                    if (!selected.includes('deputy_manager')) {
                        selected.push('deputy_manager');
                    }
                    if (!selected.includes('club_manager')) {
                        selected.push('club_manager');
                    }
                    select.val(selected).trigger('change.select2');
                }

                // Cập nhật lại Select2
                select.trigger('change.select2');
            }

            // Chạy logic khi load trang
            updateAccessLogic();

            // Chạy logic mỗi khi thay đổi
            select.on('change', updateAccessLogic);
        });
    </script>
@endpush