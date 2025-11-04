@extends('admin.layouts.app')

@section('title', 'Quyết toán quỹ sự kiện')
@section('card-title', 'Danh sách quyết toán')

@section('card-body')
<div class="d-flex justify-content-between mb-3">
    <h4>Danh sách quyết toán quỹ sự kiện</h4>
    <a href="{{ route('admin.event_fund_settlements.create') }}" class="btn btn-primary">+ Tạo quyết toán mới</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Sự kiện</th>
            <th>Tổng chi</th>
            <th>Chênh lệch</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($settlements as $settlement)
        <tr>
            <td>{{ $loop->iteration }}</td>
           <td>{{ $settlement->fundRequest->event->name ?? '—' }}</td>

            <td>{{ number_format($settlement->total_spent, 0, ',', '.') }}₫</td>
            <td>{{ $settlement->difference ? number_format($settlement->difference, 0, ',', '.') . '₫' : '-' }}</td>
            <td>
                @php
                    $statusLabels = [
                        'pending_review' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'needs_revision' => 'Cần chỉnh sửa'
                    ];
                @endphp
                <span class="badge 
                    bg-{{ $settlement->status === 'approved' ? 'success' : ($settlement->status === 'pending_review' ? 'warning' : 'danger') }}">
                    {{ $statusLabels[$settlement->status] ?? 'N/A' }}
                </span>
            </td>
            <td>{{ $settlement->created_at->format('d/m/Y') }}</td>
            <td>
                <a href="{{ route('admin.event_fund_settlements.show', $settlement->id) }}" class="btn btn-sm btn-info">Xem</a>
                <a href="{{ route('admin.event_fund_settlements.edit', $settlement->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                  {{-- Nút duyệt --}}
    @if($settlement->status !== 'approved')
    <form action="{{ route('admin.event_fund_settlements.approve', $settlement->id) }}" 
          method="POST" style="display:inline-block;" 
          onsubmit="return confirm('Xác nhận duyệt quyết toán này?');">
        @csrf
        <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
    </form>
    @endif

                <form action="{{ route('admin.event_fund_settlements.destroy', $settlement->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa quyết toán này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">Chưa có quyết toán nào</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
