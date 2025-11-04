@extends('admin.layouts.app')

@section('title', 'Chi tiết CLB')
@section('card-title', 'Chi tiết CLB: ' . $club->name)

@section('card-body')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            @if($club->logo)
                <img src="{{ Storage::url($club->logo) }}" class="rounded me-3" style="width:80px;height:80px;object-fit:cover;">
            @else
                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center me-3" style="width:80px;height:80px;font-size:32px;">
                    {{ strtoupper(substr($club->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="h4 mb-1">{{ $club->name }}</h1>
                <p class="text-muted small mb-0">{{ $club->field }}</p>
            </div>
        </div>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

                {{-- Mô tả CLB hiển thị HTML --}}
                <p><strong>Mô tả:</strong></p>
                <div class="border rounded p-2 bg-light">
                    {!! $club->description ?? '<em>Chưa có mô tả</em>' !!}
                </div>
            </div>
        </div>

        <hr>

        {{-- Ban quản lý CLB --}}
        <h5>Ban quản lý CLB</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Mã SV</th>
                        <th>Vai trò</th>
                        <th>Ngày bổ nhiệm</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @php
    $index = 1;
    $roleLabels = [
        'club_manager' => 'Chủ nhiệm',
        'deputy_manager' => 'Phó chủ nhiệm',
        'secretary' => 'Thư ký',
        'treasurer' => 'Thủ quỹ',
        'event_manager' => 'Quản lý sự kiện',
        'communication' => 'Truyền thông',
        'member' => 'Thành viên',
    ];
                    @endphp
                    @foreach($clubMembers as $member)
                        @if(in_array($member['role'], ['club_manager', 'deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication']))
                            <tr>
                                <td>{{ $index++ }}</td>
                                <td>{{ $member['member']['user']['name'] ?? $member['member']['name'] ?? '—' }}</td>
                                <td>{{ $member['member']['student_code'] ?? '—' }}</td>
                                <td>{{ $roleLabels[$member['role']] ?? $member['role'] }}</td>
                                <td>{{ isset($member['appointed_at']) ? \Carbon\Carbon::parse($member['appointed_at'])->format('d/m/Y') : '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $member['status'] === 'active' ? 'success' : 'secondary' }}">
                                        {{ $member['status'] === 'active' ? 'Hoạt động' : 'Ngưng' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if(!empty($member['member']['id']))
                                        <a href="{{ url('admin/members/' . $member['member']['id']) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr>

        {{-- Lọc & tìm kiếm thành viên thường --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="statusFilter" class="form-select">
                    <option value="">-- Chọn trạng thái --</option>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngưng hoạt động</option>
                    <option value="banned">Bị cấm</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Tìm theo tên hoặc MSSV">
            </div>
        </div>

        {{-- Danh sách thành viên thường --}}
        <h5>Danh sách thành viên</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="membersTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Mã SV</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tham gia</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="membersTableBody">
                    @php $index = 1; @endphp
                    @foreach($clubMembers as $member)
                        @if($member['role'] === 'member')
                            <tr data-name="{{ strtolower($member['member']['user']['name'] ?? $member['member']['name'] ?? '') }}"
                                data-code="{{ strtolower($member['member']['student_code'] ?? '') }}"
                                data-status="{{ $member['status'] }}">
                                <td>{{ $index++ }}</td>
                                <td>{{ $member['member']['user']['name'] ?? $member['member']['name'] ?? '—' }}</td>
                                <td>{{ $member['member']['student_code'] ?? '—' }}</td>
                                <td>{{ $roleLabels[$member['role']] ?? $member['role'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $member['status'] === 'active' ? 'success' : 'secondary' }}">
                                        {{ $member['status'] === 'active' ? 'Hoạt động' : 'Ngưng' }}
                                    </span>
                                </td>
                                <td>{{ isset($member['joined_at']) ? \Carbon\Carbon::parse($member['joined_at'])->format('d/m/Y') : '—' }}</td>
                                <td class="text-center">
                                    @if(!empty($member['member']['id']))
                                        <a href="{{ url('admin/members/' . $member['member']['id']) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @if($clubMembers->where('role', 'member')->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có thành viên nào.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('membersTableBody');

    function filterMembers() {
        const status = statusFilter.value.toLowerCase();
        const keyword = searchInput.value.toLowerCase();

        tableBody.querySelectorAll('tr').forEach(row => {
            const name = row.getAttribute('data-name');
            const code = row.getAttribute('data-code');
            const rowStatus = row.getAttribute('data-status');

            const matchStatus = status === '' || rowStatus === status;
            const matchKeyword = name.includes(keyword) || code.includes(keyword);

            row.style.display = matchStatus && matchKeyword ? '' : 'none';
        });
    }

    statusFilter.addEventListener('change', filterMembers);
    searchInput.addEventListener('input', filterMembers);
});
</script>
@endpush
