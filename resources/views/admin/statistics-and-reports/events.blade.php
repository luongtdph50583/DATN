
@extends('admin.layouts.app')

@section('title', 'Thống kê Sự kiện')


@section('card-body')
<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Sự kiện</h1>
        <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>

    <!-- Hiển thị thông báo -->
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

    <!-- Bộ lọc và sắp xếp -->
  <form method="GET" action="{{ route('admin.stats.events') }}" id="filterForm" class="row mb-4">
    <div class="col-md-3">
        <select name="sort" id="sortEvents" class="form-control">
            <option value="newest" {{ $sort=='newest' ? 'selected' : '' }}>Sự kiện mới nhất</option>
            <option value="oldest" {{ $sort=='oldest' ? 'selected' : '' }}>Sự kiện cũ nhất</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="status" id="statusFilter" class="form-control">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending" {{ $status=='pending' ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved" {{ $status=='approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="rejected" {{ $status=='rejected' ? 'selected' : '' }}>Từ chối</option>
        </select>
    </div>
</form>

<script>
    document.getElementById('sortEvents').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>


    <!-- Bảng thống kê sự kiện -->
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
                                <th>Ngày tổ chức</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $i => $event)
                                <tr>
                                    <td>{{ $i + 1 + ($events->currentPage() - 1) * $events->perPage() }}</td>
                                    <td>{{ $event->name }}</td>
                                    <td>{{ $event->club->name ?? 'Không rõ' }}</td>
                                    <td>{{ $event->created_at ? $event->created_at->format('Y-m-d') : 'Chưa có' }}</td>
                                    <td>
                                        @if($event->status == 'approved')
                                            <span class="badge bg-success">Đã duyệt</span>
                                        @elseif($event->status == 'pending')
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                        @elseif($event->status == 'rejected')
                                            <span class="badge bg-danger">Từ chối</span>
                                        @else
                                            <span class="badge bg-secondary">Không xác định</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không có sự kiện nào phù hợp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $events->appends(['sort' => $sort, 'status' => $status])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

</div>
{{-- 🧭 Biểu đồ số lượng Sự kiện theo tháng --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Biểu đồ số lượng Sự kiện theo tháng</h5>
    </div>

    {{-- 🧭 Bộ lọc thời gian cho biểu đồ --}}
    <form method="GET" action="{{ route('admin.stats.events') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Từ ngày</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Đến ngày</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
            <button type="submit" name="reset" value="true" class="btn btn-secondary w-100">Đặt lại</button>

        </div>
    </form>

    <div class="card-body">
        <canvas id="eventsChart" height="90"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('sortEvents').addEventListener('change', function() {
    const sort = this.value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = '{{ route("admin.stats.events") }}?sort=' + sort + '&status=' + status;
});

document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const sort = document.getElementById('sortEvents').value;
<<<<<<< HEAD
    window.location.href = '{{ route("admin.stats.events") }}?sort=' + sort + '&status=' + status;
});
</script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxEvents = document.getElementById('eventsChart').getContext('2d');
    const eventsChart = new Chart(ctxEvents, {
        type: 'line',
        data: {
            labels: @json($labels), // Ví dụ: ["Tháng 1", "Tháng 2", ...]
            datasets: [{
                label: 'Số lượng Sự kiện tạo mới',
                data: @json($eventsPerMonth), // mảng dữ liệu số lượng events
                fill: false,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush

