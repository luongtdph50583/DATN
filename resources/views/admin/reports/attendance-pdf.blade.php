<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Điểm danh {{ $event->name }}</title>
<style>body{font-family:DejaVu Sans,sans-serif;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #000;padding:10px;}</style>
</head>
<body>
    <h1 style="text-align:center">{{ $event->name }}</h1>
    <p style="text-align:center">CLB: {{ $event->club->name }} | Thời gian: {{ $event->start_time->format('d/m/Y H:i') }}</p>
    <div style="text-align:center;margin:20px 0">
        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" />
        <p><strong>Quét QR để điểm danh</strong></p>
    </div>
    <table>
        <thead><tr><th>STT</th><th>Họ tên</th><th>Email</th><th>Thời gian đăng ký</th></tr></thead>
        <tbody>
            @foreach($event->registrations as $i => $reg)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $reg->user->name }}</td>
                <td>{{ $reg->user->email }}</td>
                <td>{{ $reg->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>