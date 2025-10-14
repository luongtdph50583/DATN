@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Yêu cầu thành lập câu lạc bộ</h1>

    <div class="card shadow">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($clubRequests->isEmpty())
                <p class="text-muted">Không có yêu cầu thành lập nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên CLB</th>
                                <th>Mô tả</th>
                                <th>Lĩnh vực</th>
                                <th>Người gửi</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clubRequests as $req)
                                <tr>
                                    <td>{{ $req->id }}</td>
                                    <td>{{ $req->name }}</td>
                                    <td>{{ Str::limit($req->description, 60) }}</td>
                                    <td>{{ $req->field }}</td>
                                    <td>{{ $req->creator->name ?? '—' }}</td>
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
                                            <form action="{{ route('admin.club-requests.handle', $req) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button class="btn btn-sm btn-success" title="Duyệt"><i class="fas fa-check"></i></button>
                                            </form>
                                            <form action="{{ route('admin.club-requests.handle', $req) }}" method="POST" class="d-inline">
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
