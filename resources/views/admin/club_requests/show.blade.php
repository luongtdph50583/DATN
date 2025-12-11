@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu CLB')

@section('card-body')
    <div class="container py-4">

        {{-- Thanh trạng thái --}}
        <div
            class="alert alert-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }} mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Trạng thái:</strong>
                    @if($request->status === 'pending_confirmation')
                        <span class="badge bg-warning text-dark">⏳ Chờ xác nhận thành viên</span>
                    @elseif($request->status === 'pending_approval')
                        <span class="badge bg-info">✋ Chờ admin phê duyệt</span>
                    @elseif($request->status === 'approved')
                        <span class="badge bg-success">✅ Đã phê duyệt</span>
                    @elseif($request->status === 'rejected')
                        <span class="badge bg-danger">❌ Đã từ chối</span>
                    @else
                        <span class="badge bg-secondary">{{ $request->status }}</span>
                    @endif

                    <span class="ms-3">
                        <strong>Xác nhận:</strong> {{ $confirmedCount }}/{{ $totalMembers }}
                        @if($isAllConfirmed)
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @endif
                    </span>
                </div>

                {{-- Nút PDF nếu đã sinh --}}
                @if($request->pdf_file && $request->status === 'approved')
                    <div>
                        <a href="{{ route('admin.club_requests.pdf.view', $request->id) }}"
                            class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="bi bi-file-pdf"></i> Xem PDF
                        </a>
                        <a href="{{ route('admin.club_requests.pdf.download', $request->id) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-download"></i> Tải PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Thông tin CLB & Người đề xuất --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light fw-bold">Thông tin CLB & Người đề xuất</div>
            <div class="card-body">
                <div class="row">
                    {{-- Thông tin CLB --}}
                    <div class="col-md-6 border-end">
                        <h6 class="fw-semibold text-secondary mb-3">Thông tin CLB đề xuất</h6>
                        <p><strong>Tên CLB:</strong> {{ $request->name }}</p>
                        <p><strong>Khẩu hiệu:</strong> {{ $request->slogan ?? '—' }}</p>
                        <p><strong>Mô tả:</strong> {{ $request->description ?? '—' }}</p>
                        <p><strong>Lĩnh vực:</strong> {{ $request->field ?? '—' }}</p>
                        <p><strong>Mục đích:</strong> {{ $request->purpose ?? '—' }}</p>
                        <p><strong>Kế hoạch hoạt động 3 tháng:</strong>
                            @if($request->plan)
                                <a href="{{ asset('storage/' . $request->plan) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">Tải file</a>
                            @else
                                <span>Chưa có file kế hoạch.</span>
                            @endif
                        </p>
                    </div>

                    {{-- Người đề xuất --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold text-secondary mb-3">Người đề xuất</h6>
                        @if($request->creator)
                            <p><strong>Họ tên:</strong> {{ $request->creator->name }}</p>
                            <p><strong>Email:</strong> {{ $request->creator->email }}</p>
                            <p><strong>Student Code:</strong> {{ optional($request->creator->member)->student_code ?? '—' }}</p>
                            <p><strong>Khóa học:</strong> {{ optional($request->creator->member)->course ?? '—' }}</p>
                            <p><strong>Chuyên ngành:</strong> {{ optional($request->creator->member)->major ?? '—' }}</p>
                            <p><strong>Số điện thoại:</strong> {{ optional($request->creator->member)->phone ?? '—' }}</p>
                        @else
                            <p>Không có thông tin chi tiết người đề xuất.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Trạng thái xác nhận thành viên --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-check2-circle me-2"></i>Trạng thái xác nhận thành viên
            </div>
            <div class="card-body">
                @if($request->confirmations && $request->confirmations->count() > 0)
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Thời gian xác nhận</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request->members as $member)
                                            @php
                                                $confirmation = $request->confirmations->firstWhere('user_id', $member->user_id);
                                            @endphp
                                            <tr>
                                                <td>{{ optional($member->user)->name ?? '—' }}</td>
                                                <td>{{ optional($member->user)->email ?? '—' }}</td>
                                                <td>
                                                    {{ [
                                    'club_manager' => 'Chủ nhiệm',
                                    'deputy_manager' => 'Phó chủ nhiệm',
                                    'secretary' => 'Thư ký',
                                    'treasurer' => 'Thủ quỹ',
                                    'event_manager' => 'Quản lý sự kiện',
                                    'communication' => 'Truyền thông',
                                    'member' => 'Thành viên'
                                ][$member->role] ?? 'Không rõ' }}
                                                </td>
                                                <td>
                                                    @if($confirmation)
                                                        <span class="badge bg-success">✅ Đã xác nhận</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">⏳ Chưa xác nhận</span>
                                                    @endif
                                                </td>
                                                <td>{{ $confirmation ? $confirmation->confirmed_at->format('d/m/Y H:i') : '—' }}</td>
                                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">Chưa có xác nhận nào từ thành viên.</p>
                @endif
            </div>
        </div>

        {{-- Ban quản lý --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-person-badge me-2"></i>Ban quản lý
            </div>
            <div class="card-body">
                @php
                    $roles_other = $request->members->filter(fn($m) => $m->role !== 'member');
                @endphp

                @if($roles_other->count())
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Mã sinh viên</th>
                                <th>Số điện thoại</th>
                                <th>Vai trò</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles_other as $member)
                                            <tr>
                                                <td>{{ optional($member->user)->name ?? '—' }}</td>
                                                <td>{{ optional($member->user)->email ?? '—' }}</td>
                                                <td>{{ optional(optional($member->user)->member)->student_code ?? '—' }}</td>
                                                <td>{{ optional(optional($member->user)->member)->phone ?? '—' }}</td>
                                                <td>
                                                    {{ [
                                    'club_manager' => 'Chủ nhiệm',
                                    'deputy_manager' => 'Phó chủ nhiệm',
                                    'secretary' => 'Thư ký',
                                    'treasurer' => 'Thủ quỹ',
                                    'event_manager' => 'Quản lý sự kiện',
                                    'communication' => 'Truyền thông',
                                    'member' => 'Thành viên'
                                ][$member->role] ?? 'Không rõ' }}
                                                </td>
                                                <td class="text-center">
                                                    @if(optional($member->user)->member)
                                                        <a href="{{ url('/admin/members/' . $member->user->member->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            Xem
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">Chưa có ban quản lý.</p>
                @endif
            </div>
        </div>

        {{-- Thành viên --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-people-fill me-2"></i>Thành viên
            </div>
            <div class="card-body">
                @php
                    $roles_member = $request->members->filter(fn($m) => $m->role === 'member');
                @endphp

                @if($roles_member->count())
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Mã sinh viên</th>
                                <th>Số điện thoại</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles_member as $member)
                                <tr>
                                    <td>{{ optional($member->user)->name ?? '—' }}</td>
                                    <td>{{ optional($member->user)->email ?? '—' }}</td>
                                    <td>{{ optional(optional($member->user)->member)->student_code ?? '—' }}</td>
                                    <td>{{ optional(optional($member->user)->member)->phone ?? '—' }}</td>
                                    <td class="text-center">
                                        @if(optional($member->user)->member)
                                            <a href="{{ url('/admin/members/' . $member->user->member->id) }}"
                                                class="btn btn-sm btn-primary">
                                                Xem
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">Chưa có thành viên.</p>
                @endif
            </div>
        </div>

        {{-- Lịch sử phê duyệt --}}
        @if($request->approvals && $request->approvals->count() > 0)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Lịch sử phê duyệt
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Admin</th>
                                <th>Trạng thái</th>
                                <th>Ghi chú</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request->approvals as $approval)
                                <tr>
                                    <td>{{ $approval->admin->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($approval->status === 'approved')
                                            <span class="badge bg-success">✅ Đã duyệt</span>
                                        @else
                                            <span class="badge bg-danger">❌ Từ chối</span>
                                        @endif
                                    </td>
                                    <td>{{ $approval->note ?? '—' }}</td>
                                    <td>{{ $approval->approved_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Form duyệt/từ chối --}}
        @if(in_array($request->status, ['pending_approval', 'pending_confirmation']))
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Xử lý yêu cầu</div>
                <div class="card-body">
                    @if($request->status === 'pending_confirmation')
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Đơn này chưa đủ xác nhận từ thành viên ({{ $confirmedCount }}/{{ $totalMembers }}).
                            Vui lòng chờ đủ xác nhận trước khi phê duyệt.
                        </div>
                    @endif

                    <form method="POST"
                        action="{{ $request->status === 'pending_approval' ? route('admin.club_requests.approve', $request->id) : '#' }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Ghi chú xử lý (nếu có)</label>
                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            @if($request->status === 'pending_approval')
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Duyệt
                                </button>
                            @else
                                <button type="submit" class="btn btn-success" disabled>
                                    <i class="bi bi-check-circle"></i> Duyệt (Chờ xác nhận)
                                </button>
                            @endif
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.club_requests.reject', $request->id) }}" class="mt-2">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                            <textarea name="note" class="form-control" rows="2" required>{{ old('note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Bạn có chắc chắn muốn từ chối đơn này?')">
                            <i class="bi bi-x-circle"></i> Từ chối
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($request->status === 'approved')
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                Đơn này đã được phê duyệt và CLB đã được tạo thành công!
            </div>
        @endif

        @if($request->status === 'rejected')
            <div class="alert alert-danger">
                <i class="bi bi-x-circle-fill me-2"></i>
                Đơn này đã bị từ chối.
                @if($request->approvals->where('status', 'rejected')->first())
                    <br><strong>Lý do:</strong> {{ $request->approvals->where('status', 'rejected')->first()->note }}
                @endif
            </div>
        @endif

    </div>
@endsection
