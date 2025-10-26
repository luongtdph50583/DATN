@extends('admin.layouts.app')

@section('title', 'Chi Tiết Người Dùng')

@section('card-body')
<div class="modern-container">
    <!-- Header -->
    <div class="header-section">
        <div class="header-content">
            <div class="d-flex align-items-center">
                <!-- Avatar -->
                <div class="user-avatar-large me-4">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="avatar-img">
                    @else
                        <div class="avatar-fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    @endif
                </div>
                <div>
                    <h1 class="page-title mb-1">{{ $user->name }}</h1>
                    <p class="page-subtitle mb-0">
                        <span class="role-badge role-{{ $user->role }}">
                            @switch($user->role)
                                @case('admin') Admin @break
                                @case('club_manager') Quản lý CLB @break
                                @default Thành viên
                            @endswitch
                        </span>
                        <span class="status-badge status-{{ $user->status }} ms-2">
                            <span class="status-dot"></span>
                            {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                        </span>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Thông tin cơ bản -->
        <div class="col-lg-8">
            <div class="info-card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Cơ Bản</h6>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-label">ID</div>
                        <div class="info-value">#{{ $user->id }}</div>
                    </div>
                    <hr>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <a href="mailto:{{ $user->email }}" class="text-primary">{{ $user->email }}</a>
                        </div>
                    </div>
                    <hr>
                    <div class="info-row">
                        <div class="info-label">Vai trò</div>
                        <div class="info-value">
                            <span class="role-badge role-{{ $user->role }}">
                                @switch($user->role)
                                    @case('admin') Admin @break
                                    @case('club_manager') Quản lý CLB @break
                                    @default Thành viên
                                @endswitch
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="info-row">
                        <div class="info-label">Trạng thái</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $user->status }}">
                                <span class="status-dot"></span>
                                {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="info-row">
                        <div class="info-label">Ngày đăng ký</div>
                        <div class="info-value">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <hr>
                    <div class="info-row">
                        <div class="info-label">Cập nhật lần cuối</div>
                        <div class="info-value">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thao tác nhanh -->
        <div class="col-lg-4">
            <div class="action-card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thao Tác Nhanh</h6>
                </div>
                <div class="card-body">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-block w-100
                            {{ $user->status === 'active' ? 'btn-warning' : 'btn-success' }}"
                                onclick="return confirm('{{ $user->status === 'active' ? 'Khóa tài khoản này?' : 'Kích hoạt tài khoản này?' }}')">
                            {{ $user->status === 'active' ? 'Khóa Tài Khoản' : 'Kích Hoạt' }}
                        </button>
                    </form>

                    <!-- Chỉ cho phép sửa/xóa nếu không phải chính mình -->
                    @if(auth()->id() !== $user->id)
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-block w-100 mb-3">
                            Sửa Thông Tin
                        </a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                              onsubmit="return confirm('Xóa tài khoản này? Dữ liệu sẽ bị xóa vĩnh viễn!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block w-100">
                                Xóa Tài Khoản
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info small">
                            Bạn không thể xóa hoặc sửa tài khoản của chính mình.
                        </div>
                    @endif
                </div>
            </div>
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
        --dark: #1f2937;
        --light: #f9fafb;
        --border: #e5e7eb;
        --shadow: 0 10px 25px -3px rgba(0,0,0,0.1);
        --radius: 14px;
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
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid var(--primary);
        box-shadow: 0 4px 15px rgba(91, 94, 255, 0.3);
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-fallback {
        width: 100%;
        height: 100%;
        background: var(--primary);
        color: white;
        font-weight: bold;
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-title {
        font-size: 1.9rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 1rem;
    }

    .info-card, .action-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border: none;
    }

    .card-header h6 {
        margin: 0;
        font-weight: 600;
        font-size: 1rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        font-size: 1rem;
    }

    .info-label {
        font-weight: 600;
        color: #4b5563;
        min-width: 140px;
    }

    .info-value {
        color: var(--dark);
        font-weight: 500;
    }

    /* Badge Styles */
    .role-badge, .status-badge {
        padding: 0.4rem 0.9rem;
        border-radius: 1.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .role-admin { background: #fee2e2; color: #991b1b; }
    .role-club_manager { background: #dbeafe; color: #1e40af; }
    .role-member { background: #f3f4f6; color: #374151; }

    .status-active { background: #d1fae5; color: #065f46; }
    .status-inactive { background: #fee2e2; color: #991b1b; }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    /* Action Buttons */
    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-warning {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        border: none;
    }

    .btn-success {
        background: linear-gradient(135deg, #34d399, #10b981);
        color: white;
        border: none;
    }

    .btn-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, #f87171, #ef4444);
        color: white;
        border: none;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: var(--dark);
        border: none;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        .user-avatar-large {
            width: 70px;
            height: 70px;
        }
        .info-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.35rem;
        }
        .info-label {
            min-width: auto;
        }
    }
</style>
@endsection