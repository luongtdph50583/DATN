@extends('admin.layouts.app')

@section('title', 'Chi tiết Sự kiện')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Chi tiết Sự kiện</h1>
                <p class="page-subtitle">Thông tin chi tiết về sự kiện #{{ $event->id }}</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Event Detail Card -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="event-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="detail-title">
                <h2 class="event-name">{{ $event->name }}</h2>
                <p class="event-location">
                    <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                </p>
            </div>
            <div class="status-badge status-{{ $event->status }} ms-auto">
                <span class="status-dot"></span>
                {{ ucfirst($event->status) }}
            </div>
        </div>

        <div class="detail-body">
            <div class="row g-4">
                <!-- Cột 1: Thông tin cơ bản -->
                <div class="col-lg-6">
                    <h5 class="section-title"><i class="fas fa-info-circle"></i> Thông tin cơ bản</h5>
                    
                    <div class="info-group">
                        <label>ID Sự kiện</label>
                        <p><strong>#{{ $event->id }}</strong></p>
                    </div>

                    <div class="info-group">
                        <label>Câu lạc bộ</label>
                        <p>
                            @if($event->club)
                                <span class="badge bg-info text-dark">
                                    <i class="fas fa-users"></i> {{ $event->club->name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Thời gian bắt đầu</label>
                        <p>
                            <strong>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('d/m/Y') : '—' }}</strong><br>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '' }}
                            </small>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Thời gian kết thúc</label>
                        <p>
                            <strong>{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('d/m/Y') : '—' }}</strong><br>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '' }}
                            </small>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Giới hạn tham gia</label>
                        <p>
                            @if($event->max_participants)
                                <span class="badge bg-primary">
                                    <i class="fas fa-user-friends"></i> {{ $event->max_participants }} người
                                </span>
                            @else
                                <span class="badge bg-success">Không giới hạn</span>
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

                    <div class="info-group">
                        <label>Mô tả</label>
                        <div class="description-text">
                            {{ $event->description ?? '<em class="text-muted">Chưa có mô tả</em>' }}
                        </div>
                    </div>
                </div>

                <!-- Cột 2: Quản lý & Ngân sách -->
                <div class="col-lg-6">
                    <h5 class="section-title"><i class="fas fa-cog"></i> Quản lý & Ngân sách</h5>

                    <div class="info-group">
                        <label>Người tạo</label>
                        <p>
                            <strong>{{ $event->createdBy->name ?? '—' }}</strong><br>
                            <small class="text-muted">
                                <i class="fas fa-envelope"></i> {{ $event->createdBy->email ?? '' }}
                            </small>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Người duyệt</label>
                        <p>
                            @if($event->approvalBy)
                                <strong class="text-success">{{ $event->approvalBy->name }}</strong><br>
                                <small class="text-muted">
                                    <i class="fas fa-envelope"></i> {{ $event->approvalBy->email }}
                                </small>
                            @else
                                <span class="text-muted">Chưa duyệt</span>
                            @endif
                        </p>
                    </div>

                    <!-- NGÂN SÁCH - ĐẦY ĐỦ THEO SCHEMA -->
                    <div class="info-group">
                        <label>Ngân sách ước tính</label>
                        <p>
                            <strong class="text-primary">
                                {{ number_format($event->budget_estimated ?? 0, 0, ',', '.') }} VNĐ
                            </strong>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Ngân sách hiện tại</label>
                        <p>
                            <strong class="text-info">
                                {{ number_format($event->budget_current ?? 0, 0, ',', '.') }} VNĐ
                            </strong>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Ngân sách thực dùng</label>
                        <p>
                            <strong class="text-warning">
                                {{ number_format($event->budget_used ?? 0, 0, ',', '.') }} VNĐ
                            </strong>
                        </p>
                    </div>

                    <!-- TIẾN ĐỘ NGÂN SÁCH -->
                    

                    <div class="info-group">
                        <label>Thời gian tạo</label>
                        <p><i class="fas fa-calendar-plus"></i> {{ $event->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="info-group">
                        <label>Cập nhật lần cuối</label>
                        <p><i class="fas fa-sync-alt"></i> {{ $event->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- Media Section -->
            @if($event->media_id)
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="section-title"><i class="fas fa-images"></i> Media đính kèm</h5>
                    <div class="media-preview">
                        <img src="{{ asset('storage/media/' . $event->media_id . '.jpg') }}" 
                             alt="Media" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="detail-footer">
            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Sửa sự kiện
            </a>
            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display:inline;"
                  onsubmit="return confirm('Xóa sự kiện này? Dữ liệu sẽ mất vĩnh viễn!');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Xóa sự kiện
                </button>
            </form>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary ms-auto">
                <i class="fas fa-list"></i> Danh sách
            </a>
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

    .page-title { font-size: 1.9rem; font-weight: 700; color: var(--dark); margin: 0; }
    .page-subtitle { color: #6b7280; margin: 0.5rem 0 0; font-size: 0.95rem; }

    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }

    .btn-secondary { background: #e5e7eb; color: var(--dark); border: none; }
    .btn-warning { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; border: none; }
    .btn-danger { background: linear-gradient(135deg, #f87171, var(--danger)); color: white; border: none; }

    .detail-card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }

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
        width: 70px; height: 70px; background: rgba(255,255,255,0.2);
        border: 3px solid white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; font-weight: 700;
    }

    .detail-title h2 { font-size: 1.8rem; margin: 0; font-weight: 700; }
    .event-location { margin: 0.5rem 0 0; opacity: 0.9; font-size: 1rem; }

    .status-badge {
        padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.875rem;
        font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;
    }

    .status-pending { background: rgba(251, 191, 36, 0.2); color: #d97706; }
    .status-approved { background: rgba(16, 185, 129, 0.2); color: #065f46; }
    .status-rejected { background: rgba(239, 68, 68, 0.2); color: #991b1b; }
    .status-active { background: rgba(16, 185, 129, 0.2); color: #065f46; }
    .status-inactive { background: rgba(239, 68, 68, 0.2); color: #991b1b; }

    .status-dot { width: 10px; height: 10px; border-radius: 50%; background: currentColor; }

    .detail-body { padding: 2rem; }

    .section-title {
        color: var(--primary); font-weight: 600; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .info-group { margin-bottom: 1.5rem; }
    .info-group label {
        display: block; font-size: 0.875rem; color: #6b7280;
        margin-bottom: 0.35rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-group p { margin: 0; font-size: 1.1rem; color: var(--dark); font-weight: 500; }

    .description-text {
        background: #f8f9fa; padding: 1rem; border-radius: 10px;
        border-left: 4px solid var(--primary); font-size: 0.95rem; line-height: 1.6;
    }

    /* Progress Bar */
    .progress-container { margin-top: 0.5rem; }
    .progress-bar {
        height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;
    }
    .progress-fill {
        height: 100%; background: linear-gradient(90deg, var(--primary), var(--success));
        border-radius: 4px; transition: width 0.3s ease;
    }

    /* Media */
    .media-preview img {
        max-height: 300px; object-fit: cover; border-radius: var(--radius);
    }

    .detail-footer {
        padding: 1.5rem 2rem; background: #f8f9fa; border-top: 1px solid var(--border);
        display: flex; justify-content: flex-start; gap: 1rem; flex-wrap: wrap;
    }

    .badge { font-size: 0.85rem; padding: 0.35rem 0.75rem; }

    /* Responsive */
    @media (max-width: 768px) {
        .detail-header { flex-direction: column; text-align: center; }
        .detail-title h2 { font-size: 1.5rem; }
        .detail-footer { justify-content: center; }
        .col-lg-6 { margin-bottom: 2rem; }
    }
</style>
@endsection