@extends('client.layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            {{-- Session message --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">

                    <h3 class="fw-bold mb-3 text-center">
                        🔑 Quên mật khẩu
                    </h3>

                    <p class="text-muted">
                        Nhập email của bạn. Hệ thống sẽ gửi link đặt lại mật khẩu.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            📩 Gửi link đặt lại mật khẩu
                        </button>

                    </form>

                    <hr>

                    <p class="text-center mb-0">
                        <a href="{{ route('login') }}">
                            ⬅ Quay lại đăng nhập
                        </a>
                    </p>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
