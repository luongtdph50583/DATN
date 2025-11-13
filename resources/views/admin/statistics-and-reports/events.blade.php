{{-- resources/views/admin/stats/events.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Thống kê Sự kiện')

@section('card-body')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Sự kiện</h1>
        <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>
    <a href="{{ route('admin.stats.events.pdf', request()->query()) }}" class="btn btn-danger mb-3">
    <i class="fas fa-file-pdf"></i> Xuất PDF
</a>


    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.stats.events') }}" id="filterForm" class="row mb-4">
        <div class="col-md-3 mb-2">
            <label for="sortEvents">Sắp xếp</label>
            <select name="sort" id="sortEvents" class="form-control">
                <option value="newest" {{ $sort=='newest' ? 'selected' : '' }}>Mới nhất (tạo)</option>
                <option value="oldest" {{ $sort=='oldest' ? 'selected' : '' }}>Cũ nhất (tạo)</option>
                <option value="start_asc" {{ $sort=='start_asc' ? 'selected' : '' }}>Thời gian bắt đầu sớm nhất</option>
                <option value="start_desc" {{ $sort=='start_desc' ? 'selected' : '' }}>Thời gian bắt đầu muộn nhất</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <label for="statusFilter">Trạng thái</label>
            <select name="status" id="statusFilter" class="form-control">
                <option value="">-- Tất cả --</option>
                <option value="pending" {{ $status=='pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ $status=='approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="rejected" {{ $status=='rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
    <label for="clubsFilter">Câu lạc bộ</label>
    <select name="club" id="clubsFilter" class="form-control">
        <option value="">-- Tất cả CLB --</option>
        @foreach($allClubs as $club)
           <option value="{{ $club->id }}" {{ $selectedClub == $club->id ? 'selected' : '' }}>
    {{ $club->name }}
</option>

        @endforeach
    </select>
</div>
<div class="col-md-2 d-flex align-items-end gap-2">
    <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
    <a href="{{ route('admin.stats.events') }}" class="btn btn-secondary w-100">Đặt lại</a>
</div>

    </form>

    {{-- Bảng sự kiện --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách Sự kiện</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên sự kiện</th>
                                    <th>CLB tổ chức</th>
                                    <th class="text-nowrap">Thời gian bắt đầu</th>
                                    <th>Địa điểm</th>
                                    <th class="text-right">Ngân sách</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $i => $event)
                                    <tr>
                                        <td>{{ $i + 1 + ($events->currentPage()-1)*$events->perPage() }}</td>
                                        <td>{{ $event->name }}</td>
                                        <td>{{ $event->club->name ?? 'Không rõ' }}</td>
                                        <td class="text-nowrap">{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i d/m/Y') : 'Chưa xác định' }}</td>
                                        <td>{{ $event->location ?? 'Chưa xác định' }}</td>
                                        <td class="text-right">{{ $event->budget ? number_format($event->budget,0,',','.') . ' VNĐ' : 'Chưa có' }}</td>
                                        <td>
                                            @if($event->status=='approved') 
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @elseif($event->status=='pending') 
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @elseif($event->status=='rejected') 
                                                <span class="badge bg-danger">Từ chối</span>
                                            @else
                                                <span class="badge bg-secondary">Không xác định</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Không có sự kiện nào phù hợp.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $events->appends([
                            'sort'=>$sort, 
                            'status'=>$status, 
                            'clubs'=>$selectedClub, 
                            'start_date'=>$startDate,
                            'end_date'=>$endDate
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ số lượng sự kiện --}}
    <div class="card mb-4">
        <div class="card-header"><h5>Biểu đồ số lượng Sự kiện theo tháng</h5></div>
        <form method="GET" action="{{ route('admin.stats.events') }}" class="row g-3 mb-4 p-3">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="status" value="{{ $status }}">
            @foreach($selectedClubs ?? [] as $clubId)
                <input type="hidden" name="clubs[]" value="{{ $clubId }}">
            @endforeach
            <div class="col-md-3">
                <label for="start_date">Từ ngày</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date">Đến ngày</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">Lọc Biểu đồ</button>
                <a href="{{ route('admin.stats.events', ['sort'=>$sort,'status'=>$status]) }}" class="btn btn-secondary w-100">Đặt lại</a>
            </div>
        </form>
        <div class="card-body">
            <canvas id="eventsChart" height="90"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    $('#clubsFilter').select2({
        placeholder: 'Chọn CLB',
        allowClear: true,
    });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxEvents = document.getElementById('eventsChart').getContext('2d');
const eventsChart = new Chart(ctxEvents, {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Số lượng Sự kiện tạo mới',
            data: @json($eventsPerMonth),
            fill: false,
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            tension: 0.3,
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: { responsive:true, scales:{y:{beginAtZero:true}} }
});
</script>
@endpush
