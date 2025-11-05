@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa quyết toán')
@section('card-title', 'Chỉnh sửa quyết toán')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary: #5b5eff; --danger: #ef4444; --success: Use: #10b981; --gray: #6b7280;
        --light: #f9fafb; --border: #e5e7eb; --shadow: 0 4px 12px rgba(0,0,0,0.08); --radius: 12px;
    }

    .container { max-width: 1000px; padding: 1.5rem; }

    .form-label { font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.95rem; }
    .form-control, .form-select {
        border-radius: 10px; border: 1.5px solid var(--border); padding: 0.75rem 1rem;
        font-size: 0.95rem; transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(91,94,255,0.2);
    }

    .receipt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .receipt-item {
        position: relative;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        background: white;
        transition: all 0.3s ease;
        border: 1.5px solid var(--border);
        height: 140px;
    }

    .receipt-item:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }

    .receipt-img { width: 100%; height: 100%; object-fit: cover; }

    .receipt-pdf {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        height: 100%; background: #fdf2f8; color: var(--danger); text-decoration: none; padding: 1rem;
    }
    .receipt-pdf i { font-size: 2.5rem; margin-bottom: 0.5rem; }
    .receipt-pdf .file-name {
        font-size: 0.75rem; color: var(--gray); text-align: center; word-break: break-all;
        line-height: 1.3; max-height: 2.4em; overflow: hidden;
    }

    /* NÚT XÓA – LUÔN HIỆN */
    .remove-receipt {
        position: absolute; top: 8px; right: 8px;
        background: var(--danger); color: white; border: none; border-radius: 50%;
        width: 32px; height: 32px; font-size: 1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 6px rgba(239,68,68,0.3); transition: all 0.2s ease; z-index: 10;
    }
    .remove-receipt:hover { background: #dc2626; transform: scale(1.1); }

    /* ĐÃ XÓA */
    .receipt-item.removed {
        opacity: 0.6; background: #fee2e2 !important; border: 2px dashed #f87171 !important;
        pointer-events: none;
    }
    .receipt-item.removed .remove-receipt {
        background: var(--success) !important;
    }
    .receipt-item.removed .remove-receipt i:before { content: "\f00c"; }

    .btn { border-radius: 10px; padding: 0.75rem 1.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s ease; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), #4f46e5); border: none; color: white; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(91,94,255,0.3); }
    .btn-secondary { background: #94a3b8; color: white; border: none; }
    .btn-secondary:hover { background: #64748b; }

    input[type="file"] { border: 1.5px dashed var(--border); border-radius: 10px; padding: 1rem; background: #f8fafc; font-size: 0.9rem; }

    @media (max-width: 768px) {
        .receipt-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.75rem; }
        .receipt-item { height: 120px; }
        .remove-receipt { width: 28px; height: 28px; font-size: 0.9rem; }
    }
</style>
@endsection

@section('card-body')
<div class="container">
    <form action="{{ route('admin.event_fund_settlements.update', $settlement->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <!-- Tên sự kiện -->
        <div class="mb-4">
            <label class="form-label">Tên Sự Kiện</label>
            <p class="fs-5 fw-bold text-primary mb-0">{{ $settlement->fundRequest->event->name ?? '—' }}</p>
        </div>

        <!-- Yêu cầu liên quan -->
        <div class="mb-4">
            <label for="fund_request_id" class="form-label">Yêu cầu liên quan</label>
            <select name="fund_request_id" id="fund_request_id" class="form-select @error('fund_request_id') is-invalid @enderror" required>
                <option value="">-- Chọn yêu cầu --</option>
                @foreach($approvedRequests as $req)
                    <option value="{{ $req->id }}" {{ old('fund_request_id', $settlement->fund_request_id) == $req->id ? 'selected' : '' }}>
                        {{ $req->event->name }} - {{ number_format($req->amount_requested, 0, ',', '.') }}₫
                    </option>
                @endforeach
            </select>
            @error('fund_request_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Tổng chi -->
        <div class="mb-4">
            <label for="total_spent" class="form-label">Tổng chi</label>
            <input type="number" name="total_spent" id="total_spent" class="form-control @error('total_spent') is-invalid @enderror"
                   value="{{ old('total_spent', $settlement->total_spent) }}" min="0" step="1" required>
            @error('total_spent') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Chi tiết JSON -->
        <div class="mb-4">
            <label for="details" class="form-label">Chi tiết khoản chi (JSON)</label>
            <textarea name="details" id="details" class="form-control details-textarea @error('details') is-invalid @enderror" rows="6">{{
                old('details', is_array($settlement->details) ? json_encode($settlement->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]')
            }}</textarea>
            <div class="form-text">Ví dụ: <code>[{ "name": "Thuê sân", "amount": 5000000 }]</code></div>
            @error('details') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Upload mới -->
        <div class="mb-4">
            <label for="receipts" class="form-label">Upload hóa đơn / chứng từ mới</label>
            <input type="file" name="receipts[]" id="receipts" class="form-control @error('receipts') is-invalid @enderror" multiple accept="image/*,application/pdf">
            @error('receipts') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- HÓA ĐƠN HIỆN TẠI -->
        @php
            $currentReceipts = is_array($settlement->receipts) ? $settlement->receipts : 
                              (is_string($settlement->receipts) ? json_decode($settlement->receipts, true) : []);
            $currentReceipts = is_array($currentReceipts) ? $currentReceipts : [];
        @endphp

       @if(count($currentReceipts) > 0)
<div class="mb-4">
    <label class="form-label fw-bold">Hóa đơn hiện tại</label>
    <div class="row g-3">
        @foreach($currentReceipts as $file)
            @php
                $url = asset('storage/' . $file);
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            @endphp
            <div class="col-md-3 col-sm-4 col-6 receipt-wrapper">
                <div class="receipt-item">
                    @if($isImage)
                        <img src="{{ $url }}" alt="Hóa đơn" class="receipt-img">
                    @else
                        <a href="{{ $url }}" target="_blank" class="d-block text-center p-3 bg-light text-decoration-none">
                            <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                            <div class="small text-muted text-truncate">{{ basename($file) }}</div>
                        </a>
                    @endif

                    <!-- Checkbox overlay -->
                    <label class="checkbox-overlay">
                        <input type="checkbox" name="keep_receipts[]" value="{{ $file }}" checked>
                        Giữ
                    </label>

                    <div class="receipt-overlay">{{ basename($file) }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif


        <!-- Trạng thái -->
        <div class="mb-4">
            <label for="status" class="form-label">Trạng thái</label>
            <select name="status" id="status" class="form-select">
                <option value="pending_review" {{ old('status', $settlement->status) == 'pending_review' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ old('status', $settlement->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="needs_revision" {{ old('status', $settlement->status) == 'needs_revision' ? 'selected' : '' }}>Cần chỉnh sửa</option>
            </select>
        </div>

        <!-- Nút -->
        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Cập nhật
            </button>
            <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$oldReceipts = json_decode($settlement->receipts, true) ?? [];
$keepReceipts = $request->input('keep_receipts', []);

// Xóa file đã bỏ checkbox
foreach(array_diff($oldReceipts, $keepReceipts) as $file){
    Storage::disk('public')->delete($file);
}

// Cập nhật DB
$settlement->receipts = !empty($keepReceipts) ? json_encode($keepReceipts) : null;
$settlement->save();


    // Format JSON
    document.getElementById('details')?.addEventListener('blur', function () {
        try {
            const obj = JSON.parse(this.value);
            this.value = JSON.stringify(obj, null, 4);
        } catch (e) { /* ignore */ }
    });
</script>
@endsection