@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu CLB')

@section('card-body')
    <div class="container py-4">

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
                        @if($request->user)
                            <p><strong>Họ tên:</strong> {{ $request->user->name }}</p>
                            <p><strong>Email:</strong> {{ $request->user->email }}</p>
                            <p><strong>Student Code:</strong> {{ $request->user->member->student_code ?? '—' }}</p>
                            <p><strong>Khóa học:</strong> {{ $request->user->member->course ?? '—' }}</p>
                            <p><strong>Chuyên ngành:</strong> {{ $request->user->member->major ?? '—' }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $request->user->member->phone ?? '—' }}</p>
                        @else
                            <p>Không có thông tin chi tiết người đề xuất.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Ban quản lý & Thành viên --}}
    {{-- Ban quản lý --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light fw-bold">
            <i class="bi bi-person-badge me-2"></i>Ban quản lý
        </div>
        <div class="card-body">
            @php
                $roles_other = $request->clubRequestMembers->filter(fn($m) => $m->role !== 'member');
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles_other as $member)
                            <tr>
                                <td>{{ $member->user->name ?? '—' }}</td>
                                <td>{{ $member->user->email ?? '—' }}</td>
                                <td>{{ $member->user->member->student_code ?? '—' }}</td>
                                <td>{{ $member->user->member->phone ?? '—' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $member->role)) }}</td>
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
                $roles_member = $request->clubRequestMembers->filter(fn($m) => $m->role === 'member');
            @endphp

            @if($roles_member->count())
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Mã sinh viên</th>
                            <th>Số điện thoại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles_member as $member)
                            <tr>
                                <td>{{ $member->user->name ?? '—' }}</td>
                                <td>{{ $member->user->email ?? '—' }}</td>
                                <td>{{ $member->user->member->student_code ?? '—' }}</td>
                                <td>{{ $member->user->member->phone ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted mb-0">Chưa có thành viên.</p>
            @endif
        </div>
    </div>


        {{-- Giảng viên đỡ đầu --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light fw-bold">Giảng viên phụ trách</div>
        <div class="card-body">
            @if($request->advisorFaculty)
                <p><strong>Họ tên:</strong> {{ $request->advisorFaculty->user->name ?? '—' }}</p>
                <p><strong>Mã nhân viên:</strong> {{ $request->advisorFaculty->employee_code ?? '—' }}</p>
                <p><strong>Khoa/Bộ môn:</strong> {{ $request->advisorFaculty->department ?? '—' }}</p>
                <p><strong>Chức danh:</strong> {{ $request->advisorFaculty->title ?? '—' }}</p>
                <p><strong>Văn phòng:</strong> {{ $request->advisorFaculty->office_location ?? '—' }}</p>
                <p><strong>Điện thoại cơ quan:</strong> {{ $request->advisorFaculty->office_phone ?? '—' }}</p>
                <p><strong>Email cơ quan:</strong> {{ $request->advisorFaculty->email_official ?? '—' }}</p>
                <p><strong>Trạng thái xác thực:</strong>
                    {{ $request->advisorFaculty->verified ? 'Đã xác thực' : 'Chưa xác thực' }}</p>
                <p><strong>Trạng thái chấp thuận:</strong>
                    @if($request->advisor_status === 'approved')
                        Đã chấp thuận
                    @elseif($request->advisor_status === 'rejected')
                        Từ chối
                    @else
                        Chờ xác nhận
                    @endif
                </p>
            @else
                <p>Chưa có giảng viên đỡ đầu hoặc chưa xác thực.</p>
            @endif
        </div>
    </div>


        {{-- Form duyệt/từ chối --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold">Xử lý yêu cầu</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.club_requests.handle', $request->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ghi chú xử lý (nếu có)</label>
                        <textarea name="note" class="form-control" rows="2">{{ old('note', $request->note) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="status" value="approved" class="btn btn-success">Duyệt</button>
                        <button type="submit" name="status" value="rejected" class="btn btn-danger">Từ chối</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
