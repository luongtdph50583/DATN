@extends('client.layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mx-auto" style="max-width: 400px;">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-center">Cập nhật Avatar</h5>
        </div>
        <div class="card-body text-center">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="mb-3">
                <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/default-avatar.png') }}" 
                     class="rounded-circle mb-3 shadow-sm" 
                     width="120" height="120" alt="Avatar">
            </div>

            <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="avatar" class="form-control" accept="image/*" required>
                    @error('avatar')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-upload me-1"></i> Upload
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
