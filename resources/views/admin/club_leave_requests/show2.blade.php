@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu rời CLB')
@section('card-header')
        Chi tiết yêu cầu rời CLB
@endsection

@section('card-body')
    <div class="container py-3">

       

        {{-- Card tổng hợp: người gửi + CLB --}}
            <div class="card-header bg-light fw-bold">Thông tin yêu cầu</div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Bên trái: Người gửi --}}
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
                        <p><strong>Ngày gửi yêu cầu:</strong> {{ $request->requested_at->format('d/m/Y') }}</p>
                        <p><strong>Lý do rời CLB:</strong> {{ $request->reason ?? 'Không có lý do cụ thể' }}</p>
                    </div>

                    {{-- Bên phải: CLB --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold text-secondary mb-3">Thông tin CLB</h6>
                        <p><strong>Tên CLB:</strong> {{ $request->club->name }}</p>
                        <p><strong>Lĩnh vực:</strong> {{ $request->club->field }}</p>
                        <p><strong>Giới hạn thành viên:</strong> {{ $request->club->member_limit ?? '—' }}</p>
                        <p><strong>Số lượng thành viên hiện tại:</strong> {{ $request->club->members()->count() }}</p>
                        <p><strong>Mô tả:</strong> {!! $request->club->description !!}</p>
                    </div>
                </div>
            </div>

        {{-- Thông tin cá nhân thành viên --}}
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

        {{-- Trạng thái xử lý --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light fw-bold">Trạng thái xử lý</div>
            <div class="card-body">
                <p><strong>Trạng thái:</strong>
                    @if($request->status === 'pending')
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    @elseif($request->status === 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                    @elseif($request->status === 'rejected')
                        <span class="badge bg-danger">Từ chối</span>
                    @endif
                </p>
                <p><strong>Người xử lý:</strong> {{ optional($request->handledBy)->name ?? '—' }}</p>
                <p><strong>Thời gian xử lý:</strong>
                    {{ $request->handled_at ? $request->handled_at->format('d/m/Y H:i') : '—' }}</p>
                <p><strong>Ghi chú xử lý:</strong> {{ $request->note ?? '—' }}</p>
            </div>
        </div>

    </div>
@endsection