@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa quyết toán')
@section('card-title', 'Chỉnh sửa quyết toán')

@section('card-body')
<div class="container">
    <form action="{{ route('admin.event_fund_settlements.update', $settlement->id) }}" method="POST" enctype="multipart/form-data" id="settlementForm">
        @csrf
        @method('PUT')

        <!-- Tên sự kiện -->
        <div class="mb-4">
            <label class="form-label">Tên Sự Kiện</label>
            <p class="fs-5 fw-bold text-primary mb-0">{{ $settlement->fundRequest->event->name ?? '—' }}</p>
        </div>

        <!-- Yêu cầu liên quan -->
        <div class="mb-4">
            <label class="form-label">Yêu cầu liên quan</label>
            <select name="fund_request_id" class="form-select" required>
                <option value="">-- Chọn yêu cầu --</option>
                @foreach($approvedRequests as $req)
                    <option value="{{ $req->id }}" {{ old('fund_request_id', $settlement->fund_request_id) == $req->id ? 'selected' : '' }}>
                        {{ $req->event->name }} - {{ number_format($req->amount_requested, 0, ',', '.') }}₫
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tổng chi -->
        <div class="mb-4">
            <label class="form-label">Tổng chi</label>
            <input type="number" name="total_spent" class="form-control"
                   value="{{ old('total_spent', $settlement->total_spent) }}" required min="0">
        </div>

        <!-- Chi tiết khoản chi -->
        <div class="mb-4">
            <label class="form-label">Chi tiết khoản chi</label>
            <div id="details-container">
                @php
                    $detailsData = is_array($settlement->details)
                        ? $settlement->details
                        : (is_string($settlement->details) ? json_decode($settlement->details, true) : []);
                    $detailsData = is_array($detailsData) && count($detailsData) > 0 
                        ? $detailsData 
                        : [['name' => '', 'amount' => '']];
                @endphp

                @foreach($detailsData as $item)
                    <div class="detail-row mb-2 d-flex gap-2 align-items-center">
                        <input type="text" name="detail_name[]" class="form-control" placeholder="Tên khoản chi" value="{{ $item['name'] ?? '' }}">
                        <input type="number" name="detail_amount[]" class="form-control" placeholder="Số tiền" value="{{ $item['amount'] ?? '' }}"  min="0">
                        <button type="button" class="btn btn-outline-danger remove-detail"><i class="fas fa-trash"></i></button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-detail" class="btn btn-outline-primary mt-2">
                <i class="fas fa-plus"></i> Thêm khoản chi
            </button>
            <input type="hidden" name="details" id="details">
        </div>

        <!-- Hóa đơn hiện tại -->
        @php
            $currentReceipts = is_array($settlement->receipts)
                ? $settlement->receipts
                : (is_string($settlement->receipts) ? json_decode($settlement->receipts, true) : []);
        @endphp
        @if(!empty($currentReceipts))
        <div class="mb-4">
            <label class="form-label">Hóa đơn hiện tại</label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($currentReceipts as $file)
                    @php
                        $url = asset('storage/' . $file);
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg','jpeg','png']);
                    @endphp
                    <div class="position-relative border rounded p-2 text-center" style="width:150px;">
                        @if($isImage)
                            <img src="{{ $url }}" class="img-fluid rounded mb-2" style="max-height:100px;" alt="Receipt">
                        @else
                            <a href="{{ $url }}" target="_blank" class="d-block">
                                <i class="fas fa-file-pdf fa-2x text-danger"></i><br>
                                {{ basename($file) }}
                            </a>
                        @endif
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="keep_receipts[]" value="{{ $file }}" checked>
                            <label class="form-check-label small">Giữ</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Upload mới -->
        <div class="mb-4">
            <label class="form-label">Upload hóa đơn mới</label>
            <input type="file" name="receipts[]" multiple class="form-control" accept="image/*,application/pdf">
        </div>

        <!-- Trạng thái -->
        <div class="mb-4">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="pending_review" {{ $settlement->status === 'pending_review' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ $settlement->status === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="needs_revision" {{ $settlement->status === 'needs_revision' ? 'selected' : '' }}>Cần chỉnh sửa</option>
            </select>
        </div>

        <!-- Nút -->
        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Cập nhật</button>
            <a href="{{ route('admin.event_fund_settlements.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Quay lại</a>
        </div>
    </form>
</div><script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('settlementForm');
    const container = document.getElementById('details-container');
    const addBtn = document.getElementById('add-detail');

    // ✅ Thêm khoản chi
    addBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.classList.add('detail-row', 'mb-2', 'd-flex', 'gap-2', 'align-items-center');
        div.innerHTML = `
            <input type="text" name="detail_name[]" class="form-control" placeholder="Tên khoản chi">
            <input type="number" name="detail_amount[]" class="form-control" placeholder="Số tiền" step="1000" min="0">
            <button type="button" class="btn btn-outline-danger remove-detail"><i class="fas fa-trash"></i></button>
        `;
        container.appendChild(div);
    });

    // ✅ Xóa khoản chi
    container.addEventListener('click', e => {
        if (e.target.closest('.remove-detail')) {
            e.target.closest('.detail-row').remove();
        }
    });

    // ✅ Trước khi submit => gom chi tiết vào hidden input JSON
    form.addEventListener('submit', () => {
        const names = document.querySelectorAll('input[name="detail_name[]"]');
        const amounts = document.querySelectorAll('input[name="detail_amount[]"]');
        let data = [];

        names.forEach((n, i) => {
            const name = n.value.trim();
            const amount = parseFloat(amounts[i].value || 0);
            if (name || amount) data.push({ name, amount });
        });

        document.getElementById('details').value = JSON.stringify(data);
    });
});
</script>
@endsection

@section('scripts')

@endsection
