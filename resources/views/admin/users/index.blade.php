@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Quản lý Người dùng</h1>
            <p class="text-muted small mb-0">Quản lý tài khoản hệ thống</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-center p-3 bg-primary text-white rounded shadow-sm">
                <div class="small">Tổng người dùng</div>
                <div class="fw-bold fs-5">{{ $users->total() }}</div>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm mới
            </a>
            <a href="{{ route('admin.users.deleted') }}" class="btn btn-danger">
                <i class="fas fa-trash-alt me-2"></i>Thùng rác
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tên</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên..." value="{{ request('name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Nhập email..." value="{{ request('email') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="club_manager" {{ request('role') == 'club_manager' ? 'selected' : '' }}>Quản lý CLB</option>
                            <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Thành viên</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                        @if(request()->filled(['name', 'email', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">ID</th>
                                <th class="text-center" style="width: 8%;">Avatar</th>
                                <th style="width: 18%;">Tên</th>
                                <th style="width: 22%;">Email</th>
                                <th style="width: 12%;">Vai trò</th>
                                <th style="width: 12%;">Trạng thái</th>
                                <th style="width: 10%;">Ngày tạo</th>
                                <th style="width: 13%;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>


                                    <!-- Avatar -->
                                    <td class="text-center">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle" width="36" height="36">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-weight:bold;font-size:14px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Tên -->
                                    <td class="fw-bold">{{ $user->name }}</td>

                                    <!-- Email -->
                                    <td>
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none text-primary">{{ $user->email }}</a>
                                    </td>

                                    <!-- Vai trò -->
                                    <td>
                                        <span class="badge 
                                            @if($user->role == 'admin') bg-warning text-dark
                                            @elseif($user->role == 'club_manager') bg-info text-white
                                            @else bg-secondary @endif
                                        ">
                                            @switch($user->role)
                                                @case('admin') Admin @break
                                                @case('club_manager') Quản lý CLB @break
                                                @default Thành viên
                                            @endswitch
                                        </span>
                                    </td>

                                    <!-- Trạng thái -->
                                    <td>
                                        <span class="badge 
                                            @if($user->status == 'active') bg-success
                                            @else bg-danger @endif
                                        ">
                                            {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                                        </span>
                                    </td>

                                    <!-- Ngày tạo -->
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>

                                    <!-- HÀNH ĐỘNG – ĐÃ THAY BẰNG MODAL XÓA MỀM -->
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- MỞ/KHÓA -->
                                            <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                        title="{{ $user->status === 'active' ? 'Khóa' : 'Mở' }}">
                                                    <i class="fas fa-{{ $user->status === 'active' ? 'lock' : 'lock-open' }}"></i>
                                                </button>
                                            </form>

                                            <!-- XEM -->
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- SỬA -->
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- XÓA MỀM -->
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal-{{ $user->id }}"
                                                    title="Xóa tài khoản">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy người dùng</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary mt-2">Xóa bộ lọc</a>
                </div>
            @endif
        </div>
    </div>

    {{-- ==================== MODAL XÓA MỀM – SIÊU ĐẸP ==================== --}}
    @foreach($users as $user)
    <div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">Xóa tài khoản người dùng</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-user-slash fa-3x text-danger"></i>
                            </div>
                            <h5>Bạn có chắc chắn muốn xóa?</h5>
                            <p class="text-muted">Tài khoản sẽ được chuyển vào thùng rác</p>
                        </div>

                        <div class="bg-light p-4 rounded border mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" class="rounded-circle" width="60" height="60">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:60px;height:60px;font-weight:bold;font-size:24px;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold fs-5">{{ $user->name }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                    <div class="small text-muted">
                                        Vai trò: 
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'warning' : ($user->role == 'club_manager' ? 'info' : 'secondary') }}">
                                            {{ $user->role == 'admin' ? 'Admin' : ($user->role == 'club_manager' ? 'Quản lý CLB' : 'Thành viên') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Lý do xóa <span class="text-danger">*</span></label>
                            <textarea name="delete_reason" class="form-control" rows="3" 
                                      placeholder="VD: Vi phạm nội quy, tài khoản giả mạo, không hoạt động..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger px-4">
                            Xóa tài khoản
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection