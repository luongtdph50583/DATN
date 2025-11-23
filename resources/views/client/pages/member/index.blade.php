@extends('client.layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Danh sách CLB</h3>

    <!-- Form tìm kiếm -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm CLB theo tên hoặc lĩnh vực..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
        </div>
    </form>

    <div class="row">
        @forelse($clubs as $club)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                @if($club->logo)
                <img src="{{ asset('storage/'.$club->logo) }}" class="card-img-top" alt="{{ $club->name }}" style="height:150px; object-fit:cover;">
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $club->name }}</h5>
                    <p class="card-text text-truncate">{{ $club->slogan ?? $club->field }}</p>
                    <p><small>Lĩnh vực: {{ $club->field }}</small></p>
                    <div class="mt-auto">
                    <a href="{{ route('client.clubs.show', $club) }}" class="btn btn-primary w-100">Xem chi tiết / Đăng ký</a>


                    </div>
                </div>
            </div>
        </div>
        @empty
            <p class="text-muted">Hiện chưa có CLB nào.</p>
        @endforelse
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center">
        {{ $clubs->links() }}
    </div>
</div>
@endsection
