@extends('client.layouts.app')
@section('title', 'Gửi thông báo - ' . $club->name)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="mb-1">Gửi thông báo tới thành viên</h2>
                        <p class="text-muted mb-0">CLB: {{ $club->name }}</p>
                    </div>
                    <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại quản lý CLB
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('club_manager.notifications.store', ['club_id' => $club->id]) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nội dung thông báo <span class="text-danger">*</span></label>
                                <textarea name="content_html" class="form-control" rows="8" placeholder="Nhập nội dung chi tiết" required>{{ old('content_html') }}</textarea>
                                <small class="text-muted">Có thể sử dụng xuống dòng, chữ đậm/ nghiêng cơ bản.</small>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Kênh gửi <span class="text-danger">*</span></label>
                                    <select name="send_via" class="form-select" required>
                                        <option value="database" {{ old('send_via') === 'database' ? 'selected' : '' }}>In-app (hiện ở chuông thông báo)</option>
                                        <option value="mail" {{ old('send_via') === 'mail' ? 'selected' : '' }}>Email</option>
                                        <option value="both" {{ old('send_via') === 'both' ? 'selected' : '' }}>Cả hai kênh</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Đối tượng nhận <span class="text-danger">*</span></label>
                                    <select name="target" id="targetSelect" class="form-select" required>
                                        <option value="all" {{ old('target') === 'all' ? 'selected' : '' }}>Tất cả thành viên CLB</option>
                                        <option value="role" {{ old('target') === 'role' ? 'selected' : '' }}>Theo chức vụ</option>
                                        <option value="custom" {{ old('target') === 'custom' ? 'selected' : '' }}>Chọn thành viên cụ thể</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3" id="roleWrapper" style="display: none;">
                                <label class="form-label">Chức vụ trong CLB</label>
                                <select name="role" class="form-select">
                                    <option value="">-- Chọn chức vụ --</option>
                                    @foreach($memberRoles as $roleKey => $label)
                                        <option value="{{ $roleKey }}" {{ old('role') === $roleKey ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-3" id="customWrapper" style="display: none;">
                                <label class="form-label">Chọn thành viên</label>
                                <select name="user_ids[]" class="form-select" multiple size="6">
                                    @foreach($members as $member)
                                        <option value="{{ $member['id'] }}"
                                            {{ collect(old('user_ids', []))->contains($member['id']) ? 'selected' : '' }}>
                                            {{ $member['name'] }} ({{ $member['email'] }}) - {{ $memberRoles[$member['role']] ?? 'Thành viên' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Giữ Ctrl (Windows) hoặc Command (Mac) để chọn nhiều người.</small>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="theme-btn">
                                    <i class="fas fa-paper-plane me-1"></i> Gửi thông báo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const targetSelect = document.getElementById('targetSelect');
            const roleWrapper = document.getElementById('roleWrapper');
            const customWrapper = document.getElementById('customWrapper');

            function toggleTargetFields() {
                const value = targetSelect.value;
                roleWrapper.style.display = value === 'role' ? 'block' : 'none';
                customWrapper.style.display = value === 'custom' ? 'block' : 'none';
            }

            targetSelect.addEventListener('change', toggleTargetFields);
            toggleTargetFields();
        })();
    </script>
@endsection


