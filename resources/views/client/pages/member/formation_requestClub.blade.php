@extends('client.layouts.app')

@section('title', 'Gửi yêu cầu thành lập CLB')

@section('content')
    <div class="container py-4">

        <h3 class="mb-4 fw-bold">
            <i class="fas fa-users-cog me-2"></i>
            Gửi yêu cầu thành lập CLB
        </h3>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="formation-form" action="{{ route('club-requests.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            {{-- ================== THÔNG TIN CƠ BẢN ================== --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                </div>
                <div class="card-body row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên CLB <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Khẩu hiệu <span class="text-danger">*</span></label>
                        <input type="text" name="slogan" class="form-control @error('slogan') is-invalid @enderror"
                            value="{{ old('slogan') }}" required>
                        @error('slogan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Mô tả <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mục đích hoạt động <span class="text-danger">*</span></label>
                        <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror"
                            rows="3" required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lĩnh vực hoạt động <span class="text-danger">*</span></label>
                        <input type="text" name="field" class="form-control @error('field') is-invalid @enderror"
                            value="{{ old('field') }}" required>
                        @error('field')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kế hoạch 3 tháng đầu (file) <span class="text-danger">*</span></label>
                        <input type="file" name="plan_file" class="form-control @error('plan_file') is-invalid @enderror"
                            accept=".pdf,.doc,.docx" required>
                        <small class="text-muted">Chấp nhận: PDF, DOC, DOCX</small>
                        @error('plan_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Logo CLB <span class="text-danger">*</span></label>
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                            accept="image/*" required>
                        <small class="text-muted">Chấp nhận: JPG, PNG, GIF</small>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ================== THÔNG TIN LIÊN HỆ ================== --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-success text-white fw-semibold">
                    <i class="fas fa-address-book me-2"></i>Thông tin liên hệ
                </div>
                <div class="card-body row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email liên hệ <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ================== BAN CHỦ NHIỆM ================== --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-semibold">
                    <i class="fas fa-user-tie me-2"></i>Ban chủ nhiệm
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $roles = [
                                'club_manager' => ['label' => 'Chủ nhiệm', 'icon' => 'fa-crown'],
                                'deputy_manager' => ['label' => 'Phó chủ nhiệm', 'icon' => 'fa-user-shield'],
                                'secretary' => ['label' => 'Thư ký', 'icon' => 'fa-file-alt'],
                                'treasurer' => ['label' => 'Thủ quỹ', 'icon' => 'fa-wallet'],
                                'event_manager' => ['label' => 'Quản lý sự kiện', 'icon' => 'fa-calendar-check'],
                                'communication' => ['label' => 'Phụ trách truyền thông', 'icon' => 'fa-bullhorn'],
                            ];
                        @endphp

                        @foreach($roles as $key => $role)
                            <div class="col-md-4 col-lg-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas {{ $role['icon'] }} me-1"></i>
                                    {{ $role['label'] }} <span class="text-danger">*</span>
                                </label>
                                <select name="{{ $key }}_id"
                                    class="select2-search @error($key.'_id') is-invalid @enderror"
                                    required
                                    data-placeholder="Tìm kiếm...">
                                    <option value=""></option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->user->id }}"
                                            {{ old($key.'_id') == $student->user->id ? 'selected' : '' }}>
                                            {{ $student->user->name }} - MSSV: {{ $student->student_code }}
                                        </option>
                                    @endforeach
                                </select>
                                @error($key.'_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ================== THÀNH VIÊN BAN ĐẦU ================== --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-info text-white fw-semibold">
                    <i class="fas fa-users me-2"></i>Thành viên ban đầu
                </div>
                <div class="card-body">
                    <label class="form-label fw-semibold">
                        Danh sách thành viên <span class="text-danger">*</span>
                    </label>
                    <select name="members[]"
                        class="select2-search-multiple @error('members') is-invalid @enderror"
                        multiple
                        required
                        data-placeholder="Tìm kiếm và chọn nhiều thành viên...">
                        @foreach($students as $student)
                            <option value="{{ $student->user->id }}"
                                {{ in_array($student->user->id, old('members', [])) ? 'selected' : '' }}>
                                {{ $student->user->name }} - MSSV: {{ $student->student_code }}
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Có thể chọn nhiều thành viên. Sử dụng ô tìm kiếm để lọc nhanh.
                    </small>
                    @error('members')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="mt-3">
                        <div class="fw-semibold mb-2">Đã chọn: <span id="selected-count" class="badge bg-primary">0</span></div>
                        <div id="selected-members" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>

            {{-- ================== QUY TẮC ================== --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-danger text-white fw-semibold">
                    <i class="fas fa-gavel me-2"></i>Quy định CLB
                </div>
                <div class="card-body row g-3">

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Quy tắc CLB <span class="text-danger">*</span></label>
                        <textarea name="rule" class="form-control @error('rule') is-invalid @enderror"
                            rows="3" required>{{ old('rule') }}</textarea>
                        @error('rule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Giới hạn thành viên <span class="text-danger">*</span></label>
                        <input type="number" name="member_limit" min="1"
                            class="form-control @error('member_limit') is-invalid @enderror"
                            value="{{ old('member_limit') }}" required>
                        <small class="text-muted">Số lượng tối đa</small>
                        @error('member_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                </button>
                {{-- <a href="{{ route('clubs.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a> --}}
            </div>

        </form>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-search__field {
            color: #000 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function () {
            // SELECT2 SEARCH SINGLE
            $('.select2-search').select2({
                width: '100%',
                allowClear: true,
                placeholder: function() {
                    return $(this).data('placeholder');
                },
                language: {
                    noResults: function() {
                        return "Không tìm thấy sinh viên";
                    },
                    searching: function() {
                        return "Đang tìm kiếm...";
                    }
                }
            });

            // SELECT2 SEARCH MULTIPLE với hiển thị badge bên ngoài
            $('.select2-search-multiple').select2({
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder');
                },
                allowClear: true,
                closeOnSelect: false,
                language: {
                    noResults: function() {
                        return "Không tìm thấy sinh viên";
                    },
                    searching: function() {
                        return "Đang tìm kiếm...";
                    }
                }
            });

            // Hiển thị danh sách đã chọn bên ngoài
            function updateSelectedMembers() {
                const selected = $('.select2-search-multiple').select2('data');
                const container = $('#selected-members');
                const count = $('#selected-count');

                container.empty();
                count.text(selected.length);

                selected.forEach(function(item) {
                    const badge = $('<span>', {
                        'class': 'badge bg-primary',
                        'html': item.text + ' <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" data-id="' + item.id + '"></button>'
                    });
                    container.append(badge);
                });
            }

            // Event khi chọn/bỏ chọn
            $('.select2-search-multiple').on('change', function() {
                updateSelectedMembers();
            });

            // Xóa thành viên từ badge
            $(document).on('click', '#selected-members .btn-close', function() {
                const id = $(this).data('id');
                const select = $('.select2-search-multiple');
                const values = select.val() || [];
                const newValues = values.filter(v => v != id);
                select.val(newValues).trigger('change');
            });

            // Load danh sách đã chọn ban đầu (nếu có old data)
            updateSelectedMembers();

            // Xác nhận trước khi gửi form
            $('#formation-form').on('submit', function(e) {
                var selectedMembers = $('select[name="members[]"]').val();
                if (selectedMembers && selectedMembers.length > 0) {
                    return confirm('Bạn đã chọn ' + selectedMembers.length + ' thành viên. Xác nhận gửi yêu cầu thành lập CLB?');
                }
            });
        });
    </script>
@endpush
