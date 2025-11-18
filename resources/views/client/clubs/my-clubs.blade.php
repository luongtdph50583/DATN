@extends('client.layouts.app')

@section('title', 'CLB của tôi')

@section('content')
<h4 class="mb-4">CLB mà bạn đang tham gia</h4>

@if($memberships->isEmpty())
    <p class="text-muted">Bạn chưa tham gia CLB nào.</p>
@else
    <div class="row">
        @foreach($memberships as $membership)
            @php
                $club = $membership->club;
            @endphp
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    {{-- Logo CLB --}}
                    @if($club->logo)
                        <img src="{{ asset('storage/' . $club->logo) }}" class="card-img-top" alt="{{ $club->name }}" style="height:150px; object-fit:cover;">
                    @else
                        <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height:150px;">
                            No Logo
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        {{-- Tên CLB --}}
                        <h5 class="card-title">{{ $club->name }}</h5>

                        {{-- Lĩnh vực CLB --}}
                        <p class="card-text text-muted">{{ $club->field ?? 'Không có lĩnh vực' }}</p>

                        {{-- Role của user --}}
                        <span class="badge bg-info mb-2">Role: {{ ucfirst($membership->role) }}</span>

                        {{-- Nút quản lý quỹ nếu là treasurer --}}
                        @if($membership->role === 'treasurer')
                            <a href="{{ route('client.clubs.fund.show', $club->id) }}" class="btn btn-sm btn-primary mt-auto">Quản lý quỹ</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
