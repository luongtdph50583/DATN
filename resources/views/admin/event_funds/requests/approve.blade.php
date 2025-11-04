@extends('admin.layouts.app')

@section('title', 'Duyệt yêu cầu cấp kinh phí')




@section('card-body')
<div class="container py-4">
    <div class="card card-custom">
        <div class="card-header-custom text-center">
            <h5>
                <i class="fas fa-file-signature me-2"></i>
                Duyệt yêu cầu: {{ $request->event->name }}
            </h5>
        </div>

        <div class="card-body p-4">

            <!-- THÔNG BÁO LỖI (khi hủy duyệt thất bại) -->
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            <!-- THÔNG BÁO THÀNH CÔNG -->
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <form action="{{ route('admin.event_fund_requests.approve', $request->id) }}" method="POST" class="mb-4">
                @csrf

                <!-- Sự kiện -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Sự kiện
                    </label>
                    <input type="text" class="form-control" value="{{ $request->event->name }}" readonly>
                </div>

                <!-- Số tiền yêu cầu -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-money-bill-wave me-2 text-success"></i>Số tiền yêu cầu
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">₫</span>
                        <input type="text" class="form-control currency" 
                               value="{{ number_format($request->amount_requested, 0, ',', '.') }}" readonly>
                    </div>
                </div>

                <!-- Số tiền được duyệt -->
                <div class="mb-4">
                    <label for="approved_amount" class="form-label">
                        <i class="fas fa-check-circle me-2 text-info"></i>Số tiền được duyệt (VNĐ)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">₫</span>
                        <input type="number" name="approved_amount" id="approved_amount" 
                               class="form-control currency @error('approved_amount') is-invalid @enderror"
                               value="{{ old('approved_amount', $request->approved_amount ?? $request->amount_requested) }}" 
                               min="0" step="1000" required>
                    </div>
                    @error('approved_amount')
                        <div class="invalid-feedback d-block mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Nút duyệt -->
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Duyệt yêu cầu
                    </button>
                </div>
            </form>

            <!-- Form hủy duyệt / từ chối -->
            <form action="{{ route('admin.event_fund_requests.reject', $request->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Bạn có chắc muốn hủy duyệt / từ chối yêu cầu này?')">
                    <i class="fas fa-times me-2"></i>Hủy duyệt / Từ chối
                </button>
            </form>

            <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .card-custom {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .card-header-custom {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        color: white;
        padding: 1.5rem;
        border: none;
    }
    .card-header-custom h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.4rem;
    }
    .form-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    .form-control[readonly] {
        background-color: #f3f4f6;
        color: #4b5563;
        cursor: not-allowed;
        border-color: #d1d5db;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    /* === NÚT TRÒN TRÒN ĐẸP === */
    .btn {
        border-radius: 50px !important; /* TRÒN HOÀN TOÀN */
        padding: 0.75rem 2rem;
        font-weight: 500;
        border: none;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* BỎ HOVER - CHỈ ĐỔI MÀU NHẸ */
    .btn:hover {
        transform: none !important; /* BỎ HOVER LÊN XUỐNG */
        opacity: 0.9;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-success {
        background: #10b981;
        color: white;
    }
    .btn-success:hover {
        background: #059669;
    }
    .btn-danger {
        background: #ef4444;
        color: white;
    }
    .btn-danger:hover {
        background: #dc2626;
    }
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    .btn-secondary:hover {
        background: #4b5563;
    }

    .alert {
        border-radius: 12px;
        padding: 1rem 1.25rem;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
        border: none;
    }
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }
    .alert i {
        margin-right: 0.5rem;
    }
    .input-group-text {
        background: #f3f4f6;
        border-right: none;
        color: #6b7280;
        border-radius: 12px 0 0 12px !important;
    }
    .form-control.currency {
        border-left: none;
        padding-left: 0;
        border-radius: 0 12px 12px 0 !important;
    }
    .form-control.currency:focus {
        z-index: 3;
    }

    /* Input group đẹp hơn */
    .input-group {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
</style>
@endsection