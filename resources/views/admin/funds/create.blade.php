@extends('admin.layouts.app')

@section('card-title', 'Thêm giao dịch quỹ')
@section('card-header', 'Thông tin giao dịch')

@section('card-body')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Thêm giao dịch quỹ</h1>
    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin giao dịch</h6>
    </div>
    <div class="card-body">
       <form action="{{ route('admin.funds.store') }}" method="POST" enctype="multipart/form-data">

            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="club_id">Câu lạc bộ <span class="text-danger">*</span></label>
                        <select name="club_id" id="club_id" class="form-control @error('club_id') is-invalid @enderror" required>
                            <option value="">Chọn câu lạc bộ</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="club-balance-wrapper" style="display:none;">
                            <label>Số dư hiện tại của CLB:</label>
                            <p id="club-balance" class="font-weight-bold text-primary mb-2">Đang tải...</p>
                        </div>
                        @error('club_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Loại giao dịch <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">Chọn loại giao dịch</option>
                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Thu</option>
                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Chi</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount">Số tiền (VND) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                               value="{{ old('amount') }}" min="0" step="0.01" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Danh mục</label>
                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror">
                            <option value="">Chọn danh mục</option>
                            <option value="Hoạt động sự kiện" {{ old('category') == 'Hoạt động sự kiện' ? 'selected' : '' }}>Hoạt động sự kiện</option>
                            <option value="Quà tặng" {{ old('category') == 'Quà tặng' ? 'selected' : '' }}>Quà tặng</option>
                            <option value="Văn phòng phẩm" {{ old('category') == 'Văn phòng phẩm' ? 'selected' : '' }}>Văn phòng phẩm</option>
                            <option value="Đào tạo" {{ old('category') == 'Đào tạo' ? 'selected' : '' }}>Đào tạo</option>
                            <option value="Hỗ trợ thành viên" {{ old('category') == 'Hỗ trợ thành viên' ? 'selected' : '' }}>Hỗ trợ thành viên</option>
                            <option value="Khác" {{ old('category') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" id="event-wrapper" style="display:none;">
                    <label for="event_id">Sự kiện liên quan <span class="text-danger">*</span></label>
                    <select name="event_id" id="event_id" class="form-control @error('event_id') is-invalid @enderror">
                        <option value="">Chọn sự kiện</option>
                        <!-- options sẽ được populate bằng JS -->
                    </select>
                    @error('event_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small id="event-help" class="form-text text-muted">Chọn sự kiện thuộc câu lạc bộ đã chọn.</small>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Mô tả giao dịch <span class="text-danger">*</span></label>
                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                          placeholder="Mô tả chi tiết về giao dịch..." required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Tối đa 1000 ký tự</small>
            </div>
            <div class="form-group">
    <label for="receipt">Hóa đơn / Chứng từ</label>
    <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
    @error('receipt')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Chọn file ảnh hoặc PDF (tối đa 5MB)</small>
</div>

            @if(Auth::user()->role === 'admin')
                <div class="form-group">
                    <label for="status">Trạng thái</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu giao dịch
                </button>
                <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryEl = document.getElementById('category');
    const clubEl = document.getElementById('club_id');
    const eventWrapper = document.getElementById('event-wrapper');
    const eventSelect = document.getElementById('event_id');
    const balanceWrapper = document.getElementById('club-balance-wrapper');
    const balanceEl = document.getElementById('club-balance');
    const amountEl = document.getElementById('amount');
    const typeEl = document.getElementById('type');

    async function loadClubBalance(clubId) {
        if (!clubId) {
            balanceWrapper.style.display = 'none';
            return;
        }
        balanceWrapper.style.display = 'block';
        balanceEl.textContent = 'Đang tải...';
        try {
            const resp = await fetch(`/admin/club-balance/${clubId}`, { headers: { 'Accept': 'application/json' } });
            const data = await resp.json();
            if (data.success) {
                balanceEl.textContent = data.formatted;
                balanceEl.dataset.rawBalance = data.balance;
            } else {
                balanceEl.textContent = 'Không thể tải số dư';
                balanceEl.dataset.rawBalance = 0;
            }
        } catch (err) {
            balanceEl.textContent = 'Lỗi khi tải số dư';
            balanceEl.dataset.rawBalance = 0;
        }
    }

    function shouldShowEventSelect() {
        return categoryEl.value === 'Hoạt động sự kiện' && clubEl.value !== '';
    }

    async function loadEventsForClub(clubId) {
        eventSelect.innerHTML = '<option value="">Đang tải...</option>';
        try {
            const resp = await fetch(`/admin/events-by-club/${clubId}`, { headers: { 'Accept': 'application/json' } });
            const json = await resp.json();
            if (json.success && Array.isArray(json.data)) {
                if (json.data.length === 0) {
                    eventSelect.innerHTML = '<option value="">Không có sự kiện cho CLB này</option>';
                } else {
                    let html = '<option value="">Chọn sự kiện</option>';
                    json.data.forEach(ev => {
                        const selected = ev.id == "{{ old('event_id') }}" ? 'selected' : '';
                        html += `<option value="${ev.id}" ${selected} data-budget-current="${ev.budget_current}">${ev.name ?? ev.title} ${ev.start_time ? ' - ' + ev.start_time.substr(0,10) : ''}</option>`;
                    });
                    eventSelect.innerHTML = html;
                }
            } else {
                eventSelect.innerHTML = '<option value="">Không thể tải dữ liệu</option>';
            }
        } catch (err) {
            console.error(err);
            eventSelect.innerHTML = '<option value="">Lỗi khi tải sự kiện</option>';
        }
    }

    function updateEventVisibility() {
        if (shouldShowEventSelect()) {
            eventWrapper.style.display = 'block';
            loadEventsForClub(clubEl.value);
        } else {
            eventWrapper.style.display = 'none';
            eventSelect.value = '';
        }
    }

    function validateAmount() {
        let maxAmount = 0;

        if (categoryEl.value === 'Hoạt động sự kiện' && eventSelect.value) {
            maxAmount = parseFloat(eventSelect.selectedOptions[0].dataset.budgetCurrent || 0);
        } else {
            maxAmount = parseFloat(balanceEl.dataset.rawBalance || 0);
        }

        const amount = parseFloat(amountEl.value || 0);
        if (typeEl.value === 'expense' && amount > maxAmount) {
            amountEl.setCustomValidity('Số tiền chi không được vượt quá ngân sách!');
        } else {
            amountEl.setCustomValidity('');
        }
    }

    clubEl.addEventListener('change', function() {
        updateEventVisibility();
        loadClubBalance(this.value);
    });

    categoryEl.addEventListener('change', updateEventVisibility);
    eventSelect.addEventListener('change', validateAmount);
    amountEl.addEventListener('input', validateAmount);
    typeEl.addEventListener('change', validateAmount);

    const descEl = document.getElementById('description');
    descEl.addEventListener('input', function() {
        const maxLength = 1000;
        const remaining = maxLength - this.value.length;
        let counter = document.getElementById('char-counter');
        if (!counter) {
            counter = document.createElement('small');
            counter.id = 'char-counter';
            counter.className = 'form-text text-muted';
            this.parentNode.appendChild(counter);
        }
        counter.textContent = `Còn lại: ${remaining} ký tự`;
        counter.className = remaining < 0 ? 'form-text text-danger' : 'form-text text-muted';
    });

    if (clubEl.value) loadClubBalance(clubEl.value);
    updateEventVisibility();
});
</script>
@endsection
