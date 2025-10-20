@extends('admin.layouts.app')

@section('title', 'Gán chủ nhiệm CLB')

@section('card-body')
<div class="container">
    <h2>Gán chủ nhiệm cho: <strong>{{ $club->name }}</strong></h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.clubs.assign.store', $club->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Chọn chủ nhiệm:</label>
            <select name="manager_id" class="form-control" required>
                <option value="">-- Chọn người dùng --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" 
                        {{ $club->manager_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Gán</button>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
