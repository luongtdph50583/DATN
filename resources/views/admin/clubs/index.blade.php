@extends('admin.layouts.app')

@section('title', 'Danh sách câu lạc bộ')
@section('card-title', 'Danh sách câu lạc bộ')

@section('card-header')
    <div class="d-flex justify-content-between align-items-center">
        <span>Quản lý các câu lạc bộ trong hệ thống</span>
        <a href="#" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm CLB mới
        </a>
    </div>
@endsection

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Tên CLB</th>
                    <th>Lĩnh vực</th>
                    <th>Chủ nhiệm</th>
                    <th>Ngày thành lập</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clubs as $club)
                    <tr>
                        <td>{{ $club->id }}</td>
                        <td>
                            @if($club->logo)
                                <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo" class="rounded-circle" width="40" height="40">
                            @else
                                <img src="{{ asset('images/default-club.png') }}" alt="Logo" class="rounded-circle" width="40" height="40">
                            @endif
                        </td>
                        <td><strong>{{ $club->name }}</strong></td>
                        <td>{{ $club->field }}</td>
                        <td>{{ $club->manager?->name ?? '—' }}</td>
                        <td>{{ $club->founded_at ? $club->founded_at->format('d/m/Y') : '—' }}</td>
                        <td>
                            @if($club->status === 'active')
                                <span class="badge bg-success">Hoạt động</span>
                            @elseif($club->status === 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @else
                                <span class="badge bg-secondary">Ngưng hoạt động</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <!-- Xem chi tiết -->
                            <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            <!-- Sửa -->
                            <a href="{{ route('admin.clubs.edit', $club->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>

                            <!-- Nút Xóa -->
                            <button type="button" class="btn btn-danger btn-sm btn-show-delete" data-id="{{ $club->id }}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>

                            <!-- Form ẩn -->
                            <form action="{{ route('admin.clubs.destroy', $club->id) }}" method="POST"
                                class="delete-form p-3 border rounded bg-light mt-2 d-none" data-id="{{ $club->id }}">
                                @csrf
                                @method('DELETE')

                                <div class="mb-3">
                                    <label for="delete_reason_{{ $club->id }}" class="form-label">Lý do xóa CLB</label>
                                    <input type="text" name="delete_reason" id="delete_reason_{{ $club->id }}" class="form-control" placeholder="Nhập lý do xóa" required>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                    <button type="button" class="btn btn-secondary btn-cancel-delete" data-id="{{ $club->id }}">Hủy</button>
                                </div>
                            </form>
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

@push('scripts')
<script>
    // Show form xóa theo ID
    document.querySelectorAll('.btn-show-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const form = document.querySelector(`.delete-form[data-id="${id}"]`);
            form.classList.remove('d-none');
            this.style.display = 'none';
        });
    });

    // Hủy xóa
    document.querySelectorAll('.btn-cancel-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const form = document.querySelector(`.delete-form[data-id="${id}"]`);
            form.classList.add('d-none');
            const showBtn = document.querySelector(`.btn-show-delete[data-id="${id}"]`);
            showBtn.style.display = 'inline-block';
        });
    });
</script>
@endpush
