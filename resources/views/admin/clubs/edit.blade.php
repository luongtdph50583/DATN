@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sửa CLB: {{ $club->name }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.clubs.update', $club) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.clubs._form')
                <div class="mt-3">
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
