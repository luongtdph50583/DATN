<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo thống kê Câu lạc bộ</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Thống kê Câu lạc bộ</h2>

    <div class="summary">
        <p>Khoảng thời gian: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        <p>Trạng thái lọc: 
            @if($status == 'active') Đang hoạt động
            @elseif($status == 'pending') Chờ duyệt
            @elseif($status == 'inactive') Ngưng hoạt động
            @else Tất cả
            @endif
        </p>
        <p><strong>Tổng số CLB:</strong> {{ $clubs->count() }}</p>
        <p>Đang hoạt động: {{ $activeCount }} | Chờ duyệt: {{ $pendingCount }} | Ngưng hoạt động: {{ $inactiveCount }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tên Câu lạc bộ</th>
                <th>Trạng thái</th>
                <th>Ngày thành lập</th>
                <th>Số thành viên</th>
                <th>Số sự kiện</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clubs as $index => $club)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $club->name }}</td>
                    <td>{{ ucfirst($club->status) }}</td>
                    <td>{{ $club->created_at->format('d/m/Y') }}</td>
                    <td>{{ $club->members_count ?? $club->members()->count() }}</td>
                    <td>{{ $club->events_count ?? $club->events()->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
