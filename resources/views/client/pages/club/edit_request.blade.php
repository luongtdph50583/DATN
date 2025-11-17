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

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Chủ nhiệm & Cố vấn</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Đề xuất Chủ nhiệm mới</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">Giữ nguyên</option>
                                    @foreach($users as $userOption)
                                        <option value="{{ $userOption->id }}" {{ (string)old('manager_id') === (string)$userOption->id ? 'selected' : '' }}>
                                            {{ $userOption->name }} ({{ $userOption->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Đề xuất cố vấn</label>
                                <select name="advisor_id" class="form-select">
                                    <option value="">Giữ nguyên</option>
                                    @foreach($facultyMembers as $advisor)
                                        <option value="{{ $advisor->id }}" {{ (string)old('advisor_id') === (string)$advisor->id ? 'selected' : '' }}>
                                            {{ $advisor->user->name ?? 'GV' }} - {{ $advisor->department ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Đề xuất thay đổi ban quản lý</h5>
                        <small class="text-muted">Chọn thành viên mới nếu muốn thay đổi.</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Chức vụ</th>
                                        <th>Hiện tại</th>
                                        <th>Đề xuất mới</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($managementRoles as $roleKey => $roleLabel)
                                        @php
                                            $currentMember = $club->clubMembers->firstWhere('role', $roleKey);
                                            $currentName = $currentMember && $currentMember->member && $currentMember->member->user
                                                ? $currentMember->member->user->name
                                                : 'Chưa phân công';
                                            $selectedUser = old("members.$roleKey.user_id");
                                        @endphp
                                        <tr>
                                            <td>{{ $roleLabel }}</td>
                                            <td>{{ $currentName }}</td>
                                            <td>
                                                <select name="members[{{ $roleKey }}][user_id]" class="form-select">
                                                    <option value="">Giữ nguyên</option>
                                                    @foreach($users as $userOption)
                                                        <option value="{{ $userOption->id }}"
                                                            {{ (string)$selectedUser === (string)$userOption->id ? 'selected' : '' }}>
                                                            {{ $userOption->name }} ({{ $userOption->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="members[{{ $roleKey }}][role]" value="{{ $roleKey }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

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
                            <ul>
                                @foreach($pendingRequest->memberUpdates as $update)
                                    <li>{{ $managementRoles[$update->role] ?? $update->role }} → {{ $update->user->name ?? 'Không xác định' }}</li>
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

