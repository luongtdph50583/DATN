@extends('admin.layouts.app')
@section('title', 'Thùng rác - Sự kiện')

@section('card-body')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-1">Thùng rác sự kiện</h1>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
            Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($events->count())
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên sự kiện</th>
                            <th>CLB</th>
                            <th>Xóa lúc</th>
                            <th>Lý do</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td><span class="badge bg-danger">#{{ $event->id }}</span></td>
                            <td><strong>{{ $event->name }}</strong></td>
                            <td>{{ $event->club?->name ?? '—' }}</td>
                            <td>{{ $event->deleted_at->format('d/m/Y H:i') }}</td>
                            <td><small>{{ $event->delete_reason ?? '—' }}</small></td>
                            <td>
                                <form action="{{ route('admin.events.restore', $event->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Khôi phục sự kiện #{{ $event->id }} - {{ $event->name }}?')">
                                        Khôi phục
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-trash fa-3x mb-3"></i>
                    <p>Thùng rác trống</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection