
@include('admin.layouts.header')

<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Báo cáo Câu lạc bộ: {{ $club->name }}</h1>
        <a href="{{ route('admin.admin.stats.clubs') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê CLB
        </a>
    </div>

    <!-- Thông tin cơ bản -->
   <div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
    </div>
    <div class="card-body">
        <p><strong>Tên CLB:</strong> {{ $club->name }}</p>
        <p><strong>Lĩnh vực:</strong> {{ $club->field ?? 'Không rõ' }}</p>
        <p><strong>Ngày thành lập:</strong> {{ $club->created_at ? $club->created_at->format('d/m/Y') : '—' }}</p>
        <p><strong>Mô tả:</strong> {{ $club->description ?? 'Chưa có mô tả' }}</p>

        {{-- Thêm dòng này --}}
        <p>
            <strong>Trạng thái:</strong>
            @if ($club->status === 'active')
                <span >Đang hoạt động</span>
            @else
                <span >Ngừng hoạt động</span>
            @endif
        </p>
    </div>
</div>


    <!-- Khu vực thống kê -->
    <div class="row">
       <!-- Thống kê Thành viên -->
<div class="col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
            <h6 class="text-primary font-weight-bold">Thống kê Thành viên</h6>
            <p>Tổng số: <strong>{{ $totalMembers }}</strong></p>
            <p>Hoạt động: <strong>-</strong></p>
            <p>Không hoạt động: <strong>-</strong></p>
        </div>
    </div>
</div>

<!-- Thống kê Sự kiện -->
<div class="col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <h6 class="text-success font-weight-bold">Thống kê Sự kiện</h6>
            <p>Tổng số: <strong>{{ $totalEvents }}</strong></p>
            <p>Đã duyệt: <strong>{{ $approvedEvents }}</strong></p>
            <p>Chờ duyệt / Từ chối: <strong>{{ $pendingOrRejected }}</strong></p>
        </div>
    </div>
</div>
    </div>

    <!-- Xuất báo cáo PDF -->
   <div class="text-center mb-5">
    <a href="{{ route('admin.clubs.report.pdf', $club->id) }}" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Xuất báo cáo PDF
    </a>
</div>
</div>

@include('admin.layouts.footer')
