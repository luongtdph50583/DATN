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
                <th scope="col">Ngày tạo</th>
                <th scope="col" style="width: 15%">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($notifications as $notification)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $notification->data['title'] ?? '(Không có tiêu đề)' }}</td>
                    <td>{{ Str::limit($notification->data['message'] ?? '', 50) }}</td>
                    <td>
                        @php
                            $user = \App\Models\User::find($notification->notifiable_id);
                        @endphp
                        {{ $user ? $user->name : 'Người dùng không tồn tại' }}
                    </td>
                    <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.notifications.show', $notification->id) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa thông báo này?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Chưa có thông báo nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
