@extends('admin.layouts.app')

@section('title', 'Thêm mới Sự kiện')

@section('card-body')
{{-- Thêm CSS Select2 trực tiếp --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Thêm mới Sự kiện</h1>
            <p class="text-muted small mb-0">Tạo sự kiện mới cho các câu lạc bộ</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.events.store') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Có lỗi xảy ra:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-4">
                    <!-- Cột 1 -->
                    <div class="col-md-6">
                        <!-- Select2 CLB -->
<div class="mb-3">
    <label class="form-label fw-bold">Câu lạc bộ <span class="text-danger">*</span></label>
    <select name="club_id" id="club_id" class="form-select select2-club @error('club_id') is-invalid @enderror" style="width: 100%;" required>
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
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="VD: Hội thảo AI 2025" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="4" placeholder="Mô tả chi tiết về sự kiện...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time') }}" required>
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time') }}" required>
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Cột 2 -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location') }}" placeholder="VD: Hội trường A, ĐH Bách Khoa" required>
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Giới hạn số người tham gia</label>
                            <input type="number" name="max_participants" class="form-control @error('max_participants') is-invalid @enderror"
                                   value="{{ old('max_participants') }}" min="1" placeholder="VD: 200 (để trống = không giới hạn)">
                            @error('max_participants') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hiển thị sự kiện</label>
                            <select name="is_public" class="form-select @error('is_public') is-invalid @enderror" required>
                                <option value="1" {{ old('is_public', 1) == 1 ? 'selected' : '' }}>Công khai toàn trường</option>
                                <option value="0" {{ old('is_public') == 0 ? 'selected' : '' }}>Chỉ hiển thị cho CLB</option>
                            </select>
                            @error('is_public') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Người tạo <span class="text-danger">*</span></label>
                            <select name="created_by" id="created_by" class="form-select @error('created_by') is-invalid @enderror" required>
                                <option value="">-- Chọn CLB trước --</option>
                            </select>
                            @error('created_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngân sách dự kiến (VNĐ)</label>
                            <input type="number" name="budget_estimated" class="form-control @error('budget_estimated') is-invalid @enderror"
                                   value="{{ old('budget_estimated') }}" min="0" step="0.01" placeholder="VD: 50000000">
                            @error('budget_estimated') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                      <div class="mb-3">
    <label class="form-label fw-bold">Ngân sách xin cấp từ nhà trường (VNĐ)</label>
    <input type="number" name="budget_requested" class="form-control @error('budget_requested') is-invalid @enderror"
           value="{{ old('budget_requested') }}" min="0" step="0.01" placeholder="VD: 30000000">
    @error('budget_requested') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Ngân sách CLB tự chi (VNĐ)</label>
    <input type="number" name="budget_club" class="form-control @error('budget_club') is-invalid @enderror"
           value="{{ old('budget_club') }}" min="0" step="0.01" placeholder="VD: 20000000">
    @error('budget_club') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

<script>
document.getElementById('club_id').addEventListener('change', function() {
    const clubId = this.value;
    const createdBySelect = document.getElementById('created_by');
    createdBySelect.innerHTML = '<option value="">-- Đang tải... --</option>';

    if (!clubId) {
        createdBySelect.innerHTML = '<option value="">-- Chọn CLB trước --</option>';
        return;
    }

    fetch(`/admin/events/club-members/${clubId}`)
        .then(response => response.json())
        .then(data => {
            createdBySelect.innerHTML = '<option value="">-- Chọn người tạo --</option>';
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.email})`;
                if ({{ old('created_by') }} == user.id) option.selected = true;
                createdBySelect.appendChild(option);
            });
        })
        .catch(() => {
            createdBySelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#club_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Tìm và chọn câu lạc bộ...',
        allowClear: true
    });

    @if(old('club_id'))
        $('#club_id').val('{{ old('club_id') }}').trigger('change');
    @endif

    $('#club_id').on('change', function() {
        const clubId = this.value;
        const createdBySelect = document.getElementById('created_by');
        createdBySelect.innerHTML = '<option value="">-- Đang tải... --</option>';

        if (!clubId) {
            createdBySelect.innerHTML = '<option value="">-- Chọn CLB trước --</option>';
            return;
        }

   fetch('{{ url("admin/events/club-members") }}/' + clubId)

            .then(response => response.json())
            .then(data => {
                createdBySelect.innerHTML = '<option value="">-- Chọn người tạo --</option>';
                data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.email})`;
                    if ('{{ old('created_by') }}' == user.id) option.selected = true;
                    createdBySelect.appendChild(option);
                });
            })
            .catch(() => {
                createdBySelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    });

    @if(old('club_id'))
        $('#club_id').trigger('change');
    @endif
});
</script>
@endsection
@push('scripts')

@endpush