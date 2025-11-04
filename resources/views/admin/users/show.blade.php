@extends('admin.layouts.app')

@section('title', 'Chi Tiết Người Dùng')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <!-- Avatar -->
            <div class="me-4">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle" width="80" height="80">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:80px;height:80px;font-size:32px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h1 class="h4 mb-1">{{ $user->name }}</h1>
                <div>
                    <span class="badge 
                        @if($user->role == 'admin') bg-warning text-dark
                        @elseif($user->role == 'club_manager') bg-info text-white
                        @else bg-secondary @endif me-2">
                        @switch($user->role)
                            @case('admin') Admin @break
                            @case('club_manager') Quản lý CLB @break
                            @default Thành viên
                        @endswitch
                    </span>
                    <span class="badge 
                        @if($user->status == 'active') bg-success @else bg-danger @endif">
                        {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row g-4">
        <!-- Thông tin cơ bản -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Thông Tin Cơ Bản</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4 fw-bold text-muted">ID</div>
                        <div class="col-sm-8">#{{ $user->id }}</div>
                        <hr class="my-2">
                        <div class="col-sm-4 fw-bold text-muted">Email</div>
                        <div class="col-sm-8">
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                        </div>
                        <hr class="my-2">
                        <div class="col-sm-4 fw-bold text-muted">Vai trò</div>
                        <div class="col-sm-8">
                            <span class="badge 
                                @if($user->role == 'admin') bg-warning text-dark
                                @elseif($user->role == 'club_manager') bg-info text-white
                                @else bg-secondary @endif">
                                @switch($user->role)
                                    @case('admin') Admin @break
                                    @case('club_manager') Quản lý CLB @break
                                    @default Thành viên
                                @endswitch
                            </span>
                        </div>
                        <hr class="my-2">
                        <div class="col-sm-4 fw-bold text-muted">Trạng thái</div>
                        <div class="col-sm-8">
                            <span class="badge 
                                @if($user->status == 'active') bg-success @else bg-danger @endif">
                                {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                            </span>
                        </div>
                        <hr class="my-2">
                        <div class="col-sm-4 fw-bold text-muted">Ngày đăng ký</div>
                        <div class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                        <hr class="my-2">
                        <div class="col-sm-4 fw-bold text-muted">Cập nhật lần cuối</div>
                        <div class="col-sm-8">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thao tác nhanh -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Thao Tác Nhanh</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn w-100 
                            {{ $user->status === 'active' ? 'btn-warning' : 'btn-success' }}"
                                onclick="return confirm('{{ $user->status === 'active' ? 'Khóa tài khoản này?' : 'Kích hoạt tài khoản này?' }}')">
                            <i class="fas fa-{{ $user->status === 'active' ? 'lock' : 'unlock' }} me-2"></i>
                            {{ $user->status === 'active' ? 'Khóa Tài Khoản' : 'Kích Hoạt' }}
                        </button>
                    </form>

                    @if(auth()->id() !== $user->id)
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-info w-100 mb-3">
                            <i class="fas fa-edit me-2"></i>Sửa Thông Tin
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Xóa tài khoản này? Dữ liệu sẽ bị xóa vĩnh viễn!');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash-alt me-2"></i>Xóa Tài Khoản
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-2"></i>
                            Bạn không thể xóa hoặc sửa tài khoản của chính mình.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection