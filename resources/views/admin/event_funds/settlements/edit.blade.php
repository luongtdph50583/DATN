@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa quyết toán')
@section('card-title', 'Chỉnh sửa quyết toán')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .receipt-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .receipt-item:hover {
        transform: translateY(-4px);
    }
    .receipt-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    .receipt-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 0.5rem;
        text-align: center;
        font-size: 0.8rem;
    }
    .remove-receipt {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .remove-receipt:hover {
        background: #dc2626;
    }
    .details-textarea {
        font-family: monospace;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('card-body')
<div class="container py-4">
    <form action="{{ route('admin.event_fund_settlements.update', $settlement->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Tên sự kiện (chỉ đọc) -->
        <div class="mb-4">
            <label class="form-label fw-bold text-secondary">Tên Sự Kiện</label>
            <p class="fs-5 mb-0">{{ $settlement->fundRequest->event->name ?? '—' }}</p>
        </div>

        <!-- Yêu cầu liên quan -->
        <div class="mb-4">
            <label for="fund_request_id" class="form-label fw-bold">Yêu cầu liên quan</label>
            <select name="fund_request_id" id="fund_request_id" class="form-select @error('fund_request_id') is-invalid @enderror" required>
                <option value="">-- Chọn yêu cầu --</option>
                @foreach($approvedRequests as $req)
                    <option value="{{ $req->id }}" {{ old('fund_request_id', $settlement->fund_request_id) == $req->id ? 'selected' : '' }}>
                        {{ $req->event->name ?? '—' }} - {{ number_format($req->amount_requested, 0, ',', '.') }}₫
                    </option>
                @endforeach
            </select>
            @error('fund_request_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tổng chi -->
        <div class="mb-4">
            <label for="total_spent" class="form-label fw-bold">Tổng chi</label>
            <input type="number" name="total_spent" id="total_spent" class="form-control @error('total_spent') is-invalid @enderror"
                   value="{{ old('total_spent', $settlement->total_spent) }}" min="0" step="1" required>
            @error('total_spent')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Chi tiết khoản chi (JSON) -->
        <div class="mb-4">
            <label for="details" class="form-label fw-bold">Chi tiết khoản chi (JSON)</label>
            <textarea name="details" id="details" class="form-control details-textarea @error('details') is-invalid @enderror" rows="6">{{
                old('details', is_array($settlement->details) ? json_encode($settlement->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]')
            }}</textarea>
            <div class="form-text">Ví dụ: <code>[{ "name": "Thuê sân", "amount": 5000000 }]</code></div>
            @error('details')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Upload hóa đơn mới -->
        <div class="mb-4">
            <label for="receipts" class="form-label fw-bold">Upload hóa đơn / chứng từ mới</label>
            <input type="file" name="receipts[]" id="receipts" class="form-control @error('receipts') is-invalid @enderror" multiple accept="image/*,application/pdf">
            @error('receipts')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Hóa đơn hiện tại (có thể xóa) -->
        @php
            $currentReceipts = is_array($settlement->receipts) ? $settlement->receipts : 
                              (is_string($settlement->receipts) ? json_decode($settlement->receipts, true) : []);
            $currentReceipts = is_array($currentReceipts) ? $currentReceipts : [];
        @endphp

        @if(count($currentReceipts) > 0)
            <div class="mb-4">
                <label class="form-label fw-bold">Hóa đơn hiện tại (đánh dấu để xóa)</label>
                <div class="row g-3">
                    @foreach($currentReceipts as $index => $file)
                        @php
                            $path = is_string($file) ? $file : ($file['path'] ?? null);
                            if (!$path) continue;
                            $url = asset('storage/' . $path);
                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $fullPath = storage_path('app/public/' . ltrim($path, '/'));
                        @endphp

                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="receipt-item">
                                @if($isImage && file_exists($fullPath))
                                    <a href="{{ $url }}" target="_blank">
                                        <img src="{{ $url }}" alt="Hóa đơn" class="receipt-img">
                                    </a>
                                @else
                                    <a href="{{ $url }}" target="_blank" class="d-block text-center p-3 bg-light text-decoration-none">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                        <div class="small text-muted text-truncate">{{ basename($path) }}</div>
                                    </a>
                                @endif

                                <div class="receipt-overlay">
                                    <small>{{ basename($path) }}</small>
                                </div>

                                <!-- Nút xóa -->
                                <button type="button" class="remove-receipt" "data-index="{{ $index }}">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- Hidden input để gửi danh sách file giữ lại -->
                                <input type="hidden" name="keep_receipts[{{ $index }}]" value="{{ $path }}" id="keep-{{ $index }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Trạng thái -->
        <div class="mb-4">
            <label for="status" class="form-label fw-bold">Trạng thái</label>
            <select name="status" id="status" class="form-select">
                <option value="pending_review" {{ old('status', $settlement->status) == 'pending_review' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ old('status', $settlement->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="needs_revision" {{ old('status', $settlement->status) == 'needs_revision' ? 'selected' : '' }}>Cần chỉnh sửa</option>
            </select>
        </div>

        <!-- Nút submit -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Cập nhật
            </button>
            <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* CSS tối ưu cho form chỉnh sửa quyết toán */
    .container {
        max-width: 1000px;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.75rem;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    /* Receipt items - nhỏ gọn hơn */
    .receipt-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        background: white;
        transition: all 0.2s ease;
        max-width: 160px;
        margin-bottom: 1rem;
    }

    .receipt-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    /* Ảnh nhỏ hơn */
    .receipt-img {
        width: 100%;
        height: 100px; /* Giảm từ 120px xuống 100px */
        object-fit: cover;
        display: block;
        cursor: pointer;
    }

    /* File icon cho PDF */
    .receipt-item a[href$=".pdf"] {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100px;
        text-decoration: none;
        color: #374151;
    }

    .receipt-item a[href$=".pdf"] i {
        font-size: 2rem;
        color: #dc2626;
        margin-bottom: 0.5rem;
    }

    .receipt-item a[href$=".pdf"] .small {
        font-size: 0.8rem;
        color: #6b7280;
        text-align: center;
        max-width: 100%;
        word-break: break-word;
    }

    /* Overlay tên file */
    .receipt-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        text-align: center;
        line-height: 1.2;
        max-height: 2rem;
        overflow: hidden;
    }

    /* Nút xóa nhỏ gọn */
    .remove-receipt {
        position: absolute;
        top: 4px;
        right: 4px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        opacity: 0;
        transition: all 0.2s ease;
    }

    .receipt-item:hover .remove-receipt {
        opacity: 1;
    }

    .remove-receipt:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    /* Trạng thái select đẹp hơn */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 16px 12px;
        padding-right: 2.5rem;
    }

    /* JSON textarea */
    .details-textarea {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        line-height: 1.4;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
    }

    .details-textarea:focus {
        background: white;
        border-color: #3b82f6;
    }

    .form-text {
        font-size: 0.85rem;
        color: #6b7280;
        background: #f3f4f6;
        padding: 0.5rem;
        border-radius: 4px;
        margin-top: 0.5rem;
    }

    .form-text code {
        background: #e5e7eb;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-size: 0.8rem;
    }

    /* Buttons */
    .btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: #3b82f6;
        border-color: #3b82f6;
    }

    .btn-primary:hover {
        background: #2563eb;
        border-color: #2563eb;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #6b7280;
        border-color: #6b7280;
    }

    .btn-secondary:hover {
        background: #4b5563;
        border-color: #4b5563;
    }

    /* File input */
    input[type="file"] {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.75rem;
        background: #f9fafb;
    }

    /* Responsive grid cho receipts */
    @media (max-width: 768px) {
        .receipt-item {
            max-width: 140px;
        }
        .receipt-img {
            height: 80px;
        }
        .row.g-3 {
            --bs-gutter-x: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .receipt-item {
            max-width: 120px;
        }
        .receipt-img {
            height: 70px;
        }
        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    /* Animation cho remove */
    .receipt-item.removed {
        opacity: 0.5;
        pointer-events: none;
        background: #f3f4f6;
    }

    .receipt-item.removed .remove-receipt {
        background: #10b981;
    }

    /* Validation styling */
    .is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.25);
    }

    .invalid-feedback {
        font-size: 0.85rem;
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        color: #ef4444;
    }
    .receipt-item.removed {
        opacity: 0.5 !important;
        pointer-events: none;
        background: #f3f4f6 !important;
        border-style: dashed;
    }
    .receipt-item.removed .remove-receipt {
        background: #10b981 !important;
        opacity: 1 !important;
    }
    
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-receipt').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.dataset.index; // cần thêm data-index="{{ $index }}"
            const input = document.getElementById('keep-' + index);
            if (!input) return;
            const col = input.closest('.col-md-3');
            if (!col) return;
            input.remove();
            col.style.display = 'none';
        });
    });
});
 window.removeReceipt = function(index) {
    const input = document.getElementById('keep-' + index);
    if (!input) return;

    const col = input.closest('.col-md-3');
    if (!col) return;

    // Xóa input → server không nhận file này nữa
    input.remove();

    // Ẩn tạm trên UI
    col.style.display = 'none';
};


    // Format JSON
    document.getElementById('details')?.addEventListener('blur', function () {
        try {
            const obj = JSON.parse(this.value);
            this.value = JSON.stringify(obj, null, 4);
        } catch (e) { /* ignore */ }
    });
</script>
@endsection