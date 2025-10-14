@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gán hoặc thay đổi Chủ nhiệm CLB</h1>

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user"></i> {{ $club->name }}
            </h6>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.clubs.assignManager', $club) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tên CLB</label>
                    <input type="text" class="form-control" value="{{ $club->name }}" disabled>
                </div>

                <div class="mb-3">
                    <label for="manager_id" class="form-label">Chọn Chủ nhiệm</label>
                    <select name="manager_id" id="manager_id" class="form-select" required>
                        <option value="">-- Chọn sinh viên --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" 
                                {{ $club->manager_id == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->student_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
