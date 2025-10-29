@extends('admin.layouts.app')

@section('title', 'Gán Chủ nhiệm')

@section('card-body')
<h1 class="mb-4">Gán Chủ nhiệm cho CLB: {{ $club->name }}</h1>

<form action="{{ route('admin.clubs.assignManager', $club->id) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Chọn Chủ nhiệm</label>
        <select name="manager_id" class="form-control" required>
            <option value="">-- Chọn --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ ($club->manager_id ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-success">Lưu</button>
    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
@endsection
