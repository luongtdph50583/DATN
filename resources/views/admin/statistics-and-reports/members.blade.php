{{-- resources/views/admin/stats/members.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Thống kê Thành viên')


@section('card-body')
<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Thành viên</h1>
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
    <div class="row mb-4">
       
        <div class="col-md-3">
            <select id="statusFilter" class="form-control">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
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
                            <td>
                                @if($member->gender == 'male') Nam
                                @elseif($member->gender == 'female') Nữ
                                @else Khác @endif
                            </td>
                            <td>{{ $member->course ?? '-' }}</td>
                            <td>{{ $member->major ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $member->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $member->status == 'active' ? 'Hoạt động' : 'Ngưng hoạt động' }}
                                </span>
                            </td>
                            <td>{{ $member->created_at ? $member->created_at->format('d/m/Y') : '-' }}</td>
                            <td><a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                    </td> 
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có thành viên nào phù hợp</td>
                        </tr>
                    @endforelse
                                       
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $members->appends(['sort' => $sort, 'status' => $status])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- 🧭 Biểu đồ số lượng Thành viên theo tháng --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Biểu đồ số lượng Thành viên mới theo tháng</h5>
        </div>

        {{-- 🧭 Bộ lọc thời gian cho biểu đồ --}}
        <form method="GET" action="{{ route('admin.stats.members') }}" class="row g-3 mb-4">
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
            <canvas id="membersChart" height="90"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('sortMembers').addEventListener('change', function() {
    const sort = this.value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = '{{ route("admin.stats.members") }}?sort=' + sort + '&status=' + status;
});

document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const sort = document.getElementById('sortMembers').value;
    window.location.href = '{{ route("admin.stats.members") }}?sort=' + sort + '&status=' + status;
});
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Biểu đồ thống kê thành viên
    const ctxMembers = document.getElementById('membersChart').getContext('2d');
    const membersChart = new Chart(ctxMembers, {
        type: 'line', // ✅ đổi từ 'bar' sang 'line'
        data: {
            labels: @json($labels), // ["Tháng 1/2025", "Tháng 2/2025", ...]
            datasets: [{
                label: 'Số lượng thành viên mới',
                data: @json($membersPerMonth), // mảng số liệu tương ứng
                fill: false, // không tô màu dưới đường
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                tension: 0.3, // đường mượt
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision:0
                    }
                }
            }
        }
    });
</script>
@endpush
