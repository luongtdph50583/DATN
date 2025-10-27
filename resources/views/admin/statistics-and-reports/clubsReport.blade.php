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
        <!-- Logo CLB -->
        <div class="col-md-4 text-center mb-3">
            @if ($club->logo)
                <img src="{{ asset('storage/' . $club->logo) }}" 
                     alt="Logo CLB" 
                     class="img-fluid rounded shadow-sm" 
                     style="max-height: 180px; object-fit: contain;">
            @else
                <div class="border rounded py-5 bg-light text-muted">
                    <i class="fas fa-image fa-2x mb-2"></i><br>
                    Chưa có logo
                </div>
            @endif
        </div>

        <!-- Thông tin chi tiết CLB -->
        <div class="col-md-8">
            <p><strong>Mã CLB (ID):</strong> {{ $club->id }}</p>

            <p><strong>Tên CLB:</strong> {{ $club->name }}</p>

            <p><strong>Lĩnh vực hoạt động:</strong> {{ $club->field ?? 'Không rõ' }}</p>

            <p><strong>Mô tả:</strong> 
                {{ $club->description ? $club->description : 'Chưa có mô tả' }}
            </p>

            <p>
                <strong>Trạng thái:</strong>
                @if ($club->status === 'active')
                    <span class="badge bg-success px-3 py-2">Đang hoạt động</span>
                @elseif ($club->status === 'pending')
                    <span class="badge bg-warning text-dark px-3 py-2">Chờ duyệt</span>
                @else
                    <span class="badge bg-secondary px-3 py-2">Ngừng hoạt động</span>
                @endif
            </p>

            <p><strong>Người quản lý:</strong> 
                {{ $club->manager->name ?? 'Không rõ' }}
            </p>

            <p><strong>Email liên hệ:</strong> 
                {{ $club->email ?? 'Chưa có' }}
            </p>

            <p><strong>Số điện thoại:</strong> 
                {{ $club->phone ?? 'Chưa có' }}
            </p>

            <p><strong>Giới hạn thành viên:</strong> 
                {{ $club->member_limit ?? 'Không giới hạn' }}
            </p>

            <p><strong>Ngày tạo:</strong> 
                {{ $club->created_at ? $club->created_at->format('d/m/Y H:i') : '—' }}
            </p>

            <p><strong>Cập nhật gần nhất:</strong> 
                {{ $club->updated_at ? $club->updated_at->format('d/m/Y H:i') : '—' }}
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
        <table class="table table-bordered table-hover align-middle">
            <thead class="thead-light">
                <tr class="text-center">
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tham gia</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $index => $member)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $member->name }}</td>
            <td>{{ $member->email }}</td>
                        <td class="text-center">
                            @if(isset($member->role))
                                <span class="badge bg-info text-dark">{{ ucfirst($member->role) }}</span>
                            @else
                                <span class="badge bg-secondary">Thành viên</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($member->status === 'active')
                                <span class="badge bg-success px-3 py-2">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">Ngưng hoạt động</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $member->created_at ? $member->created_at->format('d/m/Y') : '—' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-primary btn-sm">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            Chưa có thành viên nào trong CLB này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Phân trang (nếu có) --}}
        @if (method_exists($members, 'links'))
            <div class="d-flex justify-content-center mt-3">
                {{ $members->links() }}
            </div>
        @endif
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
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $index => $event)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $event->name }}</td>
                        <td>
                            @if ($event->start_time)
                                {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted">Chưa xác định</span>
                            @endif
                        </td>
                        <td>{{ $event->location ?? 'Không rõ' }}</td>
                        <td>
                            @if ($event->status === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif ($event->status === 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @else
                                <span class="badge bg-danger">Từ chối</span>
                            @endif
                        </td>
                        <td>{{ $event->createdBy->name ?? 'Không rõ' }}</td>
                        <td>
                            <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-primary btn-sm">
                                Xem chi tiết
                            </a>
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
