<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách Sự kiện</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Danh sách Sự kiện</h2>

    <p>
        <strong>CLB:</strong> 
        {{ $selectedClub ? ($allClubs->firstWhere('id',$selectedClub)->name ?? 'Không rõ') : 'Tất cả' }} |
        <strong>Trạng thái:</strong> {{ $status ?: 'Tất cả' }} |
        <strong>Từ:</strong> {{ $startDate ?: '-' }} |
        <strong>Đến:</strong> {{ $endDate ?: '-' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tên sự kiện</th>
                <th>CLB tổ chức</th>
                <th>Thời gian bắt đầu</th>
                <th>Địa điểm</th>
                <th>Ngân sách</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $i => $event)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $event->name }}</td>
                <td>{{ $event->club->name ?? 'Không rõ' }}</td>
                <td>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i d/m/Y') : '-' }}</td>
                <td>{{ $event->location ?? '-' }}</td>
                <td>{{ $event->budget ? number_format($event->budget,0,',','.') . ' VNĐ' : '-' }}</td>
                <td>
                    @if($event->status=='approved') Đã duyệt
                    @elseif($event->status=='pending') Chờ duyệt
                    @elseif($event->status=='rejected') Từ chối
                    @else Không xác định
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">Không có sự kiện nào phù hợp</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
