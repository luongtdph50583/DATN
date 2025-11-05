@extends('admin.layouts.app')

@section('title', 'Chi tiết CLB')
@section('card-title', 'Chi tiết CLB: ' . $club->name)

@section('card-body')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid py-4">

    <!-- Thông tin CLB -->
    <div class="card mb-4 shadow-sm border-primary">
        <div class="card-header bg-primary text-white fw-bold">Thông tin Câu lạc bộ</div>
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                @if($club->logo)
                    <img src="{{ Storage::url($club->logo) }}" class="rounded me-3" style="width:80px;height:80px;object-fit:cover;">
                @else
                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center me-3" style="width:80px;height:80px;font-size:32px;">
                        {{ strtoupper(substr($club->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h1 class="h4 mb-1">{{ $club->name }}</h1>
                    <p class="text-muted small mb-0">{{ $club->field ?? '—' }}</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Email:</strong> {{ $club->email ?? '—' }}</div>
                <div class="col-md-4"><strong>Điện thoại:</strong> {{ $club->phone ?? '—' }}</div>
                <div class="col-md-4"><strong>Giới hạn thành viên:</strong> {{ $club->member_limit ?? '—' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Ngày thành lập:</strong> {{ $club->founded_at ? \Carbon\Carbon::parse($club->founded_at)->format('d/m/Y') : '—' }}</div>
                <div class="col-md-8"><strong>Địa điểm:</strong> {{ $club->location ?? '—' }}</div>
            </div>
            <p><strong>Mô tả:</strong></p>
            <div class="border rounded p-2 bg-light">
                {!! $club->description ?? '<em>Chưa có mô tả</em>' !!}
            </div>
            <p class="mt-3"><strong>Nội quy:</strong></p>
            <div class="border rounded p-2 bg-light">
                {!! $club->rules ?? '<em>Chưa có nội quy</em>' !!}
            </div>
        </div>
    </div>

    <!-- Ban quản lý CLB -->
    <div class="card mb-4 shadow-sm border-success">
        <div class="card-header bg-success text-white fw-bold">Ban quản lý CLB</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
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
                        @forelse($clubMembers as $member)
                            @if(in_array($member['role'], ['club_manager','deputy_manager','secretary','treasurer','event_manager','communication']))
                                <tr>
                                    <td>{{ $index++ }}</td>
                                    <td>{{ $member['member']['user']['name'] ?? '—' }}</td>
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
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Chưa có ban quản lý.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Danh sách thành viên -->
    <div class="card mb-4 shadow-sm border-secondary">
        <div class="card-header bg-secondary text-white fw-bold">Danh sách thành viên CLB</div>
        <div class="card-body">
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
                        @forelse($clubMembers as $member)
                            @if($member['role'] === 'member')
                                <tr data-name="{{ strtolower($member['member']['user']['name'] ?? '') }}"
                                    data-code="{{ strtolower($member['member']['student_code'] ?? '') }}"
                                    data-status="{{ $member['status'] }}">
                                    <td>{{ $index++ }}</td>
                                    <td>{{ $member['member']['user']['name'] ?? '—' }}</td>
                                    <td>{{ $member['member']['student_code'] ?? '—' }}</td>
                                    <td>{{ $roleLabels[$member['role']] ?? $member['role'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $member['status'] === 'active' ? 'success' : 'secondary' }}">
                                            {{ $member['status'] === 'active' ? 'Hoạt động' : 'Ngưng' }}
                                        </span>
                                    </td>
                                    <td>{{ isset($member['joined_at']) ? \Carbon\Carbon::parse($member['joined_at'])->format('d/m/Y') : '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('admin/members/' . $member['member']['id']) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#removeMemberModal"
                                                data-member-id="{{ $member['member']['id'] }}" data-club-id="{{ $club->id }}">
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Chưa có thành viên nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
  document.addEventListener('DOMContentLoaded', function () {
    // Xử lý sự kiện khi mở modal
    $('#removeMemberModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Nút kích hoạt modal
        var memberId = button.data('member-id'); // Lấy member_id
        var clubId = button.data('club-id'); // Lấy club_id

        // Cập nhật form action với club_id và member_id
        var form = $(this).find('form');
        var actionUrl = form.attr('action')
            .replace('club_id', clubId)  // Thay 'club_id' bằng clubId
            .replace('member_id', memberId); // Thay 'member_id' bằng memberId
        form.attr('action', actionUrl);
    });
});
</script>
@endpush
