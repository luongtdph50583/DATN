@extends('admin.layouts.app')

@section('title', 'Danh sách câu lạc bộ')
@section('card-title', 'Danh sách câu lạc bộ')

@section('card-header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span>Quản lý các câu lạc bộ trong hệ thống</span>
        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm CLB mới
        </a>
    </div>

    <form id="search-form" class="d-flex gap-2">
        <input type="text" name="keyword" class="form-control form-control-sm"
            placeholder="Tìm tên CLB, lĩnh vực, chủ nhiệm">
        <select name="status" class="form-select form-select-sm">
            <option value="">-- Trạng thái --</option>
            <option value="active">Hoạt động</option>
            <option value="pending">Chờ duyệt</option>
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
                            @elseif($club->status === 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @else
                                <span class="badge bg-secondary">Ngưng hoạt động</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            <a href="{{ route('admin.clubs.edit', $club->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <button type="button" class="btn btn-danger btn-sm btn-show-delete"
                                data-id="{{ $club->id }}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>

                            <form action="{{ route('admin.clubs.destroy', $club->id) }}" method="POST"
                                class="delete-form p-3 border rounded bg-light mt-2 d-none" data-id="{{ $club->id }}">
                                @csrf
                                @method('DELETE')
                                <div class="mb-3">
                                    <label for="delete_reason_{{ $club->id }}" class="form-label">Lý do xóa CLB</label>
                                    <input type="text" name="delete_reason" id="delete_reason_{{ $club->id }}"
                                        class="form-control" placeholder="Nhập lý do xóa" required>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                    <button type="button" class="btn btn-secondary btn-cancel-delete"
                                        data-id="{{ $club->id }}">Hủy</button>
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
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const form = document.querySelector(`.delete-form[data-id="${id}"]`);
                form.classList.remove('d-none');
                this.style.display = 'none';
            });
        });

        // Hủy xóa
        document.querySelectorAll('.btn-cancel-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const form = document.querySelector(`.delete-form[data-id="${id}"]`);
                form.classList.add('d-none');
                const showBtn = document.querySelector(`.btn-show-delete[data-id="${id}"]`);
                showBtn.style.display = 'inline-block';
            });
        });

        // Real-time search
        const searchForm = document.getElementById('search-form');
        const tableBody = document.getElementById('club-table-body');

        searchForm.addEventListener('input', function() {
            const formData = new FormData(searchForm);

            fetch("{{ route('admin.clubs.search') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    if (data.length === 0) {
                        html =
                            `<tr><td colspan="8" class="text-center text-muted">Không tìm thấy kết quả phù hợp.</td></tr>`;
                    } else {
                        data.forEach((club, index) => {
                            const logo = club.logo ? `/storage/${club.logo}` :
                                `/images/default-club.png`;
                            const managerName = club.manager?.member?.user?.name ?? '—';
                            const founded = club.founded_at ? new Date(club.founded_at)
                                .toLocaleDateString('vi-VN') : '—';

                            let statusBadge = '';
                            switch (club.status) {
                                case 'active':
                                    statusBadge = '<span class="badge bg-success">Hoạt động</span>';
                                    break;
                                case 'pending':
                                    statusBadge =
                                        '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                                    break;
                                default:
                                    statusBadge =
                                        '<span class="badge bg-secondary">Ngưng hoạt động</span>';
                            }

                            html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><img src="${logo}" class="rounded-circle" width="40" height="40"></td>
                                <td><strong>${club.name}</strong></td>
                                <td>${club.field}</td>
                                <td>${managerName}</td>
                                <td>${founded}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <a href="/admin/clubs/${club.id}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </a>
                                    <a href="/admin/clubs/${club.id}/edit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-show-delete" data-id="${club.id}">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                        `;
                        });
                    }

                    tableBody.innerHTML = html;
                });
        });
    </script>
@endpush
