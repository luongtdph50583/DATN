@extends('client.layouts.app')

@section('title', 'Quản lý quỹ: ' . $club->name)

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Quản lý quỹ CLB: {{ $club->name }}</h3>

    {{-- Tổng quỹ --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title">Tổng quỹ hiện tại</h5>
                <p class="card-text display-6 text-success">
                    {{ number_format($fund?->total_amount ?? 0, 0, ',', '.') }} đ
                </p>
            </div>
            <div>
                {{-- Nút thêm/rút tiền --}}
                <a href="#" class="btn btn-sm btn-primary me-2">Thêm tiền</a>
                <a href="#" class="btn btn-sm btn-warning">Rút tiền</a>
            </div>
        </div>
    </div>

    {{-- Bảng giao dịch quỹ --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Lịch sử giao dịch</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Số tiền</th>
                            <th>Ghi chú</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fund?->transactions ?? [] as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ number_format($transaction->amount, 0, ',', '.') }} đ</td>
                            <td>{{ $transaction->note }}</td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Chưa có giao dịch nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
