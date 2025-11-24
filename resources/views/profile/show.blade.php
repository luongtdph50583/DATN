@extends('client.layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">

        <!-- Cột trái: Avatar + thông tin cơ bản -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin cơ bản</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4 position-relative">
                        <img id="avatarPreview" 
                             src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/default-avatar.png') }}" 
                             class="rounded-circle shadow-sm" 
                             width="120" height="120" 
                             alt="Avatar">

                        <!-- Biểu tượng bút -->
                        <label for="avatarInput" class="position-absolute" 
                               style="bottom:0; right:10px; cursor:pointer;">
                            <i class="fas fa-pencil-alt bg-primary text-white p-2 rounded-circle"></i>
                        </label>

                        <!-- Form upload ẩn -->
                        <form id="avatarForm" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" style="display:none;">
                            @csrf
                            <input type="file" name="avatar" id="avatarInput" accept="image/*">
                        </form>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-5 col-form-label">Họ và tên:</label>
                        <div class="col-sm-7">
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-5 col-form-label">Email:</label>
                        <div class="col-sm-7">
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-5 col-form-label">Vai trò:</label>
                        <div class="col-sm-7">
                            <p class="form-control-plaintext">{{ ucfirst($user->role) }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-5 col-form-label">Trạng thái:</label>
                        <div class="col-sm-7">
                            @if($user->status == 'active')
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Không hoạt động</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin chi tiết từ bảng members -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    @php
                        $gender = match($member->gender) {
                            'male' => 'Nam',
                            'female' => 'Nữ',
                            default => 'Khác'
                        };
                    @endphp

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Họ và tên:</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Mã sinh viên:</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">{{ $member->student_code ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Giới tính:</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">{{ $gender }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Ngày sinh:</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">{{ $member->date_of_birth ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Địa chỉ:</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">{{ $member->address ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Số CCCD:</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">{{ $member->citizen_id ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Số điện thoại:</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">{{ $member->phone ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('profile.edit') }}" class="btn btn-success">
                            <i class="fas fa-edit me-1"></i> Chỉnh sửa thông tin
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS preview + submit avatar -->
<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;

    // Preview ngay
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('avatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);

    // Submit form tự động
    document.getElementById('avatarForm').submit();
});
</script>
@endsection
