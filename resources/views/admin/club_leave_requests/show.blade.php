<div class="container py-3">
    <h5 class="mb-3 text-primary fw-semibold">
        <i class="bi bi-box-arrow-left me-2"></i>Chi tiết yêu cầu rời CLB
    </h5>

    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light fw-bold">Thông tin yêu cầu</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 border-end">
                    <h6 class="fw-semibold text-secondary mb-3">Thông tin người gửi yêu cầu</h6>
                    <p><strong>Họ tên:</strong> {{ $request->user->name }}</p>
                    <p><strong>Email:</strong> {{ $request->user->email }}</p>
                    <p><strong>Vai trò:</strong> {{ ucfirst($request->user->role ?? '—') }}</p>
                    <p><strong>Trạng thái tài khoản:</strong>
                        @if($request->user->status === 'active')
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Không hoạt động</span>
                        @endif
                    </p>
                    <p><strong>Ngày gửi yêu cầu:</strong>
                        {{ $request->requested_at ? $request->requested_at->format('d/m/Y') : '—' }}</p>
                    <p><strong>Lý do rời CLB:</strong> {{ $request->reason ?? 'Không có lý do cụ thể' }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="fw-semibold text-secondary mb-3">Thông tin CLB</h6>
                    <p><strong>Tên CLB:</strong> {{ $request->club->name }}</p>
                    <p><strong>Lĩnh vực:</strong> {{ $request->club->field }}</p>
                    <p><strong>Giới hạn thành viên:</strong> {{ $request->club->member_limit ?? '—' }}</p>
                    <p><strong>Số lượng thành viên hiện tại:</strong> {{ $request->club->members()->count() }}</p>
                    <p><strong>Mô tả:</strong> {!! $request->club->description !!}</p>
                    <p><strong>Người quản lý:</strong> {{ $request->club->manager->name ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($request->user->member)
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light fw-bold">Thông tin cá nhân thành viên</div>
            <div class="card-body row g-3">
                <div class="col-md-6"><strong>Mã sinh viên:</strong> {{ $request->user->member->student_code }}</div>
                <div class="col-md-6"><strong>Giới tính:</strong> {{ $request->user->member->gender }}</div>
                <div class="col-md-6"><strong>Ngày sinh:</strong>
                    {{ \Carbon\Carbon::parse($request->user->member->date_of_birth)->format('d/m/Y') }}</div>
                <div class="col-md-6"><strong>Số điện thoại:</strong> {{ $request->user->member->phone }}</div>
                <div class="col-md-6"><strong>Địa chỉ:</strong> {{ $request->user->member->address }}</div>
                <div class="col-md-6"><strong>Dân tộc:</strong> {{ $request->user->member->ethnicity }}</div>
                <div class="col-md-6"><strong>Khóa học:</strong> {{ $request->user->member->course }}</div>
                <div class="col-md-6"><strong>Ngành học:</strong> {{ $request->user->member->major }}</div>
                <div class="col-md-6"><strong>Số CCCD:</strong> {{ $request->user->member->citizen_id }}</div>
                <div class="col-md-6"><strong>Ngày cấp:</strong>
                    {{ \Carbon\Carbon::parse($request->user->member->issued_date)->format('d/m/Y') }}</div>
                <div class="col-md-6"><strong>Nơi cấp:</strong> {{ $request->user->member->issued_place }}</div>
            </div>
        </div>
    @endif

    <div class="mt-3">
        @if($request->status === 'pending')
            <form action="{{ route('admin.club_leave_requests.handle', $request->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ghi chú (tùy chọn)</label>
                    <textarea name="note" class="form-control" rows="3"
                        placeholder="Nhập ghi chú cho yêu cầu này..."></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="approve" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Duyệt yêu cầu
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Từ chối
                    </button>
                </div>
            </form>
        @else
            <div class="alert alert-info">
                Yêu cầu này đã được xử lý:
                <strong>
                    @if($request->status === 'approved') Đã duyệt
                    @elseif($request->status === 'rejected') Từ chối
                    @endif
                </strong>
            </div>
        @endif
    </div>
</div>