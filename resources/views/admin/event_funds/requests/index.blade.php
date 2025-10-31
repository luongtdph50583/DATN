@extends('admin.layouts.app')

@section('title', 'Yêu cầu cấp kinh phí')

@section('card-title', 'Danh sách yêu cầu cấp kinh phí')

@section('card-body')
<div class="d-flex justify-content-between mb-3">
    <h4>Danh sách yêu cầu cấp kinh phí</h4>
    <a href="{{ route('admin.event_fund_requests.create') }}" class="btn btn-primary">+ Tạo yêu cầu mới</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên sự kiện</th>
            <th>Người yêu cầu</th>
            <th>Số tiền</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($requests as $req)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $req->event->name ?? '—' }}</td>
            <td>{{ $req->user->name ?? 'N/A' }}</td>
            <td>{{ number_format($req->amount_requested, 0, ',', '.') }}₫</td>
          @php
    $statusLabels = [
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'rejected' => 'Từ chối',
    ];
@endphp

<td>
    <span class="badge 
        bg-{{ $req->status === 'approved' ? 'success' : ($req->status === 'pending' ? 'warning' : 'danger') }}">
        {{ $statusLabels[$req->status] ?? 'N/A' }}
    </span>
</td>

            <td>{{ $req->created_at->format('d/m/Y') }}</td>
            <td>
                <a href="{{ route('admin.event_fund_requests.show', $req->id) }}" class="btn btn-sm btn-info">Xem</a>
                <a href="{{ route('admin.event_fund_requests.edit', $req->id) }}" class="btn btn-sm btn-warning">Sửa</a>

                <!-- Form Xóa -->
                <form action="{{ route('admin.event_fund_requests.destroy', $req->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">Chưa có yêu cầu nào</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
