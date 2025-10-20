@extends('admin.layouts.app')

@section('title', 'Danh sách yêu cầu tạo CLB')

@section('card-body')
<div class="container mt-4">
    <h2>📋 Danh sách yêu cầu tạo Câu lạc bộ</h2>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr class="table-dark text-center">
                <th>#</th>
                <th>Tên CLB</th>
                <th>Lĩnh vực</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Ngày gửi</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $index => $req)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $req->name }}</td>
                    <td>{{ $req->field }}</td>
                    <td>{{ Str::limit($req->description, 50) }}</td>
                    <td class="text-center">
                        @if ($req->status === 'pending')
                            <span class="badge bg-warning text-dark">Đang chờ</span>
                        @elseif ($req->status === 'approved')
                            <span class="badge bg-success">Đã duyệt</span>
                        @elseif ($req->status === 'rejected')
                            <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </td>
                    <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.club-requests.show', $req->id) }}" class="btn btn-primary btn-sm">Xem</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Không có yêu cầu nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
