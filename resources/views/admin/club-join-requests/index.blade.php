@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Yêu cầu tham gia câu lạc bộ</h1>

    <div class="card shadow">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($joinRequests->isEmpty())
                <p class="text-muted">Không có yêu cầu tham gia nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Người dùng</th>
                                <th>Tên CLB</th>
                                <th>Lý do</th>
                                <th>Ngày gửi</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($joinRequests as $req)
                                <tr>
                                    <td>{{ $req->id }}</td>
                                    <td>{{ $req->user->name ?? '—' }}</td>
                                    <td>{{ $req->club->name ?? '—' }}</td>
                                    <td>{{ Str::limit($req->message, 60) }}</td>
                                    <td>{{ $req->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($req->status === 'pending')
                                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                        @elseif($req->status === 'approved')
                                            <span class="badge bg-success">Đã duyệt</span>
                                        @else
                                            <span class="badge bg-danger">Từ chối</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($req->status === 'pending')
                                            <form action="{{ route('admin.club-join-requests.handle', $req) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button class="btn btn-sm btn-success" title="Duyệt"><i class="fas fa-check"></i></button>
                                            </form>
                                            <form action="{{ route('admin.club-join-requests.handle', $req) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button class="btn btn-sm btn-danger" title="Từ chối"><i class="fas fa-times"></i></button>
                                            </form>
                                        @else
                                            <span class="text-muted">Đã xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
