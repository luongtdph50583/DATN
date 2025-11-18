<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thống kê Quỹ Sự kiện</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h3>Thống kê Quỹ Sự kiện</h3>
    <p>
        Từ: {{ $startDate ?? 'Tất cả' }} |
        Đến: {{ $endDate ?? 'Tất cả' }} |
        Trạng thái: {{ $status ?? 'Tất cả' }}
    </p>
    <p>
        Tổng yêu cầu: {{ $totalRequests }} <br>
        Tổng tiền yêu cầu: {{ number_format($totalRequestedAmount) }} VNĐ <br>
        Tổng tiền giải ngân: {{ number_format($totalApprovedAmount) }} VNĐ
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Sự kiện</th>
                <th>Người yêu cầu</th>
                <th>Số tiền yêu cầu</th>
                <th>Số tiền giải ngân</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fundRequests as $i => $f)
            <tr>
                <td>{{ $i+1 }}</td>
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
                    {{ $statusVN }}
                </td>
                <td>{{ $f->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
