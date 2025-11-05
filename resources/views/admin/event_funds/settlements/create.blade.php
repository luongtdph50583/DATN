@extends('admin.layouts.app')

@section('title', 'Tạo quyết toán quỹ sự kiện')
@section('card-title', 'Tạo quyết toán mới')

@section('card-body')
<div class="container">
    <form action="{{ route('admin.event_fund_settlements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 🔹 Yêu cầu liên quan --}}
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

        {{-- 🔹 Tổng chi --}}
        <div class="mb-3">
            <label>Tổng chi</label>
            <input type="number" name="total_spent" class="form-control" value="{{ old('total_spent') }}" required>
        </div>

        {{-- 🔹 Chi tiết khoản chi --}}
        <div class="mb-3">
            <label class="form-label">Chi tiết khoản chi</label>
            <table class="table table-bordered" id="expense-table">
                <thead>
                    <tr>
                        <th style="width:60%">Tên khoản chi</th>
                        <th style="width:30%">Số tiền (₫)</th>
                        <th style="width:10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="expense_name[]" class="form-control" placeholder="Ví dụ: Thuê sân" required>
                        </td>
                        <td>
                            <input type="number" name="expense_amount[]" class="form-control" placeholder="5000000" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-success btn-sm" id="add-row">+ Thêm dòng</button>
        </div>

        {{-- 🔹 Upload hoá đơn --}}
        <div class="mb-3">
            <label>Upload hóa đơn / chứng từ</label>
            <input type="file" name="receipts[]" class="form-control" multiple>
        </div>

        {{-- 🔹 Nút hành động --}}
        <button type="submit" class="btn btn-primary">Tạo quyết toán</button>
        <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#expense-table tbody');
    const addRowBtn = document.getElementById('add-row');

    // 🟢 Thêm dòng mới
    addRowBtn.addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="expense_name[]" class="form-control" placeholder="Ví dụ: Thuê sân" required></td>
            <td><input type="number" name="expense_amount[]" class="form-control" placeholder="5000000" required></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
        `;
        tableBody.appendChild(newRow);
    });

    // 🔴 Xóa dòng
    tableBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endpush
