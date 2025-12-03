@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu cập nhật CLB')

@section('card-body')
<div class="container-fluid mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>
            Chi tiết yêu cầu cập nhật CLB
        </h3>
        <a href="{{ route('admin.club_requests_update.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    {{-- 🔹 Thông tin yêu cầu --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary bg-gradient text-white">
            <h5 class="mb-0">
                <i class="bi bi-info-circle-fill me-2"></i>
                Thông tin yêu cầu
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-building text-primary me-3 mt-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <small class="text-muted d-block">Tên CLB</small>
                            <strong class="text-dark">{{ $club->name }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-person-circle text-primary me-3 mt-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <small class="text-muted d-block">Người đề xuất</small>
                            <strong class="text-dark">{{ $update->proposer->name ?? 'Không rõ' }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-calendar-event text-primary me-3 mt-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <small class="text-muted d-block">Ngày gửi</small>
                            <strong class="text-dark">{{ $update->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-chat-quote text-primary me-3 mt-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <small class="text-muted d-block">Lý do đề xuất</small>
                            <strong class="text-dark">{{ $update->reason ?? 'Không có' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Thông tin CLB --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-info bg-gradient text-white">
            <h5 class="mb-0">
                <i class="bi bi-card-checklist me-2"></i>
                Thông tin CLB
            </h5>
        </div>
        <div class="card-body p-0">
            @php
                $fields = [
                    'name' => ['label' => 'Tên CLB', 'icon' => 'building'],
                    'slogan' => ['label' => 'Khẩu hiệu', 'icon' => 'quote'],
                    'description' => ['label' => 'Mô tả', 'icon' => 'text-paragraph'],
                    'field' => ['label' => 'Lĩnh vực', 'icon' => 'diagram-3'],
                    'member_limit' => ['label' => 'Giới hạn thành viên', 'icon' => 'people'],
                    'email' => ['label' => 'Email', 'icon' => 'envelope'],
                    'phone' => ['label' => 'Số điện thoại', 'icon' => 'telephone'],
                    'logo' => ['label' => 'Logo', 'icon' => 'image'],
                    'rules' => ['label' => 'Nội quy', 'icon' => 'file-text'],
                    'location' => ['label' => 'Địa điểm', 'icon' => 'geo-alt']
                ];
            @endphp

            @foreach($fields as $field => $meta)
                @php
                    $currentValue = $club->$field;
                    $proposedValue = $update->$field;
                    $hasChange = !is_null($proposedValue) && $proposedValue !== $currentValue;
                @endphp

                <div class="field-comparison {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row g-0">
                        {{-- Cột 1: Tên trường --}}
                        <div class="col-12 col-lg-3 bg-light p-3 border-end d-flex align-items-center">
                            <div class="w-100">
                                <h6 class="mb-1 text-info fw-bold">
                                    <i class="bi bi-{{ $meta['icon'] }} me-2"></i>
                                    {{ $meta['label'] }}
                                </h6>
                                <small class="text-muted">{{ $field }}</small>
                            </div>
                        </div>

                        {{-- Cột 2: Giá trị hiện tại --}}
                        <div class="col-12 col-lg-4 p-3 border-end">
                            @if($field === 'logo' && $currentValue)
                                <img src="{{ Storage::url($currentValue) }}" 
                                     class="rounded shadow-sm border"
                                     style="width:100px;height:100px;object-fit:cover;" 
                                     alt="Logo hiện tại">
                            @elseif($field === 'description' || $field === 'rules')
                                <div class="small text-muted" style="max-height: 100px; overflow-y: auto;">
                                    {{ $currentValue ?? '—' }}
                                </div>
                            @else
                                <div class="text-dark">
                                    {{ $currentValue ?? '—' }}
                                </div>
                            @endif
                        </div>

                        {{-- Cột 3: Giá trị đề xuất --}}
                        <div class="col-12 col-lg-5 p-3 {{ $hasChange ? 'bg-warning bg-opacity-10' : 'bg-light bg-opacity-50' }}">
                            @if($hasChange)
                                @if($field === 'logo' && $proposedValue)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-arrow-right-circle-fill text-success me-2" style="font-size: 1.5rem;"></i>
                                        <img src="{{ Storage::url($proposedValue) }}" 
                                             class="rounded shadow-sm border border-2 border-success"
                                             style="width:100px;height:100px;object-fit:cover;" 
                                             alt="Logo đề xuất">
                                    </div>
                                @elseif($field === 'description' || $field === 'rules')
                                    <div class="alert alert-warning mb-0">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                                            <strong>Nội dung mới:</strong>
                                        </div>
                                        <div class="small" style="max-height: 100px; overflow-y: auto;">
                                            {{ $proposedValue }}
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="bi bi-arrow-right-circle-fill me-2"></i>
                                        <strong class="text-success">{{ $proposedValue }}</strong>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-dash-circle"></i>
                                    <div class="small mt-1">Không thay đổi</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 🔹 Ban quản lý CLB --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-success bg-gradient text-white">
            <h5 class="mb-0">
                <i class="bi bi-people-fill me-2"></i>
                So sánh Ban quản lý CLB
            </h5>
        </div>
        <div class="card-body p-0">
            @php
                $roles = [
                    'club_manager' => 'Chủ nhiệm CLB',
                    'deputy_manager' => 'Phó chủ nhiệm',
                    'event_manager' => 'Quản lý sự kiện',
                    'communication' => 'Truyền thông',
                    'secretary' => 'Thư ký',
                    'treasurer' => 'Thủ quỹ'
                ];
            @endphp

            @foreach($roles as $role => $roleLabel)
                @php
                    // Lấy thông tin hiện tại
                    $currentMember = $currentData['members']->where('role', $role)->first();
                    
                    // Lấy đề xuất thay đổi cho role này
                    $proposedUpdate = $proposedData['members']->where('role', $role)->first();
                    
                    // Xác định action
                    $action = $proposedUpdate->action ?? null;
                    $hasChange = !is_null($proposedUpdate);
                @endphp

                <div class="role-comparison {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row g-0">
                        {{-- Cột 1: Chức vụ --}}
                        <div class="col-12 col-lg-2 bg-light p-3 border-end d-flex align-items-center">
                            <div class="w-100">
                                <h6 class="mb-1 text-success fw-bold">
                                    <i class="bi bi-award-fill me-1"></i>
                                    {{ $roleLabel }}
                                </h6>
                                <small class="text-muted">{{ $role }}</small>
                            </div>
                        </div>

                        {{-- Cột 2: Hiện tại --}}
                        <div class="col-12 col-lg-4 p-3 border-end">
                            @if($currentMember)
                                @php
                                    $currentUser = $currentMember->member->user;
                                    $currentMemberInfo = $currentMember->member;
                                @endphp
                                <div class="d-flex align-items-start">
                                    {{-- Avatar --}}
                                    <div class="flex-shrink-0 me-3">
                                        @if($currentUser->avatar)
                                            <img src="{{ asset('storage/' . $currentUser->avatar) }}" 
                                                 alt="{{ $currentUser->name }}" 
                                                 class="rounded-circle shadow-sm"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                                                 style="width: 50px; height: 50px; font-weight: bold;">
                                                {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Thông tin --}}
                                    <div class="flex-grow-1">
                                        <div class="mb-2">
                                            <strong class="text-dark">{{ $currentUser->name }}</strong>
                                            <span class="badge bg-success-subtle text-success border border-success ms-2">
                                                <i class="bi bi-check-circle"></i> Hiện tại
                                            </span>
                                        </div>
                                        
                                        <div class="small text-muted">
                                            <div class="mb-1">
                                                <i class="bi bi-envelope me-1"></i>
                                                {{ $currentUser->email }}
                                            </div>
                                            
                                            @if($currentMemberInfo->student_code)
                                                <div class="mb-1">
                                                    <i class="bi bi-card-text me-1"></i>
                                                    <strong>{{ $currentMemberInfo->student_code }}</strong>
                                                </div>
                                            @endif

                                            @if($currentMemberInfo->phone)
                                                <div class="mb-1">
                                                    <i class="bi bi-telephone me-1"></i>
                                                    {{ $currentMemberInfo->phone }}
                                                </div>
                                            @endif

                                            @if($currentMemberInfo->course || $currentMemberInfo->major)
                                                <div class="mb-1">
                                                    <i class="bi bi-mortarboard me-1"></i>
                                                    @if($currentMemberInfo->course)K{{ $currentMemberInfo->course }}@endif
                                                    @if($currentMemberInfo->major) - {{ $currentMemberInfo->major }}@endif
                                                </div>
                                            @endif

                                            @if($currentMember->appointed_at)
                                                <div class="mb-1">
                                                    <i class="bi bi-calendar-check me-1"></i>
                                                    Bổ nhiệm: {{ \Carbon\Carbon::parse($currentMember->appointed_at)->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-person-x" style="font-size: 2rem;"></i>
                                    <div class="mt-2">Chưa có người giữ vai trò</div>
                                </div>
                            @endif
                        </div>

                        {{-- Cột 3: Đề xuất --}}
                        <div class="col-12 col-lg-6 p-3 {{ $hasChange ? 'bg-warning bg-opacity-10' : 'bg-light bg-opacity-50' }}">
                            @if($hasChange)
                                {{-- CÓ ĐỀ XUẤT THAY ĐỔI --}}
                                
                                @if($action === 'remove')
                                    {{-- ACTION: REMOVE (Gỡ bỏ) --}}
                                    <div class="alert alert-danger mb-0">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-x-circle-fill me-2" style="font-size: 1.5rem;"></i>
                                            <strong>Đề xuất GỠ BỎ vai trò này</strong>
                                        </div>
                                        
                                        @if($proposedUpdate->oldUser)
                                            @php
                                                $oldUser = $proposedUpdate->oldUser;
                                                $oldMember = \App\Models\Member::where('user_id', $oldUser->id)->first();
                                            @endphp
                                            <div class="small">
                                                <div class="mb-1">
                                                    <i class="bi bi-person-dash me-1"></i>
                                                    Gỡ bỏ: <strong>{{ $oldUser->name }}</strong>
                                                </div>
                                                @if($oldMember)
                                                    <div class="text-muted">
                                                        MSV: {{ $oldMember->student_code ?? '—' }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2 small">
                                            <i class="bi bi-arrow-right me-1"></i>
                                            Sau khi duyệt: <strong class="text-danger">Không ai giữ vai trò này</strong>
                                        </div>
                                    </div>

                                @elseif($action === 'assign')
                                    {{-- ACTION: ASSIGN (Thêm mới hoặc Thay thế) --}}
                                    
                                    @php
                                        $newUser = $proposedUpdate->user;
                                        $newMember = \App\Models\Member::where('user_id', $newUser->id)->first();
                                        $isReplace = !is_null($proposedUpdate->old_user_id);
                                    @endphp

                                    <div class="alert {{ $isReplace ? 'alert-warning' : 'alert-success' }} mb-0">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-{{ $isReplace ? 'arrow-left-right' : 'plus-circle-fill' }} me-2" style="font-size: 1.5rem;"></i>
                                            <strong>Đề xuất {{ $isReplace ? 'THAY ĐỔI' : 'THÊM MỚI' }}</strong>
                                        </div>

                                        {{-- Người cũ (nếu có) --}}
                                        @if($isReplace && $proposedUpdate->oldUser)
                                            <div class="mb-3 pb-3 border-bottom">
                                                <div class="small text-muted mb-2">
                                                    <i class="bi bi-person-dash me-1"></i> Người hiện tại sẽ bị gỡ:
                                                </div>
                                                <div class="text-decoration-line-through">
                                                    <strong>{{ $proposedUpdate->oldUser->name }}</strong>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Người mới --}}
                                        <div>
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-person-plus me-1"></i> Người mới:
                                            </div>
                                            
                                            <div class="d-flex align-items-start">
                                                {{-- Avatar --}}
                                                <div class="flex-shrink-0 me-3">
                                                    @if($newUser->avatar)
                                                        <img src="{{ asset('storage/' . $newUser->avatar) }}" 
                                                             alt="{{ $newUser->name }}" 
                                                             class="rounded-circle border border-2 border-success shadow-sm"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center border border-2 border-success shadow-sm"
                                                             style="width: 50px; height: 50px; font-weight: bold;">
                                                            {{ strtoupper(substr($newUser->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Thông tin --}}
                                                <div class="flex-grow-1">
                                                    <div class="mb-2">
                                                        <strong class="text-dark">{{ $newUser->name }}</strong>
                                                        <span class="badge bg-success ms-2">
                                                            <i class="bi bi-star-fill"></i> Đề xuất
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="small">
                                                        <div class="mb-1">
                                                            <i class="bi bi-envelope me-1"></i>
                                                            {{ $newUser->email }}
                                                        </div>
                                                        
                                                        @if($newMember)
                                                            @if($newMember->student_code)
                                                                <div class="mb-1">
                                                                    <i class="bi bi-card-text me-1"></i>
                                                                    <strong>{{ $newMember->student_code }}</strong>
                                                                </div>
                                                            @endif

                                                            @if($newMember->phone)
                                                                <div class="mb-1">
                                                                    <i class="bi bi-telephone me-1"></i>
                                                                    {{ $newMember->phone }}
                                                                </div>
                                                            @endif

                                                            @if($newMember->course || $newMember->major)
                                                                <div class="mb-1">
                                                                    <i class="bi bi-mortarboard me-1"></i>
                                                                    @if($newMember->course)K{{ $newMember->course }}@endif
                                                                    @if($newMember->major) - {{ $newMember->major }}@endif
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @else
                                    {{-- ACTION không xác định --}}
                                    <div class="alert alert-secondary mb-0">
                                        <i class="bi bi-question-circle me-2"></i>
                                        Hành động không xác định
                                    </div>
                                @endif

                            @else
                                {{-- KHÔNG CÓ ĐỀ XUẤT --}}
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-dash-circle" style="font-size: 2rem;"></i>
                                    <div class="mt-2">Không có thay đổi</div>
                                    <small>Giữ nguyên như hiện tại</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 🔹 Hành động duyệt/từ chối --}}
    <div class="card shadow-sm border-0 sticky-bottom">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="bi bi-check2-square me-2"></i>
                Phê duyệt yêu cầu
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.club_requests_update.handleUpdateRequest', $update->id) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-chat-left-text me-1"></i>
                        Lý do (bắt buộc nếu từ chối)
                    </label>
                    <textarea name="rejected_reason" class="form-control" rows="3" placeholder="Nhập lý do từ chối (nếu có)..."></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="status" value="approved" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Duyệt yêu cầu
                    </button>
                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-lg">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        Từ chối yêu cầu
                    </button>
                    <a href="{{ route('admin.club_requests_update.index') }}" class="btn btn-outline-secondary btn-lg ms-auto">
                        <i class="bi bi-arrow-left me-1"></i>
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('css')

@endpush
{{-- CSS tùy chỉnh --}}

@endsection