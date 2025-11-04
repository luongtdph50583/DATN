@extends('admin.layouts.app')

@section('title', 'Sửa Kế hoạch')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Sửa Kế hoạch</h1>
            <p class="text-muted small mb-0">Cập nhật thông tin kế hoạch #{{ $plan->id }}</p>
        </div>
        <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-info">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
                @csrf @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">CLB <span class="text-danger">*</span></label>
                            <select name="club_id" class="form-select @error('club_id') is-invalid @enderror" required>
                                <option value="">-- Chọn CLB --</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ old('club_id', $plan->club_id) == $club->id ? 'selected' : '' }}>
                                        {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('club_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $plan->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>
                                {{ old('description', $plan->description) }}
                            </textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', $plan->start_date->format('Y-m-d')) }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', $plan->end_date->format('Y-m-d')) }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngân sách (VNĐ)</label>
                            <input type="number" name="budget" class="form-control @error('budget') is-invalid @enderror"
                                   value="{{ old('budget', $plan->budget) }}" min="0" step="1000">
                            @error('budget') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status', $plan->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                                <option value="pending" {{ old('status', $plan->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ old('status', $plan->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ old('status', $plan->status) == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-warning px-5">
                        <i class="fas fa-save me-2"></i>Cập nhật
                    </button>
                    <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-secondary px-5">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection