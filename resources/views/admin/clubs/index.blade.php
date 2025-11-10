@extends('admin.layouts.app')

@section('title', 'Danh sách câu lạc bộ')
@section('card-title', 'Danh sách câu lạc bộ')

@section('card-header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <!-- Tiêu đề -->
    <div>
        <span class="fw-bold">Quản lý các câu lạc bộ trong hệ thống</span>
    </div>

    <!-- Nút hành động -->
    <div class="d-flex gap-2">
        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
        </a>
        <a href="{{ route('admin.clubs.trash') }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
</div>


<form id="search-form" class="d-flex gap-2">
    <input type="text" name="keyword" class="form-control form-control-sm"
        placeholder="Tìm tên CLB, lĩnh vực, chủ nhiệm">
    <select name="status" class="form-select form-select-sm">
        <option value="">-- Trạng thái --</option>
        <option value="active">Hoạt động</option>
        <option value="inactive">Ngưng hoạt động</option>
    </select>
</form>
@endsection

@section('card-body')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive mt-3">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>STT</th>
                <th>Logo</th>
                <th>Tên CLB</th>
                <th>Lĩnh vực</th>
                <th>Chủ nhiệm</th>
                <th>Ngày thành lập</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="club-table-body">
            @forelse($clubs as $index => $club)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <img src="{{ $club->logo ? asset('storage/' . $club->logo) : asset('images/default-club.png') }}"
                        alt="Logo" class="rounded-circle" width="40" height="40">
                </td>
                <td><strong>{{ $club->name }}</strong></td>
                <td>{{ $club->field }}</td>
                <td>{{ $club->manager?->member?->user?->name ?? '—' }}</td>
                <td>{{ $club->founded_at ? $club->founded_at->format('d/m/Y') : '—' }}</td>
                <td>
                    @if ($club->status === 'active')
                    <span class="badge bg-success">Hoạt động</span>
                    @else
                    <span class="badge bg-secondary">Ngưng hoạt động</span>
                    @endif
                </td>
                <td class="text-center">
    <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-info btn-sm" title="Chi tiết">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('admin.clubs.edit', $club->id) }}" class="btn btn-warning btn-sm" title="Sửa">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" class="btn btn-danger btn-sm btn-open-delete" 
            data-id="{{ $club->id }}" data-name="{{ $club->name }}" title="Xóa">
        <i class="fas fa-trash"></i>
    </button>
</td>

            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted">Chưa có câu lạc bộ nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

{{-- ✅ MODAL NHẬP LÝ DO --}}
<div class="modal fade" id="deleteClubModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="delete-club-form" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title">Xóa câu lạc bộ</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <p id="club-delete-message" class="fw-bold"></p>
            <div class="mb-3">
                <label class="form-label">Lý do xóa</label>
                <input type="text" name="delete_reason" class="form-control" placeholder="Nhập lý do" required>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-danger">Xóa</button>
          </div>

        </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
    // 👉 Mở modal khi bấm nút Xóa
    document.querySelectorAll('.btn-open-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('club-delete-message').innerText =
                `Bạn chắc muốn xóa câu lạc bộ "${name}" không?`;

            const form = document.getElementById('delete-club-form');
            form.action = `/admin/clubs/${id}`;

            new bootstrap.Modal(document.getElementById('deleteClubModal')).show();
        });
    });
</script>
@endpush
