@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý Câu lạc bộ</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif



    <!-- Danh sách CLB -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách CLB</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
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
                                <td>{{ $club->id }}</td>
                                <td>{{ $club->name }}</td>
                                <td>{{ $club->description }}</td>
                                <td>@if($club->logo) <img src="{{ Storage::url($club->logo) }}" width="50"> @endif</td>
                                <td>{{ $club->field }}</td>
                                <td>
                                    @if($club->status === 'active') Hoạt động
                                    @elseif($club->status === 'pending') Chờ duyệt
                                    @else Không hoạt động @endif
                                </td>
                                <td>{{ $club->manager->name ?? 'Chưa gán' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick='editClub({{ json_encode($club) }})'>Sửa</button>
                                    <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#assignManagerModal" onclick="openAssignManager({{ $club->id }})">Gán chủ nhiệm</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Yêu cầu thành lập CLB -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Yêu cầu thành lập CLB</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th>Lĩnh vực</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clubRequests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->name }}</td>
                                <td>{{ $request->description }}</td>
                                <td>{{ $request->field }}</td>
                                <td>
                                    @if($request->status === 'pending') Chờ duyệt
                                    @elseif($request->status === 'approved') Đã duyệt
                                    @else Từ chối @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.club-requests.handle', $request) }}" method="POST" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                    </form>
                                    <form action="{{ route('admin.club-requests.handle', $request) }}" method="POST" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-sm btn-danger">Từ chối</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Yêu cầu tham gia CLB -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Yêu cầu tham gia CLB</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
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
                                <td>{{ $joinRequest->id }}</td>
                                <td>{{ $joinRequest->user->name }}</td>
                                <td>{{ $joinRequest->club->name }}</td>
                                <td>
                                    @if($joinRequest->status === 'pending') Chờ duyệt
                                    @elseif($joinRequest->status === 'approved') Đã duyệt
                                    @else Từ chối @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.club-join-requests.handle', $joinRequest) }}" method="POST" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                    </form>
                                    <form action="{{ route('admin.club-join-requests.handle', $joinRequest) }}" method="POST" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-sm btn-danger">Từ chối</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal gán chủ nhiệm -->
    <div class="modal fade" id="assignManagerModal" tabindex="-1" aria-labelledby="assignManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="assignManagerForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignManagerModalLabel">Gán chủ nhiệm CLB</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="club_id" id="assign_club_id">
                        <div class="form-group">
                            <label for="manager_id">Chọn chủ nhiệm</label>
                            <select name="manager_id" id="manager_id" class="form-control" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->student_id }})</option>
                                @endforeach
                            </select>
                            @error('manager_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editClub(club) {
    document.querySelector('form').action = `/admin/clubs/${club.id}`;
    document.querySelector('input[name="_method"]').value = 'PUT';
    document.querySelector('#name').value = club.name;
    document.querySelector('#description').value = club.description || '';
    document.querySelector('#field').value = club.field;
    document.querySelector('#status').value = club.status;
}

function openAssignManager(clubId) {
    document.querySelector('#assign_club_id').value = clubId;
    document.querySelector('#assignManagerForm').action = `/admin/clubs/${clubId}/assign-manager`;
}
</script>
@endsection