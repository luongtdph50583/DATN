@extends('admin.layouts.app')
@section('title', 'Danh sách yêu cầu tham gia CLB')

@section('card-body')
<div class="container mt-4">
    <h3 class="mb-4">📋 Danh sách yêu cầu tham gia CLB</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($requests->isEmpty())
        <div class="alert alert-info">Không có yêu cầu nào đang chờ duyệt.</div>
    @else
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tên người gửi</th>
                    <th>CLB muốn tham gia</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $key => $req)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $req->user->name ?? 'N/A' }}</td>
                        <td>{{ $req->club->name ?? 'N/A' }}</td>
                        <td>{{ $req->requested_at ? $req->requested_at->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge bg-warning text-dark">Đang chờ</span>
                            @elseif($req->status === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @else
                                <span class="badge bg-danger">Từ chối</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.club-join-requests.show', $req->id) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Xem
                            </a>
                            <form action="{{ route('admin.club-join-requests.approve', $req->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success" 
                                        onclick="return confirm('Xác nhận duyệt yêu cầu này?')">
                                    <i class="bi bi-check-circle"></i> Duyệt
                                </button>
                            </form>
                            <form action="{{ route('admin.club-join-requests.reject', $req->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Bạn chắc chắn muốn từ chối?')">
                                    <i class="bi bi-x-circle"></i> Từ chối
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
