@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa CLB')

@section('card-body')
<div class="container py-4">
    <h1 class="mb-4">✏️ Chỉnh sửa CLB: <strong>{{ $club->name }}</strong></h1>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Lỗi --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- CỘT TRÁI: FORM CHỈNH SỬA --}}
        <div class="col-md-7">
            <div class="card shadow-sm p-4">
                <form method="POST" action="{{ route('admin.clubs.update', $club->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Tên CLB --}}
                    <div class="mb-3">
                        <label class="form-label">Tên CLB</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $club->name) }}" required>
                    </div>

                    {{-- Lĩnh vực --}}
                    <div class="mb-3">
                        <label class="form-label">Lĩnh vực</label>
                        <input type="text" name="field" class="form-control" value="{{ old('field', $club->field) }}" required>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Email CLB</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $club->email) }}">
                    </div>

                    {{-- Số điện thoại --}}
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $club->phone) }}">
                    </div>

                    {{-- Giới hạn thành viên --}}
                    <div class="mb-3">
                        <label class="form-label">Giới hạn thành viên</label>
                        <input type="number" name="member_limit" class="form-control" value="{{ old('member_limit', $club->member_limit) }}">
                    </div>

                    {{-- Logo --}}
                    <div class="mb-3">
                        <label class="form-label">Logo CLB</label>
                        @if($club->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $club->logo) }}" class="img-thumbnail" style="max-height: 120px;">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control">
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $club->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ $club->status == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                    </div>

                    {{-- Chủ nhiệm CLB (chỉ trong thành viên CLB) --}}
                    <div class="mb-3">
                        <label class="form-label">Chủ nhiệm CLB</label>
                        <select name="manager_id" class="form-select" {{ $members->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Chọn chủ nhiệm trong CLB --</option>
                            @foreach($members as $member)
                                @php
                                    $isManagerElsewhere = in_array($member->user->id, $managerIds);
                                @endphp
                                <option value="{{ $member->user->id }}"
                                    @if($member->user->id == $club->manager_id) selected @endif
                                    @if($isManagerElsewhere && $member->user->id != $club->manager_id) disabled @endif>
                                    {{ $member->user->name }} ({{ $member->user->email }})
                                    @if($isManagerElsewhere && $member->user->id != $club->manager_id)
                                        — Đang là chủ nhiệm CLB khác
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if($members->isEmpty())
                            <small class="text-muted">CLB này chưa có thành viên — không thể gán chủ nhiệm.</small>
                        @endif
                    </div>

                    {{-- Mô tả --}}
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description', $club->description) }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
                        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">⬅ Quay lại</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- CỘT PHẢI: THÔNG TIN HIỆN TẠI --}}
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h5 class="mb-3 fw-bold">📋 Thông tin hiện tại</h5>
                <p><strong>Tên CLB:</strong> {{ $club->name }}</p>
                <p><strong>Lĩnh vực:</strong> {{ $club->field ?? 'Chưa có' }}</p>
                <p><strong>Email:</strong> {{ $club->email ?? 'Chưa có' }}</p>
                <p><strong>Điện thoại:</strong> {{ $club->phone ?? 'Chưa có' }}</p>
                <p><strong>Giới hạn thành viên:</strong> {{ $club->member_limit ?? 'Không giới hạn' }}</p>
                <p><strong>Số thành viên:</strong> {{ $members->count() }}</p>
                <p><strong>Chủ nhiệm:</strong> {{ $club->manager->name ?? 'Chưa có' }}</p>
                <p><strong>Trạng thái:</strong> 
                    <span class="badge bg-{{ $club->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($club->status) }}
                    </span>
                </p>
                <p><strong>Mô tả:</strong></p>
                <p>{{ $club->description ?? 'Không có mô tả' }}</p>

                @if($club->logo)
                    <div class="mt-3 text-center">
                        <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo CLB" class="img-thumbnail" style="max-width: 180px;">
                    </div>
                @else
                    <p><em>Chưa có logo</em></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
