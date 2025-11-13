<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Thống kê Tài khoản</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        h3, h4 {
            margin-bottom: 5px;
        }

        p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        ul {
            margin: 5px 0 10px 20px;
            padding: 0;
        }

        li {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

<h3>Thống kê Tài khoản</h3>
<p><strong>Thời gian:</strong> {{ $startDate }} → {{ $endDate }}</p>
<p><strong>Trạng thái:</strong> {{ $status ?? 'Tất cả' }}, <strong>Vai trò:</strong> {{ $role ?? 'Tất cả' }}</p>

<h4>Thống kê nhanh</h4>
<ul>
    <li>Tài khoản hoạt động: {{ $activeCount }}</li>
    <li>Tài khoản không hoạt động: {{ $inactiveCount }}</li>
</ul>

<h4>Danh sách tài khoản</h4>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($accounts as $index => $account)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $account->name }}</td>
                <td>{{ $account->email }}</td>
                <td>{{ ucfirst($account->role) }}</td>
                <td>{{ $account->status == 'active' ? 'Hoạt động' : 'Ngưng hoạt động' }}</td>
                <td>{{ $account->created_at ? $account->created_at->format('d/m/Y') : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
