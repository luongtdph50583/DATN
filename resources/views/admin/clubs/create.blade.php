@extends('admin.layouts.app')

@section('title', 'Thêm Câu lạc bộ mới')

@section('card-body')
<div class="container py-4">
    <h2 class="mb-4">➕ Thêm Câu lạc bộ mới</h2>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Hiển thị lỗi validate --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.clubs.store') }}" enctype="multipart/form-data" class="card shadow-sm p-4">
        @csrf

        {{-- Tên CLB --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Tên CLB</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name') }}" required>
        </div>

        {{-- Lĩnh vực hoạt động --}}
        <div class="mb-3">
            <label for="field" class="form-label fw-bold">Lĩnh vực hoạt động</label>
            <input type="text" name="field" id="field" class="form-control" 
                   value="{{ old('field') }}" required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Mô tả</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
        </div>

        {{-- Email liên hệ --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email liên hệ</label>
            <input type="email" name="email" id="email" class="form-control" 
                   value="{{ old('email') }}">
        </div>

        {{-- Số điện thoại --}}
        <div class="mb-3">
            <label for="phone" class="form-label fw-bold">Số điện thoại</label>
            <input type="text" name="phone" id="phone" class="form-control" 
                   value="{{ old('phone') }}">
        </div>

        {{-- Chủ nhiệm CLB --}}
        <div class="mb-3">
            <label for="manager_id" class="form-label fw-bold">Chủ nhiệm CLB</label>
            <select name="manager_id" id="manager_id" class="form-select" required>
                <option value="">-- Chọn chủ nhiệm --</option>
                @foreach($users as $user)
                    <option 
                        value="{{ $user->id }}"
                        {{ old('manager_id') == $user->id ? 'selected' : '' }}
                        {{ in_array($user->id, $managers) ? 'disabled' : '' }}
                    >
                        {{ $user->name }} ({{ $user->email }})
                        {{ in_array($user->id, $managers) ? ' - ĐÃ LÀ CHỦ NHIỆM CLB' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Giới hạn thành viên --}}
        <div class="mb-3">
            <label for="member_limit" class="form-label fw-bold">Giới hạn thành viên</label>
            <input type="number" name="member_limit" id="member_limit" 
                   class="form-control" min="1" value="{{ old('member_limit') }}">
        </div>

        {{-- Logo CLB --}}
        <div class="mb-3">
            <label for="logo" class="form-label fw-bold">Logo CLB</label>
            <input type="file" name="logo" id="logo" class="form-control">
        </div>

        {{-- Nút hành động --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary px-4">⬅ Quay lại</a>
            <button type="submit" class="btn btn-success px-4">💾 Lưu CLB</button>
        </div>
    </form>
</div>
@endsection
