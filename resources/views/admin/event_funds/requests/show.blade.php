@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu cấp kinh phí')
@section('card-title', 'Chi tiết yêu cầu cấp kinh phí')

@section('card-body')
<div class="container">
    <h2>Chi tiết yêu cầu cấp kinh phí</h2>

    <table class="table table-bordered">
        <tr>
            <th>Sự kiện</th>
            <td>{{ $request->event->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Số tiền yêu cầu</th>
            <td>{{ number_format($request->amount_requested) }} VNĐ</td>
        </tr>
        <tr>
            <th>Người tạo</th>
               <td>{{ $request->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Trạng thái</th>
            <td>{{ $request->status }}</td>
        </tr>
        <tr>
            <th>Ngày tạo</th>
            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th>Ngày cập nhật</th>
            <td>{{ $request->updated_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
