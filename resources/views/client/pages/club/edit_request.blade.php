@extends('client.layouts.app')
@section('title', 'Đề xuất sửa thông tin CLB - ' . $club->name)

@section('content')
                <div class="container mt-4 mb-5">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Đề xuất sửa thông tin CLB - {{ $club->name }}</h2>
                        <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>

                    @if($pendingRequest)
                        <div class="alert alert-info d-flex justify-content-between align-items-start">
                            <div>
                                Bạn đã có một đề xuất đang chờ duyệt. Vui lòng chờ admin xử lý.
                                <br><strong>Trạng thái:</strong> 
                                @if($pendingRequest->status === 'pending')
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @elseif($pendingRequest->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </div>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pendingRequestModal">
                                Xem chi tiết
                            </button>
                        </div>
                    @endif

                    @php
$managementRoles = [
    'club_manager' => 'Chủ nhiệm',
    'deputy_manager' => 'Phó chủ nhiệm',
    'secretary' => 'Thư ký',
    'treasurer' => 'Thủ quỹ',
    'event_manager' => 'Sự kiện',
    'communication' => 'Truyền thông',
];
                    @endphp

                    @if(!$pendingRequest || $pendingRequest->status !== 'pending')
                            <form action="{{ route('club_manager.edit_request.store', ['club_id' => $club->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Thông tin cơ bản</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tên CLB (hiện tại: {{ $club->name }})</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $club->name) }}" placeholder="Để trống nếu không đổi">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Khẩu hiệu (hiện tại: {{ $club->slogan ?? 'Chưa có' }})</label>
                                            <input type="text" name="slogan" class="form-control" value="{{ old('slogan', $club->slogan) }}" placeholder="Để trống nếu không đổi">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Mô tả</label>
                                            <textarea name="description" class="form-control" rows="4" placeholder="Để trống nếu không đổi">{{ old('description', $club->description) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Lĩnh vực hoạt động (hiện tại: {{ $club->field }})</label>
                                            <input type="text" name="field" class="form-control" value="{{ old('field', $club->field) }}" placeholder="Để trống nếu không đổi">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email (hiện tại: {{ $club->email ?? 'Chưa có' }})</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $club->email) }}" placeholder="Để trống nếu không đổi">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Số điện thoại (hiện tại: {{ $club->phone ?? 'Chưa có' }})</label>
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $club->phone) }}" placeholder="Để trống nếu không đổi">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Logo</label>
                                            <input type="file" name="logo" class="form-control" accept="image/*">
                                            @if($club->logo)
                                                <small class="text-muted">Logo hiện tại: {{ $club->logo }}</small>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Giới hạn thành viên (hiện tại: {{ $club->member_limit ?? 'Không giới hạn' }})</label>
                                            <input type="number" name="member_limit" class="form-control" value="{{ old('member_limit', $club->member_limit) }}" min="1" placeholder="Để trống nếu không đổi">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Địa điểm (hiện tại: {{ $club->location ?? 'Chưa có' }})</label>
                                            <input type="text" name="location" class="form-control" value="{{ old('location', $club->location) }}" placeholder="Để trống nếu không đổi">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nội quy</label>
                                            <textarea name="rules" class="form-control" rows="4" placeholder="Để trống nếu không đổi">{{ old('rules', $club->rules) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Chủ nhiệm </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Đề xuất Chủ nhiệm mới</label>
                                                <select name="manager_id" class="form-select" data-select2="true">
                                                    <option value="">Giữ nguyên</option>
                                                    @foreach($users as $userOption)
                                                        <option value="{{ $userOption->id }}" {{ (string) old('manager_id') === (string) $userOption->id ? 'selected' : '' }}>
                                                            {{ $userOption->name }} ({{ $userOption->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <label class="form-label">Đề xuất cố vấn</label>
                                                <select name="advisor_id" class="form-select" data-select2="true">
                                                    <option value="">Giữ nguyên</option>
                                                    @foreach($facultyMembers as $advisor)
                                                        <option value="{{ $advisor->id }}" {{ (string)old('advisor_id') === (string)$advisor->id ? 'selected' : '' }}>
                                                            {{ $advisor->user->name ?? 'GV' }} - {{ $advisor->department ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div> --}}
                                        {{-- </div>
                                    </div>
                                </div> --}} 

                  <div class="card mb-4">
    <div class="card-header ">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-people-fill me-2"></i>
                Đề xuất thay đổi ban quản lý
            </h5>
            <small class="opacity-75">Chọn thành viên trong CLB hoặc gỡ bỏ chức vụ</small>
        </div>
    </div>
    <div class="card-body p-0">
        @foreach($managementRoles as $roleKey => $roleLabel)
            @php
        // Lấy người đang giữ vai trò này
        $currentManager = $currentManagers->get($roleKey);
        $currentUserId = $currentManager?->member->user_id;
        $currentUser = $currentManager?->member->user;
        $currentMemberInfo = $currentManager?->member;
        $currentName = $currentUser?->name ?? 'Chưa phân công';

        // Giữ lại giá trị old nếu có lỗi validation
        $selectedUser = old("members.$roleKey.user_id");
            @endphp

            <div class="role-item {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="row g-0">
                    {{-- Cột 1: Chức vụ --}}
                    <div class="col-12 col-lg-3 bg-light d-flex align-items-center p-4 border-end">
                        <div class="w-100">
                            <h6 class="mb-1 text-primary">
                                <i class="bi bi-award-fill me-2"></i>
                                {{ $roleLabel }}
                            </h6>
                            <small class="text-muted">{{ $roleKey }}</small>
                        </div>
                    </div>

                    {{-- Cột 2: Người hiện tại --}}
                    <div class="col-12 col-lg-4 p-4 border-end">
                        @if($currentManager && $currentUser)
                            <div class="d-flex align-items-start h-100">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0 me-3">
                                    @if($currentUser->avatar)
                                        <img src="{{ asset('storage/' . $currentUser->avatar) }}" 
                                             alt="{{ $currentUser->name }}" 
                                             class="rounded-circle shadow-sm"
                                             style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #28a745;">
                                    @else
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm"
                                             style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold; border: 3px solid #28a745;">
                                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Thông tin chi tiết --}}
                                <div class="flex-grow-1">
                                    <div class="mb-2">
                                        <strong class="text-dark d-block">{{ $currentUser->name }}</strong>
                                        <span class="badge bg-success-subtle text-success border border-success mt-1">
                                            <i class="bi bi-check-circle-fill"></i> Đang giữ chức
                                        </span>
                                    </div>
                                    
                                    <div class="small">
                                        {{-- Email --}}
                                        <div class="mb-2 text-muted">
                                            <i class="bi bi-envelope-fill me-1 text-primary"></i>
                                            {{ $currentUser->email }}
                                        </div>

                                        {{-- Mã sinh viên --}}
                                        @if($currentMemberInfo->student_code)
                                            <div class="mb-2">
                                                <i class="bi bi-card-text me-1 text-info"></i>
                                                <strong>{{ $currentMemberInfo->student_code }}</strong>
                                            </div>
                                        @endif

                                        {{-- Khóa - Ngành --}}
                                        @if($currentMemberInfo->course || $currentMemberInfo->major)
                                            <div class="mb-2 text-muted">
                                                <i class="bi bi-mortarboard-fill me-1 text-warning"></i>
                                                @if($currentMemberInfo->course)
                                                    K{{ $currentMemberInfo->course }}
                                                @endif
                                                @if($currentMemberInfo->major)
                                                    <span class="ms-1">{{ $currentMemberInfo->major }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="row g-2 mt-1">
                                            {{-- Số điện thoại --}}
                                            @if($currentMemberInfo->phone)
                                                <div class="col-auto">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="bi bi-telephone-fill text-success"></i>
                                                        {{ $currentMemberInfo->phone }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Giới tính --}}
                                            @if($currentMemberInfo->gender)
                                                <div class="col-auto">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="bi bi-gender-{{ $currentMemberInfo->gender === 'male' ? 'male text-primary' : 'female text-danger' }}"></i>
                                                        {{ $currentMemberInfo->gender === 'male' ? 'Nam' : ($currentMemberInfo->gender === 'female' ? 'Nữ' : 'Khác') }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Ngày sinh --}}
                                            @if($currentMemberInfo->date_of_birth)
                                                <div class="col-auto">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="bi bi-calendar-event text-info"></i>
                                                        {{ \Carbon\Carbon::parse($currentMemberInfo->date_of_birth)->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-4">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" 
                                     style="width: 60px; height: 60px;">
                                    <i class="bi bi-person-x text-muted" style="font-size: 2rem;"></i>
                                </div>
                                <div class="text-muted">
                                    <strong>Chưa có người giữ vai trò</strong>
                                </div>
                                <small class="text-muted mt-1">Vị trí này đang trống</small>
                            </div>
                        @endif
                    </div>

                    {{-- Cột 3: Đề xuất mới --}}
                    <div class="col-12 col-lg-5 p-4 bg-light bg-opacity-50">
                        <div class="h-100 d-flex flex-column justify-content-center">
                            <label class="form-label fw-bold text-primary mb-3">
                                <i class="bi bi-arrow-right-circle-fill me-1"></i>
                                Thay đổi thành
                            </label>

                            {{-- Select với Select2 --}}
                            <select 
                                name="members[{{ $roleKey }}][user_id]" 
                                class="form-select form-select-lg member-select shadow-sm" 
                                data-role="{{ $roleKey }}"
                                data-current="{{ $currentUserId }}"
                                data-current-name="{{ $currentName }}"
                            >
                                <option value="">
                                    <i class="bi bi-dash-circle"></i> -- Giữ nguyên --
                                </option>
                                
                                @if($currentManager)
                                    <option value="__remove__" {{ $selectedUser === '__remove__' ? 'selected' : '' }}>
                                        🗑️ Gỡ bỏ: {{ $currentName }}
                                    </option>
                                @endif

                                <optgroup label="👥 Thành viên CLB">
                                    @foreach($clubMembers as $member)
                                        <option 
                                            value="{{ $member['id'] }}" 
                                            {{ (string) $selectedUser === (string) $member['id'] ? 'selected' : '' }}
                                            {{ $currentUserId == $member['id'] ? 'data-current="true"' : '' }}
                                        >
                                            {{ $member['text'] }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>

                            {{-- Preview thay đổi --}}
                            <div class="change-preview mt-3 p-3 rounded border" data-role="{{ $roleKey }}">
                                <i class="bi bi-info-circle me-1"></i>
                                <span class="preview-text">Không có thay đổi</span>
                            </div>

                            {{-- Hidden fields --}}
                            <input type="hidden" name="members[{{ $roleKey }}][role]" value="{{ $roleKey }}">
                            <input type="hidden" name="members[{{ $roleKey }}][old_user_id]" value="{{ $currentUserId }}">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Lưu ý --}}
        <div class="p-4 bg-info bg-opacity-10 border-top">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle-fill text-info me-3 mt-1" style="font-size: 1.5rem;"></i>
                <div>
                    <h6 class="text-info mb-2">Hướng dẫn:</h6>
                    <ul class="mb-0 small text-muted">
                        <li class="mb-1">Chỉ có thể chọn <strong>thành viên đã tham gia CLB</strong></li>
                        <li class="mb-1">Chọn <strong>"Giữ nguyên"</strong> nếu không muốn thay đổi vai trò đó</li>
                        <li class="mb-1">Chọn <strong>"Gỡ bỏ"</strong> để xóa vai trò (người đó vẫn là thành viên CLB)</li>
                        <li>Một người có thể giữ nhiều vai trò khác nhau</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS tùy chỉnh --}}






                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Lý do đề xuất</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Lý do đề xuất thay đổi <span class="text-danger">*</span></label>
                                            <textarea name="reason" class="form-control" rows="4" required placeholder="Vui lòng nêu rõ lý do đề xuất thay đổi thông tin CLB">{{ old('reason') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i> Gửi đề xuất
                                    </button>
                                </div>
                            </form>
                    @endif
                </div>

            @if($pendingRequest)
                <div class="modal fade" id="pendingRequestModal" tabindex="-1" aria-labelledby="pendingRequestLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="pendingRequestLabel">Chi tiết đề xuất đang chờ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Lý do:</strong> {{ $pendingRequest->reason }}</p>
                                <p><strong>Ngày gửi:</strong> {{ optional($pendingRequest->created_at)->format('d/m/Y H:i') }}</p>

                                @if($pendingRequest->memberUpdates && $pendingRequest->memberUpdates->isNotEmpty())
                                    <h6 class="mt-3">Ban quản lý đề xuất</h6>
                                    <ul class="list-group">
                                        @foreach($pendingRequest->memberUpdates as $update)
                                            <li class="list-group-item">
                                                <strong>{{ $managementRoles[$update->role] ?? $update->role }}:</strong>

                                                @if($update->action === 'remove')
                                                    {{-- Xóa vai trò --}}
                                                    <span class="text-danger">
                                                        <i class="bi bi-x-circle"></i>
                                                        Gỡ bỏ: {{ $update->oldUser->name ?? 'Không xác định' }}
                                                    </span>
                                                @elseif($update->action === 'assign')
                                                    {{-- Thay đổi người giữ vai trò --}}
                                                    @if($update->old_user_id)
                                                        <span class="text-warning">
                                                            <i class="bi bi-arrow-left-right"></i>
                                                            {{ $update->oldUser->name ?? 'Không xác định' }}
                                                            → {{ $update->user->name ?? 'Không xác định' }}
                                                        </span>
                                                    @else
                                                        {{-- Thêm mới --}}
                                                        <span class="text-success">
                                                            <i class="bi bi-plus-circle"></i>
                                                            Thêm: {{ $update->user->name ?? 'Không xác định' }}
                                                        </span>
                                                    @endif
                                                @else
                                                    {{-- Không rõ action --}}
                                                    <span class="text-muted">
                                                        {{ $update->user->name ?? 'Không xác định' }}
                                                    </span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
@endsection
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
           <style>
        .role-item {
            transition: all 0.3s ease;
        }

        .role-item:hover {
            background-color: #f8f9fa;
        }

        .change-preview {
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .change-preview.preview-danger {
            background-color: #fff5f5;
            border-color: #dc3545 !important;
            border-left: 4px solid #dc3545 !important;
            color: #dc3545;
        }

        .change-preview.preview-warning {
            background-color: #fff9e6;
            border-color: #ffc107 !important;
            border-left: 4px solid #ffc107 !important;
            color: #856404;
        }

        .change-preview.preview-success {
            background-color: #f0fdf4;
            border-color: #28a745 !important;
            border-left: 4px solid #28a745 !important;
            color: #28a745;
        }

        .change-preview.preview-muted {
            background-color: #f8f9fa;
            border-color: #dee2e6 !important;
            border-left: 4px solid #6c757d !important;
            color: #6c757d;
        }

        .member-select {
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .member-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        @media (max-width: 991.98px) {
            .role-item .col-12 {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6;
            }

            .role-item .col-12:last-child {
                border-bottom: none;
            }
        }
    </style>

    {{-- JavaScript cho Select2 và Preview --}}

@endpush

{{-- JavaScript cho Select2 --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Khởi tạo Select2
            $('.member-select').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Chọn thành viên --',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true
            });

            // Xử lý preview khi thay đổi
            $('.member-select').on('change', function () {
                const selected = $(this).val();
                const current = $(this).data('current');
                const currentName = $(this).data('current-name');
                const role = $(this).data('role');
                const preview = $(`.change-preview[data-role="${role}"]`);
                const previewText = preview.find('.preview-text');

                // Reset classes
                preview.removeClass('preview-danger preview-warning preview-success preview-muted');

                if (selected === '__remove__') {
                    // Gỡ bỏ
                    preview.addClass('preview-danger');
                    preview.html(`
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <strong>Sẽ gỡ bỏ:</strong> ${currentName}
                `);
                } else if (selected && selected !== current && selected !== '') {
                    // Thay đổi hoặc thêm mới
                    const selectedText = $(this).find('option:selected').text().trim();
                    if (current) {
                        preview.addClass('preview-warning');
                        preview.html(`
                        <i class="bi bi-arrow-left-right me-2"></i>
                        <strong>Thay đổi:</strong><br>
                        <small>${currentName} → ${selectedText}</small>
                    `);
                    } else {
                        preview.addClass('preview-success');
                        preview.html(`
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        <strong>Thêm mới:</strong> ${selectedText}
                    `);
                    }
                } else {
                    // Giữ nguyên
                    preview.addClass('preview-muted');
                    if (current) {
                        preview.html(`
                        <i class="bi bi-dash-circle me-2"></i>
                        <em>Giữ nguyên: ${currentName}</em>
                    `);
                    } else {
                        preview.html(`
                        <i class="bi bi-dash-circle me-2"></i>
                        <em>Không có thay đổi</em>
                    `);
                    }
                }
            });

            // Trigger change để hiển thị trạng thái ban đầu
            $('.member-select').trigger('change');
        });
    </script>
@endpush

