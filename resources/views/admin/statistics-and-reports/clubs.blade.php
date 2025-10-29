@extends('admin.layouts.app')

@section('title', 'Thống kê Câu lạc bộ')

@section('card-body')
<div class="container-fluid mt-4">
<div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Sự kiện</h1>
        <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>
    {{-- 🧭 Bộ lọc thống kê --}}
    <form id="filterForm" method="GET" action="{{ route('admin.stats.clubs') }}">
        <div class="row mb-4 align-items-end g-3">

            <!-- Sắp xếp -->
            <div class="col-md-3">
                <label for="sortClubs" class="form-label fw-bold text-primary">Sắp xếp theo:</label>
                <select name="sort" id="sortClubs" class="form-select">
                    <option value="">Tất cả</option>
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
                <input type="text" name="search" id="searchClub" value="{{ request('search') }}" class="form-control" placeholder="Nhập tên câu lạc bộ...">
            </div>
        </div>
    </form>

    <!-- Bảng dữ liệu CLB -->
    <div id="clubTableContainer">
        @include('admin.statistics-and-reports.partials.club_table', ['clubs' => $clubs])
    </div>
</div>

{{-- 🧭 Bộ lọc thời gian + Biểu đồ --}}
<div class="card mb-4 mt-4">
    <div class="card-header">
        <h5 class="mb-0">Biểu đồ số lượng CLB theo tháng</h5>
    </div>

    <form method="GET" action="{{ route('admin.stats.clubs') }}" class="row g-3 mb-4 px-3 mt-3">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Từ ngày</label>
            <input type="date" name="start_date" id="start_date" class="form-control"
                   value="{{ $startDateView }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Đến ngày</label>
            <input type="date" name="end_date" id="end_date" class="form-control"
                   value="{{ $endDateView }}">
        </div>

        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
            <button type="submit" name="reset" value="true" class="btn btn-secondary w-100">Đặt lại</button>
        </div>
    </form>

    <div class="card-body">
        <canvas id="clubsChart" height="500"></canvas>
    </div>
</div>
@endsection


@push('scripts')
{{-- ✅ Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- ✅ Xử lý AJAX realtime --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const selects = form.querySelectorAll('select');
    const search = document.getElementById('searchClub');
    let timer = null;

    // Khi thay đổi dropdown (sort, status)
    selects.forEach(select => {
        select.addEventListener('change', fetchData);
    });

    // Khi nhập tìm kiếm (realtime debounce)
    search.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(fetchData, 400);
    });

    function fetchData() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        fetch("{{ route('admin.stats.clubs') }}?" + params, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Lỗi tải dữ liệu');
            return response.text();
        })
        .then(html => {
            document.getElementById('clubTableContainer').innerHTML = html;
        })
        .catch(console.error);
    }
});
</script>

{{-- ✅ Biểu đồ Chart.js --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartLabels = @json($labels ?? []);
    const clubsData = @json($clubsPerMonth ?? []);
    const membersData = @json($membersPerMonth ?? []);
    const eventsData = @json($eventsPerMonth ?? []);

    const ctx = document.getElementById('clubsChart');
    if (!ctx || chartLabels.length === 0) {
        console.warn('Không có dữ liệu biểu đồ.');
        return;
    }

    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Số CLB',
                    data: clubsData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Số thành viên',
                    data: membersData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Số sự kiện',
                    data: eventsData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top' } }
        }
    });
});
</script>
@endpush
