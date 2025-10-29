<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Câu lạc bộ - {{ $club->name }}</title>
    {{-- PHÔNG CHỮ: Sử dụng DejaVu Sans cho hỗ trợ tiếng Việt trong PDF --}}
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.5; font-size: 14px; }
        h1, h2 { color: #2C3E50; text-align: center; }
        h3 { 
            color: #2C3E50; 
            margin-top: 20px; 
            margin-bottom: 8px; 
            padding-bottom: 5px; 
            border-bottom: 2px solid #3498db;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; font-size: 13px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .section { margin-bottom: 25px; }
        .badge { padding: 2px 6px; border-radius: 4px; color: white; font-size: 12px; }
        .bg-success { background-color: #28a745; }
        .bg-secondary { background-color: #6c757d; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; }
        .info-table td:first-child { width: 25%; font-weight: bold; background-color: #f8f8f8; }
    </style>
</head>
<body>
    <h1>BÁO CÁO TỔNG HỢP CÂU LẠC BỘ</h1>
    <h2>{{ $club->name }}</h2>

    <div class="section">
        <h3>1️⃣ Thông tin cơ bản</h3>
        <table class="info-table">
            <tr><td>Tên CLB</td><td>{{ $club->name }}</td></tr>
            <tr><td>Mã CLB (ID)</td><td>{{ $club->id }}</td></tr>
            <tr><td>Lĩnh vực hoạt động</td><td>{{ $club->field ?? 'Không rõ' }}</td></tr>
            <tr><td>Giới hạn thành viên</td><td>{{ $club->member_limit ?? 'Không giới hạn' }}</td></tr>
            <tr><td>Người quản lý</td><td>{{ $club->manager->name ?? 'Không rõ' }}</td></tr>
            <tr><td>Email liên hệ</td><td>{{ $club->email ?? 'Chưa có' }}</td></tr>
            <tr><td>Số điện thoại</td><td>{{ $club->phone ?? 'Chưa có' }}</td></tr>
            <tr><td>Trạng thái</td>
                <td>
                    @if ($club->status === 'active')
                        <span class="badge bg-success">Đang hoạt động</span>
                    @elseif ($club->status === 'pending')
                         <span class="badge bg-warning">Chờ duyệt</span>
                    @else
                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                    @endif
                </td>
            </tr>
            <tr><td>Ngày tạo</td><td>{{ $club->created_at ? $club->created_at->format('d/m/Y H:i') : '—' }}</td></tr>
        </table>
        
        <p style="margin-top: 15px;"><strong>Mô tả chi tiết:</strong></p>
        <p style="border: 1px solid #ddd; padding: 10px; background-color: #fafafa;">
            {{ $club->description ?? 'Chưa có mô tả chi tiết.' }}
        </p>
    </div>

    <div class="section">
        <h3>2️⃣ Thống kê Tổng quan</h3>
        <table style="width: 60%; margin: 0 auto;">
            <thead style="background-color: #3498db; color: white;">
                <tr><th colspan="2">Thống kê Thành viên</th></tr>
            </thead>
            <tbody>
                <tr><td>Tổng số thành viên</td><td style="text-align: right;">{{ $totalMembers }}</td></tr>
                <tr><td>Đang hoạt động</td><td style="text-align: right;">{{ $activeMembers }}</td></tr>
                <tr><td>Ngừng hoạt động</td><td style="text-align: right;">{{ $inactiveMembers }}</td></tr>
            </tbody>
        </table>

        <table style="width: 60%; margin: 20px auto 0 auto;">
            <thead style="background-color: #2ecc71; color: white;">
                <tr><th colspan="2">Thống kê Sự kiện</th></tr>
            </thead>
            <tbody>
                <tr><td>Tổng số sự kiện</td><td style="text-align: right;">{{ $totalEvents }}</td></tr>
                <tr><td>Đã duyệt (Approved)</td><td style="text-align: right;">{{ $approvedEvents }}</td></tr>
                <tr><td>Chờ duyệt / Từ chối</td><td style="text-align: right;">{{ $pendingOrRejected}}</td></tr>
            </tbody>
        </table>
    </div>

    <div style="page-break-before: always;"></div>

    <div class="section">
        <h3>3️⃣ Danh sách Thành viên</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Họ tên</th>
                    <th style="width: 25%;">Email</th>
                    <th style="width: 15%;">Vai trò</th>
                    <th style="width: 15%;">Trạng thái</th>
                    <th style="width: 15%;">Ngày tham gia</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $index => $member)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $member->user->name ?? 'Không rõ' }}</td>
                    <td>{{ $member->user->email ?? '—' }}</td>
                    <td style="text-align: center;">{{ ucfirst($member->role ?? 'thành viên') }}</td>
                    <td style="text-align: center;">
                        @if ($member->status === 'active')
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Ngừng HĐ</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ $member->created_at ? $member->created_at->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Chưa có thành viên nào trong CLB này.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>4️⃣ Danh sách Sự kiện</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">Tên sự kiện</th>
                    <th style="width: 20%;">Thời gian bắt đầu</th>
                    <th style="width: 20%;">Địa điểm</th>
                    <th style="width: 15%;">Trạng thái</th>
                    <th style="width: 10%;">Người tạo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $index => $event)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $event->name }}</td>
                    {{-- ĐÃ SỬA LỖI: Dùng $event->start_time thay vì event_date --}}
                    <td>
                        @if ($event->start_time)
                            {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}
                        @else
                            <span class="text-muted">Chưa xác định</span>
                        @endif
                    </td>
                    <td>{{ $event->location ?? 'Không rõ' }}</td>
                    <td style="text-align: center;">
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
                    <td colspan="6" style="text-align:center;">Chưa có sự kiện nào được ghi nhận.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 50px; border-top: 1px solid #ccc; padding-top: 10px;">
        <p style="text-align: right; font-size: 12px; margin-bottom: 5px;">Báo cáo được tạo tự động bởi Hệ thống.</p>
        <p style="text-align: right; font-size: 12px; font-style: italic;">Ngày tạo báo cáo: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
