@extends('client.layouts.app')

@section('title', 'Danh sách yêu cầu rời CLB')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Danh sách yêu cầu rời CLB</h2>
            <!-- Nếu cần bộ lọc/trạng thái sau này, thêm ở đây -->
        </div>

        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CLB</th>
                    <th>Thành viên</th>
                    <th>Trạng thái</th>
                    <th>Ngày yêu cầu</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ $req->club->name ?? $req->club_id }}</td>
                        <td>{{ $req->user->name ?? $req->user_id }}</td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge bg-warning">Đang chờ</span>
                            @elseif($req->status === 'approved')
                                <span class="badge bg-success">Đã chấp nhận</span>
                            @elseif($req->status === 'expired')
                                <span class="badge bg-secondary">Quá hạn</span>
                            @endif
                        </td>
                        <td>{{ $req->requested_at }}</td>
                        {{-- <td class="text-end">
                            <!-- Xem chi tiết -->
                            <a href="{{ route('club_manager.club.leave_requests.view', [$req->club_id, $req->id]) }}"
                                class="btn btn-sm btn-outline-secondary">
                                Xem chi tiết
                            </a>

                            <!-- Duyệt (chỉ hiển thị khi pending) -->
                            @if($req->status === 'pending')
                                <form action="{{ route('club_manager.club.leave_requests.handle', [$req->club_id, $req->id]) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        Duyệt
                                    </button>
                                </form>
                            @endif
                        </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Chưa có yêu cầu nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $requests->links() }}
    </div>
@endsection
