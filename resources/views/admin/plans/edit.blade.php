@extends('admin.layouts.app')
@section('title', 'Sửa Kế hoạch' )

@section('card-body')
<div class="plans-container">
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Sửa Kế hoạch</h1>
                <p class="page-subtitle">Cập nhật thông tin kế hoạch</p>
            </div>
            <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-info">
                Xem chi tiết
            </a>
        </div>
    </div>

    <div class="form-section">
        <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
            @csrf @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Có lỗi:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">CLB</label>
                        <select name="club_id" class="form-select @error('club_id') is-invalid @enderror" required>
                            <option value="">-- Chọn CLB --</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ $plan->club_id == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('club_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Tiêu đề</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $plan->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="5" required>{{ old('description', $plan->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $plan->start_date->format('Y-m-d')) }}" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', $plan->end_date->format('Y-m-d')) }}" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Ngân sách</label>
                        <input type="number" name="budget" class="form-control @error('budget') is-invalid @enderror"
                               value="{{ old('budget', $plan->budget) }}" step="1000" min="0">
                        @error('budget') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="draft" {{ $plan->status == 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="pending" {{ $plan->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="approved" {{ $plan->status == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="rejected" {{ $plan->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions mt-5 pt-4 border-top">
                <button type="submit" class="btn btn-warning btn-lg px-5">
                    Cập nhật
                </button>
                <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary btn-lg px-5 ms-3">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    /* DÙNG CHUNG CSS VỚI create.blade.php */
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --dark: #1e293b; --border: #e2e8f0; --shadow: 0 10px 25px -3px rgba(0,0,0,0.1); --radius: 16px; --transition: all 0.3s ease; }
    .plans-container { background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%); padding: 2rem; min-height: 100vh; }
    .header-section { background: white; padding: 1.8rem 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 1.5rem; border-left: 5px solid var(--warning); }
    /* ... phần còn lại giống create.blade.php ... */
    .btn-warning { background: linear-gradient(135deg, var(--warning), #d97706); color: white; }
    .btn-warning:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(245, 158, 11, 0.5); }
</style>
@endsection