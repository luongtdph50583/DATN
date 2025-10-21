@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu tạo Câu lạc bộ')

@section('card-body')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Danh sách yêu cầu tạo Câu lạc bộ</h1>

    {{-- Thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách yêu cầu</h5>
        </div>

        {{-- Bộ lọc tìm kiếm --}}
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.club-requests.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label fw-bold">Tìm kiếm</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Tên CLB hoặc người gửi...">
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label fw-bold">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="field" class="form-label fw-bold">Lĩnh vực</label>
                    <input type="text" name="field" id="field" value="{{ request('field') }}" class="form-control" placeholder="Ví dụ: CNTT, Văn hóa...">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    <a href="{{ route('admin.club-requests.index') }}" class="btn btn-secondary w-100">Đặt lại</a>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Người gửi</th>
                        <th>Email</th>
                        <th class="text-start">Tên CLB</th>
                        <th>Lĩnh vực</th>
                        <th>Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th width="180">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->user->name ?? 'Ẩn danh' }}</td>
                            <td>{{ $request->user->email ?? 'Không rõ' }}</td>
                            <td class="text-start">{{ $request->name }}</td>
                            <td>{{ $request->field ?? '—' }}</td>
                            <td>
                                <span class="badge-status {{ $request->status }}">
                                    @switch($request->status)
                                        @case('approved') Đã duyệt @break
                                        @case('rejected') Từ chối @break
                                        @default Chờ duyệt
                                    @endswitch
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.club-requests.show', $request->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>xem
                                </a>

                                <form action="{{ route('admin.club-requests.handle', $request->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" name="action" value="approve" class="btn btn-sm btn-success" title="Duyệt">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" title="Từ chối">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted py-4">Không có yêu cầu nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- CSS cho badge trạng thái --}}
<style>
.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
    min-width: 100px;
}
.badge-status.pending {
    background-color: #ffc107;
    color: #212529;
}
.badge-status.approved {
    background-color: #28a745;
    color: #fff;
}
.badge-status.rejected {
    background-color: #dc3545;
    color: #fff;
}
.table td {
    vertical-align: middle;
    word-wrap: break-word;
    max-width: 250px;
}
</style>
@endsection
