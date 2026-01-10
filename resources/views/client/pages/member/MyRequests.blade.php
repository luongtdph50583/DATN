@extends('client.layouts.app')

@section('title', 'Các yêu cầu của tôi')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Các yêu cầu của tôi</h3>

  @forelse($requests as $req)
    <div class="card mb-3 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                @if($req->type == 'formation')
                    <span class="badge bg-primary">Thành lập CLB</span>
                    <h5>{{ $req->name ?? 'CLB mới' }}</h5>
                @elseif($req->type == 'join')
                    <span class="badge bg-info">Tham gia CLB</span>
                    <h5>{{ $req->club->name ?? 'CLB' }}</h5>
                @endif

                <small>Ngày gửi: {{ optional($req->created_at)->format('d/m/Y H:i') }}</small>
            </div>

            <div>
            

                @if($req->type == 'formation')
                    <a href="{{ route('formation-request.show', $req) }}" class="btn btn-sm btn-primary ms-2">Xem chi tiết</a>
                @elseif($req->type == 'join')
                    <a href="#" class="btn btn-sm btn-primary ms-2">Xem chi tiết</a>
                @endif
            </div>
        </div>
    </div>
@empty
    <p class="text-muted">Bạn chưa gửi yêu cầu nào.</p>
@endforelse


    {{-- Phân trang --}}
    @if($requests->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
