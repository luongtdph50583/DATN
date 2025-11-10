{{-- resources/views/admin/members/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Quản lý Thành viên')

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Quản lý Thành viên</h1>
            <p class="text-muted small mb-0">Quản lý thông tin chi tiết thành viên hệ thống</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
                Thêm thành viên
            </a>
            <form action="{{ route('admin.members.export.excel') }}" method="GET" class="d-inline">
                @csrf
                <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                <input type="hidden" name="search_email" value="{{ request('search_email') }}">
                <input type="hidden" name="search_major" value="{{ request('search_major') }}">
                <button type="submit" class="btn btn-success">
                    Xuất Excel
                </button>
            </form>
            <button type="button" class="btn btn-warning text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#topMembersModal">
                TOP 10 THÀNH VIÊN NHIỀU CLB NHẤT
            </button>
            <a href="{{ route('admin.members.trashed') }}" class="btn btn-outline-danger">
                Lịch sử xóa ({{ \App\Models\Member::onlyTrashed()->count() }})
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.members.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tên</label>
                <input type="text" name="search_name" class="form-control" 
                       placeholder="Nhập tên..." value="{{ request('search_name') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="search_email" class="form-control" 
                       placeholder="Nhập email..." value="{{ request('search_email') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Câu lạc bộ</label>
                <select name="club_id[]" class="form-select select2-club" multiple="multiple" style="width: 100%">
                    @foreach(\App\Models\Club::orderBy('name')->get() as $club)
                        <option value="{{ $club->id }}" 
                            {{ is_array(request('club_id')) && in_array($club->id, request('club_id')) ? 'selected' : '' }}>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Tìm kiếm
                </button>
                @if(request()->hasAny(['search_name', 'search_email', 'club_id']))
                    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($members->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>MSSV</th>
                                <th>Email</th>
                                <th>SĐT</th>
                                <th>Khóa</th>
                                <th>Chuyên ngành</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr id="member-{{ $member->id }}" 
                                    class="{{ session('restored_id') == $member->id ? 'highlight-restored' : '' }}">
                                    <td><span class="badge bg-primary">#{{ $member->id }}</span></td>
                                    <td class="fw-bold">{{ $member->user->name ?? '—' }}</td>
                                    <td><code class="small">{{ $member->student_code ?? '—' }}</code></td>
                                    <td>
                                        <a href="mailto:{{ $member->user->email ?? '' }}" class="text-decoration-none">
                                            {{ Str::limit($member->user->email ?? '—', 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $member->phone ?? '—' }}</td>
                                    <td>{{ $member->course ?? '—' }}</td>
                                    <td>{{ Str::limit($member->major ?? '—', 20) }}</td>
                                    <td>
                                        <span class="badge {{ $member->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $member->status == 'active' ? 'Hoạt động' : 'Khóa' }}
                                        </span>
                                    </td>

                                    <!-- DUY NHẤT 1 CHỖ ĐƯỢC THAY ĐỔI: ICON SIÊU ĐẸP GIỐNG EVENT -->
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.members.show', $member) }}" 
                                               class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- <a href="{{ route('admin.members.edit', $member) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a> --}}
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal-{{ $member->id }}"
                                                    title="Xóa thành viên">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    {{ $members->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có thành viên nào</h5>
                    <a href="{{ route('admin.members.create') }}" class="btn btn-primary mt-2">
                        Thêm thành viên đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL XÓA MỀM – GIỮ NGUYÊN CỦA BẠN -->
    @foreach($members as $member)
    <div class="modal fade" id="deleteModal-{{ $member->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.members.destroy', $member) }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">Xóa thành viên</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-user-slash fa-3x text-danger"></i>
                            </div>
                            <h5>Bạn có chắc chắn muốn xóa?</h5>
                            <p class="text-muted">Thành viên sẽ được chuyển vào thùng rác</p>
                        </div>
                        <div class="bg-light p-4 rounded border mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:60px;height:60px;font-weight:bold;font-size:20px;">
                                        {{ substr($member->user->name ?? 'T', 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5">{{ $member->user->name ?? 'Không tên' }}</div>
                                    <div class="small text-muted">{{ $member->user->email ?? '—' }}</div>
                                    <div class="small text-muted">MSSV: <code>{{ $member->student_code ?? '—' }}</code></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Lý do xóa *</label>
                            <textarea name="delete_reason" class="form-control" rows="3" 
                                      placeholder="VD: Vi phạm nội quy..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger px-4">Xóa thành viên</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- MODAL TOP 10 – GIỮ NGUYÊN -->
    <div class="modal fade" id="topMembersModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-warning text-white">
                    <h5 class="modal-title fw-bold">
                        TOP 10 THÀNH VIÊN THAM GIA NHIỀU CLB NHẤT THÁNG {{ now()->format('m/Y') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    @if($topMembers->count() > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Hạng</th>
                                    <th>Thành viên</th>
                                    <th>MSSV</th>
                                    <th>Chuyên ngành</th>
                                    <th class="text-center">Số CLB</th>
                                    <th>Phần thưởng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topMembers as $index => $member)
                                    <tr class="{{ $index < 3 ? 'table-warning' : '' }}">
                                        <td class="text-center fw-bold">
                                            @if($index == 0)
                                                <span class="badge bg-warning fs-6">1st</span>
                                            @elseif($index == 1)
                                                <span class="badge bg-secondary fs-6">2nd</span>
                                            @elseif($index == 2)
                                                <span class="badge bg-danger fs-6">3rd</span>
                                            @else
                                                <span class="badge bg-dark">#{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;font-weight:bold;font-size:16px;">
                                                    {{ substr($member->user->name ?? 'T', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $member->user->name ?? 'Chưa có tên' }}</div>
                                                    <small class="text-muted">{{ $member->user->email ?? '—' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code class="fw-bold">{{ $member->student_code ?? '—' }}</code></td>
                                        <td>{{ $member->major ?? '—' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success fs-5 fw-bold">{{ $member->clubs_count }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                @if($index == 0) 2.000.000đ + Voucher 500k
                                                @elseif($index == 1) 1.000.000đ + Voucher 300k
                                                @elseif($index == 2) 500.000đ + Voucher 200k
                                                @else Khuyến khích 100.000đ
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-trophy fa-4x text-muted mb-3 opacity-50"></i>
                            <h5 class="text-muted">Chưa có dữ liệu tháng này</h5>
                            <p class="text-muted">Hãy khuyến khích sinh viên tham gia nhiều CLB hơn!</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <small class="text-muted">Cập nhật lúc: {{ now()->format('H:i d/m/Y') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    .highlight-restored {
        animation: highlightFlash 3s ease-in-out;
        background-color: #fff3cd !important;
    }
    @keyframes highlightFlash {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }
    /* Làm icon nhỏ gọn đẹp hơn */
    .btn-sm i { font-size: 0.9rem; }
</style>
@endpush

@push('scripts')
@if(session('restored_id'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const row = document.getElementById("member-{{ session('restored_id') }}");
        if (row) {
            row.scrollIntoView({ behavior: "smooth", block: "center" });
            row.classList.add("highlight-restored");
        }
    });
</script>
@endif
@endpush
@push('scripts')
<script>
$(document).ready(function() {
    $('.select2-club').select2({
        theme: 'bootstrap-5',
        placeholder: "CLB...",
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush