@extends('admin.layouts.app')

@section('title', 'Chi tiết thay đổi CLB')
@section('card-title', 'Chi tiết thay đổi CLB')

@section('card-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('admin.club_update_logs.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <div>
            <a href="{{ route('admin.clubs.show', $log->club_id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Xem CLB
            </a>
        </div>
    </div>
@endsection

@section('card-body')
    @php
        // Lấy changed_fields - xử lý cả array và JSON string (có thể bị double encode)
        $changedFields = [];

        // Ưu tiên lấy từ raw để tránh vấn đề với cast
        $rawValue = $log->getRawOriginal('changed_fields');

        if ($rawValue) {
            // Nếu là string, decode
            if (is_string($rawValue)) {
                // Thử decode lần 1
                $decoded = json_decode($rawValue, true);

                // Nếu decode thành công và là array
                if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
                    $changedFields = $decoded;
                }
                // Nếu vẫn là string (double encoded), decode thêm lần nữa
                elseif (is_string($decoded) && !empty($decoded)) {
                    $decoded2 = json_decode($decoded, true);
                    if (is_array($decoded2) && json_last_error() === JSON_ERROR_NONE) {
                        $changedFields = $decoded2;
                    }
                }
            }
            // Nếu đã là array, dùng luôn
            elseif (is_array($rawValue)) {
                $changedFields = $rawValue;
            }
        }

        // Fallback: Thử lấy từ model cast nếu raw không có
        // if (empty($changedFields)) {
        //     $modelFields = $log->changed_fields;
        //     if (is_array($modelFields) && !empty($modelFields)) {
        //         $changedFields = $modelFields;
        //     } elseif (is_string($modelFields) && !empty($modelFields)) {
        //         $decoded = json_decode($modelFields, true);
        //         if (is_array($decoded)) {
        //             $changedFields = $decoded;
        //         } elseif (is_string($decoded)) {
        //             $decoded2 = json_decode($decoded, true);
        //             if (is_array($decoded2)) {
        //                 $changedFields = $decoded2;
        //             }
        //         }
        //     }
        // }

        $fieldLabels = [
            'name' => 'Tên CLB',
            'slogan' => 'Khẩu hiệu',
            'description' => 'Mô tả',
            'field' => 'Lĩnh vực',
            'location' => 'Địa điểm',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'rules' => 'Nội quy',
            'member_limit' => 'Giới hạn thành viên',
            'advisor_id' => 'Giảng viên phụ trách ',
            'logo' => 'Logo',
            'status' => 'Trạng thái',
        ];
        $roleLabels = [
            'club_manager' => 'Chủ nhiệm',
            'deputy_manager' => 'Phó chủ nhiệm',
            'secretary' => 'Thư ký',
            'treasurer' => 'Thủ quỹ',
            'event_manager' => 'Quản lý sự kiện',
            'communication' => 'Truyền thông',
        ];
    @endphp

    <!-- Thông tin chung -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin chung</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Tên CLB:</th>
                            <td>
                                <a href="{{ route('admin.clubs.show', $log->club_id) }}" class="text-decoration-none">
                                    {{ $log->club->name ?? '—' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Loại thay đổi:</th>
                            <td>
                                @if($log->type === 'admin')
                                    <span class="badge bg-success">Admin thực hiện</span>
                                @elseif($log->type === 'proposer')
                                    <span class="badge bg-warning text-dark">Đề xuất từ CLB</span>
                                @else
                                    <span class="badge bg-secondary text-light">Không xác định</span>
                                @endif
                            </td>
                        </tr>

                        @if($log->type === 'proposer' && $log->proposer)
                            <tr>
                                <th>Người đề xuất:</th>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $log->proposer->name }}</span>
                                </td>
                            </tr>
                        @endif


                        <tr>
                            <th>Người thực hiện:</th>
                            <td>
                                @if($log->admin)
                                    <span class="badge bg-primary">{{ $log->admin->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Trạng thái:</th>
                            <td>
                                @if($log->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @elseif($log->status === 'rejected')
                                    <span class="badge bg-danger">Từ chối</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày thay đổi:</th>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @if($log->rejected_reason)
                            <tr>
                                <th>Lý do từ chối:</th>
                                <td class="text-danger">{{ $log->rejected_reason }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug: Hiển thị dữ liệu thô (chỉ khi debug mode) -->
    {{-- @if(config('app.debug'))
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0"><i class="fas fa-bug me-2"></i>Debug Info (Chỉ hiển thị khi APP_DEBUG=true)</h6>
        </div>
        <div class="card-body">
            <p><strong>Changed Fields Type:</strong> {{ gettype($log->changed_fields) }}</p>
            <p><strong>Is Array:</strong> {{ is_array($log->changed_fields) ? 'Yes' : 'No' }}</p>
            <p><strong>Count:</strong> {{ is_array($changedFields) ? count($changedFields) : 0 }} fields</p>
            <p><strong>Changed Fields (Parsed Array):</strong></p>
            <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">@php
                                        if (is_array($changedFields)) {
                                            echo json_encode($changedFields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                        } else {
                                            echo 'Not an array. Type: ' . gettype($changedFields) . "\n";
                                            echo 'Value: ' . var_export($changedFields, true);
                                        }
                                    @endphp</pre>
            <p class="mt-3"><strong>Raw Changed Fields (JSON):</strong></p>
            <pre class="bg-light p-3 rounded"
                style="max-height: 200px; overflow-y: auto;">{{ $log->getRawOriginal('changed_fields') }}</pre>
            <p class="mt-3"><strong>Model Casted:</strong></p>
            <pre class="bg-light p-3 rounded"
                style="max-height: 200px; overflow-y: auto;">{{ json_encode($log->changed_fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    @endif --}}

    <!-- Các trường thay đổi -->
    @if(!empty($changedFields) && is_array($changedFields) && count($changedFields) > 0)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Các trường đã thay đổi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="25%">Trường</th>
                                <th width="37.5%">Giá trị cũ</th>
                                <th width="37.5%">Giá trị mới</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($changedFields as $field => $change)
                                @php
                                    // Bỏ qua managers (sẽ hiển thị riêng)
                                    if ($field === 'managers') {
                                        continue;
                                    }

                                    // Kiểm tra xem change có phải là array với old/new không
                                    $isValidChange = is_array($change) && (isset($change['old']) || isset($change['new']));
                                @endphp
                                @if($isValidChange)
                                    <tr>
                                        <td><strong>{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</strong></td>
                                        <td>
                                            @if($field === 'logo' && $change['old'])
                                                @if(file_exists(storage_path('app/public/' . $change['old'])))
                                                    <img src="{{ asset('storage/' . $change['old']) }}" class="img-thumbnail"
                                                        style="max-width: 100px; max-height: 100px;" alt="Logo cũ">
                                                @else
                                                    @php
                                                        $oldValue = $change['old'] ?? null;
                                                        if (is_array($oldValue)) {
                                                            $oldValue = json_encode($oldValue, JSON_UNESCAPED_UNICODE);
                                                        } elseif (is_object($oldValue)) {
                                                            $oldValue = (string) $oldValue;
                                                        } elseif ($oldValue === null) {
                                                            $oldValue = '—';
                                                        } else {
                                                            $oldValue = (string) $oldValue;
                                                        }
                                                    @endphp
                                                    <span class="text-muted">{{ $oldValue }}</span>
                                                @endif
                                            @elseif($field === 'status')
                                                @php
                                                    $oldStatus = is_array($change['old'] ?? null) ? null : ($change['old'] ?? null);
                                                @endphp
                                                <span class="badge {{ $oldStatus === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $oldStatus === 'active' ? 'Hoạt động' : ($oldStatus === 'inactive' ? 'Ngưng hoạt động' : ($oldStatus ?? '—')) }}
                                                </span>
                                            @elseif($field === 'advisor_id')
                                                @php
                                                    $oldAdvisorId = is_array($change['old']) ? null : $change['old'];
                                                    $oldAdvisor = $oldAdvisorId ? \App\Models\FacultyMember::with('user')->find($oldAdvisorId) : null;
                                                @endphp
                                                {{ $oldAdvisor ? ($oldAdvisor->user->name ?? 'ID: ' . $oldAdvisorId) : ($oldAdvisorId ?? '—') }}
                                            @else
                                                @php
                                                    $oldValue = $change['old'] ?? null;
                                                    if (is_array($oldValue)) {
                                                        $oldValue = json_encode($oldValue, JSON_UNESCAPED_UNICODE);
                                                    } elseif (is_object($oldValue)) {
                                                        $oldValue = (string) $oldValue;
                                                    } elseif ($oldValue === null) {
                                                        $oldValue = '—';
                                                    }
                                                @endphp
                                                <span class="text-muted">{{ $oldValue }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($field === 'logo' && $change['new'])
                                                @if(file_exists(storage_path('app/public/' . $change['new'])))
                                                    <img src="{{ asset('storage/' . $change['new']) }}" class="img-thumbnail"
                                                        style="max-width: 100px; max-height: 100px;" alt="Logo mới">
                                                @else
                                                    @php
                                                        $newValue = $change['new'] ?? null;
                                                        if (is_array($newValue)) {
                                                            $newValue = json_encode($newValue, JSON_UNESCAPED_UNICODE);
                                                        } elseif (is_object($newValue)) {
                                                            $newValue = (string) $newValue;
                                                        } elseif ($newValue === null) {
                                                            $newValue = '—';
                                                        } else {
                                                            $newValue = (string) $newValue;
                                                        }
                                                    @endphp
                                                    <span class="text-success">{{ $newValue }}</span>
                                                @endif
                                            @elseif($field === 'status')
                                                @php
                                                    $newStatus = is_array($change['new'] ?? null) ? null : ($change['new'] ?? null);
                                                @endphp
                                                <span class="badge {{ $newStatus === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $newStatus === 'active' ? 'Hoạt động' : ($newStatus === 'inactive' ? 'Ngưng hoạt động' : ($newStatus ?? '—')) }}
                                                </span>
                                            @elseif($field === 'advisor_id')
                                                @php
                                                    $newAdvisorId = is_array($change['new']) ? null : $change['new'];
                                                    $newAdvisor = $newAdvisorId ? \App\Models\FacultyMember::with('user')->find($newAdvisorId) : null;
                                                @endphp
                                                <span class="text-success fw-bold">
                                                    {{ $newAdvisor ? ($newAdvisor->user->name ?? 'ID: ' . $newAdvisorId) : ($newAdvisorId ?? '—') }}
                                                </span>
                                            @else
                                                @php
                                                    $newValue = $change['new'] ?? null;
                                                    if (is_array($newValue)) {
                                                        $newValue = json_encode($newValue, JSON_UNESCAPED_UNICODE);
                                                    } elseif (is_object($newValue)) {
                                                        $newValue = (string) $newValue;
                                                    } elseif ($newValue === null) {
                                                        $newValue = '—';
                                                    } else {
                                                        $newValue = (string) $newValue;
                                                    }
                                                @endphp
                                                <span class="text-success fw-bold">{{ $newValue }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thay đổi ban quản lý -->
        @if(isset($changedFields['managers']) && is_array($changedFields['managers']))
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Thay đổi ban quản lý</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%">Chức vụ</th>
                                    <th width="37.5%">Người cũ</th>
                                    <th width="37.5%">Người mới</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($changedFields['managers'] as $role => $change)
                                    @if(isset($change['old']) || isset($change['new']))
                                        <tr>
                                            <td><strong>{{ $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}</strong></td>

                                            {{-- Người cũ --}}
                                            <td>
                                                @php
                                                    $oldMemberId = null;
                                                    if (!empty($change['old'])) {
                                                        $oldMemberId = is_array($change['old']) ? ($change['old']['id'] ?? null) : $change['old'];
                                                    }
                                                @endphp

                                                @if($oldMemberId)
                                                    @php
                                                        $oldMember = \App\Models\Member::with('user')->find($oldMemberId);
                                                    @endphp
                                                    @if($oldMember && $oldMember->user)
                                                        <span class="text-muted">
                                                            {{ $oldMember->user->name }}
                                                            <small class="text-muted">({{ $oldMember->student_code ?? '—' }})</small>
                                                        </span>
                                                    @else
                                                        <span class="text-muted">ID: {{ $oldMemberId }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Người mới --}}
                                            <td>
                                                @php
                                                    $newMemberId = null;
                                                    if (!empty($change['new'])) {
                                                        $newMemberId = is_array($change['new']) ? ($change['new']['id'] ?? null) : $change['new'];
                                                    }
                                                @endphp

                                                @if($newMemberId)
                                                    @php
                                                        $newMember = \App\Models\Member::with('user')->find($newMemberId);
                                                    @endphp
                                                    @if($newMember && $newMember->user)
                                                        <span class="text-success fw-bold">
                                                            {{ $newMember->user->name }}
                                                            <small class="text-muted">({{ $newMember->student_code ?? '—' }})</small>
                                                        </span>
                                                    @else
                                                        <span class="text-success fw-bold">ID: {{ $newMemberId }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-danger">Đã bỏ</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Không có thông tin thay đổi nào được ghi nhận.
        </div>
    @endif
@endsection
