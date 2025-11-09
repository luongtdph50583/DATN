@extends('admin.layouts.app')

@section('card-title', 'Quản lý Quỹ')

@section('card-header', 'Danh sách giao dịch quỹ')

@section('card-body')


@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Bộ lọc -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-form" action="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}">
            <div class="row align-items-end">
          <div class="col-md-3">
                    <div class="form-group">
                        <label for="club_id">Câu lạc bộ</label>
                        <select name="club_id" id="club_id" class="form-control form-control-sm select2">
                            <option value="">Tất cả CLB</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

               
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_from">Từ ngày</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_to">Đến ngày</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="filter-actions d-flex">
                            <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                            <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tóm tắt quỹ -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng thu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-income">0 VND</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tổng chi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-expense">0 VND</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Số dư</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="balance">0 VND</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ duyệt</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $transactions->where('status', 'pending')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách giao dịch -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách giao dịch quỹ</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Câu lạc bộ</th>
                        <th>Loại</th>
                        <th>Số tiền</th>
                        <th>Mô tả</th>
                        <th>Danh mục</th>
                        <th>Trạng thái</th>
                        <th>Người tạo</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->club->name }}</td>
                            <td>
                                @if($transaction->type === 'income')
                                    <span class="badge bg-success text-white">Thu</span>
                                @else
                                    <span class="badge bg-danger text-white">Chi</span>
                                @endif
                            </td>
                            <td class="text-right">{{ $transaction->formatted_amount }}</td>
                            <td>{{ Str::limit($transaction->description, 50) }}</td>
                            <td>{{ $transaction->category ?? '-' }}</td>
                            <td>
                                @if($transaction->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($transaction->status === 'approved')
                                    <span class="badge bg-success text-white">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger text-white">Từ chối</span>
                                @endif
                            </td>
                            <td>{{ $transaction->creator->name }}</td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Xem chi tiết -->
                                    <a href="{{ route('admin.funds.show', $transaction) }}" 
                                       class="btn btn-info btn-action" 
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye fa-lg"></i>
                                        <span class="btn-text">Xem</span>
                                    </a>
                                    
                                    <!-- Chỉnh sửa (Admin hoặc người tạo) -->
                                    @if(Auth::user()->role === 'admin' || $transaction->created_by === Auth::id())
                                        <a href="{{ route('admin.funds.edit', $transaction) }}" 
                                           class="btn btn-warning btn-action" 
                                           title="Chỉnh sửa">
                                            <i class="fas fa-edit fa-lg"></i>
                                            <span class="btn-text">Sửa</span>
                                        </a>
                                    @endif
                                    
                                    <!-- Chỉ Admin mới thấy các nút này -->
                                    @if(Auth::user()->role === 'admin')
                                        <!-- Phê duyệt/Từ chối (chỉ khi pending) -->
                                        @if($transaction->status === 'pending')
                                            <form action="{{ route('admin.funds.approve', $transaction) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn phê duyệt giao dịch này?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-success btn-action" 
                                                        title="Phê duyệt">
                                                    <i class="fas fa-check-circle fa-lg"></i>
                                                    <span class="btn-text">Duyệt</span>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.funds.reject', $transaction) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn từ chối giao dịch này?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-danger btn-action" 
                                                        title="Từ chối">
                                                    <i class="fas fa-times-circle fa-lg"></i>
                                                    <span class="btn-text">Từ chối</span>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <!-- Xóa -->
                                        <form action="{{ route('admin.funds.destroy', $transaction) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa giao dịch này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-action" 
                                                    title="Xóa">
                                                <i class="fas fa-trash-alt fa-lg"></i>
                                                <span class="btn-text">Xóa</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>

// Load fund summary when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadFundSummaryFromFilters();
});

// Load fund summary when club filter changes
['club_id','type','status','date_from','date_to'].forEach(function(id){
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('change', loadFundSummaryFromFilters);
    }
});

