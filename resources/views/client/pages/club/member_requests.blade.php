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

        {{-- Filter và Search --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('club_manager.member_requests.index', ['club_id' => $club->id]) }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Tên, email, MSSV..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select" data-select2="true">
                                <option value="">Tất cả</option>
                                <option value="pending_interview" {{ request('status') === 'pending_interview' ? 'selected' : '' }}>Chờ lên lịch</option>
                                <option value="waiting_attendance" {{ request('status') === 'waiting_attendance' ? 'selected' : '' }}>Chờ điểm danh</option>
                                <option value="waiting_approval" {{ request('status') === 'waiting_approval' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('club_manager.member_requests.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Batch Actions --}}
        <div class="card mb-3 shadow-sm" id="batchActionsPanel" style="display: none;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Đã chọn: <span id="selectedCount">0</span> yêu cầu</span>
                    <div class="btn-group">
                        <button type="button" id="batchScheduleBtn" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#batchScheduleModal" style="display: none;">
                            <i class="fas fa-calendar-alt me-1"></i> Lên lịch phỏng vấn hàng loạt
                        </button>
                        <button type="button" id="batchCompleteBtn" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#batchCompleteModal" style="display: none;">
                            <i class="fas fa-clipboard-check me-1"></i> Điểm danh hàng loạt
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">
                            <i class="fas fa-times me-1"></i> Bỏ chọn
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                        </th>
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
                            <td>
                                @if(!in_array($request->status, ['approved', 'rejected', 'cancelled']))
                                    <input type="checkbox" class="request-checkbox" value="{{ $request->id }}" 
                                           data-status="{{ $request->status }}"
                                           onchange="updateBatchActions()">
                                @endif
                            </td>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $request->user->name ?? 'N/A' }}</td>
                            <td>{{ $request->user->email ?? 'N/A' }}</td>
                            <td>{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                @switch($request->status)
                                    @case('pending_interview')
                                        <span class="badge bg-warning text-dark">Chờ lên lịch phỏng vấn</span>
                                        @break
                                    @case('waiting_attendance')
                                        <span class="badge bg-info text-dark">Chờ điểm danh</span>
                                        @break
                                    @case('waiting_approval')
                                        <span class="badge bg-secondary">Chờ duyệt</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-success">Đã duyệt</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Từ chối</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-dark">Đã hủy</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">Không xác định</span>
                                @endswitch
                            </td>
                            <td>
                                @if(!in_array($request->status, ['approved', 'rejected', 'cancelled']))
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas"
                                        data-bs-target="#clubRequestDetail{{ $request->id }}"
                                        onclick="loadRequestDetail({{ $request->id }})">
                                        <i class="fas fa-cog me-1"></i> Xử lý yêu cầu
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="offcanvas"
                                        data-bs-target="#clubRequestDetail{{ $request->id }}"
                                        onclick="loadRequestDetail({{ $request->id }})">
                                        <i class="fas fa-eye me-1"></i> Xem chi tiết
                                    </button>
                                @endif

                                {{-- Offcanvas --}}
                                <div class="offcanvas offcanvas-end border-0 shadow-lg rounded-4" tabindex="-1"
                                    id="clubRequestDetail{{ $request->id }}" style="width: 80%; background-color: #f8f9fa;">
                                    <div class="offcanvas-header px-4 pt-4 pb-2 border-bottom">
                                        <h5 class="offcanvas-title fw-semibold">Chi tiết yêu cầu CLB</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body px-4 pb-4" id="requestDetailContent{{ $request->id }}">
                                        <div class="text-center text-muted py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Chưa có yêu cầu tham gia nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Batch Schedule Modal --}}
    <div class="modal fade" id="batchScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lên lịch phỏng vấn hàng loạt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('club_manager.member_requests.batch_schedule', ['club_id' => $club->id]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="request_ids" id="batchScheduleRequestIds">
                        <div class="mb-3">
                            <label class="form-label">Người phỏng vấn <span class="text-danger">*</span></label>
                            <select name="interviewer_id" class="form-select" data-select2="true" required>
                                <option value="">-- Chọn người phỏng vấn --</option>
                                @foreach($interviewers as $interviewer)
                                    <option value="{{ $interviewer->id }}">{{ $interviewer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thời gian phỏng vấn <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" placeholder="Nhập địa điểm phỏng vấn" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú gửi ứng viên</label>
                            <textarea name="interview_note" class="form-control" rows="2" placeholder="Ghi chú về buổi phỏng vấn..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lên lịch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Batch Complete Modal --}}
    <div class="modal fade" id="batchCompleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Điểm danh phỏng vấn hàng loạt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('club_manager.member_requests.batch_complete', ['club_id' => $club->id]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="request_ids" id="batchCompleteRequestIds">
                        <div class="mb-3">
                            <label class="form-label">Kết quả phỏng vấn <span class="text-danger">*</span></label>
                            <select name="interview_result" class="form-select" data-select2="true" required>
                                <option value="pass">Đạt (Pass)</option>
                                <option value="fail">Không đạt (Fail)</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="no_show">Vắng mặt</option>
                                <option value="cancelled">Hủy</option>
                            </select>
                        </div>
                       
                        <div class="mb-3">
                            <label class="form-label">Nhận xét / Đánh giá</label>
                            <textarea name="interview_feedback" class="form-control" rows="3" placeholder="Nhập nhận xét về ứng viên..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-warning">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loadRequestDetail(requestId) {
            const contentDiv = document.getElementById('requestDetailContent' + requestId);
            if (contentDiv.innerHTML.trim() === '' || contentDiv.querySelector('.spinner-border')) {
                fetch(`{{ route('club_manager.member_requests.show', ['club_id' => $club->id, 'request_id' => '__ID__']) }}`.replace('__ID__', requestId))
                    .then(response => response.text())
                    .then(html => {
                        contentDiv.innerHTML = html;
                        // Reinitialize any scripts if needed
                    })
                    .catch(error => {
                        contentDiv.innerHTML = '<div class="alert alert-danger">Không thể tải chi tiết yêu cầu.</div>';
                    });
            }
        }

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.request-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBatchActions();
        }

        function updateBatchActions() {
            const checked = document.querySelectorAll('.request-checkbox:checked');
            const panel = document.getElementById('batchActionsPanel');
            const count = document.getElementById('selectedCount');
            const scheduleBtn = document.getElementById('batchScheduleBtn');
            const completeBtn = document.getElementById('batchCompleteBtn');
            
            if (checked.length > 0) {
                panel.style.display = 'block';
                count.textContent = checked.length;
                
                const ids = Array.from(checked).map(cb => cb.value);
                document.getElementById('batchScheduleRequestIds').value = JSON.stringify(ids);
                document.getElementById('batchCompleteRequestIds').value = JSON.stringify(ids);
                
                // Kiểm tra trạng thái của các request đã chọn
                let hasPendingInterview = false;
                let hasWaitingAttendance = false;
                
                checked.forEach(cb => {
                    const status = cb.getAttribute('data-status');
                    if (status === 'pending_interview') {
                        hasPendingInterview = true;
                    }
                    if (status === 'waiting_attendance') {
                        hasWaitingAttendance = true;
                    }
                });
                
                // Hiển thị/ẩn các nút dựa trên trạng thái
                if (scheduleBtn) {
                    scheduleBtn.style.display = hasPendingInterview ? 'inline-block' : 'none';
                }
                if (completeBtn) {
                    completeBtn.style.display = hasWaitingAttendance ? 'inline-block' : 'none';
                }
            } else {
                panel.style.display = 'none';
            }
        }

        function clearSelection() {
            document.querySelectorAll('.request-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBatchActions();
        }

        // Handle AJAX form submissions
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.ajax-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const url = this.action;
                    const reload = this.dataset.reload === 'true';

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (reload) {
                                location.reload();
                            } else {
                                alert(data.message || 'Thành công!');
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra!');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xử lý yêu cầu!');
                    });
                });
            });
        });
    </script>
@endsection
