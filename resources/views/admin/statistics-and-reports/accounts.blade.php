{{-- resources/views/admin/stats/accounts.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Thống kê Tài khoản')

@section('card-body')
<div class="container-fluid">
<a href="{{ route('admin.stats.accounts.pdf', request()->all()) }}" class="btn btn-danger mb-3">
    <i class="fas fa-file-pdf"></i> Xuất PDF
</a>
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Tài khoản</h1>
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
                            Tài khoản hoạt động
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
                            Tài khoản không hoạt động
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
        <div class="col-md-3">
            <select id="roleFilter" class="form-control">
                <option value="">-- Tất cả vai trò --</option>
                <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="member" {{ $role == 'member' ? 'selected' : '' }}>Member</option>
            </select>
        </div>
    </div>

    <!-- Bảng danh sách tài khoản -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách Tài khoản</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên người dùng</th>
                                    <th>Email</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $index => $account)
                                    <tr>
                                        <td>{{ $index + 1 + ($accounts->currentPage() - 1) * $accounts->perPage() }}</td>
                                        <td>{{ $account->name }}</td>
                                        <td>{{ $account->email }}</td>
                                        <td>{{ ucfirst($account->role) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $account->status == 'active' ? 'success' : 'secondary' }}">
                                                {{ $account->status == 'active' ? 'Hoạt động' : 'Ngưng hoạt động' }}
                                            </span>
                                        </td>
                                        <td>{{ $account->created_at ? $account->created_at->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Không có tài khoản nào phù hợp</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $accounts->appends(['status' => $status, 'role' => $role])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ số lượng tài khoản theo tháng -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Biểu đồ số lượng Tài khoản mới theo tháng</h5>
        </div>

       <form method="GET" action="{{ route('admin.stats.accounts') }}" class="row g-3 mb-4">
    <div class="col-md-3">
        <label for="start_date" class="form-label">Từ ngày</label>
        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
    </div>
    <div class="col-md-3">
        <label for="end_date" class="form-label">Đến ngày</label>
        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
    </div>
    <!-- Thêm ẩn status + role -->
    <input type="hidden" name="status" id="hiddenStatus" value="{{ $status }}">
    <input type="hidden" name="role" id="hiddenRole" value="{{ $role }}">
    <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100">Lọc</button>
        <button type="submit" name="reset" value="true" class="btn btn-secondary w-100">Đặt lại</button>
    </div>
</form>


        <div class="card-body">
            <canvas id="accountsChart" height="90"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function reloadWithFilters() {
    const status = document.getElementById('statusFilter').value;
    const role = document.getElementById('roleFilter').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    const params = new URLSearchParams({
        status: status,
        role: role,
        start_date: startDate,
        end_date: endDate
    });

    window.location.href = '{{ route("admin.stats.accounts") }}?' + params.toString();
}

document.getElementById('statusFilter').addEventListener('change', reloadWithFilters);
document.getElementById('roleFilter').addEventListener('change', reloadWithFilters);
</script>

@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxAccounts = document.getElementById('accountsChart').getContext('2d');
    const accountsChart = new Chart(ctxAccounts, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Số lượng tài khoản mới',
                data: @json($accountsPerMonth),
                fill: false,
                borderColor: 'rgba(100, 100, 100, 1)',
                backgroundColor: 'rgba(100, 100, 100, 0.6)',
                tension: 0.3,
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
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>
@endpush
