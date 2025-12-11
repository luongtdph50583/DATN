@extends('client.layouts.app')

@section('title', 'Đơn cần xác nhận')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4">
            <i class="fas fa-clipboard-check me-2"></i>
            Đơn thành lập CLB cần xác nhận của bạn
        </h3>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($confirmations->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Bạn không có đơn nào cần xác nhận.
            </div>
        @else
            <div class="row">
                @foreach($confirmations as $confirmation)
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $confirmation->clubRequest->name }}</h5>
                                <p class="card-text text-muted">
                                    <i class="fas fa-quote-left me-2"></i>
                                    {{ $confirmation->clubRequest->slogan }}
                                </p>
                                <p class="mb-2">
                                    <strong>Lĩnh vực:</strong> {{ $confirmation->clubRequest->field }}
                                </p>
                                <p class="mb-3">
                                    <strong>Người tạo:</strong> {{ $confirmation->clubRequest->creator->name }}
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('club_requests.show', $confirmation->clubRequest) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Xem chi tiết
                                    </a>
                                    <form action="{{ route('club_requests.confirm', $confirmation->clubRequest) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm"
                                            onclick="return confirm('Bạn xác nhận tham gia CLB này?')">
                                            <i class="fas fa-check me-1"></i>Xác nhận tham gia
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    Tạo lúc: {{ $confirmation->clubRequest->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
