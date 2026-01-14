@extends('client.layouts.app')

@section('title', 'Quản lý quỹ: ' . $club->name)

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Quản lý quỹ CLB: {{ $club->name }}</h3>

    {{-- Tổng quỹ & thống kê --}}   
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-2">
                <h6>Tổng quỹ hiện tại</h6>
                <p class="display-6 text-success">{{ number_format($fund?->balance ?? 0, 0, ',', '.') }} đ</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-2">
                <h6>Tổng thu</h6>
                <p class="display-6 text-primary">
                    {{ number_format($transactions->where('type','income')->sum('collected_amount'),0,',','.') }} đ
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-2">
                <h6>Tổng chi</h6>
                <p class="display-6 text-danger">
    {{ number_format($totalApprovedExpense, 0, ',', '.') }} đ
</p>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-center justify-content-center">
            <button class="btn btn-sm btn-primary me-2" data-bs-toggle="collapse" data-bs-target="#addTransactionForm">
                Thu tiền
            </button>
            <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#expenseTransactionForm">
                Chi tiền
            </button>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('club_manager.fund.index', $club->id) }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label>Từ ngày</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <label>Đến ngày</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary mt-1">Lọc</button>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('club_manager.fund.export', $club->id) }}" class="btn btn-outline-success mt-1">
                        Xuất Excel
                    </a>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('club_manager.fund.index', $club->id) }}" class="btn btn-outline-secondary mt-1">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Form thêm giao dịch Thu --}}
    <div class="collapse mb-3" id="addTransactionForm">
        <div class="card card-body">
            <form action="{{ route('club_manager.fund.transactions.store', $club->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="income">

                <div class="mb-2">
                    <label>Số tiền dự kiến</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ghi chú / Nội dung</label>
                    <textarea name="description" id="editor-income" class="form-control" rows="8">{{ old('description') }}</textarea>
                </div>

                <div class="mb-2">
                    <label>Danh mục thu</label>
                    <select name="category" id="income_category" class="form-select">
                        <option value="">-- Chọn danh mục --</option>
                        <option value="membership_fee">Hội phí</option>
                        <option value="donation">Đóng góp</option>
                        <option value="other">Khác</option>
                    </select>
                    <input type="text" name="custom_category" class="form-control mt-1" placeholder="Nhập danh mục khác nếu chọn Khác" style="display:none;">
                </div>

                <div class="mb-2">
                    <label>Khoảng thời gian thu</label>
                    <div class="row">
                        <div class="col"><input type="date" name="start_date" class="form-control"></div>
                        <div class="col"><input type="date" name="end_date" class="form-control"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <label>Hình ảnh chứng từ (tùy chọn)</label>
                    <input type="file" name="receipt" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-success">Thêm giao dịch thu</button>
            </form>
        </div>
    </div>

    {{-- Form thêm giao dịch Chi --}}
    <div class="collapse mb-3" id="expenseTransactionForm">
        <div class="card card-body">
            <form action="{{ route('club_manager.fund.transactions.store', $club->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="expense">

                <div class="mb-2">
                    <label>Số tiền chi</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ghi chú / Nội dung</label>
                    <textarea name="description" id="editor-expense" class="form-control" rows="8">{{ old('description') }}</textarea>
                </div>

                <div class="mb-2">
                    <label>Loại chi</label>
                    <select name="category" id="expenseCategory" class="form-select">
                        <option value="event_expense">Chi cho sự kiện</option>
                        <option value="other">Chi khác</option>
                    </select>
                </div>

                <div class="mb-2" id="eventSelectWrapper">
                    <label>Chọn sự kiện</label>
                    <select name="event_id" class="form-select">
                        <option value="">-- Chọn sự kiện --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2" id="customCategoryWrapper" style="display:none;">
                    <label>Danh mục chi khác</label>
                    <input type="text" name="custom_category" class="form-control" placeholder="Nhập danh mục chi khác">
                </div>

                <div class="mb-2">
                    <label>Hình ảnh chứng từ (bắt buộc)</label>
                    <input type="file" name="receipt" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-danger">Thêm giao dịch chi</button>
            </form>
        </div>
    </div>

    {{-- Bảng lịch sử giao dịch --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Lịch sử giao dịch</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Loại</th>
                            <th>Số tiền </th>
                            <th>Danh mục</th>
                            <th>Người tạo</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>
                                <span class="badge {{ $transaction->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $transaction->type === 'income' ? 'Thu' : 'Chi' }}
                                </span>
                            </td>
                            <td>{{ number_format($transaction->amount,0,',','.') }} đ</td>
                            <td>
                                @php
                                    $labels = [
                                        'membership_fee' => 'Hội phí',
                                        'donation' => 'Đóng góp',
                                        'other' => 'Khác',
                                        'event_expense' => 'Chi cho sự kiện',
                                    ];
                                @endphp
                                {{ $transaction->custom_category ?? ($labels[$transaction->category] ?? $transaction->category ?? '-') }}
                            </td>
                            <td>{{ $transaction->creator->name ?? '-' }}</td>
                            <td>
                                @switch($transaction->status)
                                    @case('pending')   <span class="badge bg-warning">Chờ duyệt</span> @break
                                    @case('approved')  <span class="badge bg-primary">Đã duyệt</span> @break
                                    @case('in_progress') <span class="badge bg-info text-dark">Đang thu/chi</span> @break
                                    @case('completed') <span class="badge bg-success">Hoàn tất</span> @break
                                    @default <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endswitch
                            </td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-nowrap text-center">

                                {{-- DUYỆT KHOẢN THU --}}
                                @if($transaction->type === 'income' && $transaction->status === 'pending')
                                    <form action="{{ route('club_manager.approve.income', [$club->id, $transaction->id]) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-primary"
                                                onclick="return confirm('Xác nhận DUYỆT khoản thu này?')">
                                            Duyệt
                                        </button>
                                    </form>
                                @elseif($transaction->type === 'income' && $transaction->status === 'approved')
                                    <a href="{{ route('club_manager.fund.transactions.edit', [$club->id, $transaction->id]) }}"
                                       class="btn btn-sm btn-info">Cập nhật thu</a>
                                @endif

                                {{-- DUYỆT KHOẢN CHI --}}
                                @if($transaction->type === 'expense' && $transaction->status === 'pending')
                                    <form action="{{ route('club_manager.approve.expense', [$club->id, $transaction->id]) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-warning"
                                                onclick="return confirm('Duyệt khoản CHI sẽ trừ tiền quỹ ngay lập tức!\nBạn có chắc chắn?')">
                                            Duyệt chi
                                        </button>
                                    </form>
                                @endif

                                {{-- Xem chi tiết --}}
                                <button type="button" class="btn btn-sm btn-secondary ms-1"
                                        data-bs-toggle="modal" data-bs-target="#transactionModal{{ $transaction->id }}">
                                    Chi tiết
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Chưa có giao dịch nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal chi tiết --}}
    @foreach($transactions as $transaction)
    <div class="modal fade" id="transactionModal{{ $transaction->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết giao dịch #{{ $transaction->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr><th width="180">Loại:</th><td>{{ $transaction->type === 'income' ? 'Thu' : 'Chi' }}</td></tr>
                        <tr><th>Số tiền dự kiến:</th><td>{{ number_format($transaction->amount,0,',','.') }} đ</td></tr>
                        <tr><th>Số tiền thực tế:</th><td>{{ number_format($transaction->collected_amount ?? 0,0,',','.') }} đ</td></tr>
                        <tr><th>Ghi chú:</th><td>{!! nl2br(e($transaction->description ?? '-')) !!}</td></tr>
                        <tr><th>Danh mục:</th><td>{{ $transaction->custom_category ?? ($labels[$transaction->category] ?? $transaction->category ?? '-') }}</td></tr>
                        <tr><th>Người tạo:</th><td>{{ $transaction->creator->name ?? '-' }}</td></tr>
                        <tr><th>Trạng thái:</th><td>
                            @switch($transaction->status)
                                @case('pending') Chờ duyệt @break
                                @case('approved') Đã duyệt @break
                                @case('completed') Hoàn tất @break
                                @case('in_progress') Đang thu/chi @break
                                @default {{ ucfirst($transaction->status) }}
                            @endswitch
                        </td></tr>
                        <tr><th>Ngày tạo:</th><td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
                        @if($transaction->event_id)
                        <tr><th>Sự kiện liên quan:</th><td>{{ $transaction->event->name ?? '-' }}</td></tr>
                        @endif
                        <tr>
                            <th>Chứng từ:</th>
                            <td>
                                @if($transaction->receipt)
                                    <a href="{{ asset('storage/'.$transaction->receipt) }}" target="_blank" class="text-decoration-underline">
                                        Xem ảnh chứng từ
                                    </a>
                                @else
                                    Không có
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/translations/vi.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tạo 2 editor riêng cho 2 form
        ClassicEditor.create(document.querySelector('#editor-income'), { language: 'vi' }).catch(err => console.error(err));
        ClassicEditor.create(document.querySelector('#editor-expense'), { language: 'vi' }).catch(err => console.error(err));

        // Toggle form thu/chi
        const thuForm = document.getElementById('addTransactionForm');
        const chiForm = document.getElementById('expenseTransactionForm');
        thuForm.addEventListener('show.bs.collapse', () => bootstrap.Collapse.getInstance(chiForm)?.hide());
        chiForm.addEventListener('show.bs.collapse', () => bootstrap.Collapse.getInstance(thuForm)?.hide());

        // Custom category thu
        const incomeCat = document.getElementById('income_category');
        const incomeCustom = incomeCat?.closest('form')?.querySelector('input[name="custom_category"]');
        if (incomeCat && incomeCustom) {
            incomeCat.addEventListener('change', () => {
                incomeCustom.style.display = incomeCat.value === 'other' ? 'block' : 'none';
                incomeCustom.required = incomeCat.value === 'other';
            });
        }

        // Expense category toggle
        const expenseCat = document.getElementById('expenseCategory');
        const eventWrapper = document.getElementById('eventSelectWrapper');
        const customWrapper = document.getElementById('customCategoryWrapper');
        function toggleExpense() {
            if (expenseCat.value === 'event_expense') {
                eventWrapper.style.display = 'block';
                customWrapper.style.display = 'none';
            } else {
                eventWrapper.style.display = 'none';
                customWrapper.style.display = 'block';
            }
        }
        expenseCat?.addEventListener('change', toggleExpense);
        toggleExpense();
    });
</script>
@endsection