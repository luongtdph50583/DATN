@extends('admin.layouts.app')

@section('title', 'Thêm Câu lạc bộ mới')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Thêm Câu lạc bộ mới</h1>
            <p class="text-muted small mb-0">Tạo CLB mới trong hệ thống</p>
        </div>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.clubs.store') }}" enctype="multipart/form-data">
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
                            <label class="form-label fw-bold">Tên CLB <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lĩnh vực hoạt động <span class="text-danger">*</span></label>
                            <input type="text" name="field" class="form-control @error('field') is-invalid @enderror"
                                   value="{{ old('field') }}" required>
                            @error('field') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">
                                {{ old('description') }}
                            </textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email liên hệ</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chủ nhiệm CLB <span class="text-danger">*</span></label>
                            <select name="manager_id" class="form-select @error('manager_id') is-invalid @enderror" required>
                                <option value="">-- Chọn chủ nhiệm --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('manager_id') == $user->id ? 'selected' : '' }}
                                        {{ in_array($user->id, $managers) ? 'disabled' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                        {{ in_array($user->id, $managers) ? ' - ĐÃ LÀ CHỦ NHIỆM' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Giới hạn thành viên</label>
                            <input type="number" name="member_limit" class="form-control @error('member_limit') is-invalid @enderror"
                                   value="{{ old('member_limit') }}" min="1">
                            @error('member_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Logo CLB</label>
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">JPG, PNG, GIF. Tối đa 2MB.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fas fa-save me-2"></i>Lưu CLB
                    </button>
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary px-5">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection