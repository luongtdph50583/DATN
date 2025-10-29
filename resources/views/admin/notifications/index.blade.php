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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh sách thông báo</h5>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tạo thông báo mới
            </a>
        </div>

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
                        <th scope="col">Nội dung</th>
                        <th scope="col">Người nhận</th>
                        <th scope="col">Kênh gửi</th>
                        <th scope="col">Ngày tạo</th>
                        <th scope="col">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $item['id'] ?? '' }}" class="select-item"
                                    data-type="{{ $item['source'] ?? 'notification' }}">
                            </td>
                            <td>{{ $item['title'] ?? '(Không có tiêu đề)' }}</td>
                            <td>{{ Str::limit($item['content'] ?? '(Không có nội dung)', 60) }}</td>
                            <td>{{ $item['user'] ?? 'Không xác định' }}</td>
                            <td>
                                @foreach ($item['channels'] as $channel => $status)
                                    @php
            $badgeClass = match ($status) {
                'sent' => 'bg-success',
                'failed' => 'bg-danger',
                default => 'bg-secondary',
            };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $channel }}: {{ $status }}</span>
                                @endforeach
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:i') }}</td>
                            <td class="d-flex gap-1">
                                @if(!empty($item['user_id']))
                                    <form action="{{ route('admin.notifications.resend', [$item['batch_id'], $item['user_id']]) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Gửi lại</button>
                                    </form>
                                @endif


                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có thông báo nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>

@endsection
@push('scripts')
    <script>
        // Chọn tất cả
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
