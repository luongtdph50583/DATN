@extends('admin.layouts.app')

@section('title', 'Báo cáo CLB - ' . $club->name)

@section('card-body')
<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Báo cáo Câu lạc bộ: {{ $club->name }}</h1>
        <a href="{{ route('admin.stats.clubs') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê CLB
        </a>
    </div>

    <!-- Thông tin cơ bản -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
        </div>
        <div class="card-body row">
            <!-- Logo -->
            <div class="col-md-4 text-center mb-3">
                @if ($club->logo)
                    <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo CLB" class="img-fluid rounded" style="max-height: 180px;">
                @else
                    <div class="border rounded py-5 bg-light text-muted">Chưa có logo</div>
                @endif
            </div>

            <!-- Thông tin -->
            <div class="col-md-8">
                <p><strong>Tên CLB:</strong> {{ $club->name }}</p>
                <p><strong>Lĩnh vực:</strong> {{ $club->field ?? 'Không rõ' }}</p>
                <p><strong>Ngày thành lập:</strong> {{ $club->created_at ? $club->created_at->format('d/m/Y') : '—' }}</p>
               <p><strong>Người thành lập:</strong> {{ $club->manager->name ?? 'Không rõ' }}</p>
                <p><strong>Mô tả:</strong> {{ $club->description ?? 'Chưa có mô tả' }}</p>
                <p>
                    <strong>Trạng thái:</strong>
                    @if ($club->status === 'active')
                        <span class="badge bg-success px-3 py-2">Đang hoạt động</span>
                    @else
                        <span class="badge bg-secondary px-3 py-2">Ngừng hoạt động</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row">
        <!-- Thống kê Thành viên -->
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <h6 class="text-primary font-weight-bold mb-3">Thống kê Thành viên</h6>
                    <p>Tổng số: <strong>{{ $totalMembers }}</strong></p>
                    <p>Hoạt động: <strong>{{ $activeMembers }}</strong></p>
                    <p>Không hoạt động: <strong>{{ $inactiveMembers }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Thống kê Sự kiện -->
        <div class="col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <h6 class="text-success font-weight-bold mb-3">Thống kê Sự kiện</h6>
                    <p>Tổng số: <strong>{{ $totalEvents }}</strong></p>
                    <p>Đã duyệt: <strong>{{ $approvedEvents }}</strong></p>
                    <p>Chờ duyệt / Từ chối: <strong>{{ $pendingOrRejected }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Thành viên -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Thành viên CLB</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
    <tr>
        <th>#</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Vai trò</th>
        <th>Trạng thái</th>
        <th>Ngày tham gia</th>
        <th>Hành động</th> <!-- cột mới -->
    </tr>
</thead>
<tbody>
    @forelse ($members as $index => $member)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $member->name }}</td>
            <td>{{ $member->email }}</td>
            <td>{{ ucfirst($member->pivot->role ?? 'thành viên') }}</td>
          <td>
                    @if ($member->status === 'active')
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-secondary">Không hoạt động</span>
                    @endif
                </td>
            <td>{{ $member->pivot->created_at->format('d/m/Y') }}</td>
            <td>
                <a href="#" class="btn btn-primary btn-sm">Xem chi tiết</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center text-muted">Chưa có thành viên nào.</td>
        </tr>
    @endforelse
</tbody>

            </table>
        </div>
    </div>

    <!-- Danh sách Sự kiện -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-success">Danh sách Sự kiện CLB</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
    <tr>
        <th>#</th>
        <th>Tên sự kiện</th>
        <th>Ngày tổ chức</th>
        <th>Địa điểm</th>
        <th>Trạng thái</th>
        <th>Người tạo</th>
        <th>Hành động</th> <!-- cột mới -->
    </tr>
</thead>
<tbody>
    @forelse ($events as $index => $event)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $event->name }}</td>
            <td>{{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y H:i') }}</td>
            <td>{{ $event->location }}</td>
            <td>
                @if ($event->status === 'approved')
                    <span class="badge bg-success">Đã duyệt</span>
                @elseif ($event->status === 'pending')
                    <span class="badge bg-warning">Chờ duyệt</span>
                @else
                    <span class="badge bg-danger">Từ chối</span>
                @endif
            </td>
           <td>{{ $event->createdBy->name ?? 'Không rõ' }}</td>

            <td>
                <a href="#" class="btn btn-primary btn-sm">Xem chi tiết</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center text-muted">Chưa có sự kiện nào.</td>
        </tr>
    @endforelse
</tbody>

            </table>
        </div>
    </div>

    <!-- Xuất báo cáo PDF -->
    <div class="text-center mb-5">
        <a href="{{ route('admin.clubs.report.pdf', $club->id) }}" class="btn btn-danger px-4 py-2">
            <i class="fas fa-file-pdf"></i> Xuất báo cáo PDF
        </a>
    </div>
</div>

{{-- CSS nhẹ --}}
<style>
.badge {
    font-size: 0.9rem;
    border-radius: 12px;
}
.card-header {
    background-color: #f8f9fc;
}
p strong {
    color: #2c3e50;
}
</style>
@endsection
