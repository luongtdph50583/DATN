@extends('admin.layouts.app')

@section('title', 'Sửa Người dùng')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Sửa Người dùng</h1>
            <p class="text-muted small mb-0">Cập nhật thông tin tài khoản</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
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
                            <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" placeholder="Nguyễn Văn A" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" placeholder="user@example.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu mới <small class="text-muted">(bỏ trống nếu không đổi)</small></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="••••••••">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Cột 2 -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Vai trò <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">-- Chọn vai trò --</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="club_manager" {{ old('role', $user->role) === 'club_manager' ? 'selected' : '' }}>Quản lý CLB</option>
                                <option value="member" {{ old('role', $user->role) === 'member' ? 'selected' : '' }}>Thành viên</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Khóa</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                            @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">JPG, PNG, GIF. Tối đa 2MB.</div>

                            @if($user->avatar)
                                <div class="mt-3 text-center">
                                    <p class="text-muted mb-2">Ảnh hiện tại:</p>
                                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="img-thumbnail" style="max-height: 120px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i>Cập nhật
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary px-5">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection