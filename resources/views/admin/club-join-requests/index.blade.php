@extends('admin.layouts.app')

@section('title', 'Yêu cầu tham gia CLB')

@section('card-body')
<div class="container-fluid">
    <h1 class="h3 mb-4">Danh sách yêu cầu tham gia CLB</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>CLB</th>
                <th>Thành viên</th>
                <th>Trạng thái</th>
                <th>Ngày yêu cầu</th>
                <th width="220">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $req)
                <tr>
                    <td>{{ $req->club->name }}</td>
                    <td>{{ $req->user->name }}</td>
                    <td>
                        @if($req->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($req->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $req->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.club-join-requests.show', $req->id) }}" class="btn btn-info btn-sm">
                            👁 Xem
                        </a>

                        @if($req->status == 'pending')
                        <form action="{{ route('admin.club-join-requests.approve', $req->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">✔ Chấp nhận</button>
                        </form>

                        <form action="{{ route('admin.club-join-requests.reject', $req->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-danger btn-sm">✘ Từ chối</button>
                        </form>
                        @else
                            <i>Đã xử lý</i>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Không có yêu cầu</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
