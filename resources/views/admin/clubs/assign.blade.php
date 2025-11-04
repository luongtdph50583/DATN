@extends('admin.layouts.app')

@section('title', 'Gán chủ nhiệm cho CLB')

@section('card-body')
<div class="container py-4">
    <h1 class="mb-4">
        Gán chủ nhiệm cho CLB: <strong>{{ $club->name }}</strong>
    </h1>

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

    <form method="POST" action="{{ route('admin.clubs.assign.store', $club->id) }}">
        @csrf

        <div class="mb-3">
            <label for="manager_id" class="form-label">Chọn chủ nhiệm mới:</label>
            <select name="manager_id" id="manager_id" class="form-select">
                <option value="">-- Chọn người làm chủ nhiệm --</option>
                @foreach($users as $user)
                    @php
                        $isDisabled = in_array($user->id, $managerIds); // đã là chủ nhiệm CLB khác
                    @endphp
                    <option 
                        value="{{ $user->id }}" 
                        {{ $club->manager_id == $user->id ? 'selected' : '' }}
                        {{ $isDisabled ? 'disabled' : '' }}
                    >
                        {{ $user->name }} ({{ $user->email }})
                        @if($isDisabled)
                            — Đã là chủ nhiệm CLB khác
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Gán chủ nhiệm</button>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
