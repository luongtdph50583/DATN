@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa yêu cầu cấp kinh phí')

@section('card-title', 'Chỉnh sửa yêu cầu cấp kinh phí')

@section('card-body')
<div class="container">
    <h2>Chỉnh sửa yêu cầu cấp kinh phí</h2>

    <form action="{{ route('admin.event_fund_requests.update', $request->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Sự kiện</label>
            <select name="event_id" class="form-control">
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $request->event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Số tiền yêu cầu</label>
            <input type="number" name="amount_requested" class="form-control" value="{{ $request->amount_requested }}" required>
        </div>

        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
