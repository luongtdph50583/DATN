@php
$status = $request->status;
// Xác định các hành động có thể thực hiện dựa trên trạng thái mới
$canSchedule = $status === 'pending_interview';
$canCompleteInterview = $status === 'waiting_attendance';
$canDecide = $status === 'waiting_approval';
$canCancel = in_array($status, ['pending_interview', 'waiting_attendance', 'waiting_approval']);
$latestSchedule = $request->interviewSchedules->sortByDesc('scheduled_at')->first();
@endphp

<div class="container py-3">
    <div class="mb-4">
        <h5 class="fw-semibold mb-3">Quy trình xử lý</h5>
        <div class="d-flex flex-column gap-3">
            @foreach($timeline as $step)
                <div class="d-flex gap-3 align-items-start">
                    <div class="rounded-circle d-flex align-items-center justify-content-center
                                    {{ $step['completed'] ? 'bg-success text-white' : 'bg-light text-muted' }}"
                        style="width:40px;height:40px;">
                        {{ $step['step'] }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <p class="fw-semibold mb-1">{{ $step['title'] }}</p>
                            @if(!empty($step['timestamp']))
                                <span class="badge bg-light text-dark">
                                    {{ optional($step['timestamp'])->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="mb-0 text-muted small">{{ $step['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
        @php
$statusMap = [
    'pending_interview' => 'Chờ phỏng vấn',
    'waiting_attendance' => 'Chờ điểm danh',
    'waiting_approval' => 'Chờ duyệt',
    'approved' => 'Đã duyệt',
    'rejected' => 'Bị từ chối',
    'cancelled' => 'Đã hủy',
];
        @endphp

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light fw-semibold">Thông tin ứng viên</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Họ tên:</strong> {{ $request->user->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $request->user->email }}</p>
                        <p class="mb-1"><strong>SĐT:</strong> {{ $request->user->member->phone ?? '—' }}</p>
                        <p class="mb-1"><strong>MSSV:</strong> {{ $request->user->member->student_code ?? '—' }}</p>
                        <p class="mb-1"><strong>Giới tính:</strong> {{ $request->user->member->gender ?? '—' }}</p>
<p class="mb-1"><strong>Ngày sinh:</strong>
                            {{ $request->user->member->date_of_birth ? \Carbon\Carbon::parse($request->user->member->date_of_birth)->format('d/m/Y') : '—' }}
                        </p>
                        <p class="mb-1"><strong>Khóa:</strong> {{ $request->user->member->course ?? '—' }}</p>
                        <p class="mb-1"><strong>Chuyên ngành:</strong> {{ $request->user->member->major ?? '—' }}</p>
                        <p class="mb-0"><strong>Địa chỉ:</strong> {{ $request->user->member->address ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        {{-- <p class="mb-1"><strong>Lý do tham gia:</strong></p>
                        <p class="text-muted">{{ $request->reason ?? '—' }}</p> --}}
                        <p class="mb-1"><strong>Thời gian gửi:</strong>
                            {{ optional($request->requested_at)->format('d/m/Y H:i') }}
                        </p>
                        <p class="mb-0"><strong>Trạng thái hiện tại:</strong>
                            <span class="badge bg-primary-subtle text-primary text-uppercase">
                                {{ $statusMap[$request->status] ?? '—' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>


            @if($request->formAnswers->count())
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light fw-semibold">Câu trả lời biểu mẫu</div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($request->formAnswers as $answer)
                                <div class="list-group-item">
                                    <p class="fw-semibold mb-1">{{ $answer->question->question ?? 'Câu hỏi' }}</p>
                                    <p class="mb-0 text-muted">{!! nl2br(e($answer->answer ?? '—')) !!}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($request->membership_file))
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light fw-semibold">Đơn xin tham gia</div>
                    <div class="card-body">
                        <iframe src="{{ asset('storage/' . $request->membership_file) }}" width="100%" height="400"
                            class="border rounded"></iframe>
                    </div>
                </div>
            @endif

            @if($membership)
                <div class="alert alert-success">
                    Thành viên đã có trong danh sách CLB (tham gia ngày
{{ optional($membership->joined_at)->format('d/m/Y') }}).
                </div>
            @endif
        </div>

        <div class="col-lg-4">
        @if($request->interview_scheduled_at)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light fw-semibold">Thông tin phỏng vấn</div>
                <div class="card-body">
                    <p class="mb-1"><strong>Người phỏng vấn:</strong> {{ $request->interviewer?->name ?? '—' }}</p>
                    <p class="mb-1"><strong>Thời gian:</strong>
                        {{ optional($request->interview_scheduled_at)->format('d/m/Y H:i') }}
                    </p>
                    <p class="mb-1"><strong>Địa điểm:</strong> {{ $request->interview_location ?? '—' }}</p>
                    <p class="mb-1"><strong>Kết quả:</strong>
                        {{ $request->interview_result ? ucfirst($request->interview_result) : 'Chưa có' }}
                    </p>
                    <p class="mb-0"><strong>Ghi chú:</strong> {{ $request->interview_note ?? '—' }}</p>
                </div>
            </div>
        @endif


            @if($canSchedule)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light fw-semibold">
                        <i class="fas fa-calendar-alt me-2"></i>Sắp lịch phỏng vấn
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('club_manager.member_requests.handle', ['club_id' => $club->id, 'request_id' => $request->id]) }}"
                            class="ajax-form" data-reload="true">
                            @csrf
                            <input type="hidden" name="action" value="schedule">
                            <div class="mb-2">
                                <label class="form-label">Người phỏng vấn <span class="text-danger">*</span></label>
                                <select name="interviewer_id" class="form-select form-select-sm" data-select2="true" required>
                                    <option value="">-- Chọn người phỏng vấn --</option>
                                    @foreach($interviewers as $interviewer)
                                        <option value="{{ $interviewer->id }}"
                                            @selected($latestSchedule?->interviewer_id === $interviewer->id || $request->interviewer_id === $interviewer->id)>
                                            {{ $interviewer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        <div class="mb-2">
<label class="form-label">Thời gian phỏng vấn <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" class="form-control form-control-sm"
                                value="{{ optional($request->interview_scheduled_at)->format('Y-m-d\TH:i') }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                            
                            <div class="mb-2">
                                <label class="form-label">Ghi chú gửi ứng viên</label>
                                <textarea name="interview_note" class="form-control" rows="2"
                                    placeholder="Ghi chú về buổi phỏng vấn...">{{ $request->interview_note }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú nội bộ</label>
                                <textarea name="note" class="form-control" rows="2"
                                    placeholder="Ghi chú nội bộ (không gửi cho ứng viên)">{{ $request->note }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-save me-1"></i> Lưu lịch phỏng vấn
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($canCompleteInterview)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light fw-semibold">
                        <i class="fas fa-clipboard-check me-2"></i>Điểm danh / Đánh giá phỏng vấn
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('club_manager.member_requests.handle', ['club_id' => $club->id, 'request_id' => $request->id]) }}"
                            class="ajax-form" data-reload="true">
                            @csrf
                            <input type="hidden" name="action" value="complete_interview">
                            <div class="mb-2">
                                <label class="form-label">Kết quả phỏng vấn <span class="text-danger">*</span></label>
                                <select name="interview_result" class="form-select form-select-sm" required>
<option value="pass" @selected($request->interview_result === 'pass')>Đạt (Pass)
                                    </option>
                                    <option value="fail" @selected($request->interview_result === 'fail')>Không đạt (Fail)
                                    </option>
                                    <option value="no_show" @selected($request->interview_result === 'no_show')>Vắng mặt
                                    </option>
                                    <option value="cancelled" @selected($request->interview_result === 'cancelled')>Hủy
                                    </option>
                                </select>
                            </div>
                          
                            <div class="mb-2">
                                <label class="form-label">Nhận xét / Đánh giá</label>
                                <textarea name="interview_feedback" class="form-control" rows="3"
                                    placeholder="Nhập nhận xét về ứng viên...">{{ $request->interview_note }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú nội bộ</label>
                                <textarea name="note" class="form-control" rows="2"
                                    placeholder="Ghi chú nội bộ (không gửi cho ứng viên)">{{ $request->note }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-warning w-100">
                                <i class="fas fa-check-circle me-1"></i> Lưu kết quả phỏng vấn
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($canDecide)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-semibold">
                        <i class="fas fa-gavel me-2"></i>Ra quyết định duyệt / từ chối
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('club_manager.member_requests.handle', ['club_id' => $club->id, 'request_id' => $request->id]) }}"
                            class="ajax-form" data-reload="true">
                            @csrf
                            <div class="mb-3">
<label class="form-label">Ghi chú gửi ứng viên</label>
                                <textarea name="note" class="form-control" rows="3"
                                    placeholder="Nhập ghi chú gửi cho ứng viên (nếu có)">{{ $request->note }}</textarea>
                                <small class="text-muted">Ghi chú này sẽ được gửi cho ứng viên qua thông báo.</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" name="action" value="approve" class="btn btn-success flex-grow-1"
                                    onclick="return confirm('Bạn có chắc chắn muốn duyệt ứng viên này vào CLB?');">
                                    <i class="fas fa-check me-1"></i> Duyệt vào CLB
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger flex-grow-1"
                                    onclick="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu này?');">
                                    <i class="fas fa-times me-1"></i> Từ chối
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if($canCancel)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-semibold">
                        <i class="fas fa-ban me-2"></i>Hủy yêu cầu
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('club_manager.member_requests.handle', ['club_id' => $club->id, 'request_id' => $request->id]) }}"
                            class="ajax-form" data-reload="true"
                            onsubmit="return confirm('Bạn có chắc chắn muốn hủy yêu cầu này?');">
                            @csrf
                            <input type="hidden" name="action" value="cancel">
                            <div class="mb-3">
                                <label class="form-label">Lý do hủy</label>
                                <textarea name="note" class="form-control" rows="2"
                                    placeholder="Nhập lý do hủy yêu cầu (nếu có)">{{ $request->note }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-ban me-1"></i> Hủy yêu cầu
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if(in_array($status, ['approved', 'rejected', 'cancelled']))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
Yêu cầu đã được xử lý với trạng thái <strong class="text-uppercase">{{ $status }}</strong>.
                    @if($request->handled_at)
                        <br><small>Thời gian xử lý: {{ $request->handled_at->format('d/m/Y H:i') }}</small>
                    @endif
                    @if($request->handler)
                        <br><small>Người xử lý: {{ $request->handler->name }}</small>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>