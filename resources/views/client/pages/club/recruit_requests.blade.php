@extends('client.layouts.app')
@section('title', 'Tuyển thành viên - ' . $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="mb-0">Tuyển thành viên - {{ $club->name }}</h2>
            <div class="btn-group">
                <a href="{{ route('club_manager.recruit_form.create', ['club_id' => $club->id]) }}" class="btn btn-primary">
                    <i class="fas fa-file-alt me-1"></i> Quản lý form tuyển thành viên
                </a>
                <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
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
                                @else
                                    <span class="badge bg-secondary">Chờ duyệt</span>
                                @endif
                            </td>
                            <td class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal" data-bs-target="#answersModal{{ $request->id }}">
                                    <i class="fas fa-eye me-1"></i> Xem câu trả lời
                                </button>

                                @if($request->status === 'pending')
                                    <form action="{{ route('club_manager.recruit.approve', ['club_id' => $club->id, 'member_id' => $request->user->member->id ?? 0]) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                            <i class="fas fa-check me-1"></i> Duyệt
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectRecruitModal{{ $request->id }}">
                                        <i class="fas fa-times me-1"></i> Từ chối
                                    </button>
                                @else
                                    <span class="text-muted">Đã xử lý</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Answers Modal -->
                        <div class="modal fade" id="answersModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Câu trả lời - {{ $request->user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if($request->formAnswers->isEmpty())
                                            <p class="text-muted mb-0">Thành viên chưa trả lời form.</p>
                                        @else
                                            <ul class="list-group">
                                                @foreach($request->formAnswers as $answer)
                                                    <li class="list-group-item">
                                                        <strong>{{ $answer->question->question ?? 'Câu hỏi' }}:</strong>
                                                        <div>{{ $answer->formatted_answer }}</div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectRecruitModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Từ chối yêu cầu</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('club_manager.recruit.reject', ['club_id' => $club->id, 'request_id' => $request->id]) }}" method="POST">
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
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có yêu cầu tuyển thành viên nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

