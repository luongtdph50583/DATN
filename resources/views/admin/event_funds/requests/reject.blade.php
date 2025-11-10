@extends('admin.layouts.app')

@section('card-title', 'Từ chối yêu cầu giải ngân')

@section('card-body')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger">Thông tin yêu cầu giải ngân</h6>
    </div>
    <div class="card-body">

        <!-- Thông tin chi tiết yêu cầu -->
        <table class="table table-bordered mb-4">
            <tr>
                <th>ID</th>
                <td>{{ $request->id }}</td>
            </tr>
            <tr>
                <th>Sự kiện</th>
                <td>{{ $request->event->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Câu lạc bộ</th>
                <td>{{ $request->event->club->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Người yêu cầu</th>
                <td>{{ $request->requestedBy->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Số tiền yêu cầu</th>
                <td>{{ number_format($request->amount_requested) }} đ</td>
            </tr>
            <tr>
                <th>Số tiền đã duyệt</th>
                <td>{{ number_format($request->approved_amount ?? 0) }} đ</td>
            </tr>
            <tr>
                <th>Mô tả / Ghi chú</th>
                <td>{{ $request->note }}</td>
            </tr>
            <tr>
                <th>Trạng thái</th>
                <td>
                    @if($request->status === 'pending_disbursement')
                        Chờ giải ngân
                    @elseif($request->status === 'disbursing')
                        Đang giải ngân
                    @elseif($request->status === 'disbursed')
                        Đã giải ngân
                    @elseif($request->status === 'rejected')
                        Từ chối
                    @endif
                </td>
            </tr>
            <tr>
                <th>Ngày tạo</th>
                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <!-- Form từ chối -->
        <form action="{{ route('admin.event_fund_requests.reject.submit', $request->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="rejection_reason" class="form-label">Lý do từ chối</label>
                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required>{{ old('rejection_reason') }}</textarea>
            </div>
            <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
            <a href="{{ route('admin.funds.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection
