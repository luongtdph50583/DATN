@extends('admin.layouts.app')

@section('title', 'Thống kê Quỹ Sự kiện')

@section('card-body')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê Quỹ Sự kiện</h1>
        <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary btn-sm">← Quay lại trang thống kê</a>
    </div>

    <!-- Tổng quan -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng yêu cầu</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequests }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tổng tiền yêu cầu</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRequestedAmount) }} VNĐ</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng tiền giải ngân</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalApprovedAmount) }} VNĐ</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <form method="GET" action="{{ route('admin.stats.funds') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Từ ngày</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Đến ngày</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select name="status" id="status" class="form-control">
                <option value="">-- Tất cả --</option>
                <option value="pending_disbursement" {{ $status=='pending_disbursement'?'selected':'' }}>Chờ giải ngân</option>
                <option value="disbursing" {{ $status=='disbursing'?'selected':'' }}>Đang giải ngân</option>
                <option value="disbursed" {{ $status=='disbursed'?'selected':'' }}>Đã giải ngân</option>
                <option value="rejected" {{ $status=='rejected'?'selected':'' }}>Bị từ chối</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
            <button type="submit" name="reset" value="true" class="btn btn-secondary w-100">Đặt lại</button>
        </div>
    </form>

    <!-- Danh sách chi tiết -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách yêu cầu quỹ</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Sự kiện</th>
            <th>Người yêu cầu</th>
            <th>Số tiền yêu cầu</th>
            <th>Số tiền giải ngân</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($fundRequests as $i => $f)
            <tr>
                <td>{{ $i + 1 + ($fundRequests->currentPage()-1)*$fundRequests->perPage() }}</td>
                <td>{{ $f->event->name ?? '-' }}</td>
                <td>{{ $f->requestedBy->name ?? '-' }}</td>
                <td>{{ number_format($f->amount_requested) }} VNĐ</td>
                <td>{{ number_format($f->approved_amount ?? 0) }} VNĐ</td>
                <td>
                    @php
                        $statusVN = match($f->status) {
                            'pending_disbursement' => 'Chờ giải ngân',
                            'disbursing' => 'Đang giải ngân',
                            'disbursed' => 'Đã giải ngân',
                            'rejected' => 'Đã từ chối',
                            default => $f->status
                        };
                    @endphp
                    <span class="badge 
                        @if($f->status == 'pending_disbursement') bg-warning
                        @elseif($f->status == 'disbursing') bg-info
                        @elseif($f->status == 'disbursed') bg-success
                        @elseif($f->status == 'rejected') bg-danger
                        @else bg-secondary @endif">
                        {{ $statusVN }}
                    </span>
                </td>
                <td>{{ $f->created_at->format('d/m/Y') }}</td>
                <td class="text-center">
                 <a href="{{ route('admin.event_fund_requests.show', $f->id) }}" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i>
</a>

                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted">Không có yêu cầu nào</td></tr>
        @endforelse
    </tbody>
</table>


            <div class="d-flex justify-content-center mt-3">
                {{ $fundRequests->appends(request()->all())->links() }}
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Biểu đồ số lượng yêu cầu quỹ theo tháng</h5>
        </div>
        <div class="card-body">
            <canvas id="fundChart" height="90"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('fundChart').getContext('2d');
const fundChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Số lượng yêu cầu quỹ',
            data: @json($requestsPerMonth),
            fill: false,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.3,
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision:0 } } } }
});
</script>
@endpush
