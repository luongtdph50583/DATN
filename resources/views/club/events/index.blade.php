@extends('client.layouts.app')
@section('title', 'Sự kiện - ' . $club->name)

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Quản lý sự kiện</h2>
            <p class="text-muted">CLB: <strong>{{ $club->name }}</strong></p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('club_manager.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tạo sự kiện
            </a>
         <a href="{{ route('club_manager.events.requests') }}" class="btn btn-outline-secondary">
    <i class="fas fa-list me-1"></i> Yêu cầu tạo sự kiện của bạn
</a>

        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Sự kiện</th>
                        <th>Thời gian</th>
                        <th>Đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td><strong>{{ $event->name }}</strong></td>
                            <td>
                                {{ \Carbon\Carbon::parse($event->start_time)->format('d/m H:i') }} - 
                                {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                            </td>
                            <td>{{ $event->registrations_count ?? 0 }}</td>
                           <td>
    @php
        $now = \Carbon\Carbon::now();
        $start = \Carbon\Carbon::parse($event->start_time);
        $end = \Carbon\Carbon::parse($event->end_time);
    @endphp

    @if($now->lt($start))
        <span class="badge bg-secondary">Sự kiện chưa diễn ra</span>
    @elseif($now->between($start, $end))
        <span class="badge bg-success">Sự kiện đang diễn ra</span>
    @else
        <span class="badge bg-dark">Sự kiện đã kết thúc</span>
    @endif
</td>

                            <td>
                               <a href="{{ route('club_manager.events.show', $event->id) }}" 
   class="btn btn-sm btn-outline-primary">
    Xem chi tiết
</a>

                                <a href="#" class="btn btn-sm btn-success">Điểm danh</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Chưa có sự kiện đã duyệt</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection
