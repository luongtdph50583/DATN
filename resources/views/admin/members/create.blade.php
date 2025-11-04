@extends('admin.layouts.app')

@section('title', 'Thêm Thành viên Mới')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Thêm Thành viên Mới</h1>
            <p class="text-muted small mb-0">Tạo hồ sơ chi tiết cho thành viên mới</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.members.store') }}" method="POST">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Người dùng <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Chọn người dùng --</option>
                                @forelse($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @empty
                                    <option value="" disabled>Không có người dùng khả dụng</option>
                                @endforelse
                            </select>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mã số sinh viên</label>
                            <input type="text" name="student_code" class="form-control @error('student_code') is-invalid @enderror"
                                   value="{{ old('student_code') }}" placeholder="VD: PH4995">
                            @error('student_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Giới tính</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth') }}">
                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">
                                {{ old('address') }}
                            </textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Khóa học</label>
                            <input type="text" name="course" class="form-control @error('course') is-invalid @enderror"
                                   value="{{ old('course') }}" placeholder="VD: K67">
                            @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chuyên ngành</label>
                            <input type="text" name="major" class="form-control @error('major') is-invalid @enderror"
                                   value="{{ old('major') }}" placeholder="VD: Công nghệ thông tin">
                            @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số CCCD</label>
                            <input type="text" name="citizen_id" class="form-control @error('citizen_id') is-invalid @enderror"
                                   value="{{ old('citizen_id') }}" placeholder="0123456789">
                            @error('citizen_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngày cấp CCCD</label>
                            <input type="date" name="issued_date" class="form-control @error('issued_date') is-invalid @enderror"
                                   value="{{ old('issued_date') }}">
                            @error('issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nơi cấp CCCD</label>
                            <input type="text" name="issued_place" class="form-control @error('issued_place') is-invalid @enderror"
                                   value="{{ old('issued_place') }}" placeholder="Công an TP.HCM">
                            @error('issued_place') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Dân tộc</label>
                            <input type="text" name="ethnicity" class="form-control @error('ethnicity') is-invalid @enderror"
                                   value="{{ old('ethnicity') }}" placeholder="Kinh">
                            @error('ethnicity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="0901234567">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Khóa</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-user-plus me-2"></i>Thêm thành viên
                    </button>
                    <a href="{{ route('admin.members.index') }}" class="btn btn-secondary px-5">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection