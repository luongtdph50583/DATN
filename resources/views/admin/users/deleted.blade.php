@extends('admin.layouts.app')

@section('title', 'Thùng rác người dùng')

@section('card-body')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4">Thùng rác người dùng</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Lý do xóa</th>
                                <th>Ngày xóa</th>
                                <th class="text-center" width="20%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="text-center">{{ $user->id }}</td>
                                <td class="fw-bold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge 
                                        @if($user->role == 'admin') bg-warning text-dark
                                        @elseif($user->role == 'club_manager') bg-info
                                        @else bg-secondary @endif
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td>{{ $user->delete_reason ?? 'Không có' }}</td>
                                <td>{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <!-- KHÔI PHỤC -->
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Khôi phục">
                                            <i class="fas fa-undo"></i> Khôi phục
                                        </button>
                                    </form>
                                    <!-- XÓA VĨNH VIỄN -->
                                    <form action="{{ route('admin.users.forceDelete', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Xóa vĩnh viễn {{ $user->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa vĩnh viễn">
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có người dùng nào trong thùng rác</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary mt-2">Quay lại danh sách</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
