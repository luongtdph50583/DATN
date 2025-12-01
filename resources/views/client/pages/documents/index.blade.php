@extends('client.layouts.app')

@section('title', 'Tài liệu CLB - ' . ($club->name ?? 'CLB'))

@section('content')
        <div class="container py-4">

            <h2 class="mb-4 fw-bold">📚 Tài liệu của {{ $club->name }}</h2>

            {{-- Nếu là chủ nhiệm / quản lý có quyền tạo --}}
            @if(in_array($userRole, ['club_manager', 'deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication']))
                <div class="mb-3">
                    <a href="{{ route('club_manager.club.documents.create', $club->id) }}" class="btn btn-primary">
                        ➕ Thêm tài liệu
                    </a>
                </div>
            @endif
            {{-- ✅ Hiển thị thông báo thành công / lỗi --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @if($documents->isEmpty())
                        <p class="text-muted">Chưa có tài liệu nào.</p>
                    @else
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tên tài liệu</th>
                                    <th>Loại file</th>
                                    <th>Người đăng</th>
                                    <th>Trạng thái</th>
                                    <th>Quyền truy cập</th>
                                    <th>Ngày tạo</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $doc)
                                    <tr>
                                        <td>
                                            <a href="{{ route('club_manager.club.documents.view', [$club->id, $doc->id]) }}"
                                                class="text-decoration-none">
                                                <i class="bi bi-file-earmark-text me-1"></i>
                                                {{ $doc->title }}
                                            </a>
                                        </td>

                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ strtoupper($doc->file_type ?? 'N/A') }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $doc->uploader->name ?? 'Không rõ' }}
                                        </td>

                                        <td>
                                            @if($doc->status === 'approved')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Đã duyệt
                                                </span>
                                            @elseif($doc->status === 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock"></i> Chờ duyệt
                                                </span>
                                            @elseif($doc->status === 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> Từ chối
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Không rõ</span>
                                            @endif
                                        </td>

                                        <td>
                                            @php
            $accessLevels = is_array($doc->access_level)
                ? $doc->access_level
                : json_decode($doc->access_level, true);

            $accessLabels = [
                'public' => '🌍 Công khai',
                'member' => '👥 Thành viên',
                'communication' => '📢 Truyền thông',
                'event_manager' => '🎉 Tổ chức',
                'secretary' => '📝 Thư ký',
                'treasurer' => '💰 Thủ quỹ',
                'deputy_manager' => '👔 Phó CN',
                'club_manager' => '⭐ Chủ nhiệm',
            ];
                                            @endphp

                                            @if(in_array('public', $accessLevels ?? []))
                                                <span class="badge bg-success">🌍 Công khai</span>
                                            @elseif(in_array('member', $accessLevels ?? []))
                                                <span class="badge bg-info">👥 Thành viên</span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    🔒 Hạn chế
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $doc->created_at->format('d/m/Y H:i') }}
                                            <br>
                                            <small class="text-muted">{{ $doc->created_at->diffForHumans() }}</small>
                                        </td>

                                       <td class="text-end">
    <div class="btn-group" role="group">
        <a href="{{ route('club_manager.club.documents.view', [$club->id, $doc->id]) }}"
            class="btn btn-sm btn-info" title="Xem">
            <i class="bi bi-eye">xem</i>
        </a>

        @if(in_array($userRole, ['club_manager', 'deputy_manager', 'secretary']))
            <a href="{{ route('club_manager.club.documents.edit', [$club->id, $doc->id]) }}"
                class="btn btn-sm btn-warning" title="Sửa">
                <i class="bi bi-pencil">sửa</i>
            </a>

            <form action="{{ route('club_manager.club.documents.destroy', [$club->id, $doc->id]) }}"
                method="POST" class="d-inline" onsubmit="return confirmDelete(this);">
                @csrf
                @method('DELETE')
                <input type="hidden" name="delete_reason" value="">
                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                    <i class="bi bi-trash">xóa</i>
                </button>
            </form>
        @endif
    </div>
</td>

@push('scripts')
<script>
function confirmDelete(form) {
    const reason = prompt("Nhập lý do xóa tài liệu:");
    if (reason === null || reason.trim() === "") {
        alert("Bạn phải nhập lý do xóa.");
        return false;
    }
    form.querySelector('input[name="delete_reason"]').value = reason;
    return true;
}
</script>
@endpush

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $documents->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
@endsection