@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu tham gia CLB')

@section('card-body')
<div class="container py-3">
    <h5 class="mb-3 text-primary fw-semibold">
        <i class="bi bi-person-plus-fill me-2"></i>Chi tiết yêu cầu tham gia CLB
    </h5>

    {{-- Thông tin yêu cầu --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light fw-bold">Thông tin yêu cầu</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 border-end">
                    <h6 class="fw-semibold text-secondary mb-3">Người gửi yêu cầu</h6>
                    <p><strong>Họ tên:</strong> {{ $request->user->name }}</p>
                    <p><strong>Email:</strong> {{ $request->user->email }}</p>
                    <p><strong>Lý do tham gia:</strong> {{ $request->reason ?? 'Không có' }}</p>
                    <p><strong>Ngày gửi:</strong> {{ $request->requested_at?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold text-secondary mb-3">Thông tin CLB</h6>
                    <p><strong>Tên CLB:</strong> {{ $request->club->name }}</p>
                    <p><strong>Lĩnh vực:</strong> {{ $request->club->field }}</p>
                    <p><strong>Người quản lý:</strong> {{ $request->club->manager->name ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Xử lý yêu cầu --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">Xử lý yêu cầu</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.club_join_requests.handle', $request->id) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Ghi chú của người xử lý</label>
                    <textarea name="note" class="form-control" rows="2">{{ old('note', $request->note) }}</textarea>
                </div>

                @switch($request->status)
                    @case('pending')
                        <div class="d-flex justify-content-end gap-2 mb-3 action-buttons">
                            <button type="button" class="btn btn-warning show-schedule-btn">
                                <i class="bi bi-calendar-event me-1"></i> Lên lịch phỏng vấn
                            </button>

                            <button type="submit" name="action" value="approve" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Duyệt ngay
                            </button> 

                            <button type="submit" name="action" value="reject" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i> Từ chối
                            </button>
                        </div>

                        <div class="schedule-form border-top pt-3 mt-3 d-none">
                            <h6 class="fw-semibold text-primary mb-3">
                                <i class="bi bi-calendar-check me-1"></i> Lên lịch phỏng vấn
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Người phỏng vấn</label>
                                    <select name="interviewer_id" class="form-select">
                                        <option value="">-- Chọn người phỏng vấn --</option>
                                        @foreach($interviewers as $interviewer)
                                            <option value="{{ $interviewer->id }}">{{ $interviewer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Thời gian phỏng vấn</label>
                                    <input type="datetime-local" name="scheduled_at" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Địa điểm</label>
                                    <input type="text" name="location" class="form-control" placeholder="Phòng, khu vực...">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Ghi chú phỏng vấn</label>
                                    <textarea name="interview_note" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-end gap-2">
                                <button type="submit" name="action" value="schedule" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Xác nhận
                                </button>
                                <button type="button" class="btn btn-secondary hide-schedule-btn">
                                    <i class="bi bi-x-lg me-1"></i> Hủy
                                </button>
                            </div>
                        </div>
                        @break

                    @case('scheduling_interview')
                        <div class="alert alert-warning mb-0">📅 Đã lên lịch phỏng vấn, chờ xác nhận.</div>
                        @break

                    @case('interview')
                        <button type="submit" name="action" value="complete_interview" class="btn btn-primary">
                            <i class="bi bi-check2-square me-1"></i> Đánh dấu đã phỏng vấn
                        </button>
                        @break

                    @case('interview_completed')
                        <button type="submit" name="action" value="approve" class="btn btn-success">Duyệt vào CLB</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Từ chối</button>
                        @break

                    @case('approved')
                        <div class="alert alert-success mb-0">✅ Đã duyệt — sinh viên đã trở thành thành viên CLB.</div>
                        @break

                    @case('rejected')
                        <div class="alert alert-danger mb-0">❌ Yêu cầu đã bị từ chối.</div>
                        @break

                    @case('cancelled')
                        <div class="alert alert-secondary mb-0">🚫 Yêu cầu đã bị hủy.</div>
                        @break
                @endswitch
            </form>
        </div>
    </div>
</div>
@endsection
