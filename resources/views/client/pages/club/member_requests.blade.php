@extends('client.layouts.app')
@section('title', 'Yêu cầu tham gia CLB - ' . $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Yêu cầu tham gia CLB - {{ $club->name }}</h2>
            <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên người dùng</th>
                        <th>Email</th>
                        <th>Ngày yêu cầu</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $index => $request)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $request->user->name ?? 'N/A' }}</td>
                            <td>{{ $request->user->email ?? 'N/A' }}</td>
                            <td>{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                @if($request->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @elseif($request->status === 'rejected')
                                    <span class="badge bg-danger">Từ chối</span>
                                @elseif($request->status === 'interview')
                                    <span class="badge bg-info">Phỏng vấn</span>
                                @elseif($request->status === 'interview_completed')
                                    <span class="badge bg-warning">Đã phỏng vấn</span>
                                @else
                                    <span class="badge bg-secondary">Chờ duyệt</span>
                                @endif
                            </td>
                            <td>
                                @if($request->status === 'pending')
                                    <form action="{{ route('club_manager.member_requests.approve', ['club_id' => $club->id, 'member_id' => $request->user->member->id ?? 0]) }}" 
                                          method="POST" class="d-inline me-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                            <i class="fas fa-check"></i> Duyệt
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal{{ $request->id }}" 
                                            title="Từ chối">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Từ chối yêu cầu</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('club_manager.member_requests.reject', ['club_id' => $club->id, 'request_id' => $request->id]) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Lý do từ chối</label>
                                                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Nhập lý do từ chối..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Đã xử lý</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có yêu cầu tham gia nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

