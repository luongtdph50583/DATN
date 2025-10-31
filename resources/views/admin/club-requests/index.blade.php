@extends('admin.layouts.app')

@section('title', 'Yêu cầu tạo CLB')

@section('card-body')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">📋 Danh sách yêu cầu tạo CLB</h1>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form tìm kiếm --}}
    <form method="GET" action="{{ route('admin.club-requests.index') }}" class="mb-3 row g-2">
        <div class="col-md-4">
            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tìm theo tên CLB...">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">🔍 Tìm kiếm</button>
        </div>
    </form>

    {{-- Bảng danh sách --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tên CLB</th>
                        <th>Người gửi</th>
                        <th>Lĩnh vực</th>
                        <th>Email</th>
                        <th>Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $index => $r)
                        <tr>
                            <td>{{ $index + $requests->firstItem() }}</td>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->user->name ?? 'N/A' }}</td>
                            <td>{{ $r->field ?? '-' }}</td>
                            <td>{{ $r->email ?? '-' }}</td>
                            <td>
                                @if ($r->status == 'pending')
                                    <span class="badge bg-warning text-dark">Đang chờ</span>
                                @elseif ($r->status == 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </td>
                            <td>{{ $r->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.club-requests.show', $r->id) }}" class="btn btn-info btn-sm">
                                    👁 Xem
                                </a>
                                <form action="{{ route('admin.club-requests.destroy', $r->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xác nhận xóa yêu cầu này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑 Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Không có yêu cầu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            <div class="mt-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
