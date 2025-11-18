@extends('client.layouts.app')
@section('title', 'Sự kiện - ' . $club->name)

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Quản lý sự kiện</h2>
            <p class="text-muted">CLB: <strong>{{ $club->name }}</strong></p>
        </div>
        <a href="{{ route('club.events.create') }}" class="btn btn-primary">
            Tạo sự kiện
        </a>
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
                        <th>Loại</th>
                        <th>Thời gian</th>
                        <th>Đăng ký</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td>
                            <strong>{{ $event->title }}</strong>
                            @if(!$event->is_published)<span class="badge bg-secondary ms-2">Nháp</span>@endif
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ ['offline'=>'Offline','lien_hoan'=>'Liên hoan','hop'=>'Họp'][$event->type] }}
                            </span>
                        </td>
                        <td>
                            {{ $event->start_time->format('d/m H:i') }} - 
                            {{ $event->end_time->format('H:i') }}
                        </td>
                        <td>{{ $event->registrations_count }}</td>
                        <td>
                            <a href="{{ route('club.events.registrations', $event) }}" class="btn btn-sm btn-outline-primary">Đăng ký</a>
                            <a href="{{ route('club.events.attendance', $event) }}" class="btn btn-sm btn-success">Điểm danh</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">Chưa có sự kiện</td></tr>
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