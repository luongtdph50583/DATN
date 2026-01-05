@extends('client.layouts.app')

@section('title', 'Đăng nhập hệ thống CLB')

@section('content')
<div class="container py-5">
    
    <div class="row justify-content-center">
        <div class="col-md-6">

            {{-- Thông báo session --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    
                    <h3 class="fw-bold mb-3 text-center">
                        🔐 Đăng nhập tài khoản
                    </h3>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}">
                                    Quên mật khẩu?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                             Đăng nhập
                        </button>

                    </form>

                    <hr>

                    <p class="text-center mb-0">
                        Chưa có tài khoản?
                        <a href="{{ route('register') }}">Đăng ký ngay</a>
                    </p>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
