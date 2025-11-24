@extends('client.layouts.app')

@section('content')
<div class="container">
    <h2>Chỉnh sửa thông tin cá nhân</h2>

    <!-- Thông báo thành công -->
    @if(session('status') === 'profile-updated')
        <div class="alert alert-success">Cập nhật thông tin thành công!</div>
    @endif

    <!-- Thông báo lỗi validate chung -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label>Mã số sinh viên</label>
            <input type="text" name="student_code" class="form-control @error('student_code') is-invalid @enderror" value="{{ old('student_code', $member->student_code ?? '') }}">
            @error('student_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Giới tính</label>
            <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                <option value="">Chọn giới tính</option>
                <option value="male" {{ old('gender', $member->gender ?? '') == 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ old('gender', $member->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                <option value="other" {{ old('gender', $member->gender ?? '') == 'other' ? 'selected' : '' }}>Khác</option>
            </select>
            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Ngày sinh</label>
            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $member->date_of_birth ?? '') }}">
            @error('date_of_birth')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Địa chỉ</label>
            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $member->address ?? '') }}">
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Khóa học</label>
            <input type="text" name="course" class="form-control @error('course') is-invalid @enderror" value="{{ old('course', $member->course ?? '') }}">
            @error('course')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Chuyên ngành</label>
            <input type="text" name="major" class="form-control @error('major') is-invalid @enderror" value="{{ old('major', $member->major ?? '') }}">
            @error('major')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Số CCCD</label>
            <input type="text" name="citizen_id" class="form-control @error('citizen_id') is-invalid @enderror" value="{{ old('citizen_id', $member->citizen_id ?? '') }}">
            @error('citizen_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Ngày cấp</label>
            <input type="date" name="issued_date" class="form-control @error('issued_date') is-invalid @enderror" value="{{ old('issued_date', $member->issued_date ?? '') }}">
            @error('issued_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Nơi cấp</label>
            <input type="text" name="issued_place" class="form-control @error('issued_place') is-invalid @enderror" value="{{ old('issued_place', $member->issued_place ?? '') }}">
            @error('issued_place')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Dân tộc</label>
            <input type="text" name="ethnicity" class="form-control @error('ethnicity') is-invalid @enderror" value="{{ old('ethnicity', $member->ethnicity ?? '') }}">
            @error('ethnicity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Số điện thoại</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $member->phone ?? '') }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection
