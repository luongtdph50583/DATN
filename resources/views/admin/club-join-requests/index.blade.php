@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu tạo Câu lạc bộ')

@section('card-body')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Danh sách yêu cầu tạo Câu lạc bộ</h1>

    {{-- Thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách yêu cầu</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Người gửi</th>
                        <th>Tên CLB</th>
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
                            <td>{{ $request->user->name ?? 'Không rõ' }}</td>
                            <td class="text-start">{{ $request->name }}</td>
                            <td>{{ $request->field ?? '—' }}</td>
                            <td>
                                <span class="badge-status {{ $request->status }}">
                                    @switch($request->status)
                                        @case('approved')
                                            Đã duyệt
                                            @break
                                        @case('rejected')
                                            Từ chối
                                            @break
                                        @default
                                            Chờ duyệt
                                    @endswitch
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.club-requests.show', $request->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Xem
                                </a>

                                <form action="{{ route('admin.club-requests.handle', $request->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted py-4">Không có yêu cầu nào</td>
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
