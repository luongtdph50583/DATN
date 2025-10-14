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

                               <a href="{{ route('admin.clubs.showAssignForm', $club->id) }}" 
   class="btn btn-sm btn-info">
   <i class="fas fa-user"></i> Gán chủ nhiệm
</a>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
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
