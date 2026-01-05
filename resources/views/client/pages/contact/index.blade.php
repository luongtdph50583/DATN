@extends('client.layouts.app') {{-- đổi theo layout của bạn --}}

@section('title', 'Liên hệ')

@section('content')
<div class="container py-5" style="max-width: 700px">

    <h2 class="mb-4 text-center">Liên hệ với chúng tôi</h2>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Hiển thị lỗi --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Vui lòng kiểm tra lại thông tin</strong>
            <ul class="mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.contact.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea name="message" rows="5" class="form-control">{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Gửi liên hệ
        </button>
    </form>

</div>
@endsection
