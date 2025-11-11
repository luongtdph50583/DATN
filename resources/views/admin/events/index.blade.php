@extends('admin.layouts.app')
@section('title', 'Quản lý Sự kiện')

@section('card-body')
<div class="container-fluid py-4">

    <!-- Header + Nút hành động -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Quản lý Sự kiện</h1>
            <p class="text-muted small mb-0">Theo dõi, duyệt và quản lý toàn bộ sự kiện của các CLB</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm sự kiện
            </a>
            <a href="{{ route('admin.events.deleted') }}" class="btn btn-outline-danger">
                Lịch sử xóa
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#topClubsModal">
                TOP CLB THÁNG
            </button>
        </div>
    </div>

    <!-- Thông báo -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.events.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search_name" class="form-control" 
                           placeholder="Tìm theo tên sự kiện..." value="{{ request('search_name') }}">
                </div>
                <div class="col-md-4">
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
                        Tìm kiếm
                    </button>
                    @if (request()->hasAny(['search_name', 'club_id']))
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                            Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="card">
        <div class="card-body p-0">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên sự kiện</th>
                                <th>Thời gian</th>
                                <th>Địa điểm</th>
                                <th>Số người</th>
                                <th>Trạng thái</th>
                                <th>Người tạo</th>
                                <th>CLB</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $event->id }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-decoration-none">
                                            {{ Str::limit($event->name, 40) }}
                                        </a>
                                    </td>
                                    <td class="small">
                                        {{ $event->start_time?->format('d/m H:i') }} - 
                                        {{ $event->end_time?->format('d/m H:i') }}
                                    </td>
                                    <td>{{ Str::limit($event->location, 25) }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $event->registrations->count() ?? 0 }} / {{ $event->max_participants ?? '∞' }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($event->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success">Đã duyệt</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">Từ chối</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">—</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $event->createdBy?->name ?? 'Hệ thống' }}
                                            <br>
                                            {{ $event->created_at->format('d/m H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $event->club?->name ?? '—' }}
                                        </span>
                                    </td>

                                    <!-- HÀNH ĐỘNG – ĐÚNG Y CHANG FILE USER BẠN GỬI -->
                                    <td class="text-center">
                                        <div class="btn-group" role="group">

                                            <!-- Duyệt / Từ chối (chỉ khi pending) -->
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

                                            <!-- Xem -->
                                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Sửa -->
                                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Xóa mềm -->
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $event->id }}" title="Xóa sự kiện">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <p>Chưa có sự kiện nào</p>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL TOP CLB THÁNG -->
    <div class="modal fade" id="topClubsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        TOP 10 CLB NHIỀU SỰ KIỆN NHẤT THÁNG {{ now()->format('m/Y') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    @if($topClubs->count() > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Hạng</th>
                                    <th>CLB</th>
                                    <th>Số sự kiện</th>
                                    <th>Thưởng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topClubs as $index => $club)
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <span class="badge bg-warning">1st</span>
                                        @elseif($index == 1)
                                            <span class="badge bg-secondary">2nd</span>
                                        @elseif($index == 2)
                                            <span class="badge bg-danger">3rd</span>
                                        @else
                                            <span class="badge bg-dark">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ $club->name }}</td>
                                    <td><span class="badge bg-success">{{ $club->events_count }}</span></td>
                                    <td>
                                        @if($index == 0) 5.000.000đ
                                        @elseif($index == 1) 3.000.000đ
                                        @elseif($index == 2) 1.000.000đ
                                        @else Khuyến khích 300.000đ
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5 text-muted">
                            <p>Chưa có dữ liệu tháng này</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xóa mềm -->
    @foreach($events as $event)
    <div class="modal fade" id="deleteModal-{{ $event->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.events.softdelete', $event) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Xóa sự kiện</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa sự kiện:</p>
                        <strong>{{ $event->name }}</strong>
                        <div class="mt-3">
                            <label class="form-label">Lý do xóa <span class="text-danger">*</span></label>
                            <textarea name="delete_reason" class="form-control" rows="3" required placeholder="Nhập lý do..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Áp dụng Select2 cho dropdown CLB
        $('select[name="club_id"]').select2({
            placeholder: '-- Tất cả CLB --',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush

@endsection