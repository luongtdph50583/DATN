@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu tham gia CLB')

@section('card-body')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Chi tiết yêu cầu tham gia CLB</h5>

            <div class="row">
                <!-- Logo CLB nếu có -->
                @if($request->club->logo)
                    <div class="col-md-3 text-center mb-3">
                        <img src="{{ asset('storage/' . $request->club->logo) }}" class="img-fluid rounded"
                            style="max-height:150px;">
                    </div>
                @endif

                <!-- Thông tin CLB -->
            <!-- Thông tin CLB -->
            <div class="col-md-{{ $request->club->logo ? '9' : '12' }}">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light fw-bold">Thông tin CLB</div>
                    <div class="card-body">
                        <p><strong>Tên CLB:</strong> {{ $request->club->name }}</p>
                        <p><strong>Lĩnh vực:</strong> {{ $request->club->field }}</p>
                        <p><strong>Trạng thái:</strong>
                            @if($request->club->status === 'active')
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Ngừng hoạt động</span>
                            @endif
                        </p>
                        <p><strong>Ngày thành lập:</strong> {{ \Carbon\Carbon::parse($request->club->founded_at)->format('d/m/Y') }}
                        </p>
                        <p><strong>Địa điểm hoạt động:</strong> {{ $request->club->location }}</p>
                        <p><strong>Giới hạn thành viên:</strong> {{ $request->club->member_limit }}</p>
                        <p><strong>Số lượng thành viên hiện tại:</strong> {{ $request->club->members->count() }}</p>
                        <p><strong>Người quản lý:</strong> {{ $request->club->manager->name ?? '—' }}</p>
                       
                    </div>
                </div>
            </div>


            <!-- Thông tin người gửi yêu cầu -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold">Thông tin người gửi yêu cầu</div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $request->user->name }}</p>
                    <p><strong>Email:</strong> {{ $request->user->email }}</p>
                    <p><strong>Vai trò:</strong> {{ ucfirst($request->user->role) }}</p>
                    <p><strong>Trạng thái tài khoản:</strong>
                        @if($request->user->status === 'active')
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Không hoạt động</span>
                        @endif
                    </p>
                    <p><strong>Ngày gửi yêu cầu:</strong> {{ $request->requested_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Thông tin cá nhân thành viên -->
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

            <!-- Form duyệt/từ chối -->
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Xử lý yêu cầu</div>
                <div class="card-body">
                    @if($request->status === 'pending')
                        <form method="POST" action="{{ route('admin.club_join_requests.handle', $request->id) }}">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label">Ghi chú của người xử lý (nếu có)</label>
                                                        <textarea name="note" class="form-control" rows="2">{{ old('note', $request->note) }}</textarea>
                                                    </div>

                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="submit" name="action" value="approve" class="btn btn-success">Duyệt</button>
                                                        <button type="submit" name="action" value="reject" class="btn btn-danger">Từ chối</button>
                                                    </div>
                                                </form>
                    @else
                        <div class="alert alert-info">
                            Yêu cầu đã được xử lý: <strong>{{ ucfirst($request->status) }}</strong>
                        </div>
                        @if($request->note)
                            <div class="mb-2"><strong>Ghi chú xử lý:</strong> {{ $request->note }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection