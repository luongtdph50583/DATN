{{-- resources/views/admin/stats/members.blade.php --}}
@include('admin.layouts.header')

<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Thành viên </h1>
        <a href="{{ route('admin.admin.stats') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>

    <!-- Card thống kê -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Thành viên hoạt động</div>
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
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Thành viên không hoạt động</div>
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
            <select id="sortMembers" class="form-control">
                <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Thành viên mới nhất</option>
                <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Thành viên cũ nhất</option>
            </select>
        </div>
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
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Địa chỉ</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tham gia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $i => $member)
                                    <tr>
                                        <td>{{ $i + 1 + ($members->currentPage() - 1) * $members->perPage() }}</td>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->email }}</td>
                                        <td>{{ $member->phone ?? '—' }}</td>
                                        <td>{{ $member->address ?? '—' }}</td>
                                        <td>
                                            @if($member->status == 'active')
                                                <span class="badge badge-success">Hoạt động</span>
                                            @elseif($member->status == 'inactive')
                                                <span class="badge badge-secondary">Không hoạt động</span>
                                            @else
                                                <span class="badge badge-light">Không rõ</span>
                                            @endif
                                        </td>
                                        <td>{{ $member->created_at ? $member->created_at->format('Y-m-d') : '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Không có thành viên nào phù hợp.</td>
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
</div>

@include('admin.layouts.footer')

<script>
document.getElementById('sortMembers').addEventListener('change', function() {
    const sort = this.value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = '{{ route("admin.admin.stats.members") }}?sort=' + sort + '&status=' + status;
});

document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const sort = document.getElementById('sortMembers').value;
    window.location.href = '{{ route("admin.admin.stats.members") }}?sort=' + sort + '&status=' + status;
});
</script>
