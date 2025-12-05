@extends('client.layouts.app')

@section('title', 'Chi tiết sự kiện')

@section('content')
<div class="container py-4">

    <div class="card shadow-sm">
        <div class="card-body">

            <h3 class="card-title mb-3">{{ $event->name }}</h3>

            <p class="text-muted mb-1">
                <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
            </p>

            <p class="text-muted mb-2">
                <i class="fas fa-clock"></i>
                {{ $event->start_time }} - {{ $event->end_time }}
            </p>

            <p class="mb-3">
                <strong>CLB tổ chức:</strong> {{ $event->club->name }}
            </p>

            {{-- =======================
                XỬ LÝ TRẠNG THÁI
            ======================== --}}
            @php
                $start = \Carbon\Carbon::parse($event->start_time);
                $end   = \Carbon\Carbon::parse($event->end_time);
            @endphp

            @if(now()->lt($start))
                <span class="badge bg-info text-dark px-3 py-2">Sắp diễn ra</span>

            @elseif(now()->between($start, $end))
                <span class="badge bg-warning text-dark px-3 py-2">Đang diễn ra</span>

            @else
                <span class="badge bg-secondary px-3 py-2">Đã kết thúc</span>
            @endif

            <hr>

            {{-- Nội dung hoặc mô tả sự kiện --}}
            <div class="mb-3">
                <h5>Mô tả sự kiện</h5>
                <p>{{ $event->description ?? 'Không có mô tả.' }}</p>
            </div>

            {{-- =======================
                XỬ LÝ NÚT ĐĂNG KÝ
            ======================== --}}
            @if(now()->lt($start))

                @if($joined)
                    <span class="badge bg-success p-2">Bạn đã đăng ký tham gia</span>
                @else
                    <form action="{{ route('events.join', $event->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Đăng ký tham gia
                        </button>
                    </form>
                @endif

            @else
                <p class="text-muted mt-2"><em>Sự kiện đã diễn ra hoặc đã kết thúc — không thể đăng ký.</em></p>
            @endif

        </div>
    </div>

</div>
@endsection
