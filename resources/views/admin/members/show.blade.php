@extends('admin.layouts.app')

@section('title', 'Chi tiết Thành viên')

@section('card-body')
    <div class="modern-container">
        <!-- Header -->
        <div class="header-section">
            <div class="header-content">
                <div>
                    <h1 class="page-title">Chi tiết Thành viên</h1>
                    <p class="page-subtitle">Thông tin đầy đủ hồ sơ thành viên</p>
                </div>
                <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">
                    Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- Member Detail Card -->
        <div class="detail-card">
            <div class="detail-header">
                <div class="avatar-placeholder">
                    {{ strtoupper(substr($member->user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="detail-title">
                    <h2 class="member-name">{{ $member->user->name ?? 'Chưa có tên' }}</h2>
                    <p class="member-email">
                        <i class="fas fa-envelope"></i> {{ $member->user->email ?? '—' }}
                    </p>
                </div>
                <div class="status-badge status-{{ $member->status }} ms-auto">
                    <span class="status-dot"></span>
                    {{ $member->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                </div>
            </div>

            <div class="detail-body">
                <div class="row g-4">
                    <!-- Cột 1 -->
                    <div class="col-md-6">
                        <div class="info-group">
                            <label>ID Thành viên</label>
                            <p>#{{ $member->id }}</p>
                        </div>
                        <div class="info-group">
                            <label>Mã số sinh viên (MSSV)</label>
                            <p>
                                <code>{{ $member->student_code ?? '—' }}</code>
                            </p>
                        </div>


                        <div class="info-group">
                            <label>Giới tính</label>
                            <p>{{ $member->gender ? ucfirst($member->gender === 'male' ? 'Nam' : ($member->gender === 'female' ? 'Nữ' : 'Khác')) : '—' }}
                            </p>
                        </div>

                        <div class="info-group">
                            <label>Ngày sinh</label>
                            <p>{{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '—' }}
                            </p>
                        </div>

                        <div class="info-group">
                            <label>Địa chỉ</label>
                            <p>{{ $member->address ?? '—' }}</p>
                        </div>

                        <div class="info-group">
                            <label>Khóa học</label>
                            <p><strong>{{ $member->course ?? '—' }}</strong></p>
                        </div>

                        <div class="info-group">
                            <label>Chuyên ngành</label>
                            <p>{{ $member->major ?? '—' }}</p>
                        </div>
                    </div>

                    <!-- Cột 2 -->
                    <div class="col-md-6">
                        <div class="info-group">
                            <label>Số CCCD</label>
                            <p><code>{{ $member->citizen_id ?? '—' }}</code></p>
                        </div>

                        <div class="info-group">
                            <label>Ngày cấp CCCD</label>
                            <p>{{ $member->issued_date ? \Carbon\Carbon::parse($member->issued_date)->format('d/m/Y') : '—' }}
                            </p>
                        </div>

                        <div class="info-group">
                            <label>Nơi cấp CCCD</label>
                            <p>{{ $member->issued_place ?? '—' }}</p>
                        </div>

                        <div class="info-group">
                            <label>Dân tộc</label>
                            <p>{{ $member->ethnicity ?? '—' }}</p>
                        </div>

                        <div class="info-group">
                            <label>Số điện thoại</label>
                            <p>
                                <a href="tel:{{ $member->phone }}" class="text-primary">
                                    {{ $member->phone ?? '—' }}
                                </a>
                            </p>
                        </div>

                        <div class="info-group">
                            <label>Thời gian tạo</label>
                            <p>{{ $member->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="info-group">
                            <label>Cập nhật lần cuối</label>
                            <p>{{ $member->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-footer">
                <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-warning">
                    Sửa thông tin
                </a>
                <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" style="display:inline;"
                    onsubmit="return confirm('Xóa thành viên này? Dữ liệu sẽ mất vĩnh viễn!');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa thành viên</button>
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
            --dark: #1f2937;
            --light: #f9fafb;
            --border: #e5e7eb;
            --shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
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

        .avatar-placeholder {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            backdrop-filter: blur(5px);
        }

        .detail-title h2 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: 700;
        }

        .member-email {
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

        .status-active {
            background: rgba(16, 185, 129, 0.2);
            color: #065f46;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #991b1b;
        }

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

        .info-group p code {
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 0.95rem;
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

        /* Responsive */
        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
                text-align: center;
            }

            .detail-title h2 {
                font-size: 1.5rem;
            }

            .detail-footer {
                justify-content: center;
            }
        }
    </style>
@endsection
