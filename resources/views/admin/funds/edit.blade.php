@extends('admin.layouts.app')

@section('card-title', 'Chỉnh sửa giao dịch quỹ')
@section('card-header', 'Thông tin giao dịch')

@section('card-body')
<div class="edit-page">
    <div class="page-header">
        <h1 class="page-title">Chỉnh sửa giao dịch quỹ</h1>
        <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}" class="btn-back">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Quay lại</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="alert error">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="close">×</button>
        </div>
    @endif

    <div class="form-card">
        <div class="card-header">Thông tin giao dịch</div>
        <div class="card-body">
            <form action="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.update' : 'club-manager.funds.update', $fund) }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- === Câu lạc bộ + Loại giao dịch === -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Câu lạc bộ <span class="req">*</span></label>
                        <select name="club_id" class="input" required>
                            <option value="">Chọn câu lạc bộ</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ old('club_id', $fund->club_id) == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Loại giao dịch <span class="req">*</span></label>
                        <select name="type" class="input" required>
                            <option value="">Chọn loại</option>
                            <option value="income" {{ old('type', $fund->type) == 'income' ? 'selected' : '' }}>Thu</option>
                            <option value="expense" {{ old('type', $fund->type) == 'expense' ? 'selected' : '' }}>Chi</option>
                        </select>
                    </div>
                </div>

                <!-- === Số tiền + Danh mục === -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Số tiền (VND) <span class="req">*</span></label>
                        <input type="number" name="amount" class="input" value="{{ old('amount', $fund->amount) }}" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Danh mục</label>
                        <select name="category" id="category" class="input">
                            <option value="">Chọn danh mục</option>
                            <option value="Hoạt động sự kiện" {{ old('category', $fund->category) == 'Hoạt động sự kiện' ? 'selected' : '' }}>Hoạt động sự kiện</option>
                            <option value="Quà tặng" {{ old('category', $fund->category) == 'Quà tặng' ? 'selected' : '' }}>Quà tặng</option>
                            <option value="Văn phòng phẩm" {{ old('category', $fund->category) == 'Văn phòng phẩm' ? 'selected' : '' }}>Văn phòng phẩm</option>
                            <option value="Đào tạo" {{ old('category', $fund->category) == 'Đào tạo' ? 'selected' : '' }}>Đào tạo</option>
                            <option value="Hỗ trợ thành viên" {{ old('category', $fund->category) == 'Hỗ trợ thành viên' ? 'selected' : '' }}>Hỗ trợ thành viên</option>
                            <option value="Khác" {{ old('category', $fund->category) == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>

                <!-- === Sự kiện liên quan === -->
                <div class="form-group full-width" id="event-select-group" style="display: none;">
                    <label>Sự kiện liên quan</label>
                    <select name="event_id" id="event_id" class="input">
                        <option value="">-- Chọn sự kiện --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id', $fund->event_id) == $event->id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- === Mô tả === -->
                <div class="form-group full-width">
                    <label>Mô tả giao dịch <span class="req">*</span></label>
                    <textarea name="description" id="description" rows="4" class="input textarea" required>{{ old('description', $fund->description) }}</textarea>
                    <small id="char-counter" class="hint"></small>
                </div>

                <!-- === Hóa đơn / chứng từ === -->
                <div class="form-group full-width">
                    <label>Hóa đơn / Chứng từ</label>
                    @php
                        $receipts = is_array(json_decode($fund->receipt, true))
                            ? json_decode($fund->receipt, true)
                            : ($fund->receipt ? [$fund->receipt] : []);
                    @endphp

                    <div class="receipts-grid" id="receipt-container">
                        @foreach($receipts as $file)
                            @php
                                $url = asset('storage/' . $file);
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            @endphp
                            <div class="receipt-item" data-file="{{ $file }}">
                                @if(in_array($ext, ['jpg','jpeg','png','gif']))
                                    <a href="{{ $url }}" target="_blank">
                                        <img src="{{ $url }}" alt="Hóa đơn">
                                    </a>
                                @else
                                    <a href="{{ $url }}" target="_blank" class="file-link">{{ basename($file) }}</a>
                                @endif
                                <button type="button" class="remove-btn" onclick="removeReceipt(this)">×</button>
                            </div>
                        @endforeach
                    </div>
                    <input type="file" name="receipt[]" multiple class="input file-input" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <!-- === Trạng thái (Admin) === -->
                @if(Auth::user()->role === 'admin')
                    <div class="form-group full-width">
                        <label>Trạng thái</label>
                        <select name="status" class="input">
                            <option value="approved" {{ old('status', $fund->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="pending" {{ old('status', $fund->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="rejected" {{ old('status', $fund->status) == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Cập nhật</button>
                    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}" class="btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- === SCRIPT xử lý xóa + hiển thị === -->
<script>
function removeReceipt(button) {
    if (!confirm('Xóa file này?')) return;
    const item = button.closest('.receipt-item');
    const file = item.getAttribute('data-file');
    item.remove();
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_receipts[]';
    input.value = file;
    document.querySelector('form').appendChild(input);
}

document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category');
    const eventGroup = document.getElementById('event-select-group');
    function toggleEventDropdown() {
        eventGroup.style.display = (categorySelect.value === 'Hoạt động sự kiện') ? 'block' : 'none';
    }
    categorySelect.addEventListener('change', toggleEventDropdown);
    toggleEventDropdown();
});
</script>

<style>
/* === FULL WIDTH + CLEAN DESIGN === */ :root { --primary: #4361ee; --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --gray: #6b7280; --light: #f8fafc; --border: #e2e8f0; --radius: 10px; --shadow: 0 1px 3px rgba(0,0,0,0.1); --font: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; } * { box-sizing: border-box; margin: 0; padding: 0; } body { font-family: var(--font); background: #f1f5f9; color: #1e293b; line-height: 1.6; } /* FULL WIDTH PAGE */ .edit-page { width: 100%; max-width: 100%; margin: 0 auto; padding: 1.5rem; } /* Header */ .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; } .page-title { font-size: 1.6rem; font-weight: 600; color: #1e293b; } .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; background: #e2e8f0; color: #475569; padding: 0.6rem 1rem; border-radius: var(--radius); font-size: 0.9rem; text-decoration: none; transition: 0.2s; } .btn-back:hover { background: #cbd5e1; transform: translateY(-1px); } .btn-back svg { stroke: #475569; } /* Alert */ .alert { padding: 0.9rem 1.2rem; border-radius: var(--radius); margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: flex-start; font-size: 0.95rem; box-shadow: var(--shadow); background: #fef2f2; color: #991b1b; border-left: 4px solid var(--danger); } .alert ul { margin: 0; padding-left: 1.2rem; } .alert .close { background: none; border: none; font-size: 1.4rem; cursor: pointer; color: var(--gray); opacity: 0.7; } .alert .close:hover { opacity: 1; } /* Form Card - FULL WIDTH */ .form-card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; width: 100%; max-width: 100%; } .card-header { padding: 0.9rem 1.2rem; background: #f8fafc; border-bottom: 1px solid var(--border); font-weight: 600; font-size: 0.95rem; color: #374151; } .card-body { padding: 1.5rem; } /* Form Layout */ .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; } .form-group { display: flex; flex-direction: column; } .form-group.full-width { grid-column: 1 / -1; } .form-group label { font-size: 0.9rem; color: #374151; margin-bottom: 0.4rem; font-weight: 500; } .req { color: var(--danger); font-weight: bold; } .input, .textarea { padding: 0.65rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 0.95rem; background: white; transition: 0.2s; } .input:focus, .textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(67,97,238,0.15); } .error { border-color: var(--danger); } .error-text { color: var(--danger); font-size: 0.8rem; margin-top: 0.3rem; } .hint { font-size: 0.8rem; color: var(--gray); margin-top: 0.3rem; } /* File Input */ .file-input { padding: 0.5rem; border: 1px dashed var(--border); background: #fafafa; border-radius: var(--radius); font-size: 0.9rem; width: 100%; } /* Receipts Grid */ .receipts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 0.75rem; margin-bottom: 0.75rem; } .receipt-item { position: relative; width: 100px; height: 100px; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: #f9fafb; } .receipt-item img { width: 100%; height: 100%; object-fit: cover; } .file-link { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: var(--primary); font-size: 0.8rem; text-align: center; padding: 0.5rem; } .file-link svg { margin-bottom: 0.3rem; stroke: var(--primary); } .remove-btn { position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; background: var(--danger); color: white; border: none; border-radius: 50%; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; } .remove-btn:hover { background: #dc2626; } /* Status Display */ /* Status Display */ .status-display { display: flex; align-items: center; padding: 0.65rem; background: #f9fafb; border: 1px solid var(--border); border-radius: var(--radius); font-size: 0.9rem; min-height: 42px; /* Đồng bộ chiều cao với input */ } .tag { padding: 0.25rem 0.6rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; } .tag.pending { background: #fffbeb; color: var(--warning); } .tag.approved { background: #ecfdf5; color: var(--success); } .tag.rejected { background: #fef2f2; color: var(--danger); } /* Info Grid */ .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1.5rem 0; padding: 1rem; background: #f9fafb; border-radius: var(--radius); border: 1px solid var(--border); } .info-item label { font-size: 0.8rem; color: var(--gray); margin-bottom: 0.3rem; } .info-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; } /* Actions */ .form-actions { display: flex; gap: 0.75rem; margin-top: 1.5rem; flex-wrap: wrap; } .btn-primary, .btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 1.2rem; border-radius: var(--radius); font-size: 0.95rem; font-weight: 500; text-decoration: none; transition: 0.2s; border: none; cursor: pointer; } .btn-primary { background: var(--primary); color: white; } .btn-primary:hover { background: #3b55d3; transform: translateY(-1px); } .btn-secondary { background: #e2e8f0; color: #475569; } .btn-secondary:hover { background: #cbd5e1; } /* Responsive */ @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } .info-grid { grid-template-columns: 1fr; } .form-actions { justify-content: stretch; } .btn-primary, .btn-secondary { flex: 1; justify-content: center; } }
</style>
@endsection
