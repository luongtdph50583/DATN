@extends('admin.layouts.app')

@section('title', 'Chi tiết Sự kiện #' . $event->id)

@section('card-body')
<div class="container-fluid py-4">

    {{-- Thông báo --}}
    @foreach (['success', 'error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

        <!-- NGÂN SÁCH CHI TIẾT -->
<div class="col-lg-4">
    <div class="card shadow-sm h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-money-bill-wave text-success"></i> Ngân sách chi tiết
            </h5>
          
        </div>
        <div class="card-body">

            <!-- Tổng quan nhanh -->
            <div class="row text-center mb-4 pb-3 border-bottom">
                <div class="col-4">
                    <small class="text-muted">Tổng dự kiến</small>
                    <h5 class="text-primary fw-bold mb-0">{{ number_format($event->budgetItems->sum('estimated_cost')) }}đ</h5>
                </div>
                <div class="col-4">
                    <small class="text-muted">Xin cấp trường</small>
                    <h5 class="text-warning fw-bold mb-0">{{ number_format($event->budgetItems->where('type', 'school_fund')->sum('estimated_cost')) }}đ</h5>
                </div>
                <div class="col-4">
                    <small class="text-muted">CLB tự chi</small>
                    <h5 class="text-info fw-bold mb-0">{{ number_format($event->budgetItems->where('type', 'club_fund')->sum('estimated_cost')) }}đ</h5>
                </div>
            </div>

            <!-- Danh sách chi tiết -->
            @if($event->budgetItems->count())
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Đầu mục</th>
                                <th class="text-end">Dự kiến</th>
                                <th class="text-center">Nguồn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->budgetItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->item_name }}</strong>
                                        @if($item->description)
                                            <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($item->estimated_cost) }}đ
                                    </td>
                                    <td class="text-center">
                                        @if($item->type === 'school_fund')
                                            <span class="badge bg-warning text-dark">Trường</span>
                                        @elseif($item->type === 'club_fund')
                                            <span class="badge bg-info">CLB</span>
                                        @else
                                            <span class="badge bg-secondary">Khác</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>TỔNG CỘNG</td>
                                <td class="text-end text-primary">
                                    {{ number_format($event->budgetItems->sum('estimated_cost')) }}đ
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-file-invoice-dollar fa-3x mb-3 opacity-50"></i>
                    <p>Chưa có đầu mục chi tiêu nào</p>

                </div>
            @endif
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
                            @foreach($event->registrations as $reg)
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

                        <div class="mt-2">
                          {{ $registrations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>Chưa có người đăng ký</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

      <div class="row g-4 mt-2">
    <!-- Giao dịch quỹ sự kiện -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-exchange-alt text-warning"></i> Giao dịch quỹ ({{ $event->funRequests->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                @if ($event->funRequests->count())
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Người tạo</th>
                                    <th>Số tiền yêu cầu</th>
                                    <th>Số tiền duyệt</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                    <th>Người duyệt</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($funRequests as $index => $req)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ optional($req->requestedBy)->name ?? 'Không rõ' }}</td>
                                        <td class="text-end">{{ number_format($req->amount_requested ?? 0, 0, ',', '.') }} VNĐ</td>
                                        <td class="text-end">{{ number_format($req->approved_amount ?? 0, 0, ',', '.') }} VNĐ</td>
                                        <td>{{ Str::limit($req->note ?? '-', 50) }}</td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'pending_disbursement' => ['label' => 'Chờ giải ngân', 'class' => 'bg-warning text-dark'],
                                                    'disbursing' => ['label' => 'Đang giải ngân', 'class' => 'bg-info text-white'],
                                                    'disbursed' => ['label' => 'Đã giải ngân', 'class' => 'bg-success text-white'],
                                                    'rejected' => ['label' => 'Từ chối', 'class' => 'bg-danger text-white'],
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusMap[$req->status]['class'] ?? 'bg-secondary' }}">
                                                {{ $statusMap[$req->status]['label'] ?? 'Không xác định' }}
                                            </span>
                                        </td>
                                        <td>{{ optional($req->approvedBy)->name ?? '-' }}</td>
                                        <td>{{ optional($req->created_at)?->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.event_fund_requests.show', $req) }}" 
                                               class="btn btn-info btn-sm" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2 px-2">
                            {{ $funRequests->links() }}
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary mt-3 text-center">
                        <i class="fas fa-info-circle me-2"></i>Chưa có yêu cầu quỹ nào cho sự kiện này.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Giao dịch CLB liên quan sự kiện -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-exchange-alt text-warning"></i> Giao dịch CLB cho sự kiện ({{ $clubTransactions->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                @if($clubTransactions->count())
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Người tạo</th>
                                    <th>Loại</th>
                                    <th>Số tiền</th>
                                    <th>Danh mục</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clubTransactions as $index => $tx)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $tx->creator->name ?? '-' }}</td>
                                        <td>{{ $tx->type === 'income' ? 'Thu' : 'Chi' }}</td>
                                        <td class="text-end">{{ number_format($tx->amount,0,',','.') }} đ</td>
                                        @php
