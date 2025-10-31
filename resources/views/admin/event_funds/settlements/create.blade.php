@extends('admin.layouts.app')

@section('title', 'Tạo quyết toán quỹ sự kiện')
@section('card-title', 'Tạo quyết toán mới')

@section('card-body')
<div class="container">
    <form action="{{ route('admin.event_fund_settlements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Yêu cầu liên quan</label>
            <select name="fund_request_id" class="form-control" required>
                @foreach($approvedRequests as $req)
                    <option value="{{ $req->id }}">
                        {{ $req->event->name ?? '—' }} - {{ number_format($req->amount_requested, 0, ',', '.') }}₫
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tổng chi</label>
            <input type="number" name="total_spent" class="form-control" value="{{ old('total_spent') }}" required>
        </div>

        <div class="mb-3">
            <label>Chi tiết khoản chi</label>
            <textarea name="details" class="form-control" placeholder='[{"name":"Thuê sân","amount":5000000}]'>{{ old('details') }}</textarea>
            <small class="text-muted">Nhập JSON các khoản chi, ví dụ: [{"name":"Thuê sân","amount":5000000}]</small>
        </div>

        <div class="mb-3">
            <label>Upload hóa đơn / chứng từ</label>
            <input type="file" name="receipts[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Tạo quyết toán</button>
        <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
