@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Sự kiện')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Chỉnh sửa Sự kiện</h1>
                <p class="page-subtitle">Cập nhật thông tin sự kiện #{{ $event->id }}</p>
            </div>
            <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-info">
                Xem chi tiết
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <div class="form-card">
            <form action="{{ route('admin.events.update', $event->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                                    <option value="{{ $club->id }}" {{ old('club_id', $event->club_id) == $club->id ? 'selected' : '' }}>
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
                                   value="{{ old('name', $event->name) }}" placeholder="VD: Hội thảo AI 2025" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Mô tả -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="4" placeholder="Mô tả chi tiết về sự kiện...">{{ old('description', $event->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Thời gian bắt đầu -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time', $event->start_time?->format('Y-m-d\TH:i')) }}" required>
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Thời gian kết thúc -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time', $event->end_time?->format('Y-m-d\TH:i')) }}" required>
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Cột 2 -->
                    <div class="col-md-6">
                        <!-- Địa điểm -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location', $event->location) }}" placeholder="VD: Hội trường A, ĐH Bách Khoa" required>
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Số người tham gia tối đa -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Giới hạn số người tham gia</label>
                            <input type="number" name="max_participants" class="form-control @error('max_participants') is-invalid @enderror"
                                   value="{{ old('max_participants', $event->max_participants) }}" min="1" placeholder="VD: 200 (để trống = không giới hạn)">
                            @error('max_participants') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Công khai -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Hiển thị công khai</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_public" value="1"
                                       {{ old('is_public', $event->is_public) ? 'checked' : '' }} id="is_public">
                                <label class="form-check-label" for="is_public">
                                    {{ old('is_public', $event->is_public) ? 'Công khai' : 'Nội bộ' }}
                                </label>
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="pending" {{ old('status', $event->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ old('status', $event->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ old('status', $event->status) == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Người tạo -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Người tạo <span class="text-danger">*</span></label>
                            <select name="created_by" class="form-select @error('created_by') is-invalid @enderror" required>
                                <option value="">-- Chọn người tạo --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('created_by', $event->created_by) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('created_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Người duyệt (nếu đã duyệt) -->
                        @if($event->approval_by)
                            <div class="form-group">
                                <label class="form-label fw-bold">Người duyệt</label>
                                <div class="form-control bg-light">
                                    {{ $event->approvalBy->name ?? '—' }} ({{ $event->approvalBy->email ?? '' }})
                                </div>
                            </div>
                        @endif

                        <!-- Ngân sách -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Ngân sách (VNĐ)</label>
                            <input type="number" name="budget" class="form-control @error('budget') is-invalid @enderror"
                                   value="{{ old('budget', $event->budget) }}" min="0" step="0.01" placeholder="VD: 45000000">
                            @error('budget') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="form-actions mt-5 pt-4 border-top">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        Cập nhật sự kiện
                    </button>
                    <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-secondary btn-lg px-5 ms-3">
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

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

    .btn-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
        border: none;
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

    .bg-light {
        background-color: #f8f9fa !important;
        color: #6b7280;
        font-style: italic;
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