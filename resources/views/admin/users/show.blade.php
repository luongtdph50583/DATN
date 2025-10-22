@extends('admin.layouts.app')

@section('title', 'Chi Tiết Tài Khoản Người Dùng')

@section('card-body')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi Tiết Tài Khoản</h1>
        <a href="{{ route('admin.users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- User Info Card -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Cơ Bản</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-right">
                            <strong>ID:</strong>
                        </div>
                        <div class="col-md-9">{{ $user->id }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3 text-right">
                            <strong>Tên:</strong>
                        </div>
                        <div class="col-md-9">{{ $user->name }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3 text-right">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-md-9">{{ $user->email }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3 text-right">
                            <strong>Vai trò:</strong>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                {{ $user->role === 'admin' ? 'Quản Trị Viên' : 'Thành Viên' }}
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3 text-right">
                            <strong>Trạng thái:</strong>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-{{ $user->status ? 'success' : 'secondary' }}">
                                {{ $user->status ? 'Hoạt động' : 'Khóa' }}
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3 text-right">
<strong>Ngày đăng ký:</strong>
                        </div>
                        <div class="col-md-9">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3 text-right">
                            <strong>Cập nhật lần cuối:</strong>
                        </div>
                        <div class="col-md-9">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao Tác Nhanh</h6>
                </div>
                <div class="card-body">
                    @if($user->status)
                        <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Khóa tài khoản này?')">
                                <i class="fas fa-lock"></i> Khóa Tài Khoản
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Kích hoạt tài khoản này?')">
                                <i class="fas fa-unlock"></i> Kích Hoạt
                            </button>
                        </form>
                    @endif

                    @if(auth()->id() !== $user->id)
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-block mb-3">
                            <i class="fas fa-edit"></i> Sửa Thông Tin
                        </a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Xóa tài khoản này?')">
                                <i class="fas fa-trash"></i> Xóa Tài Khoản
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Clubs Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i> Câu Lạc Bộ
</h6>
                </div>
                <div class="card-body">
                    @if($user->clubs->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($user->clubs as $club)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $club->name }}
                                    <span class="badge badge-primary badge-pill">{{ $club->pivot->role ?? 'Thành viên' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Chưa tham gia câu lạc bộ nào</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Section (if user has posts) -->
    @if($user->posts && $user->posts->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-newspaper"></i> Bài Viết (5 gần nhất)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Ngày đăng</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->posts->take(5) as $post)
                                <tr>
                                    <td>{{ Str::limit($post->title, 30) }}</td>
                                    <td>{{ $post->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $post->status ? 'success' : 'secondary' }}">
                                            {{ $post->status ? 'Hiển thị' : 'Ẩn' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($user->posts->count() > 5)
                        <small class="text-muted">... và {{ $user->posts->count() - 5 }} bài viết khác</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection