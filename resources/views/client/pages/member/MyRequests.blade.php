@extends('client.layouts.app')

@section('title', 'Các yêu cầu của tôi ')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Yêu cầu thành lập CLB của tôi</h3>

    @forelse($requests as $req)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $req->name }}</h5>
                    <p class="mb-1 text-truncate">{{ $req->slogan ?? $req->field ?? '-' }}</p>
                    <small>Ngày gửi: {{ $req->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div>
                    <span class="badge 
                        {{ $req->status == 'pending' ? 'bg-warning text-dark' : ($req->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                        {{ ucfirst($req->status) }}
                    </span>
                    <a href="{{ route('formation-request.show', $req) }}" class="btn btn-sm btn-primary ms-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Bạn chưa gửi yêu cầu nào.</p>
    @endforelse

    <div class="d-flex justify-content-center">
        {{ $requests->links() }}
    </div>
</div>
@endsection
