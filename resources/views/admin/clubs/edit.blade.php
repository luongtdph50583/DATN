@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa CLB')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Chỉnh sửa CLB</h1>
            <p class="text-muted small mb-0">{{ $club->name }}</p>
        </div>
        <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-info">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form & Info -->
    <div class="row g-4">
        <!-- Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.clubs.update', $club) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

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

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tên CLB <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $club->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lĩnh vực <span class="text-danger">*</span></label>
                                <input type="text" name="field" class="form-control @error('field') is-invalid @enderror"
                                       value="{{ old('field', $club->field) }}" required>
                                @error('field') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $club->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $club->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giới hạn thành viên</label>
                                <input type="number" name="member_limit" class="form-control @error('member_limit') is-invalid @enderror"
                                       value="{{ old('member_limit', $club->member_limit) }}" min="1">
                                @error('member_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $club->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ old('status', $club->status) == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Mô tả</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">
                                    {{ old('description', $club->description) }}
                                </textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chủ nhiệm CLB</label>
                                <select name="manager_id" class="form-select @error('manager_id') is-invalid @enderror"
                                        {{ $members->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">-- Chọn trong thành viên --</option>
                                    @foreach($members as $member)
                                        @php $isManagerElsewhere = in_array($member->user->id, $managerIds); @endphp
                                        <option value="{{ $member->user->id }}"
                                            {{ old('manager_id', $club->manager_id) == $member->user->id ? 'selected' : '' }}
                                            {{ $isManagerElsewhere && $member->user->id != $club->manager_id ? 'disabled' : '' }}>
                                            {{ $member->user->name }} ({{ $member->user->email }})
                                            {{ $isManagerElsewhere && $member->user->id != $club->manager_id ? ' — Chủ nhiệm khác' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($members->isEmpty())
                                    <small class="text-muted">Chưa có thành viên → không thể gán.</small>
                                @endif
                                @error('manager_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Logo CLB</label>
                                @if($club->logo)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($club->logo) }}" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                @endif
                                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                            <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary px-5">
                                <i class="fas fa-times me-2"></i>Hủy bỏ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Thông tin hiện tại</h6>
                </div>
                <div class="card-body small">
                    <p><strong>Tên:</strong> {{ $club->name }}</p>
                    <p><strong>Lĩnh vực:</strong> {{ $club->field ?? '—' }}</p>
                    <p><strong>Email:</strong> {{ $club->email ?? '—' }}</p>
                    <p><strong>ĐT:</strong> {{ $club->phone ?? '—' }}</p>
                    <p><strong>Giới hạn:</strong> {{ $club->member_limit ?? 'Không giới hạn' }}</p>
                    <p><strong>Thành viên:</strong> {{ $members->count() }}</p>
                    <p><strong>Chủ nhiệm:</strong> {{ $club->manager->name ?? '—' }}</p>
                    <p><strong>Trạng thái:</strong>
                        <span class="badge {{ $club->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $club->status == 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </p>
                    @if($club->logo)
                        <div class="text-center mt-3">
                            <img src="{{ Storage::url($club->logo) }}" class="img-thumbnail" style="max-height: 120px;">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection