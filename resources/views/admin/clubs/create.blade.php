@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thêm Câu lạc bộ mới</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.clubs._form')
                <div class="mt-3">
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-success">Tạo CLB</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