function buildQueryFromFilters() {
    const params = new URLSearchParams();
    const clubId = document.getElementById('club_id').value;
    const type = document.getElementById('type').value;
    const status = document.getElementById('status').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;

    if (clubId) params.append('club_id', clubId);
    if (type) params.append('type', type);
    if (status) params.append('status', status);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    return params.toString();
}

function loadFundSummaryFromFilters() {
    const query = buildQueryFromFilters();
    fetch(`{{ route('admin.funds.summary') }}?${query}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-income').textContent = data.formatted_income;
            document.getElementById('total-expense').textContent = data.formatted_expense;
            document.getElementById('balance').textContent = data.formatted_balance;
        })
        .catch(error => {
            console.error('Error loading fund summary:', error);
        });
}
</script>

<style>
/* Đồng bộ chiều cao và canh hàng cho bộ lọc */
.filter-form .form-group label {
    font-weight: 600;
    font-size: .875rem;
    color: #4a5568;
}

.filter-form .form-control {
    height: 36px;
    border-radius: .5rem;
    border: 1px solid #e6e8f0;
}

.filter-form .form-control:focus {
    border-color: #6993ff;
    box-shadow: 0 0 0 .2rem rgba(105,147,255,.15);
}

/* Tránh trình duyệt áp kiểu riêng làm lệch chiều cao */
.filter-form .form-control { min-width: 140px; }

.filter-actions .btn { margin-right: .5rem; }
.filter-actions .btn:last-child { margin-right: 0; }

.filter-form .form-group label {
    display: inline-block;
    margin-bottom: .25rem;
}

.filter-form .form-group {
    margin-bottom: .5rem;
}
/* CSS cho phần hành động với biểu tượng */
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    text-decoration: none;
    border: none;
    cursor: pointer;
    min-width: auto;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-action i {
    font-size: 1rem;
    line-height: 1;
}

.btn-text {
    font-size: 0.8rem;
    font-weight: 500;
}

/* Màu sắc cho từng loại nút */
.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
    color: #212529;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
    color: white;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
    color: white;
}

/* Responsive cho mobile */
@media (max-width: 768px) {
    .action-buttons {
        gap: 0.25rem;
    }
    
    .btn-action {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .btn-action i {
        font-size: 0.9rem;
    }
    
    .btn-text {
        font-size: 0.75rem;
    }
}

/* Responsive cho tablet */
@media (max-width: 992px) {
    .btn-text {
        display: none;
    }
    
    .btn-action {
        padding: 0.5rem;
        min-width: 2.5rem;
        justify-content: center;
    }
}

/* Hiệu ứng hover đặc biệt */
.btn-action:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Animation cho icon */
.btn-action i {
    transition: transform 0.2s ease-in-out;
}

.btn-action:hover i {
    transform: scale(1.1);
}
/* ----- SELECT2 CUSTOM STYLE ----- */
.select2-container .select2-selection--single {
    height: 36px !important;
    border: 1px solid #e6e8f0 !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    padding-left: 10px !important;
    background-color: #fff !important;
    transition: all 0.2s ease-in-out !important;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    color: #2d3748 !important;
    font-size: 14px !important;
    line-height: 34px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 34px !important;
    right: 8px !important;
}

.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #6993ff !important;
    box-shadow: 0 0 0 0.2rem rgba(105,147,255,.15) !important;
}

.select2-dropdown {
    border-radius: 8px !important;
    border: 1px solid #e6e8f0 !important;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08) !important;
}

.select2-results__option {
    padding: 8px 12px !important;
    font-size: 14px !important;
    color: #2d3748 !important;
    transition: background-color 0.15s ease-in-out;
}

.select2-results__option--highlighted {
    background-color: #6993ff !important;
    color: #fff !important;
}

</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('#club_id').select2({
        placeholder: '-- Tất cả CLB --',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
@endsection


