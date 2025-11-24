@extends('client.layouts.app')

@section('title', 'Gửi yêu cầu thành lập CLB')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Gửi yêu cầu thành lập CLB</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('formation_request.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tên CLB <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Khẩu hiệu</label>
            <input type="text" name="slogan" class="form-control" value="{{ old('slogan') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả CLB</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Mục đích hoạt động</label>
            <textarea name="purpose" class="form-control" rows="3">{{ old('purpose') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Lĩnh vực hoạt động</label>
            <input type="text" name="field" class="form-control" value="{{ old('field') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Kế hoạch 3 tháng đầu</label>
            <textarea name="plan" class="form-control" rows="4">{{ old('plan') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Email liên hệ</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại liên hệ</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Logo CLB</label>
            <input type="file" name="logo" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label class="form-label">Giảng viên đỡ đầu (nếu có)</label>
            <select name="advisor_id" class="form-select">
                <option value="">-- Chọn giảng viên --</option>
                @foreach(\App\Models\User::where('role', 'advisor')->get() as $advisor)
                    <option value="{{ $advisor->id }}" {{ old('advisor_id') == $advisor->id ? 'selected' : '' }}>
                        {{ $advisor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Quy tắc CLB</label>
            <textarea name="rule" class="form-control" rows="3">{{ old('rule') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng thành viên tối đa</label>
            <input type="number" name="member_limit" class="form-control" value="{{ old('member_limit') }}">
        </div>

        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>
</div>
@endsection
