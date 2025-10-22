@extends('admin.layouts.app')

@section('title', 'Thống kê CLB')

@section('card-body')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê CLB</h1>
        <!-- ✅ Nút quay lại -->
        <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>

    <!-- Dropdown chọn phương thức sắp xếp -->
    <div class="row mb-4">
        <div class="col-md-4">
            <select id="sortClubs" class="form-control">
                <option value="top_members" {{ $sort=='top_members'?'selected':'' }}>Top CLB đông thành viên nhất</option>
                <option value="least_members" {{ $sort=='least_members'?'selected':'' }}>Top CLB ít thành viên nhất</option>
                <option value="oldest" {{ $sort=='oldest'?'selected':'' }}>Top CLB thành lập sớm nhất</option>
                <option value="most_events" {{ $sort=='most_events'?'selected':'' }}>Top CLB nhiều sự kiện nhất</option>
            </select>
        </div>
    </div>

    <!-- Bảng thống kê CLB -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách CLB</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên CLB</th>
                                    <th>Thành viên</th>
                                    <th>Ngày thành lập</th>
                                    <th>Số sự kiện</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clubs as $club)
                                    <tr>
                                        <td>{{ $club->id }}</td>
                                        <td>{{ $club->name }}</td>
                                        <td>{{ $club->members_count }}</td>
                                        <td>{{ $club->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $club->events_count }}</td>
                                        <td>
                                            <a href="{{ route('admin.clubs.report.show', $club->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                Xem báo cáo
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $clubs->appends(['sort' => $sort])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- 🧭 Biểu đồ số lượng CLB theo tháng --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Biểu đồ số lượng CLB theo tháng</h5>
    </div>

    {{-- 🧭 Bộ lọc thời gian --}}
<form method="GET" action="{{ route('admin.stats.clubs') }}" class="row g-3 mb-4">
    <div class="col-md-3">
        <label for="start_date" class="form-label">Từ ngày</label>
        <input type="date" name="start_date" id="start_date" class="form-control"
               value="{{ $startDate }}">
    </div>
    <div class="col-md-3">
        <label for="end_date" class="form-label">Đến ngày</label>
        <input type="date" name="end_date" id="end_date" class="form-control"
               value="{{ $endDate }}">
    </div>
 
    <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100">Lọc</button>
        <button type="submit" name="reset" value="true" class="btn btn-secondary w-100">Đặt lại</button>
    </div>
</form>
    <div class="card-body">
        <canvas id="clubsChart" height="90"></canvas>
    </div>
</div>

{{-- === CSS phụ trợ (nếu cần) === --}}
<style>
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}
.table-bordered th, .table-bordered td {
    vertical-align: middle;
}
.btn-sm {
    padding: 4px 8px;
    font-size: 13px;
}
</style>

{{-- === Script xử lý sắp xếp === --}}
<script>
document.getElementById('sortClubs').addEventListener('change', function() {
    const sort = this.value;
    window.location.href = '{{ route("admin.stats.clubs") }}?sort=' + sort;
});
</script>


@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('clubsChart').getContext('2d');
    const clubsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Số lượng CLB tạo mới',
                data: @json($clubsPerMonth),
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
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