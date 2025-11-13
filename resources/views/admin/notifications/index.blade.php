@extends('admin.layouts.app')

@section('title', 'Quản lý thông báo')
@section('card-title', 'Danh sách thông báo')
@section('card-header', 'Trung tâm thông báo')


@section('card-body')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center gap-3 mb-3">
        <h5 class="mb-0">Danh sách thông báo</h5>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo thông báo mới
        </a>
    </div>

    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-sm-6 col-md-3">
            <label for="from_date" class="form-label">Từ ngày</label>
            <input type="date" id="from_date" name="from_date" class="form-control"
                value="{{ $filters['from_date'] ?? '' }}">
        </div>
        <div class="col-sm-6 col-md-3">
            <label for="to_date" class="form-label">Đến ngày</label>
            <input type="date" id="to_date" name="to_date" class="form-control" value="{{ $filters['to_date'] ?? '' }}">
        </div>
        <div class="col-sm-12 col-md-6 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Lọc</button>
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.notifications.bulkDelete') }}" id="bulk-delete-form">
        @csrf

        <div class="mb-2">
            <button type="button" id="bulk-delete-btn" class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i> Xóa đã chọn
            </button>
        </div>

        <table id="notifications-table" class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 5%">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th scope="col">Tiêu đề</th>
                    <th scope="col">Người nhận</th>
                    <th scope="col">Kênh gửi</th>
                    <th scope="col">Thời gian</th>
                    <th scope="col" style="width: 10%">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activities as $index => $item)
                    <tr>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $item['id'] ?? '' }}" class="select-item"
                                data-type="{{ $item['source'] ?? 'notification' }}">
                        </td>
                        <td>{{ $item['title'] ?? '(Không có tiêu đề)' }}</td>
                        <td>{{ $item['user'] ?? 'Không xác định' }}</td>
                        <td>
                            @forelse ($item['channels'] as $channel => $status)
                                @php
                                    $badgeClass = match ($status) {
                                        'sent' => 'bg-success',
                                        'failed' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $channel }}: {{ $status }}</span>
                            @empty
                                <span class="text-muted">Không rõ</span>
                            @endforelse
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:i') }}</td>
                        <td class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#notificationModal{{ $index }}">
                                Xem chi tiết
                            </button>

                            @if(!empty($item['user_id']))
                                <form action="{{ route('admin.notifications.resend', [$item['batch_id'], $item['user_id']]) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Gửi lại</button>
                                </form>
                            @endif
                        </td>
                    </tr>

                    <div class="modal fade" id="notificationModal{{ $index }}" tabindex="-1"
                        aria-labelledby="notificationModalLabel{{ $index }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="notificationModalLabel{{ $index }}">
                                        {{ $item['title'] ?? '(Không có tiêu đề)' }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Người nhận:</strong> {{ $item['user'] ?? 'Không xác định' }}</p>
                                    <p><strong>Thời gian:</strong>
                                        {{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:i') }}</p>
                                    <p><strong>Kênh gửi:</strong>
                                        @forelse ($item['channels'] as $channel => $status)
                                            <span class="badge bg-info text-dark">{{ $channel }} ({{ $status }})</span>
                                        @empty
                                            <span class="text-muted">Không rõ</span>
                                        @endforelse
                                    </p>
                                    <hr>
                                    <h6>Nội dung</h6>
                                    <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;">
                                        {{ $item['content'] ?? '(Không có nội dung)' }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Chưa có thông báo nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectAll = document.getElementById('select-all');
            const form = document.getElementById('bulk-delete-form');

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    const checked = this.checked;
                    document.querySelectorAll('.select-item').forEach(item => {
                        item.checked = checked;
                    });
                });
            }

            if (bulkDeleteBtn && form) {
                bulkDeleteBtn.addEventListener('click', function () {
                    if (!confirm('Bạn có chắc muốn xóa các thông báo đã chọn?')) return;

                    const selected = form.querySelectorAll('.select-item:checked');
                    form.querySelectorAll('.dynamic-type').forEach(el => el.remove());

                    if (selected.length === 0) {
                        alert('Vui lòng chọn ít nhất một thông báo để xóa.');
                        return;
                    }

                    selected.forEach(item => {
                        const id = item.value;
                        const type = item.dataset.type ?? 'notification';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `types[${id}]`;
                        input.value = type;
                        input.classList.add('dynamic-type');

                        form.appendChild(input);
                    });

                    form.submit();
                });
            }
        });
    </script>
@endpush
