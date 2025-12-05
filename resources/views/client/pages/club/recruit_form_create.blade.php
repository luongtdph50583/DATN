@extends('client.layouts.app')
@section('title', 'Tạo form tuyển thành viên - ' . $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="mb-0">Tạo form tuyển thành viên mới</h2>
            <a href="{{ route('club_manager.recruit_forms.list', ['club_id' => $club->id]) }}"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thông tin form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('club_manager.recruit_forms.store', ['club_id' => $club->id]) }}"
                    method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tên form <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                            placeholder="Ví dụ: Form tuyển thành viên kỳ 1/2024">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Mô tả về form tuyển thành viên này...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="is_default"
                                name="is_default" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                Đặt làm form mặc định
                            </label>
                            <small class="text-muted d-block">
                                Form mặc định sẽ được sử dụng khi thành viên đăng ký tham gia CLB.
                            </small>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Tạo form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection




