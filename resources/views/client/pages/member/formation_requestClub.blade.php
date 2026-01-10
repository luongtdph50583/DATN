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
    <label class="form-label">Lĩnh vực hoạt động <span class="text-danger">*</span></label>
    <input type="text" name="field" class="form-control" value="{{ old('field') }}" required>
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
    <label class="form-label">Email liên hệ <span class="text-danger">*</span></label>
    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Số điện thoại liên hệ <span class="text-danger">*</span></label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Số lượng thành viên tối đa <span class="text-danger">*</span></label>
    <input type="number" name="member_limit" class="form-control" value="{{ old('member_limit') }}" required min="1">
</div>
<div class="mb-3">
    <label class="form-label">Quy tắc CLB (file) <span class="text-danger">*</span></label>
    <input 
        type="file" 
        name="rule_file" 
        class="form-control"
        accept=".pdf,.doc,.docx"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label">
        Giấy tờ có chữ ký đồng ý thành lập CLB <span class="text-danger">*</span>
    </label>
    <input
        type="file"
        name="approval_document"
        class="form-control"
        accept=".pdf,.doc,.docx,.jpg,.png"
        required
    >
</div>




<div class="mb-3">
    <label class="form-label">Logo CLB <span class="text-danger">*</span></label>
    <input type="file" name="logo" class="form-control" accept="image/*" required>
</div>

<div class="mb-3">
    <label class="form-label">Kế hoạch 3 tháng đầu (file) <span class="text-danger">*</span></label>
    <input type="file" name="plan_file" class="form-control" accept=".pdf,.doc,.docx" required>
</div>

<div class="mb-3">
    <label class="form-label">
        Giảng viên phụ trách <span class="text-danger">*</span>
    </label>

    <select name="advisor_id"
            class="form-select select2"
            required
            data-placeholder="Chọn giảng viên">
        <option value=""></option>

        @foreach($advisors as $advisor)
            <option value="{{ $advisor->id }}"
                {{ old('advisor_id') == $advisor->id ? 'selected' : '' }}>

                {{ $advisor->name }}
                @if($advisor->facultyMember && $advisor->facultyMember->employee_code)
                    ({{ $advisor->facultyMember->employee_code }})
                @endif

            </option>
        @endforeach
    </select>
</div>







        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
</script>

@endsection
