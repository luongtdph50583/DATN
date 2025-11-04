@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa yêu cầu cấp kinh phí')

@section('card-title', 'Chỉnh sửa yêu cầu cấp kinh phí')

@section('card-body')
<div class="container">
    <h2>Chỉnh sửa yêu cầu cấp kinh phí</h2>

    <form action="{{ route('admin.event_fund_requests.update', $request->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Sự kiện</label>
            <select name="event_id" class="form-control">
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $request->event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Số tiền yêu cầu</label>
            <input type="number" name="amount_requested" class="form-control" value="{{ $request->amount_requested }}" required>
        </div>
<!-- Số tiền đã duyệt (chỉ đọc) -->
<div class="mb-4">
    <label class="form-label fw-bold text-secondary">Số tiền đã duyệt</label>
    <p class="fs-5 mb-0">
        {{ isset($request->approved_amount) 
            ? number_format($request->approved_amount) . ' VNĐ' 
            : 'Chưa duyệt' }}
    </p>
</div>

       <div class="mb-3">
    <label>Trạng thái</label>
    <input type="text" class="form-control" value="{{ ucfirst($request->status) }}" readonly>
</div>


        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* === GỐC FILE TRƯỚC – ÁP DỤNG CHO FILE EDIT === */
    .container {
        max-width: 1000px;
        padding: 2rem 1rem;
    }
    
    .card-custom {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: white;
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
        display: flex;
        align-items: center;
    }
    
    .card-body-custom {
        padding: 2rem;
    }
    
    /* Form styling */
    .form-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    
    .form-control[readonly] {
        background-color: #f3f4f6;
        color: #4b5563;
        cursor: not-allowed;
        border-color: #d1d5db;
    }
    
    /* Input số tiền */
    .input-group-text {
        background: #f3f4f6;
        border-right: none;
        color: #6b7280;
        border-radius: 8px 0 0 8px;
    }
    
    .form-control.currency {
        border-left: none;
        border-radius: 0 8px 8px 0;
        padding-left: 0;
    }
    
    /* Readonly fields */
    .readonly-field {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-weight: 500;
    }
    
    /* Buttons */
    .btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .btn-primary {
        background: #3b82f6;
        border-color: #3b82f6;
    }
    
    .btn-primary:hover {
        background: #2563eb;
        border-color: #2563eb;
    }
    
    .btn-secondary {
        background: #6b7280;
        border-color: #6b7280;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        border-color: #4b5563;
    }
    
    /* Status display */
    .status-display {
        background: #f0f9ff;
        border: 1px solid #0ea5e9;
        border-radius: 6px;
        padding: 0.75rem;
        color: #0369a1;
        font-weight: 500;
    }
    
    /* Spacing */
    .mb-4 { margin-bottom: 1.5rem !important; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .card-body-custom {
            padding: 1.5rem;
        }
        .btn {
            width: 100%;
            margin-bottom: 0.75rem;
        }
    }
    
    /* Validation */
    .is-invalid {
        border-color: #ef4444 !important;
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
    }
</style>
@endsection
