@extends('admin.layouts.app')

@section('title', 'Chi tiết Sự kiện')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Chi tiết Sự kiện</h1>
                <p class="page-subtitle">Thông tin chi tiết về sự kiện</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Event Detail Card -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="event-icon">
                Calendar
            </div>
            <div class="detail-title">
                <h2 class="event-name">{{ $event->name }}</h2>
                <p class="event-location">
                    Location: {{ $event->location }}
                </p>
            </div>
            <div class="status-badge status-{{ $event->status }} ms-auto">
                <span class="status-dot"></span>
                {{ ucfirst($event->status) }}
            </div>
        </div>

        <div class="detail-body">
            <div class="row g-4">
                <!-- Cột 1 -->
                <div class="col-md-6">
                    <div class="info-group">
                        <label>ID Sự kiện</label>
                        <p>#{{ $event->id }}</p>
                    </div>

                    <div class="info-group">
                        <label>Câu lạc bộ</label>
                        <p>
                            @if($event->club)
                                <span class="badge bg-info text-dark">{{ $event->club->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Thời gian bắt đầu</label>
                        <p>
                            <strong>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('d/m/Y') : '—' }}</strong><br>
                            <small class="text-muted">{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '' }}</small>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Thời gian kết thúc</label>
                        <p>
                            <strong>{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('d/m/Y') : '—' }}</strong><br>
                            <small class="text-muted">{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '' }}</small>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Giới hạn tham gia</label>
                        <p>
                            @if($event->max_participants)
                                <span class="text-primary">{{ $event->max_participants }} người</span>
                            @else
                                <span class="text-muted">Không giới hạn</span>
                            @endif
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Công khai</label>
                        <p>
                            <span class="status-badge status-{{ $event->is_public ? 'active' : 'inactive' }}">
                                <span class="status-dot"></span>
                                {{ $event->is_public ? 'Công khai' : 'Nội bộ' }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Cột 2 -->
                <div class="col-md-6">
                    <div class="info-group">
                        <label>Mô tả chi tiết</label>
                        <p class="description-text">
                            {{ $event->description ?? '<em class="text-muted">Chưa có mô tả</em>' }}
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Người tạo</label>
                        <p>
                            <strong>{{ $event->createdBy->name ?? '—' }}</strong><br>
                            <small class="text-muted">{{ $event->createdBy->email ?? '' }}</small>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Người duyệt</label>
                        <p>
                            @if($event->approvalBy)
                                <strong>{{ $event->approvalBy->name }}</strong><br>
                                <small class="text-muted">{{ $event->approvalBy->email }}</small>
                            @else
                                <span class="text-muted">Chưa duyệt</span>
                            @endif
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Ngân sách</label>
                        <p>
                            @if($event->budget)
                                <strong class="text-success">{{ number_format($event->budget, 0, ',', '.') }} VNĐ</strong>
                            @else
                                <span class="text-muted">Chưa có</span>
                            @endif
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Thời gian tạo</label>
                        <p>{{ $event->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="info-group">
                        <label>Cập nhật lần cuối</label>
                        <p>{{ $event->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-footer">
            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning">
                Sửa sự kiện
            </a>
            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display:inline;"
                  onsubmit="return confirm('Xóa sự kiện này? Dữ liệu sẽ mất vĩnh viễn!');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Xóa sự kiện</button>
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #5b5eff;
        --primary-dark: #4a49d6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #0ea5e9;
        --dark: #1f2937;
        --light: #f9fafb;
        --border: #e5e7eb;
        --shadow: 0 10px 25px -3px rgba(0,0,0,0.1);
        --radius: 16px;
    }

    .modern-container {
        background: linear-gradient(135deg, #f0f4ff 0%, #e0eaff 100%);
        padding: 2rem;
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    .header-section {
        background: white;
        padding: 1.8rem 2rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-size: 1.9rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .page-subtitle {
        color: #6b7280;
        margin: 0.5rem 0 0;
        font-size: 0.95rem;
    }

    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: var(--dark);
        border: none;
    }

    .btn-warning {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        border: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, #f87171, var(--danger));
        color: white;
        border: none;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .detail-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .detail-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .event-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border: 3px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 700;
        backdrop-filter: blur(5px);
    }

    .detail-title h2 {
        font-size: 1.8rem;
        margin: 0;
        font-weight: 700;
    }

    .event-location {
        margin: 0.5rem 0 0;
        opacity: 0.9;
        font-size: 1rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-pending { background: rgba(251, 191, 36, 0.2); color: #d97706; }
    .status-approved { background: rgba(16, 185, 129, 0.2); color: #065f46; }
    .status-rejected { background: rgba(239, 68, 68, 0.2); color: #991b1b; }
    .status-active { background: rgba(16, 185, 129, 0.2); color: #065f46; }
    .status-inactive { background: rgba(239, 68, 68, 0.2); color: #991b1b; }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: currentColor;
    }

    .detail-body {
        padding: 2rem;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group label {
        display: block;
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.35rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-group p {
        margin: 0;
        font-size: 1.1rem;
        color: var(--dark);
        font-weight: 500;
    }

    .description-text {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        border-left: 4px solid var(--primary);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .detail-footer {
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.75rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .detail-header {
            flex-direction: column;
            text-align: center;
        }
        .detail-title h2 { font-size: 1.5rem; }
        .detail-footer { justify-content: center; }
    }
</style>
@endsection