@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Sự kiện')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Chỉnh sửa Sự kiện</h1>
            <p class="text-muted small mb-0">Cập nhật thông tin sự kiện #{{ $event->id }}</p>
        </div>
        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-info">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.events.update', $event) }}" method="POST">
                @csrf @method('PUT')

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
                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sự kiện <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $event->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">
                                {{ old('description', $event->description) }}
                            </textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time', $event->start_time?->format('Y-m-d\TH:i')) }}" required>
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time', $event->end_time?->format('Y-m-d\TH:i')) }}" required>
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Cột 2 -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location', $event->location) }}" required>
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Giới hạn số người tham gia</label>
                            <input type="number" name="max_participants" class="form-control @error('max_participants') is-invalid @enderror"
                                   value="{{ old('max_participants', $event->max_participants) }}" min="1">
                            @error('max_participants') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hiển thị sự kiện</label>
                            <select name="is_public" class="form-select @error('is_public') is-invalid @enderror" required>
                                <option value="1" {{ old('is_public', $event->is_public) == 1 ? 'selected' : '' }}>Công khai toàn trường</option>
                                <option value="0" {{ old('is_public', $event->is_public) == 0 ? 'selected' : '' }}>Chỉ hiển thị cho CLB</option>
                            </select>
                            @error('is_public') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="pending" {{ old('status', $event->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ old('status', $event->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ old('status', $event->status) == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
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

                        @if($event->approval_by)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Người duyệt</label>
                                <div class="form-control bg-light">
                                    {{ $event->approvalBy->name }} ({{ $event->approvalBy->email }})
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngân sách dự kiến (VNĐ)</label>
                            <input type="number" name="budget_estimated" class="form-control @error('budget_estimated') is-invalid @enderror"
                                   value="{{ old('budget_estimated', $event->budget_estimated) }}" min="0" step="0.01">
                            @error('budget_estimated') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngân sách hiện có (VNĐ)</label>
                            <input type="number" name="budget_current" class="form-control @error('budget_current') is-invalid @enderror"
                                   value="{{ old('budget_current', $event->budget_current) }}" min="0" step="0.01">
                            @error('budget_current') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngân sách đã sử dụng (VNĐ)</label>
                            <input type="number" name="budget_used" class="form-control @error('budget_used') is-invalid @enderror"
                                   value="{{ old('budget_used', $event->budget_used) }}" min="0" step="0.01">
                            @error('budget_used') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i>Cập nhật
                    </button>
                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary px-5">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection