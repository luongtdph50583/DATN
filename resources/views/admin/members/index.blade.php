@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Quản lý Thành viên</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
            <form action="{{ route('admin.members.export.excel') }}" method="GET" class="d-inline">
                @csrf
                <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                <input type="hidden" name="club_id" value="{{ request('club_id') }}">
                <input type="hidden" name="role" value="{{ request('role') }}">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </button>
            </form>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                <tr>
                                    <td>{{ $member->id }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->phone ?? 'N/A' }}</td>
                                    <td>{{ $member->address ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($member->status) }}</td>
                                    <td>
                                        <!-- Nút Xem -->
                                        <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <!-- Nút Sửa -->
                                        <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <!-- Form Xóa -->
                                        <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" style="display:inline-block;"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5>Chưa có thành viên nào</h5>
                                            <p class="text-muted">Hãy thêm thành viên đầu tiên để bắt đầu quản lý.</p>
                                            <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Thêm thành viên
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection