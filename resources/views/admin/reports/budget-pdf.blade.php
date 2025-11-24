<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Báo cáo Ngân sách Sự kiện</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge-approved { background:#d4edda; color:#155724; padding:5px 10px; border-radius:4px; }
        .badge-pending { background:#fff3cd; color:#856404; padding:5px 10px; border-radius:4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TRƯỜNG ĐẠI HỌC SƯ PHẠM</h1>
        <h2>BÁO CÁO NGÂN SÁCH SỰ KIỆN NĂM {{ now()->year }}</h2>
        <p>Ngày lập báo cáo: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Sự kiện</th>
                <th>CLB</th>
                <th>Tổng dự kiến</th>
                <th>Xin cấp trường</th>
                <th>CLB tự chi</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $i => $e)
            <tr>
                <td class="text-center">{{ $i+1 }}</td>
                <td>{{ $e->name }}</td>
                <td>{{ $e->club->name }}</td>
                <td class="text-right">{{ number_format($e->budgetItems->sum('estimated_cost')) }}đ</td>
                <td class="text-right">{{ number_format($e->budgetItems->where('type','school_fund')->sum('estimated_cost')) }}đ</td>
                <td class="text-right">{{ number_format($e->budgetItems->where('type','club_fund')->sum('estimated_cost')) }}đ</td>
                <td class="text-center">
                    @if($e->status=='approved') <span class="badge-approved">Đã duyệt</span>
                    @else <span class="badge-pending">Chờ duyệt</span> @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#e9ecef;">
                <td colspan="3" class="text-center">TỔNG CỘNG</td>
                <td class="text-right">{{ number_format($totalEstimated) }}đ</td>
                <td class="text-right">{{ number_format($totalSchool) }}đ</td>
                <td class="text-right">{{ number_format($totalClub) }}đ</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>