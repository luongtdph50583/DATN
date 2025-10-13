@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    {{-- ====== Tiêu đề & Thông báo ====== --}}
    <h1 class="h3 mb-4 text-gray-800">Quản lý Câu lạc bộ</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ====== Nút thêm CLB ====== --}}
    <div class="mb-3">
        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm CLB
        </a>
    </div>

    {{-- ====== Danh sách CLB ====== --}}
    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Danh sách CLB
            </h6>
        </div>
        <div class="card-body">
            @if($clubs->isEmpty())
                <p class="text-muted">Chưa có câu lạc bộ nào.</p>
            @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="clubsTable" width="100%">
                    <thead class="table-light text-center">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th>Logo</th>
                            <th>Lĩnh vực</th>
                            <th>Trạng thái</th>
                            <th>Chủ nhiệm</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clubs as $club)
                        <tr>
                            <td class="text-center">{{ $club->id }}</td>
                            <td>{{ $club->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($club->description, 80) }}</td>
                            <td class="text-center">
                                @if($club->logo)
                                    <img src="{{ Storage::url($club->logo) }}" width="50" height="50" class="rounded">
                                @endif
                            </td>
                            <td>{{ $club->field }}</td>
                            <td class="text-center">
                                @if($club->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @elseif($club->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </td>
                            <td>{{ $club->manager->name ?? 'Chưa gán' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>

                                <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa CLB?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" data-bs-target="#assignManagerModal"
                                        onclick="openAssignManager({{ $club->id }})">
                                    <i class="fas fa-user"></i> Gán chủ nhiệm
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ====== Yêu cầu thành lập CLB ====== --}}
    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-check"></i> Yêu cầu thành lập CLB
            </h6>
        </div>
        <div class="card-body">
            @if($clubRequests->isEmpty())
                <p class="text-muted">Không có yêu cầu mới.</p>
            @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle" width="100%">
                    <thead class="table-light text-center">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th>Lĩnh vực</th>
                            <th>Người tạo</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clubRequests as $request)
                        <tr>
                            <td class="text-center">{{ $request->id }}</td>
                            <td>{{ $request->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($request->description, 80) }}</td>
                            <td>{{ $request->field }}</td>
                            <td>{{ $request->creator->name ?? '—' }}</td>
                            <td class="text-center">
                                @if($request->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($request->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.club-requests.handle', $request) }}" method="POST" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                </form>

                                <form action="{{ route('admin.club-requests.handle', $request) }}" method="POST" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ====== Yêu cầu tham gia CLB ====== --}}
    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users"></i> Yêu cầu tham gia CLB
            </h6>
        </div>
        <div class="card-body">
            @if($clubJoinRequests->isEmpty())
                <p class="text-muted">Không có yêu cầu tham gia.</p>
            @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle" width="100%">
                    <thead class="table-light text-center">
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>CLB</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clubJoinRequests as $joinRequest)
                        <tr>
                            <td class="text-center">{{ $joinRequest->id }}</td>
                            <td>{{ $joinRequest->user->name }}</td>
                            <td>{{ $joinRequest->club->name }}</td>
                            <td class="text-center">
                                @if($joinRequest->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($joinRequest->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.club-join-requests.handle', $joinRequest) }}" method="POST" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                </form>

                                <form action="{{ route('admin.club-join-requests.handle', $joinRequest) }}" method="POST" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ====== Modal Gán Chủ nhiệm ====== --}}
<div class="modal fade" id="assignManagerModal" tabindex="-1" aria-labelledby="assignManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="assignManagerForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignManagerModalLabel">Gán hoặc thay đổi Chủ nhiệm CLB</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="assign_club_id" name="club_id">
                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Chọn Chủ nhiệm</label>
                        <select name="manager_id" id="manager_id" class="form-select" required>
                            <option value="">-- Chọn sinh viên --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->student_id }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openAssignManager(clubId) {
    const form = document.getElementById('assignManagerForm');
    form.action = '/admin/clubs/' + clubId + '/assign-manager';
    document.getElementById('assign_club_id').value = clubId;
}
</script>
@endsection
