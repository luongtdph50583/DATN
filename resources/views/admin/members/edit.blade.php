@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Thành viên')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Chỉnh sửa Thành viên</h1>
                <p class="page-subtitle">Cập nhật thông tin chi tiết hồ sơ thành viên</p>
            </div>
            <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <div class="form-card">
            <form action="{{ route('admin.members.update', $member->id) }}" method="POST">
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
                        <!-- Người dùng (không cho sửa user_id) -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Người dùng</label>
                            <div class="form-control bg-light text-dark">
                                {{ $member->user->name }} ({{ $member->user->email }})
                            </div>
                        </div>
                        <!-- Mã số sinh viên -->
<div class="form-group">
    <label class="form-label fw-bold">Mã số sinh viên <span class="text-danger">*</span></label>
    <input type="text" name="student_code"
           class="form-control @error('student_code') is-invalid @enderror"
           value="{{ old('student_code', $member->student_code) }}"
           placeholder="Nhập mã số sinh viên (VD: PH4995)">
    @error('student_code')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                        <!-- Giới tính -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Giới tính</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Ngày sinh -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth', $member->date_of_birth) }}">
                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Địa chỉ -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                      rows="3" placeholder="Nhập địa chỉ...">{{ old('address', $member->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Khóa học -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Khóa học</label>
                            <input type="text" name="course" class="form-control @error('course') is-invalid @enderror"
                                   value="{{ old('course', $member->course) }}" placeholder="VD: K67">
                            @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Cột 2 -->
                    <div class="col-md-6">
                        <!-- Chuyên ngành -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Chuyên ngành</label>
                            <input type="text" name="major" class="form-control @error('major') is-invalid @enderror"
                                   value="{{ old('major', $member->major) }}" placeholder="VD: Công nghệ thông tin">
                            @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- CCCD -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Số CCCD</label>
                            <input type="text" name="citizen_id" class="form-control @error('citizen_id') is-invalid @enderror"
                                   value="{{ old('citizen_id', $member->citizen_id) }}" placeholder="0123456789">
                            @error('citizen_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Ngày cấp -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Ngày cấp CCCD</label>
                            <input type="date" name="issued_date" class="form-control @error('issued_date') is-invalid @enderror"
                                   value="{{ old('issued_date', $member->issued_date) }}">
                            @error('issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Nơi cấp -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Nơi cấp CCCD</label>
                            <input type="text" name="issued_place" class="form-control @error('issued_place') is-invalid @enderror"
                                   value="{{ old('issued_place', $member->issued_place) }}" placeholder="Công an TP.HCM">
                            @error('issued_place') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Dân tộc -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Dân tộc</label>
                            <input type="text" name="ethnicity" class="form-control @error('ethnicity') is-invalid @enderror"
                                   value="{{ old('ethnicity', $member->ethnicity) }}" placeholder="Kinh">
                            @error('ethnicity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Số điện thoại -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $member->phone) }}" placeholder="0901234567">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Trạng thái -->
                        <div class="form-group">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Khóa</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="form-actions mt-5 pt-4 border-top">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        Cập nhật thành viên
                    </button>
                    <a href="{{ route('admin.members.index') }}" class="btn btn-secondary btn-lg px-5 ms-3">
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

    .form-card {
        max-width: 100%;
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

    .bg-light {
        background-color: #f8f9fa !important;
        font-weight: 500;
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