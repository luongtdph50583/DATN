@extends('admin.layouts.app')

@section('title', 'Chi tiết quyết toán')
@section('card-title', 'Chi tiết quyết toán')

@section('card-body')
<div class="container py-4">

    <h3 class="text-center mb-4">
        <i class="fas fa-file-invoice-dollar me-2"></i>
        {{ $settlement->fundRequest->event->name ?? '—' }}
    </h3>

    {{-- Thông tin tổng quan --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Yêu cầu liên quan</h6>
                    <h5 class="text-success fw-bold">
                        {{ number_format($settlement->fundRequest->amount_requested ?? 0, 0, ',', '.') }}₫
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="text-muted">Tổng chi</h6>
                    <h5 class="text-danger fw-bold">
                        {{ number_format($settlement->total_spent ?? 0, 0, ',', '.') }}₫
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Chênh lệch</h6>
                    <h5 class="{{ ($settlement->difference ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                        {{ ($settlement->difference ?? 0) >= 0 ? '+' : '' }}{{ number_format($settlement->difference ?? 0, 0, ',', '.') }}₫
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Chi tiết chi phí --}}
    @php
        $details = is_string($settlement->details) ? json_decode($settlement->details, true) : $settlement->details;
        $details = is_array($details) ? $details : [];
    @endphp

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-list me-2"></i>Chi tiết chi phí
        </div>
        <div class="card-body">
            @if(count($details) > 0)
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên khoản chi</th>
                            <th class="text-end">Số tiền (₫)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $item)
                            <tr>
                                <td>{{ $item['name'] ?? '—' }}</td>
                                <td class="text-end">{{ number_format($item['amount'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-muted">Không có chi tiết chi phí.</p>
            @endif
        </div>
    </div>

    {{-- Hóa đơn / chứng từ --}}
    @php
        $receipts = is_array($settlement->receipts)
            ? $settlement->receipts
            : (json_decode($settlement->receipts, true) ?? []);
    @endphp

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-receipt me-2"></i>Hóa đơn / Chứng từ
        </div>
        <div class="card-body">
            @if(count($receipts) > 0)
                <div class="row">
                    @foreach($receipts as $file)
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                <img src="{{ asset('storage/' . $file) }}" 
                                     alt="Hóa đơn"
                                     class="img-fluid rounded border"
                                     onerror="this.src='https://via.placeholder.com/150x100?text=No+Image';">
                            </a>
                            <div class="small text-muted mt-1">{{ basename($file) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-muted">Không có chứng từ.</p>
            @endif
        </div>
    </div>

    {{-- Trạng thái duyệt --}}
    @php
        $statusMap = [
            'pending_review' => ['label' => 'Chờ duyệt', 'class' => 'warning'],
            'approved' => ['label' => 'Đã duyệt', 'class' => 'success'],
            'needs_revision' => ['label' => 'Cần chỉnh sửa', 'class' => 'danger'],
        ];
        $status = $statusMap[$settlement->status] ?? ['label' => 'Không xác định', 'class' => 'secondary'];
    @endphp

    <div class="card mb-4">
        <div class="card-header bg-light">
            <i class="fas fa-check-circle me-2"></i>Trạng thái duyệt
        </div>
        <div class="card-body text-center">
            <span class="badge bg-{{ $status['class'] }} p-2 mb-2">
                {{ $status['label'] }}
            </span>
            <div class="mt-2">
                <strong>Người duyệt:</strong> {{ $settlement->reviewer->name ?? 'Chưa có' }}<br>
                <strong>Ngày duyệt:</strong> 
                {{ $settlement->reviewed_at ? $settlement->reviewed_at->format('d/m/Y H:i') : '—' }}
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

</div>
@endsection
