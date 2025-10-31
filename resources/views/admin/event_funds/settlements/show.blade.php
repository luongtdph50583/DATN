@extends('admin.layouts.app')

@section('title', 'Chi tiết quyết toán')
@section('card-title', 'Chi tiết quyết toán')

@section('card-body')
<div class="container">
    <h4>{{ $settlement->fundRequest->event->name ?? '—' }}</h4>

    <table class="table table-bordered">
        <tr>
            <th>Yêu cầu liên quan</th>
            <td>{{ $settlement->fundRequest->event->name ?? '—' }} - {{ number_format($settlement->fundRequest->amount_requested, 0, ',', '.') }}₫</td>
        </tr>
        <tr>
            <th>Tổng chi</th>
            <td>{{ number_format($settlement->total_spent, 0, ',', '.') }}₫</td>
        </tr>
        <tr>
            <th>Chênh lệch</th>
            <td>{{ number_format($settlement->difference, 0, ',', '.') }}₫</td>
        </tr>
      @php
    // Nếu đã là array thì dùng trực tiếp, nếu là string thì decode, nếu null thì default []
    $details = $settlement->details;

    if (is_string($details)) {
        $details = json_decode($details, true) ?? [];
    } elseif (!is_array($details)) {
        $details = [];
    }
@endphp

<ul>
@forelse($details as $item)
    <li>{{ $item['name'] ?? '-' }}: {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}₫</li>
@empty
    <li>-</li>
@endforelse
</ul>

        <tr>
            <th>Hóa đơn / chứng từ</th>
            <td>
@php
    $receipts = is_array($settlement->receipts)
                ? $settlement->receipts
                : json_decode($settlement->receipts, true) ?? [];
@endphp

@foreach($receipts as $file)
    <a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a><br>
@endforeach

            </td>
        </tr>
        <tr>
            <th>Trạng thái</th>
            <td>
                @php
                    $statusMap = [
                        'pending_review' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'needs_revision' => 'Cần chỉnh sửa',
                    ];
                @endphp
                <span class="badge bg-{{ $settlement->status == 'approved' ? 'success' : ($settlement->status == 'pending_review' ? 'warning' : 'danger') }}">
                    {{ $statusMap[$settlement->status] ?? $settlement->status }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Người duyệt</th>
            <td>{{ $settlement->reviewer->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Ngày duyệt</th>
            <td>{{ $settlement->reviewed_at ? $settlement->reviewed_at->format('d/m/Y') : '-' }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
