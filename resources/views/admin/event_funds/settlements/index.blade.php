@extends('admin.layouts.app')

@section('title', 'Quyết toán quỹ sự kiện')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Danh sách quyết toán quỹ sự kiện</h1>
            <p class="text-muted small mb-0">Theo dõi và phê duyệt các bản quyết toán của CLB</p>
        </div>
        <a href="{{ route('admin.event_fund_settlements.create') }}" class="btn btn-primary">
            + Tạo quyết toán mới
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($settlements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:20%;">Sự kiện</th>
                                <th style="width:15%;">Tổng chi</th>
                                <th style="width:15%;">Chênh lệch</th>
                                <th style="width:12%;">Trạng thái</th>
                                <th style="width:15%;">Ngày tạo</th>
                                <th style="width:18%; text-align:center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settlements as $settlement)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $loop->iteration }}</span></td>
                                    <td class="fw-semibold">
                                        {{ $settlement->fundRequest->event->name ?? '—' }}
                                    </td>
                                    <td>{{ number_format($settlement->total_spent, 0, ',', '.') }}₫</td>
                                    <td>
                                        {{ $settlement->difference 
                                            ? number_format($settlement->difference, 0, ',', '.') . '₫' 
                                            : '-' }}
                                    </td>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                'pending_review' => ['label' => 'Chờ duyệt', 'class' => 'bg-warning text-dark'],
                                                'approved' => ['label' => 'Đã duyệt', 'class' => 'bg-success'],
                                                'needs_revision' => ['label' => 'Cần chỉnh sửa', 'class' => 'bg-danger'],
                                            ];
                                            $status = $statusLabels[$settlement->status] ?? ['label' => 'Không xác định', 'class' => 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>{{ $settlement->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.event_fund_settlements.show', $settlement->id) }}" 
                                               class="btn btn-sm btn-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.event_fund_settlements.edit', $settlement->id) }}" 
                                               class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($settlement->status !== 'approved')
                                                <form action="{{ route('admin.event_fund_settlements.approve', $settlement->id) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Xác nhận duyệt quyết toán này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.event_fund_settlements.destroy', $settlement->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa quyết toán này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

               
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có quyết toán nào</h5>
                    <a href="{{ route('admin.event_fund_settlements.create') }}" class="btn btn-primary mt-2">
                        Tạo quyết toán đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
