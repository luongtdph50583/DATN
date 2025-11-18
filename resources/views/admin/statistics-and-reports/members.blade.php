{{-- resources/views/admin/stats/members.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Thống kê Thành viên')

@section('card-body')
<div class="container-fluid">
    <!-- Tiêu đề -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Thành viên</h1>
        <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>

    <!-- Card thống kê -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Thành viên hoạt động
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCount }}</div>
                    </div>
                    <i class="fas fa-user-check fa-2x text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Thành viên không hoạt động
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactiveCount }}</div>
                    </div>
                    <i class="fas fa-user-times fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <form id="filterForm" class="row mb-4">
        <div class="col-md-3">
            <label for="statusFilter">Trạng thái</label>
            <select id="statusFilter" name="status" class="form-control">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="clubsFilter">Câu lạc bộ</label>
            <select id="clubsFilter" name="clubs[]" class="form-control" multiple>
                @foreach ($allClubs as $club)
                    <option value="{{ $club->id }}"
                        {{ in_array($club->id, $selectedClubs ?? []) ? 'selected' : '' }}>
                        {{ $club->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- Bảng danh sách -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4" id="membersTableContainer">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách Thành viên</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên người dùng</th>
                                    <th>Giới tính</th>
                                    <th>Khóa học</th>
                                    <th>Chuyên ngành</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tham gia</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $index => $member)
                                    <tr>
                                        <td>{{ $index + 1 + ($members->currentPage() - 1) * $members->perPage() }}</td>
                                        <td>{{ $member->user->name ?? 'Không rõ' }}</td>
                                        <td>{{ $member->gender == 'male' ? 'Nam' : ($member->gender == 'female' ? 'Nữ' : 'Khác') }}</td>
                                        <td>{{ $member->course ?? '-' }}</td>
                                        <td>{{ $member->major ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $member->status == 'active' ? 'success' : 'secondary' }}">
                                                {{ $member->status == 'active' ? 'Hoạt động' : 'Ngưng hoạt động' }}
                                            </span>
                                        </td>
                                        <td>{{ $member->created_at ? $member->created_at->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Xem chi tiết</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Không có thành viên nào phù hợp</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="d-flex justify-content-center mt-3" id="paginationLinks">
                        {{ $members->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Biểu đồ số lượng Thành viên mới theo tháng</h5>
        </div>
        <div class="card-body">
            <canvas id="membersChart" height="90"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    // init Select2
    $('#clubsFilter').select2({
        placeholder: "-- Chọn câu lạc bộ --",
        allowClear: true,
        width: '100%'
    });

    // Hàm fetch AJAX table + pagination
    function fetchMembers(url = null) {
        let actionUrl = url || "{{ route('admin.stats.members') }}";
        $.ajax({
            url: actionUrl,
            data: $('#filterForm').serialize(),
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                let newTable = $(res).find('#membersTableContainer').html();
                $('#membersTableContainer').html(newTable);
                attachPaginationLinks(); // attach lại event cho pagination
            },
            error: function(err) { console.error(err); }
        });
    }

    // Event filter
    $('#statusFilter, #clubsFilter').on('change', function() {
        fetchMembers();
    });

    // Xử lý pagination AJAX
    function attachPaginationLinks() {
        $('#membersTableContainer').find('.pagination a').on('click', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            fetchMembers(url);
        });
    }

    attachPaginationLinks(); // lần đầu attach

});
</script>

<script>
    // Chart.js
    const ctxMembers = document.getElementById('membersChart').getContext('2d');
    const membersChart = new Chart(ctxMembers, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Số lượng thành viên mới',
                data: @json($membersPerMonth),
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { precision:0 } }
            }
        }
    });
</script>
@endpush
