@extends('admin.layouts.app')

@section('title', 'Chi tiết quyết toán')
@section('card-title', 'Chi tiết quyết toán')



@section('card-body')
<div class="settlement-container">
    <div class="settlement-card mx-auto px-4">
        
        <!-- HEADER -->
        <div class="card-header-custom text-center position-relative">
            <h1 class="header-title">
                <i class="fas fa-file-invoice-dollar me-3"></i>
                {{ $settlement->fundRequest->event->name ?? '—' }}
            </h1>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-request">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-amount text-success">
                    {{ number_format($settlement->fundRequest->amount_requested, 0, ',', '.') }}₫
                </div>
                <div class="stat-label">Yêu cầu liên quan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-spent">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-amount text-danger">
                    {{ number_format($settlement->total_spent, 0, ',', '.') }}₫
                </div>
                <div class="stat-label">Tổng chi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-diff">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="stat-amount {{ $settlement->difference >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $settlement->difference >= 0 ? '+' : '' }}{{ number_format($settlement->difference, 0, ',', '.') }}₫
                </div>
                <div class="stat-label">Chênh lệch</div>
            </div>
        </div>

        <!-- EXPENSES -->
        @php
            $details = $settlement->details;
            if (is_string($details)) $details = json_decode($details, true) ?? [];
            if (!is_array($details)) $details = [];
        @endphp
        
        <div class="section-header">
            <i class="fas fa-list-money text-primary"></i>
            <span>Chi tiết chi phí</span>
        </div>
        <div class="expense-list">
            @forelse($details as $item)
                <div class="expense-item">
                    <div class="expense-name">{{ $item['name'] ?? '—' }}</div>
                    <div class="expense-amount">
                        {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}₫
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                    <p>Chưa có chi tiết chi phí</p>
                </div>
            @endforelse
        </div>

        <!-- RECEIPTS -->
        @php
            $receipts = is_array($settlement->receipts) ? $settlement->receipts : json_decode($settlement->receipts, true) ?? [];
        @endphp
        
        <div class="section-header">
            <i class="fas fa-images text-purple-600"></i>
            <span>Hóa đơn / Chứng từ</span>
        </div>
        @if(count($receipts) > 0)
            <div class="receipts-grid">
                @foreach($receipts as $file)
                    <div class="receipt-item">
                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="d-block">
                            <img src="{{ asset('storage/' . $file) }}" 
                                 alt="Hóa đơn" 
                                 class="receipt-img"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI5MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+SG9hIGRvbjwvdGV4dD48L3N2Zz4='">
                            <div class="receipt-overlay">
                                <small>{{ basename($file) }}</small>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-file-invoice fa-4x mb-4 opacity-50"></i>
                <h5>Chưa upload chứng từ</h5>
            </div>
        @endif

        <!-- APPROVAL -->
        <div class="approval-section">
            <div class="approval-grid">
                @php
                    $statusMap = [
                        'pending_review' => ['label' => 'Chờ duyệt', 'class' => 'status-pending'],
                        'approved' => ['label' => 'Đã duyệt', 'class' => 'status-approved'],
                        'needs_revision' => ['label' => 'Cần chỉnh sửa', 'class' => 'status-revision'],
                    ];
                    $status = $statusMap[$settlement->status] ?? ['label' => 'Unknown', 'class' => 'status-pending'];
                @endphp
                
                <div>
                    <div class="status-badge {{ $status['class'] }} mb-4">
                        <i class="fas fa-circle-check"></i>
                        {{ $status['label'] }}
                    </div>
                </div>
                
                <div class="reviewer-info">
                    <i class="fas fa-user-circle fa-4x text-gray-300 mb-3"></i>
                    <h5>{{ $settlement->reviewer->name ?? 'Chưa có' }}</h5>
                    <p class="text-muted mb-2">Ngày duyệt</p>
                    <div class="stat-amount text-primary">
                        {{ $settlement->reviewed_at ? $settlement->reviewed_at->format('d/m/Y H:i') : '—' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER BUTTON -->
        <div class="p-5 text-center">
            <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>
                Quay lại danh sách
            </a>
        </div>
    </div>
</div>
<style>
    /* === CHI TIẾT QUYẾT TOÁN – NHẸ, SẠCH, KHÔNG DƯ THỪA === */
    .settlement-container {
        background: #f9fbfc;
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .settlement-card {
        max-width: 900px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    .card-header-custom {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        color: white;
        padding: 2.5rem 1.5rem;
        text-align: center;
    }

    .header-title {
        font-size: 1.8rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .header-title i {
        font-size: 2rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
        background: #f8fafc;
    }

    .stat-card {
        text-align: center;
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.3rem;
        color: white;
    }

    .bg-request { background: #10b981; }
    .bg-spent   { background: #ef4444; }
    .bg-diff    { background: #6366f1; }

    .stat-amount {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1.5rem 2rem 1rem;
        font-size: 1.2rem;
        font-weight: 600;
        color: #1e293b;
        border-bottom: 1px solid #e2e8f0;
    }

    .section-header i {
        font-size: 1.4rem;
    }

    /* Expense List */
    .expense-list {
        padding: 0 2rem 1.5rem;
    }

    .expense-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 1rem;
    }

    .expense-item:last-child {
        border-bottom: none;
    }

    .expense-name {
        color: #374151;
        font-weight: 500;
    }

    .expense-amount {
        color: #1e40af;
        font-weight: 600;
    }

    /* Empty State - Chi tiết chi phí */
    .text-center.py-8 {
        color: #94a3b8;
        font-style: italic;
        padding: 2.5rem 1rem;
        text-align: center;
    }

    .text-center.py-8 i {
        color: #cbd5e1;
        margin-bottom: 1rem;
        font-size: 2.5rem;
    }

    .text-center.py-8 p {
        margin: 0;
        font-size: 1rem;
    }

    /* Receipts Grid */
    .receipts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        padding: 1rem 2rem 2rem;
    }

    .receipt-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .receipt-item:hover {
        transform: translateY(-4px);
    }

    .receipt-img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
        background: #f1f5f9;
    }

    .receipt-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 0.5rem;
        text-align: center;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Empty State - Chứng từ */
    .text-center.py-12 {
        color: #94a3b8;
        font-style: italic;
        padding: 3rem 1rem;
        text-align: center;
    }

    .text-center.py-12 i {
        color: #cbd5e1;
        margin-bottom: 1.5rem;
        font-size: 3.5rem;
    }

    .text-center.py-12 h5 {
        color: #64748b;
        font-weight: 500;
        margin: 0;
        font-size: 1.1rem;
    }

    /* Approval Section */
    .approval-section {
        padding: 2rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .approval-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: center;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-pending   { background: #fef3c7; color: #d97706; }
    .status-approved  { background: #d1fae5; color: #059669; }
    .status-revision  { background: #fee2e2; color: #dc2626; }

    .reviewer-info {
        text-align: center;
    }

    .reviewer-info i {
        color: #cbd5e1;
    }

    .reviewer-info h5 {
        margin: 0.5rem 0;
        color: #1e293b;
        font-weight: 600;
    }

    .reviewer-info p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }

    .reviewer-info .stat-amount {
        color: #3b82f6;
        font-weight: 600;
    }

    /* Footer Button */
    .p-5.text-center {
        padding: 2rem !important;
        background: #f1f5f9;
        border-top: 1px solid #e2e8f0;
    }

    .btn-back {
        background: #64748b;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
    }

    .btn-back:hover {
        background: #475569;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(71, 85, 105, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .settlement-card {
            margin: 0 1rem;
            border-radius: 12px;
        }
        .stats-grid,
        .approval-grid {
            grid-template-columns: 1fr;
        }
        .header-title {
            font-size: 1.5rem;
            flex-direction: column;
        }
        .card-header-custom {
            padding: 2rem 1rem;
        }
        .section-header {
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
        }
        .receipts-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            padding: 1rem;
        }
        .receipt-img {
            height: 110px;
        }
    }

    @media (max-width: 576px) {
        .expense-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .expense-amount {
            align-self: flex-end;
        }
    }
</style>
@endsection


