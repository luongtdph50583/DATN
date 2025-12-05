@extends('client.layouts.app')

@section('title', 'Đăng ký tham gia sự kiện')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Sự kiện bạn có thể tham gia</h3>

    @foreach($events as $event)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">

                <h5 class="card-title">{{ $event->name }}</h5>

                <p class="text-muted mb-1">
                    <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fas fa-clock"></i>
                    {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}
                    -
                    {{ \Carbon\Carbon::parse($event->end_time)->format('d/m/Y H:i') }}
                </p>

                <p class="mb-2">
                    <strong>CLB tổ chức:</strong> {{ $event->club->name }}
                </p>

                {{-- ===========================
                      XỬ LÝ TRẠNG THÁI
                ============================= --}}
                @php
                    $start = \Carbon\Carbon::parse($event->start_time);
                    $end   = \Carbon\Carbon::parse($event->end_time);
                @endphp

                {{-- SỰ KIỆN CHƯA BẮT ĐẦU --}}
                @if(now()->lt($start))

                    @if(in_array($event->id, $joinedEventIds))
                        <span class="badge bg-success px-3 py-2">Đã tham gia</span>
                    @else
                     <form action="{{ route('events.join', ['id' => $event->id]) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-primary btn-sm">
        Đăng ký tham gia
    </button>
</form>

                    @endif

                {{-- SỰ KIỆN ĐANG DIỄN RA --}}
                @elseif(now()->between($start, $end))
                    <span class="badge bg-warning text-dark px-3 py-2">Đang diễn ra</span>

                {{-- SỰ KIỆN ĐÃ KẾT THÚC --}}
                @else
                    <span class="badge bg-secondary px-3 py-2">Đã kết thúc</span>
                @endif

                {{-- Nút xem chi tiết --}}
                <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-dark btn-sm ms-2">
                    Xem chi tiết
                </a>

            </div>
        </div>
    @endforeach

</div>
@endsection
