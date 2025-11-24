@extends('client.layouts.app')

@section('title', 'Gửi yêu cầu thành lập CLB')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Gửi yêu cầu thành lập CLB</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('formation_request.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tên CLB <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Khẩu hiệu <span class="text-danger">*</span></label>
            <input type="text" name="slogan" class="form-control" value="{{ old('slogan') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả CLB <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Mục đích hoạt động <span class="text-danger">*</span></label>
            <textarea name="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Lĩnh vực hoạt động <span class="text-danger">*</span></label>
            <input type="text" name="field" class="form-control" value="{{ old('field') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kế hoạch 3 tháng đầu (file) <span class="text-danger">*</span></label>
            <input type="file" name="plan_file" class="form-control" accept=".pdf,.doc,.docx" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email liên hệ <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại liên hệ <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Logo CLB <span class="text-danger">*</span></label>
            <input type="file" name="logo" class="form-control" accept="image/*" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giảng viên phụ trách <span class="text-danger">*</span></label>
            <select name="advisor_id" class="form-select" >
                <option value="">-- Chọn giảng viên --</option>
                @foreach(\App\Models\User::where('role', 'advisor')->get() as $advisor)
                    <option value="{{ $advisor->id }}" {{ old('advisor_id') == $advisor->id ? 'selected' : '' }}>
                        {{ $advisor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Quy tắc CLB <span class="text-danger">*</span></label>
            <textarea name="rule" class="form-control" rows="3" required>{{ old('rule') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng thành viên tối đa <span class="text-danger">*</span></label>
            <input type="number" name="member_limit" class="form-control" value="{{ old('member_limit') }}" required min="1">
        </div>

        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>
</div>
@endsection
