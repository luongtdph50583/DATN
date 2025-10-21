<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Câu lạc bộ - {{ $club->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.5; font-size: 14px; }
        h1, h2 { color: #2C3E50; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        .section { margin-bottom: 20px; }
        .badge { padding: 4px 8px; border-radius: 4px; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-secondary { background-color: #6c757d; }
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
        <p><strong>Trạng thái:</strong>
            @if ($club->status === 'active')
                <span class="badge bg-success">Đang hoạt động</span>
            @else
                <span class="badge bg-secondary">Ngừng hoạt động</span>
            @endif
        </p>
        <p><strong>Mô tả:</strong> {{ $club->description ?? '—' }}</p>
    </div>

    <div class="section">
        <h3>2️⃣ Thống kê Thành viên</h3>
        <table>
            <tr><th>Tổng số</th><td>{{ $totalMembers }}</td></tr>
            <tr><th>Hoạt động</th><td>-</td></tr>
            <tr><th>Không hoạt động</th><td>-</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>3️⃣ Thống kê Sự kiện</h3>
        <table>
            <tr><th>Tổng số</th><td>{{ $totalEvents }}</td></tr>
            <tr><th>Đã duyệt</th><td>{{ $approvedEvents }}</td></tr>
            <tr><th>Chờ duyệt / Từ chối</th><td>{{ $pendingOrRejected}}</td></tr>
        </table>
    </div>

    <p style="text-align: right; margin-top: 50px;">Ngày tạo báo cáo: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
