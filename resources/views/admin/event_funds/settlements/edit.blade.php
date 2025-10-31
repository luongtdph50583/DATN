@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa quyết toán')
@section('card-title', 'Chỉnh sửa quyết toán')

@section('card-body')
<div class="container">
    <form action="{{ route('admin.event_fund_settlements.update', $settlement->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label><strong>Tên Sự Kiện</strong></label>
            <span>{{ $settlement->fundRequest->event->name ?? '—' }}</span>
        </div>

        <div class="mb-3">
            <label>Tổng chi</label>
            <input type="number" name="total_spent" class="form-control" value="{{ old('total_spent', $settlement->total_spent) }}" required>
        </div>

        <div class="mb-3">
            <label>Chi tiết khoản chi</label>
           <textarea name="details" class="form-control" rows="5">{{ old('details', json_encode($settlement->details, JSON_UNESCAPED_UNICODE)) }}</textarea>

        </div>

        <div class="mb-3">
            <label>Upload hóa đơn / chứng từ mới</label>
            <input type="file" name="receipts[]" class="form-control" multiple>

            @if($settlement->receipts)
                <div class="mt-2">
                    <strong>Hóa đơn hiện tại:</strong><br>
                    @foreach($settlement->receipts as $file)
                        <a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a><br>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="pending_review" {{ $settlement->status == 'pending_review' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ $settlement->status == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="needs_revision" {{ $settlement->status == 'needs_revision' ? 'selected' : '' }}>Cần chỉnh sửa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
