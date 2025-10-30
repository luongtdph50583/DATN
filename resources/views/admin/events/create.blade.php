@extends('admin.layouts.app')

@section('title', 'Thêm mới Sự kiện')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Thêm mới Sự kiện</h1>
                <p class="page-subtitle">Tạo sự kiện mới cho các câu lạc bộ</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <div class="form-card">
            <form action="{{ route('admin.events.store') }}" method="POST">
                @csrf

                <!-- Lỗi -->
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
                        <!-- CLB -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Câu lạc bộ <span class="text-danger">*</span></label>
                            <select name="club_id" class="form-select @error('club_id') is-invalid @enderror" required>
                                <option value="">-- Chọn CLB --</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                        {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('club_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Tên sự kiện -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Tên sự kiện <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="VD: Hội thảo AI 2025" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Mô tả -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="4" placeholder="Mô tả chi tiết về sự kiện...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Thời gian bắt đầu -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time') }}" required>
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Thời gian kết thúc -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time') }}" required>
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Cột 2 -->
                    <div class="col-md-6">
                        <!-- Địa điểm -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location') }}" placeholder="VD: Hội trường A, ĐH Bách Khoa" required>
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Số người tham gia tối đa -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Giới hạn số người tham gia</label>
                            <input type="number" name="max_participants" class="form-control @error('max_participants') is-invalid @enderror"
                                   value="{{ old('max_participants') }}" min="1" placeholder="VD: 200 (để trống = không giới hạn)">
                            @error('max_participants') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Công khai -->
                       <!-- Hiển thị công khai -->
<div class="form-group">
    <label class="form-label fw-bold">Hiển thị sự kiện</label>
    <select name="is_public" class="form-select @error('is_public') is-invalid @enderror" required>
        <option value="1" {{ old('is_public') == 1 ? 'selected' : '' }}>Công khai toàn trường</option>
        <option value="0" {{ old('is_public') == 0 ? 'selected' : '' }}>Chỉ hiển thị cho CLB</option>
    </select>
    @error('is_public') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>


                        <!-- Trạng thái -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                     
                     <!-- Người tạo -->
<div class="form-group">
    <label class="form-label fw-bold">Người tạo <span class="text-danger">*</span></label>
    <select name="created_by" id="created_by" class="form-select @error('created_by') is-invalid @enderror" required>
        <option value="">-- Chọn CLB trước để hiển thị người tạo --</option>
    </select>
    @error('created_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>


                        <!-- Ngân sách -->
                       <!-- Ngân sách dự kiến -->
<div class="form-group">
    <label class="form-label fw-bold">Ngân sách dự kiến (VNĐ)</label>
    <input type="number" name="budget_estimated" class="form-control @error('budget_estimated') is-invalid @enderror"
           value="{{ old('budget_estimated') }}" min="0" step="0.01" placeholder="VD: 50000000">
    @error('budget_estimated') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<!-- Ngân sách hiện có -->
<div class="form-group">
    <label class="form-label fw-bold">Ngân sách hiện có (VNĐ)</label>
    <input type="number" name="budget_current" class="form-control @error('budget_current') is-invalid @enderror"
           value="{{ old('budget_current') }}" min="0" step="0.01" placeholder="VD: 30000000">
    @error('budget_current') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="form-actions mt-5 pt-4 border-top">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        Tạo sự kiện
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-lg px-5 ms-3">
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const clubSelect = document.querySelector('select[name="club_id"]');
    const userSelect = document.querySelector('#created_by');

    clubSelect.addEventListener('change', function () {
        const clubId = this.value;
        userSelect.innerHTML = '<option value="">Đang tải...</option>';

        if (clubId) {
            fetch(`/admin/events/get-managers/${clubId}`)
                .then(response => response.json())
                .then(data => {
                    userSelect.innerHTML = '';
                    if (data.success && data.data.length > 0) {
                        userSelect.innerHTML = '<option value="">-- Chọn người tạo --</option>';
                        data.data.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = `${user.name} (${user.email})`;
                            userSelect.appendChild(option);
                        });
                    } else {
                        userSelect.innerHTML = '<option value="">Không có quản lý nào trong CLB này</option>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    userSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                });
        } else {
            userSelect.innerHTML = '<option value="">-- Chọn CLB trước để hiển thị người tạo --</option>';
        }
    });
});
</script>

<style>
    :root {
        --primary: #5b5eff;
        --primary-dark: #4a49d6;
        --success: #10b981;
        --danger: #ef4444;
        --dark: #1f2937;
        --border: #e5e7eb;
        --shadow: 0 10px 25px -3px rgba(0,0,0,0.1);
        --radius: 14px;
    }

    .modern-container {
        background: linear-gradient(135deg, #f0f4ff 0%, #e0eaff 100%);
        padding: 2rem;
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    .header-section {
        background: white;
        padding: 1.8rem 2rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-size: 1.9rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .page-subtitle {
        color: #6b7280;
        margin: 0.5rem 0 0;
        font-size: 0.95rem;
    }

    .form-section {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 2.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        color: var(--dark);
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .form-control, .form-select {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(91, 94, 255, 0.2);
        outline: none;
    }

    .is-invalid {
        border-color: var(--danger) !important;
    }

    .invalid-feedback {
        font-size: 0.875rem;
        color: var(--danger);
        margin-top: 0.25rem;
    }

    .form-check-input {
        width: 1.5em;
        height: 1.5em;
        margin-top: 0.25em;
    }

    .form-check-label {
        font-weight: 500;
        color: var(--dark);
        margin-left: 0.5rem;
    }

    .form-actions {
        display: flex;
        justify-content: flex-start;
        gap: 1rem;
        padding-top: 1.5rem;
    }

    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(91, 94, 255, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(91, 94, 255, 0.5);
    }

    .btn-secondary {
        background: #e5e7eb;
        color: var(--dark);
        border: none;
    }

    .btn-secondary:hover {
        background: #d1d5db;
        transform: translateY(-1px);
    }

    .alert {
        border-radius: 10px;
        padding: 1rem 1.5rem;
        border: none;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 5px solid var(--danger);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        .form-actions {
            flex-direction: column;
        }
        .btn {
            width: 100%;
        }
    }
</style>
@endsection

