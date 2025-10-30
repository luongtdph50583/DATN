@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu tham gia CLB')

@section('card-body')
<div class="container-fluid">

    <h2 class="mb-4">Chi tiết yêu cầu</h2>

    <div class="card shadow p-4">
        <h5 class="mb-3">
            <strong>CLB:</strong> {{ $requestJoin->club->name }}
        </h5>

        <div class="d-flex align-items-center mb-3">
            <img src="{{ $requestJoin->user->avatar ?? 'https://via.placeholder.com/50' }}" 
                 class="rounded-circle me-3" width="50" height="50">
            <div>
                <strong>{{ $requestJoin->user->name }}</strong><br>
                <small>Email: {{ $requestJoin->user->email }}</small>
            </div>
        </div>

        <p><strong>Trạng thái:</strong>
            @if($requestJoin->status == 'pending')
                <span class="badge bg-warning">Pending</span>
            @elseif($requestJoin->status == 'approved')
                <span class="badge bg-success">Approved</span>
            @else
                <span class="badge bg-danger">Rejected</span>
            @endif
        </p>

        <p><strong>Lý do tham gia:</strong></p>
        <div class="border p-3 bg-light rounded">
            {!! nl2br(e($requestJoin->reason)) !!}
        </div>

        <hr>
        <p><strong>Ngày gửi:</strong> {{ $requestJoin->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Ngày cập nhật:</strong> {{ $requestJoin->updated_at->format('d/m/Y H:i') }}</p>

        <div class="mt-3">
            <a href="{{ route('admin.club-join-requests.index') }}" class="btn btn-secondary">
                ← Quay lại
            </a>
        </div>
    </div>
</div>
@endsection
