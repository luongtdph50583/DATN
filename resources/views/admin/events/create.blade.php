@extends('admin.layouts.app')

@section('title', 'Thêm mới Sự kiện')

@section('card-body')
    {{-- Thêm CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 mb-1">Thêm mới Sự kiện</h1>
                <p class="text-muted small mb-0">Tạo sự kiện mới cho các câu lạc bộ</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.events.store') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Cột 1 -->
                        <div class="col-md-6">
                            <!-- Select CLB -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Câu lạc bộ <span class="text-danger">*</span></label>
                                <select name="club_id" id="club_id" class="form-select select2 @error('club_id') is-invalid @enderror" style="width: 100%;" required>
                                    <option value="">-- Chọn CLB --</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                            {{ $club->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('club_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên sự kiện <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="VD: Hội thảo AI 2025" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Mô tả chi tiết về sự kiện...">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                            </div>
                        </div>

                        <!-- Cột 2 -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="VD: Hội trường A, ĐH Bách Khoa" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Giới hạn số người tham gia</label>
                                <input type="number" name="max_participants" class="form-control" value="{{ old('max_participants') }}" min="1" placeholder="VD: 200">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Hiển thị sự kiện</label>
                                <select name="is_public" class="form-select" required>
                                    <option value="1" {{ old('is_public', 1) == 1 ? 'selected' : '' }}>Công khai toàn trường</option>
                                    <option value="0" {{ old('is_public') == 0 ? 'selected' : '' }}>Chỉ hiển thị cho CLB</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="">-- Chọn trạng thái --</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                                </select>
                            </div>

                            <!-- Select Người tạo -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Người tạo <span class="text-danger">*</span></label>
                                <select name="created_by" id="created_by" class="form-select" required>
                                    <option value="">-- Chọn CLB trước --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ngân sách dự kiến (VNĐ)</label>
                                <input type="number" name="budget_estimated" class="form-control" value="{{ old('budget_estimated') }}" min="0" placeholder="VD: 50000000">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ngân sách xin cấp từ nhà trường (VNĐ)</label>
                                <input type="number" name="budget_requested" class="form-control" value="{{ old('budget_requested') }}" min="0" placeholder="VD: 30000000">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ngân sách CLB tự chi (VNĐ)</label>
                                <input type="number" name="budget_club" class="form-control" value="{{ old('budget_club') }}" min="0" placeholder="VD: 20000000">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-plus me-2"></i>Tạo sự kiện
                        </button>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary px-5">
                            <i class="fas fa-times me-2"></i>Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Kích hoạt Select2 cho CLB
            $('#club_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Tìm và chọn câu lạc bộ...',
                allowClear: true
            });

            // Khi chọn CLB → tải danh sách thành viên
            $('#club_id').on('change', function () {
                const clubId = $(this).val();
                const createdBySelect = $('#created_by');

                createdBySelect.html('<option value="">-- Đang tải... --</option>');

                if (!clubId) {
                    createdBySelect.html('<option value="">-- Chọn CLB trước --</option>');
                    return;
                }

                fetch(`/admin/events/club-members/${clubId}`)
                    .then(response => response.json())
                    .then(data => {
                        createdBySelect.html('<option value="">-- Chọn người tạo --</option>');
                        data.forEach(user => {
                            const selected = (user.id == '{{ old('created_by') }}') ? 'selected' : '';
                            createdBySelect.append(`<option value="${user.id}" ${selected}>${user.name} (${user.email})</option>`);
                        });
                    })
                    .catch(() => {
                        createdBySelect.html('<option value="">Lỗi tải dữ liệu</option>');
                    });
            });

            // Nếu có old('club_id') thì tự động load danh sách
            @if(old('club_id'))
                $('#club_id').trigger('change');
            @endif
    });
    </script>

@endsection
