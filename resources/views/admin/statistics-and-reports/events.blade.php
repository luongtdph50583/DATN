@include('admin.layouts.header')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Sự kiện</h1>
        <!-- ✅ Nút quay lại -->
        <a href="{{ route('admin.admin.stats') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>

    <!-- Bộ lọc và sắp xếp -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select id="sortEvents" class="form-control">
                <option value="newest" {{ $sort=='newest' ? 'selected' : '' }}>Sự kiện mới nhất</option>
                <option value="oldest" {{ $sort=='oldest' ? 'selected' : '' }}>Sự kiện cũ nhất</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="statusFilter" class="form-control">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" {{ $status=='pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ $status=='approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="rejected" {{ $status=='rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
        </div>
    </div>

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
        <span class="badge badge-success">Đã duyệt</span>
    @elseif($event->status == 'pending')
        <span class="badge badge-warning">Chờ duyệt</span>
    @elseif($event->status == 'rejected')
        <span class="badge badge-danger">Từ chối</span>
    @else
        <span class="badge badge-secondary">Không xác định</span>
    @endif
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

@include('admin.layouts.footer')

<script>
document.getElementById('sortEvents').addEventListener('change', function() {
    const sort = this.value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = '{{ route("admin.admin.stats.events") }}?sort=' + sort + '&status=' + status;
});

document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const sort = document.getElementById('sortEvents').value;
    window.location.href = '{{ route("admin.admin.stats.events") }}?sort=' + sort + '&status=' + status;
});
</script>
