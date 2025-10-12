 @extends('layouts.app')

 @section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Yêu cầu tham gia CLB</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách yêu cầu tham gia</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>CLB</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clubJoinRequests as $joinRequest)
                            <tr>
                                <td>{{ $joinRequest->id }}</td>
                                <td>{{ $joinRequest->user->name }}</td>
                                <td>{{ $joinRequest->club->name }}</td>
                                <td>
                                    @if($joinRequest->status === 'pending') Chờ duyệt
                                    @elseif($joinRequest->status === 'approved') Đã duyệt
                                    @else Từ chối @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.club-join-requests.handle', $joinRequest) }}" method="POST" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                    </form>
                                    <form action="{{ route('admin.club-join-requests.handle', $joinRequest) }}" method="POST" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-sm btn-danger">Từ chối</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection