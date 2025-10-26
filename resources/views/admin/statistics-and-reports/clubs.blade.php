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

<form method="GET" action="{{ route('admin.stats.index') }}">
    <div class="row mb-4 align-items-end g-3">
        <!-- Sắp xếp -->
        <div class="col-md-3">
            <label for="sortClubs" class="form-label fw-bold text-primary">Sắp xếp theo:</label>
            <select name="sort" id="sortClubs" class="form-select">
                <option value="top_members" {{ request('sort')=='top_members'?'selected':'' }}>CLB đông thành viên nhất</option>
                <option value="least_members" {{ request('sort')=='least_members'?'selected':'' }}>CLB ít thành viên nhất</option>
                <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>CLB thành lập sớm nhất</option>
                <option value="most_events" {{ request('sort')=='most_events'?'selected':'' }}>CLB nhiều sự kiện nhất</option>
            </select>
        </div>

        <!-- Trạng thái -->
        <div class="col-md-3">
            <label for="filterStatus" class="form-label fw-bold text-primary">Lọc theo trạng thái:</label>
            <select name="status" id="filterStatus" class="form-select">
                <option value="">Tất cả</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Đang hoạt động</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Chờ duyệt</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Ngưng hoạt động</option>
            </select>
        </div>

        <!-- Tìm kiếm -->
        <div class="col-md-4">
            <label for="searchClub" class="form-label fw-bold text-primary">Tìm kiếm theo tên CLB:</label>
            <div class="input-group">
                <input type="text" name="search" id="searchClub" value="{{ request('search') }}" class="form-control" placeholder="Nhập tên câu lạc bộ...">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Áp dụng
                </button>
            </div>
        </div>
    </div>
</form>





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
                                <th>Lĩnh vực</th>
                                <th>Trạng thái</th>
                                <th>Số thành viên</th>
                                <th>Số sự kiện</th> <!-- ✅ cột mới -->
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($clubs as $club)
                            <tr>
                                <td>{{ $club->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($club->logo)
                                            <img src="{{ asset('storage/' . $club->logo) }}" 
                                                 alt="{{ $club->name }}" width="40" height="40"
                                                 class="rounded-circle me-2" style="object-fit:cover;">
                                        @endif
                                        <span>{{ $club->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $club->field ?? '—' }}</td>
                                <td>
                                    @if($club->status === 'active')
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @elseif($club->status === 'pending')
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @else
                                        <span class="badge bg-secondary">Ngưng hoạt động</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $club->members_count ?? 0 }}
                                    /
                                    {{ $club->member_limit ?? 'Không giới hạn' }}
                                </td>

                                <td>
                                    {{ $club->events_count ?? 0 }}
                                </td>

                                <td>{{ $club->created_at ? $club->created_at->format('Y-m-d') : '—' }}</td>
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
                    {{ $clubs->appends(request()->only(['sort','start_date','end_date']))->links() }}
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

<script>
document.getElementById('applyFilters').addEventListener('click', function () {
    const sort = document.getElementById('sortClubs').value;
    const status = document.getElementById('filterStatus').value;
    const search = document.getElementById('searchClub').value;

    const params = new URLSearchParams({
        sort: sort,
        status: status,
        search: search
    });

    // Reload trang với query string
    window.location.href = `?${params.toString()}`;
});
</script>

