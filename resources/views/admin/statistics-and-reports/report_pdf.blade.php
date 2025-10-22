<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Câu lạc bộ - {{ $club->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.5; font-size: 14px; }
        h1, h2 { color: #2C3E50; text-align: center; }
        h3 { color: #2C3E50; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; font-size: 13px; }
        th { background-color: #f2f2f2; }
        .section { margin-bottom: 25px; }
        .badge { padding: 2px 6px; border-radius: 4px; color: white; font-size: 12px; }
        .bg-success { background-color: #28a745; }
        .bg-secondary { background-color: #6c757d; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; }
    </style>
</head>
<body>
    <h1>BÁO CÁO CÂU LẠC BỘ</h1>
    <h2>{{ $club->name }}</h2>

    <div class="section">
        <h3>1️⃣ Thông tin cơ bản</h3>
        <p><strong>Tên CLB:</strong> {{ $club->name }}</p>
        <p><strong>Lĩnh vực:</strong> {{ $club->field ?? 'Không rõ' }}</p>
        <p><strong>Ngày thành lập:</strong> {{ $club->created_at ? $club->created_at->format('d/m/Y') : '—' }}</p>
        <p><strong>Người quản lý:</strong> {{ $club->manager->name ?? 'Không rõ' }}</p>
        <p><strong>Mô tả:</strong> {{ $club->description ?? '—' }}</p>
        <p><strong>Trạng thái:</strong>
            @if ($club->status === 'active')
                <span class="badge bg-success">Đang hoạt động</span>
            @else
                <span class="badge bg-secondary">Ngừng hoạt động</span>
            @endif
        </p>
    </div>

    <div class="section">
        <h3>2️⃣ Thống kê Thành viên</h3>
        <table>
            <tr><th>Tổng số</th><td>{{ $totalMembers }}</td></tr>
            <tr><th>Hoạt động</th><td>{{ $activeMembers }}</td></tr>
            <tr><th>Không hoạt động</th><td>{{ $inactiveMembers }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>3️⃣ Danh sách Thành viên</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tham gia</th>
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
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Chưa có thành viên nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>4️⃣ Thống kê Sự kiện</h3>
        <table>
            <tr><th>Tổng số</th><td>{{ $totalEvents }}</td></tr>
            <tr><th>Đã duyệt</th><td>{{ $approvedEvents }}</td></tr>
            <tr><th>Chờ duyệt / Từ chối</th><td>{{ $pendingOrRejected}}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>5️⃣ Danh sách Sự kiện</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sự kiện</th>
                    <th>Ngày tổ chức</th>
                    <th>Địa điểm</th>
                    <th>Trạng thái</th>
                    <th>Người tạo</th>
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
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Chưa có sự kiện nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="text-align: right; margin-top: 30px;">Ngày tạo báo cáo: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