$categoryLabels = [
    'membership_fee' => 'Hội phí',
    'donation' => 'Đóng góp',
    'other' => 'Khác',
    'event_expense' => 'Chi cho sự kiện',
];
@endphp

<td>
    @if($tx->custom_category)
        {{ $tx->custom_category }}
    @elseif($tx->category)
        {{ $categoryLabels[$tx->category] ?? $tx->category }}
    @else
        -
    @endif
</td>

                                        <td>{{ $tx->description }}</td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'pending' => ['label' => 'Chờ duyệt', 'class' => 'bg-warning text-dark'],
                                                    'approved' => ['label' => 'Đã duyệt', 'class' => 'bg-primary text-white'],
                                                    'in_progress' => ['label' => 'Đang thu/chi', 'class' => 'bg-info text-dark'],
                                                    'completed' => ['label' => 'Hoàn tất', 'class' => 'bg-success text-white'],
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusMap[$tx->status]['class'] ?? 'bg-secondary' }}">
                                                {{ $statusMap[$tx->status]['label'] ?? 'Không xác định' }}
                                            </span>
                                        </td>
                                        <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2 px-2">
                            {{-- Nếu muốn phân trang cho clubTransactions, dùng: {{ $clubTransactions->links() }} --}}
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary text-center m-3">
                        <i class="fas fa-info-circle me-2"></i>Chưa có giao dịch nào cho sự kiện này
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

        

        <!-- Media -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-images text-secondary"></i> Hình ảnh / Video sự kiện
                    </h5>
                </div>
                <div class="card-body">
                    @if($event->media->count())
                        <div class="row g-3">
                            @foreach($event->media as $media)
                                <div class="col-md-2 col-4">
                                    <div class="card border-0 shadow-sm">
                                        @if(Str::startsWith($media->file_type, 'image'))
                                            <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                 class="img-fluid rounded" 
                                                 style="height:100px; object-fit:cover; width:100%;" 
                                                 alt="{{ $media->file_name }}">
                                        @elseif(Str::startsWith($media->file_type, 'video'))
                                            <video controls 
                                                   class="w-100 rounded" 
                                                   style="height:100px; object-fit:cover;">
                                                <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ $media->file_type }}">
                                            </video>
                                        @endif
                                        <div class="card-body py-1 px-2 text-center">
                                            <small class="d-block text-truncate" title="{{ $media->file_name }}">{{ $media->file_name }}</small>
                                            <div class="mt-1">
                                                <a href="{{ asset('storage/' . $media->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Mở media">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <a href="{{ asset('storage/' . $media->file_path) }}" download class="btn btn-sm btn-outline-success" title="Tải xuống">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p>Chưa có hình ảnh / video nào</p>
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
                                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Xóa ngay
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
