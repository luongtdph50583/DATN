@extends('admin.layouts.app')

@section('card-title', 'Quản lý Quỹ')
@section('card-header', 'Danh sách giao dịch quỹ')
@section('card-body')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Bộ lọc --}}
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Bộ lọc</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}">
            <div class="row g-3 align-items-end">
                {{-- CLB --}}
                <div class="col-md-3">
                    <label for="club_id" class="form-label">Câu lạc bộ</label>
                    <select name="club_id" id="club_id" class="form-select form-select-sm select2">
                        <option value="">Tất cả CLB</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="pending_disbursement" {{ request('status') == 'pending_disbursement' ? 'selected' : '' }}>Chờ giải ngân</option>
                        <option value="disbursing" {{ request('status') == 'disbursing' ? 'selected' : '' }}>Đang giải ngân</option>
                        <option value="disbursed" {{ request('status') == 'disbursed' ? 'selected' : '' }}>Đã giải ngân</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>

                {{-- Từ ngày --}}
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Từ ngày</label>
                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                {{-- Đến ngày --}}
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Đến ngày</label>
                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>

                {{-- Nút lọc & reset --}}
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tính toán dữ liệu tổng chi / chờ giải ngân --}}
@php
$totalExpense = $transactions->whereIn('status', ['disbursing','disbursed'])->sum('approved_amount');
$pendingCount = $transactions->where('status', 'pending_disbursement')->count();
@endphp

{{-- Tóm tắt quỹ --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-danger h-100">
            <div class="card-body">
                <h6 class="text-danger">Tổng đã giải ngân </h6>
                <h5>{{ number_format($totalExpense, 0, ',', '.') }} VNĐ</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-warning h-100">
            <div class="card-body">
                <h6 class="text-warning">Chờ giải ngân</h6>
                <h5>{{ $pendingCount }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Bảng giao dịch --}}
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Danh sách giao dịch quỹ</h6>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Sự kiện / CLB</th>
                    <th>Số tiền yêu cầu</th>
                    <th>Số tiền duyệt</th>
                    <th>Mô tả / Ghi chú</th>
                    <th>Trạng thái</th>
                    <th>Người yêu cầu</th>
                    <th>Người duyệt</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $req)
                <tr>
                    <td>{{ $req->id }}</td>
                    <td>{{ $req->event->name ?? '-' }}<br><small class="text-muted">{{ $req->event->club->name ?? '-' }}</small></td>
                    <td class="text-end">{{ number_format($req->amount_requested) }}đ</td>
                    <td class="text-end">{{ number_format($req->approved_amount ?? 0) }}đ</td>
                    <td>{{ Str::limit($req->note, 50) }}</td>
                    <td>
                        @if($req->status === 'pending_disbursement')<span class="badge bg-warning text-dark">Chờ giải ngân</span>
                        @elseif($req->status === 'disbursing')<span class="badge bg-info text-white">Đang giải ngân</span>
                        @elseif($req->status === 'disbursed')<span class="badge bg-success text-white">Đã giải ngân</span>
                        @elseif($req->status === 'rejected')<span class="badge bg-danger text-white">Từ chối</span>
                        @endif
                    </td>
                    <td>{{ $req->requestedBy->name ?? '-' }}</td>
                    <td>{{ $req->approvedBy->name ?? '-' }}</td>
                    <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.event_fund_requests.show', $req) }}" class="btn btn-info btn-sm">Xem</a>
                        @if(Auth::user()->role === 'admin')
                            @if($req->status === 'pending_disbursement')
                                <a href="{{ route('admin.event_fund_requests.approve', $req->id) }}" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                <a href="{{ route('admin.event_fund_requests.reject', $req->id) }}" class="btn btn-danger btn-sm">Từ chối</a>
                            @elseif($req->status === 'disbursing')
                                <a href="{{ route('admin.event_fund_requests.update_disbursement', $req->id) }}" class="btn btn-info btn-sm"><i class="fas fa-file-upload"></i></a>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Phân trang với filter --}}
        <div class="d-flex justify-content-center">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Include Select2 JS & CSS nếu chưa include ở layout -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: "Chọn CLB",
            allowClear: true
        });
    });
</script>
@endsection
