@extends('client.layouts.app')

@section('title', 'Cập nhật giao dịch thu #'.$transaction->id)

@section('content')
<div class="container py-4">
    <h3>Cập nhật giao dịch thu #{{ $transaction->id }}</h3>

    <form action="{{ route('club_manager.fund.transactions.update', [$club_id, $transaction->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Số tiền thực tế đã thu</label>
            <input type="number" step="0.01" name="collected_amount" class="form-control" 
                   value="{{ $transaction->collected_amount ?? $transaction->amount }}" required>
        </div>

        <div class="mb-3">
            <label>File Excel / Chứng từ (Bắt Buộc)</label>
            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv,.jpg,.jpeg,.png,.pdf" required>
            @if($transaction->excel_file)
                <small class="text-muted">File hiện tại: <a href="{{ asset('storage/'.$transaction->excel_file) }}" target="_blank">Xem</a></small>
            @endif
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('club_manager.fund.index', $club_id) }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
