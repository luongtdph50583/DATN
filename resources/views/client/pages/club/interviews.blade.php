@extends('client.layouts.app')
@section('title', 'Quản lý phỏng vấn - ' . $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Quản lý phỏng vấn - {{ $club->name }}</h2>
            <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        {{-- Filter và Search --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('club_manager.interviews.index', ['club_id' => $club->id]) }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Tên, email, MSSV..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select" data-select2="true">
                                <option value="">Tất cả</option>
                                <option value="pending_interview" {{ request('status') === 'pending_interview' ? 'selected' : '' }}>Chờ lên lịch</option>
                                <option value="waiting_attendance" {{ request('status') === 'waiting_attendance' ? 'selected' : '' }}>Chờ điểm danh</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Người phỏng vấn</label>
                            <select name="interviewer_id" class="form-select" data-select2="true">
                                <option value="">Tất cả</option>
                                @foreach($interviewers as $interviewer)
                                    <option value="{{ $interviewer->id }}" {{ request('interviewer_id') == $interviewer->id ? 'selected' : '' }}>
                                        {{ $interviewer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('club_manager.interviews.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @php
            $statusLabels = [
                'pending_interview' => 'Chờ lên lịch',
                'waiting_attendance' => 'Chờ điểm danh',
            ];
            $resultLabels = [
                'pass' => 'Đạt',
                'fail' => 'Không đạt',
                'no_show' => 'Vắng mặt',
                'pending' => 'Chờ kết quả',
            ];
        @endphp

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ứng viên</th>
                        <th>Ngày yêu cầu</th>
                        <th>Trạng thái</th>
                        <th>Thông tin phỏng vấn</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $index => $request)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $request->user->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $request->user->email ?? 'N/A' }}</div>
                            </td>
                            <td>{{ optional($request->requested_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $label = $statusLabels[$request->status] ?? $request->status;
                                    $badgeMap = [
                                        'pending' => 'badge bg-secondary',
                                        'scheduling_interview' => 'badge bg-warning text-dark',
                                        'interview' => 'badge bg-info text-dark',
                                        'interview_completed' => 'badge bg-success',
                                    ];
                                    $badgeClass = $badgeMap[$request->status] ?? 'badge bg-light text-dark';
                                @endphp
                                <span class="{{ $badgeClass }}">{{ $label }}</span>
                            </td>
                            <td>
                                @if($request->interview_scheduled_at)
                                    <div><i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($request->interview_scheduled_at)->format('d/m/Y H:i') }}</div>
                                @endif
                                @if($request->interview_location)
                                    <div><i class="fas fa-map-marker-alt me-1"></i> {{ $request->interview_location }}</div>
                                @endif
                                @if($request->interview_result)
                                    <div><i class="fas fa-info-circle me-1"></i> Kết quả: {{ $resultLabels[$request->interview_result] ?? $request->interview_result }}</div>
                                @endif
                            </td>
                            <td class="d-flex flex-wrap gap-2">
                                @if($request->status === 'pending')
                                    <form action="{{ route('club_manager.interviews.contact', ['club_id' => $club->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-phone me-1"></i> Đang liên hệ
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($request->status, ['pending','scheduling_interview']))
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#scheduleModal{{ $request->id }}">
                                        <i class="fas fa-calendar me-1"></i> Lên lịch
                                    </button>
                                @endif

                                @if($request->status === 'interview')
                                    <button type="button" class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#attendanceModal{{ $request->id }}">
                                        <i class="fas fa-check me-1"></i> Điểm danh
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Schedule Modal -->
                        <div class="modal fade" id="scheduleModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Lên lịch phỏng vấn</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('club_manager.interviews.schedule', ['club_id' => $club->id]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Thời gian <span class="text-danger">*</span></label>
                                                <input type="datetime-local" name="scheduled_at" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Địa điểm</label>
                                                <input type="text" name="location" class="form-control" placeholder="Ví dụ: Phòng A101...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ghi chú</label>
                                                <textarea name="note" class="form-control" rows="3" placeholder="Thông tin thêm cho buổi phỏng vấn..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-primary">Lưu lịch</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Modal -->
                        <div class="modal fade" id="attendanceModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Điểm danh phỏng vấn</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('club_manager.interviews.attendance', ['club_id' => $club->id]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Kết quả <span class="text-danger">*</span></label>
                                                <select name="attendance_status" class="form-select" data-select2="true" required>
                                                    <option value="completed">Đã hoàn thành</option>
                                                    <option value="no_show">Không tham gia</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ghi chú</label>
                                                <textarea name="interview_note" class="form-control" rows="3" placeholder="Nhận xét về buổi phỏng vấn..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-success">Xác nhận</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Chưa có yêu cầu nào cần phỏng vấn.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

