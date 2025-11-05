@extends('admin.layouts.app')

@section('title', 'Yêu cầu cấp kinh phí')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Danh sách yêu cầu cấp kinh phí</h1>
            <p class="text-muted small mb-0">Theo dõi và duyệt các yêu cầu cấp kinh phí cho sự kiện</p>
        </div>
        <a href="{{ route('admin.event_fund_requests.create') }}" class="btn btn-primary">
            + Tạo yêu cầu mới
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
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:20%;">Tên sự kiện</th>
                                <th style="width:15%;">Người yêu cầu</th>
                                <th style="width:15%;">Số tiền yêu cầu</th>
                                <th style="width:15%;">Số tiền được duyệt</th>
                                <th style="width:10%;">Trạng thái</th>
                                <th style="width:10%;">Ngày tạo</th>
                                <th style="width:15%; text-align:center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $loop->iteration }}</span></td>
                                    <td class="fw-semibold">{{ $req->event->name ?? '—' }}</td>
                                    <td>{{ $req->user->name ?? '—' }}</td>
                                    <td>{{ number_format($req->amount_requested, 0, ',', '.') }}₫</td>
                                    <td>{{ $req->approved_amount ? number_format($req->approved_amount, 0, ',', '.') . '₫' : '—' }}</td>

                                    @php
                                        $statusLabels = [
                                            'pending' => ['label' => 'Chờ duyệt', 'class' => 'bg-warning text-dark'],
                                            'approved' => ['label' => 'Đã duyệt', 'class' => 'bg-success'],
                                            'rejected' => ['label' => 'Từ chối', 'class' => 'bg-danger'],
                                        ];
                                        $status = $statusLabels[$req->status] ?? ['label' => 'Không xác định', 'class' => 'bg-secondary'];
                                    @endphp
                                    <td>
                                        <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                    </td>

                                    <td>{{ $req->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.event_fund_requests.show', $req->id) }}" 
                                               class="btn btn-sm btn-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('admin.event_fund_requests.edit', $req->id) }}" 
                                               class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if($req->status !== 'approved')
                                                <form action="{{ route('admin.event_fund_requests.approve', $req->id) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Xác nhận duyệt yêu cầu này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.event_fund_requests.destroy', $req->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu này?');">
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
                    <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có yêu cầu cấp kinh phí nào</h5>
                    <a href="{{ route('admin.event_fund_requests.create') }}" class="btn btn-primary mt-2">
                        Tạo yêu cầu đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
