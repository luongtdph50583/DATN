@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu cấp kinh phí')
@section('card-title', 'Chi tiết yêu cầu cấp kinh phí')

@section('card-body')
<div class="container">
    <h2>Chi tiết yêu cầu cấp kinh phí</h2>

    <table class="table table-bordered">
        <tr>
            <th>Sự kiện</th>
            <td>{{ $request->event->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Số tiền yêu cầu</th>
            <td>{{ number_format($request->amount_requested) }} VNĐ</td>
        </tr>
        <tr>
    <th>Số tiền đã duyệt</th>
    <td>{{ isset($request->approved_amount) ? number_format($request->approved_amount) . ' VNĐ' : 'Chưa duyệt' }}</td>
</tr>
        <tr>
            <th>Người tạo</th>
               <td>{{ $request->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Trạng thái</th>
            <td>{{ $request->status }}</td>
        </tr>
        <tr>
            <th>Ngày tạo</th>
            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th>Ngày cập nhật</th>
            <td>{{ $request->updated_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
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
    .container {
        max-width: 900px;
    }
    h2 {
        color: #1e2937;
        font-weight: 600;
        margin-bottom: 1.75rem;
        font-size: 1.75rem;
    }
    .table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 2rem;
    }
    .table th {
        background: #f8fafc;
        color: #1e293b;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem 1.25rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .table td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        color: #374151;
        font-size: 1rem;
    }
    .table tr:hover {
        background-color: #f1f5f9;
    }
    .table tr:last-child td {
        border-bottom: none;
    }
    .table .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.8em;
        border-radius: 50px;
        font-weight: 500;
    }
    .badge-pending   { background: #fef3c7; color: #d97706; }
    .badge-approved  { background: #d1fae5; color: #059669; }
    .badge-rejected  { background: #fee2e2; color: #dc2626; }
    .btn {
        border-radius: 8px;
        padding: 0.65rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-secondary {
        background: #6b7280;
        border-color: #6b7280;
    }
    .btn-secondary:hover {
        background: #4b5563;
        border-color: #4b5563;
    }
    /* Icon nhỏ trong bảng */
    th i, td i {
        margin-right: 0.5rem;
        color: #64748b;
    }
    /* Responsive */
    @media (max-width: 768px) {
        .container { padding: 1rem; }
        h2 { font-size: 1.5rem; }
        .table th, .table td { padding: 0.75rem; font-size: 0.9rem; }
        .btn { padding: 0.5rem 1rem; font-size: 0.9rem; }
    }
</style>
@endsection
