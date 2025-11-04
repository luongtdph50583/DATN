@extends('admin.layouts.app')

@section('title', 'Quản lý Sự kiện')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Quản lý Sự kiện</h1>
            <p class="text-muted small mb-0">Theo dõi, duyệt và quản lý toàn bộ sự kiện của các CLB</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm sự kiện
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.events.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold">Tên sự kiện</label>
                    <input type="text" name="search_name" class="form-control" 
                           placeholder="Nhập tên..." value="{{ request('search_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Câu lạc bộ</label>
                    <select name="club_id" class="form-select">
                        <option value="">-- Tất cả CLB --</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-2"></i>Tìm
                    </button>
                    @if (request()->hasAny(['search_name', 'club_id']))
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 20%;">Tên sự kiện</th>
                                <th style="width: 18%;">Thời gian</th>
                                <th style="width: 15%;">Địa điểm</th>
                                <th style="width: 8%;">Số người</th>
                                <th style="width: 10%;">Trạng thái</th>
                                <th style="width: 10%;">Người tạo</th>
                                <th style="width: 8%;">CLB</th>
                                <th style="width: 16%; text-align: center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $event->id }}</span></td>

                                    <td class="fw-bold">
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-decoration-none">
                                            {{ Str::limit($event->name, 40) }}
                                        </a>
                                    </td>

                                    <td class="small">
                                        <div><strong>Bắt đầu:</strong> {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('d/m H:i') : '—' }}</div>
                                        <div><strong>Kết thúc:</strong> {{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('d/m H:i') : '—' }}</div>
                                    </td>

                                    <td>{{ Str::limit($event->location, 30) }}</td>

                                    <td>
                                        @if($event->max_participants)
                                            <span class="text-primary fw-bold">{{ $event->max_participants }}</span>
                                        @else
                                            <span class="text-muted">Không giới hạn</span>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $statusLabels = [
                                                'pending' => ['label' => 'Chờ duyệt', 'class' => 'bg-warning text-dark'],
                                                'approved' => ['label' => 'Đã duyệt', 'class' => 'bg-success'],
                                                'rejected' => ['label' => 'Từ chối', 'class' => 'bg-danger'],
                                            ];
                                            $status = $statusLabels[$event->status] ?? ['label' => $event->status, 'class' => 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                    </td>

                                    <td class="small">{{ $event->createdBy->name ?? '—' }}</td>

                                    <td>
                                        @if($event->club)
                                            <span class="badge bg-info text-dark">{{ Str::limit($event->club->name, 15) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <!-- NÚT HÀNH ĐỘNG CHUẨN – ĐỒNG BỘ VỚI USERS -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($event->status === 'pending')
                                                <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.events.reject', $event) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Từ chối">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Xóa vĩnh viễn?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có sự kiện nào</h5>
                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Thêm sự kiện đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection