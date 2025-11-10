@extends('admin.layouts.app')

@section('title', 'Chi tiết Sự kiện #' . $event->id)

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="fas fa-calendar-check text-primary me-2"></i>
                {{ $event->name }}
            </h1>
            <p class="text-muted small mb-0">
                ID: #{{ $event->id }} • 
                <span class="badge {{ $event->status == 'approved' ? 'bg-success' : ($event->status == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ $event->status == 'pending' ? 'Chờ duyệt' : ($event->status == 'approved' ? 'Đã duyệt' : 'Bị từ chối') }}
                </span>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Sửa
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- THÔNG TIN CHÍNH -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-primary"></i> Thông tin sự kiện
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">CLB tổ chức</td>
                                    <td><span class="badge bg-info text-white">{{ $event->club?->name ?? '—' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Địa điểm</td>
                                    <td><i class="fas fa-map-marker-alt text-danger"></i> {{ $event->location }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Thời gian</td>
                                    <td>
                                        <i class="fas fa-clock text-primary"></i> 
                                        {{ $event->start_time?->format('d/m/Y H:i') }} → {{ $event->end_time?->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Giới hạn</td>
                                    <td>
                                        @if($event->max_participants)
                                            <span class="badge bg-primary text-white">{{ $event->max_participants }} người</span>
                                            <small class="text-muted">(đã đăng ký: {{ $event->registrations->count() }})</small>
                                        @else
                                            <span class="badge bg-success text-white">Không giới hạn</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Công khai</td>
                                    <td>
                                        <span class="badge {{ $event->is_public ? 'bg-success' : 'bg-secondary' }} text-white">
                                            {{ $event->is_public ? 'Công khai' : 'Nội bộ CLB' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary"><i class="fas fa-users"></i> Quản lý</h6>
                            <div class="mb-3">
                                <small class="text-muted">Người tạo</small><br>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width:40px;height:40px;">
                                        {{ substr($event->createdBy?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $event->createdBy?->name ?? 'Hệ thống' }}</strong><br>
                                        <small class="text-muted">{{ $event->createdBy?->email ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Người duyệt</small><br>
                                @if($event->approvalBy)
                                    <div class="d-flex align-items-center gap-2 text-success">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>{{ $event->approvalBy->name }}</strong>
                                    </div>
                                @else
                                    <span class="text-warning"><i class="fas fa-hourglass-half"></i> Chưa duyệt</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-primary"><i class="fas fa-align-left"></i> Mô tả</h6>
                    <div class="p-3 bg-light rounded">
                        {!! $event->description ? nl2br(e($event->description)) : '<em class="text-muted">Chưa có mô tả</em>' !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- NGÂN SÁCH -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-money-bill-wave text-success"></i> Ngân sách
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-2">
                        <h6 class="text-primary">Dự kiến</h6>
                        <h4 class="text-success fw-bold">{{ number_format($event->budget_estimated ?? 0) }} VNĐ</h4>
                    </div>
                    <div class="mb-2">
                        <h6 class="text-primary">Xin cấp từ nhà trường</h6>
                        <h4 class="text-warning fw-bold">{{ number_format($event->budget_requested ?? 0) }} VNĐ</h4>
                    </div>
                    <div class="mb-2">
                        <h6 class="text-primary">Ngân sách CLB tự chi</h6>
                        <h4 class="text-info fw-bold">{{ number_format($event->budget_club ?? 0) }} VNĐ</h4>
                    </div>

                    <div class="progress mb-3" style="height: 30px;">
                        @php
                            $used = $event->transactions->sum('amount') ?? 0;
                            $percent = $event->budget_estimated > 0 ? ($used / $event->budget_estimated) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-danger" style="width: {{ $percent }}%">
                            {{ number_format($used) }}đ đã dùng
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NGƯỜI THAM GIA & GIAO DỊCH QUỸ -->
    <div class="row g-4 mt-2">
        <!-- Người tham gia -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-users text-info"></i> Người tham gia ({{ $event->registrations->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($event->registrations->count())
                        <div class="list-group list-group-flush">
                            @foreach($event->registrations->take(5) as $reg)
                                <div class="list-group-item d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width:40px;height:40px;">
                                        {{ substr($reg->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="ms-2">
                                        <strong>{{ $reg->user?->name ?? 'Ẩn danh' }}</strong><br>
                                        <small class="text-muted">Đăng ký: {{ $reg->created_at->format('d/m H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($event->registrations->count() > 5)
                            <div class="p-3 text-center border-top">
                                <small class="text-muted">... và {{ $event->registrations->count() - 5 }} người khác</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>Chưa có người đăng ký</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Giao dịch quỹ -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-exchange-alt text-warning"></i> Giao dịch quỹ ({{ $event->transactions->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($event->transactions->count())
                        <div class="list-group list-group-flush">
                            @foreach($event->transactions->take(5) as $t)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $t->type == 'income' ? '+' : '-' }}{{ number_format($t->amount) }}đ</strong><br>
                                            <small class="text-muted">{{ $t->description }}</small>
                                        </div>
                                        <small class="text-muted">{{ $t->created_at->format('d/m') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($event->transactions->count() > 5)
                            <div class="p-3 text-center border-top">
                                <small class="text-muted">... và {{ $event->transactions->count() - 5 }} giao dịch khác</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-money-bill fa-3x mb-3"></i>
                            <p>Chưa có giao dịch</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hình ảnh sự kiện -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-images text-secondary"></i> Hình ảnh sự kiện
                    </h5>
                </div>
                <div class="card-body">
                    @if($event->media_id)
                        <img src="{{ $event->poster_url }}" class="img-fluid rounded shadow" alt="Poster">
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p>Chưa có hình ảnh</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Nút xóa mềm -->
    <div class="mt-4 text-end">
        <button type="button" class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#softDeleteModal">
            <i class="fas fa-trash-alt"></i> Xóa sự kiện
        </button>
    </div>
</div>

<!-- Modal Xóa mềm -->
<div class="modal fade" id="softDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.events.softdelete', $event) }}" method="POST">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa 
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Bạn có chắc muốn <strong class="text-danger">xóa mềm</strong> sự kiện:</p>
                    <h5 class="text-primary text-center">{{ $event->name }}</h5>
                    <div class="mt-4">
                        <label class="form-label fw-bold text-danger">
                            <i class="fas fa-edit"></i> Lý do xóa <span class="text-danger">*</span>
                        </label>
                        <textarea name="delete_reason" class="form-control" rows="4" required
                                  placeholder="Vui lòng nhập lý do xóa (bắt buộc để khôi phục sau này)"
                                  style="resize: none;"></textarea>
                    </div>
                    <div class="alert alert-info mt-3 small">
                        <i class="fas fa-info-circle"></i>
                        Sự kiện sẽ được chuyển vào thùng rác. Bạn có thể khôi phục bất cứ lúc nào!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Xóa  ngay
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
