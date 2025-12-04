@extends('client.layouts.app')
@section('title', 'Yêu cầu tạo sự kiện - ' . $club->name)

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            
            <div>
                <h2>Yêu cầu tạo sự kiện</h2>
                <p class="text-muted">CLB: <strong>{{ $club->name }}</strong></p>
            </div>
        </div>
        <a href="{{ route('club_manager.events.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
       
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Sự kiện</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
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
                            <td>
                                @if($event->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($event->status === 'rejected')
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Chưa có yêu cầu nào</td>
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
