<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa người dùng</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Sửa người dùng</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu (bỏ trống nếu không thay đổi)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Vai trò</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="club_manager" {{ $user->role === 'club_manager' ? 'selected' : '' }}>Quản lý CLB</option>
                    <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Thành viên</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="mb-3">
                <label for="student_id" class="form-label">Mã sinh viên</label>
                <input type="text" name="student_id" id="student_id" class="form-control" value="{{ old('student_id', $user->student_id) }}">
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Khoa</label>
                <input type="text" name="department" id="department" class="form-control" value="{{ old('department', $user->department) }}">
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Ảnh đại diện</label>
                <input type="file" name="avatar" id="avatar" class="form-control">
                @if ($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" width="100" class="mt-2">
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>