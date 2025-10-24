@extends('admin.layouts.app')

@section('card-title', 'Chỉnh sửa giao dịch quỹ')

@section('card-header', 'Thông tin giao dịch')

@section('card-body')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa giao dịch quỹ</h1>
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
            <form action="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.update' : 'club-manager.funds.update', $fund) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="club_id">Câu lạc bộ <span class="text-danger">*</span></label>
                            <select name="club_id" id="club_id" class="form-control @error('club_id') is-invalid @enderror" required>
                                <option value="">Chọn câu lạc bộ</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ (old('club_id', $fund->club_id) == $club->id) ? 'selected' : '' }}>
                                        {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>
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
                                <option value="income" {{ (old('type', $fund->type) == 'income') ? 'selected' : '' }}>Thu</option>
                                <option value="expense" {{ (old('type', $fund->type) == 'expense') ? 'selected' : '' }}>Chi</option>
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
                                   value="{{ old('amount', $fund->amount) }}" min="0" step="0.01" required>
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
                                <option value="Hoạt động sự kiện" {{ (old('category', $fund->category) == 'Hoạt động sự kiện') ? 'selected' : '' }}>Hoạt động sự kiện</option>
                                <option value="Quà tặng" {{ (old('category', $fund->category) == 'Quà tặng') ? 'selected' : '' }}>Quà tặng</option>
                                <option value="Văn phòng phẩm" {{ (old('category', $fund->category) == 'Văn phòng phẩm') ? 'selected' : '' }}>Văn phòng phẩm</option>
                                <option value="Đào tạo" {{ (old('category', $fund->category) == 'Đào tạo') ? 'selected' : '' }}>Đào tạo</option>
                                <option value="Hỗ trợ thành viên" {{ (old('category', $fund->category) == 'Hỗ trợ thành viên') ? 'selected' : '' }}>Hỗ trợ thành viên</option>
                                <option value="Khác" {{ (old('category', $fund->category) == 'Khác') ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả giao dịch <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Mô tả chi tiết về giao dịch..." required>{{ old('description', $fund->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                </div>

                @if(Auth::user()->role === 'admin')
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="approved" {{ (old('status', $fund->status) == 'approved') ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="pending" {{ (old('status', $fund->status) == 'pending') ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="rejected" {{ (old('status', $fund->status) == 'rejected') ? 'selected' : '' }}>Từ chối</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="form-group">
                        <label>Trạng thái hiện tại</label>
                        <div class="form-control-plaintext">
                            @if($fund->status === 'pending')
                                <span class="badge badge-warning">Chờ duyệt</span>
                            @elseif($fund->status === 'approved')
                                <span class="badge badge-success">Đã duyệt</span>
                            @else
                                <span class="badge badge-danger">Từ chối</span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Thông tin bổ sung -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Người tạo</label>
                            <div class="form-control-plaintext">{{ $fund->creator->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Người phê duyệt</label>
                            <div class="form-control-plaintext">{{ $fund->approver->name ?? 'Chưa có' }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ngày tạo</label>
                            <div class="form-control-plaintext">{{ $fund->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cập nhật lần cuối</label>
                            <div class="form-control-plaintext">{{ $fund->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật giao dịch
                    </button>
                    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>

<script>
// Format amount input
document.getElementById('amount').addEventListener('input', function() {
    let value = this.value;
    if (value < 0) {
        this.value = 0;
    }
});

// Character counter for description
document.getElementById('description').addEventListener('input', function() {
    const maxLength = 1000;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    // Create or update character counter
    let counter = document.getElementById('char-counter');
    if (!counter) {
        counter = document.createElement('small');
        counter.id = 'char-counter';
        counter.className = 'form-text text-muted';
        this.parentNode.appendChild(counter);
    }
    
    counter.textContent = `Còn lại: ${remaining} ký tự`;
    
    if (remaining < 0) {
        counter.className = 'form-text text-danger';
    } else {
        counter.className = 'form-text text-muted';
    }
});
</script>
@endsection
