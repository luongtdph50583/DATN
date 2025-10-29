@extends('admin.layouts.app')

@section('title')
    Quản lý thông báo
@endsection

@section('card-title')
    Danh sách thông báo
@endsection

@section('card-header')
    Trung tâm thông báo
@endsection

@section('card-body')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Danh sách thông báo</h5>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo thông báo mới
        </a>
    </div>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 5%">#</th>
                <th scope="col">Tiêu đề</th>
                <th scope="col">Nội dung</th>
                <th scope="col">Người nhận</th>
                <th scope="col">Kênh gửi</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col" style="width: 15%">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($notifications as $notification)
                @php
                    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                    $title = $data['title'] ?? '(Không có tiêu đề)';
                    $message = $data['message'] ?? '';
                    $user = $userMap[$notification->notifiable_id] ?? null;

                    $channels = $data['channels'] ?? ['database'];
                    $channel = implode(', ', array_map(function ($c) {
                        return match ($c) {
                            'database' => 'hệ thống',
                            'mail' => 'email',
                            default => $c,
                        };
                    }, $channels));
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $title }}</td>
                    <td>{{ Str::limit($message, 50) }}</td>
                    <td>{{ $user ? $user->name : 'Người dùng không tồn tại' }}</td>
                    <td>{{ $channel }}</td>
                    <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        {{-- Nếu cần thêm chức năng xem hoặc xóa, bỏ comment dưới đây --}}
                        {{-- <a href="{{ route('admin.notifications.show', $notification->id) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a> --}}
                        {{-- <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa thông báo này?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form> --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Chưa có thông báo nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
@endsection