@extends('admin.layouts.app')
@section('title', 'Thêm Kế hoạch Mới')

@section('card-body')
<div class="plans-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Thêm Kế hoạch Mới</h1>
                <p class="page-subtitle">Tạo kế hoạch hoạt động cho CLB</p>
            </div>
            <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-section">
        <form action="{{ route('admin.plans.store') }}" method="POST" id="plan-form">
            @csrf

            <!-- Lỗi -->
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
                <!-- Cột 1 -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">CLB <span class="text-danger">*</span></label>
                        <select name="club_id" class="form-select @error('club_id') is-invalid @enderror" required>
                            <option value="">-- Chọn CLB --</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('club_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="Hội thảo AI 2025" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Mô tả <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="5" placeholder="Mô tả chi tiết kế hoạch..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Cột 2 -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">Ngân sách dự kiến</label>
                        <input type="number" name="budget" class="form-control @error('budget') is-invalid @enderror"
                               value="{{ old('budget') }}" step="1000" min="0" placeholder="50,000,000">
                        @error('budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Đơn vị: VNĐ</small>
                    </div>
                </div>
            </div>

            <!-- Nút -->
            <div class="form-actions mt-5 pt-4 border-top">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    Tạo kế hoạch
                </button>
                <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary btn-lg px-5 ms-3">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    :root {
        --primary: #6366f1; --primary-dark: #4f46e5;
        --success: #10b981; --danger: #ef4444; --warning: #f59e0b;
        --dark: #1e293b; --border: #e2e8f0; --shadow: 0 10px 25px -3px rgba(0,0,0,0.1);
        --radius: 16px; --transition: all 0.3s ease;
    }

    .plans-container { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 2rem; min-height: 100vh; }
    .header-section { background: white; padding: 1.8rem 2rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 1.5rem; border-left: 5px solid var(--primary); }
    .header-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
    .page-title { font-size: 1.9rem; font-weight: 700; color: var(--dark); margin: 0; }
    .page-subtitle { color: #64748b; margin: 0.5rem 0 0; font-size: 0.95rem; }

    .form-section { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 2.5rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { color: var(--dark); font-size: 0.95rem; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control, .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 0.75rem 1rem; font-size: 1rem; transition: var(--transition); }
    .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.2); outline: none; }
    .is-invalid { border-color: var(--danger) !important; }
    .invalid-feedback { font-size: 0.875rem; color: var(--danger); margin-top: 0.25rem; }
    .form-text { font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem; }
    .form-actions { display: flex; justify-content: flex-start; gap: 1rem; }
    .btn { border-radius: 10px; font-weight: 600; padding: 0.75rem 1.5rem; font-size: 1rem; transition: var(--transition); }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border: none; color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99, 102, 241, 0.5); }
    .btn-secondary { background: #e5e7eb; color: var(--dark); border: none; }
    .btn-secondary:hover { background: #d1d5db; }
    .alert { border-radius: 12px; padding: 1rem 1.5rem; border: none; }
    .alert-danger { background: #fee2e2; color: #991b1b; border-left: 5px solid var(--danger); }

    @media (max-width: 768px) {
        .header-content { flex-direction: column; text-align: center; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; }
    }
</style>
@endsection