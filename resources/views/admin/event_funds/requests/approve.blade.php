@extends('admin.layouts.app')

@section('title', 'Duyệt yêu cầu cấp kinh phí')

@section('card-body')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h5>
                <i class="fas fa-file-signature me-2"></i>
                Duyệt yêu cầu: {{ $request->event->name }}
            </h5>
        </div>
        <div class="card-body">

            <!-- THÔNG TIN CHI TIẾT YÊU CẦU -->
            <h6 class="mb-3">Thông tin chi tiết yêu cầu</h6>
            <table class="table table-bordered mb-4">
                <tr>
                    <th>ID</th>
                    <td>{{ $request->id }}</td>
                </tr>
                <tr>
                    <th>Sự kiện</th>
                    <td>{{ $request->event->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Người yêu cầu</th>
                    <td>{{ $request->requestedBy->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Số tiền yêu cầu</th>
                    <td>{{ number_format($request->amount_requested) }} đ</td>
                </tr>
                <tr>
                    <th>Trạng thái sự kiện</th>
                    <td>
                        @switch($request->event->status)
                            @case('pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @break
                            @case('approved')
                                <span class="badge bg-success">Đã duyệt</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">Từ chối</span>
                                @break
                            @default
                                <span class="badge bg-secondary">N/A</span>
                        @endswitch
                    </td>
                </tr>
            </table>

            <!-- FORM DUYỆT -->
            <form action="{{ route('admin.event_fund_requests.approve', $request->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="approved_amount" class="form-label">Số tiền được duyệt (VNĐ)</label>
                    <input type="number" name="approved_amount" id="approved_amount"
                           class="form-control @error('approved_amount') is-invalid @enderror"
                           value="{{ old('approved_amount', $request->approved_amount ?? $request->amount_requested) }}"
                           min="0" step="1000" required>
                    @error('approved_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
          <div class="mb-3">
    <label for="disbursement_start" class="form-label">Thời gian giải ngân từ ngày</label>
    <input type="date" name="disbursement_start" id="disbursement_start"
           class="form-control @error('disbursement_start') is-invalid @enderror"
           value="{{ old('disbursement_start', $request->disbursement_start ? \Illuminate\Support\Carbon::parse($request->disbursement_start)->format('Y-m-d') : '') }}">
    @error('disbursement_start')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="disbursement_end" class="form-label">Đến ngày</label>
    <input type="date" name="disbursement_end" id="disbursement_end"
           class="form-control @error('disbursement_end') is-invalid @enderror"
           value="{{ old('disbursement_end', $request->disbursement_end ? \Illuminate\Support\Carbon::parse($request->disbursement_end)->format('Y-m-d') : '') }}">
    @error('disbursement_end')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>



                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check me-2"></i>Duyệt yêu cầu
                </button>
                <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </form>

            <!-- Nút từ chối -->
            <a href="{{ route('admin.event_fund_requests.reject', $request->id) }}" class="btn btn-danger mt-3">
                <i class="fas fa-times me-2"></i>Từ chối
            </a>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
