@extends('client.layouts.app')
@section('title', 'Tạo sự kiện mới')

@section('content')
<div class="container py-5">
    <h2>Tạo sự kiện mới</h2>

    <form action="{{ route('club_manager.events.store') }}" method="POST">
        @csrf

      

        <div class="row g-3">
         <!-- Tên sự kiện -->
<div class="col-md-6">
    <label class="form-label fw-bold">Tên sự kiện <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Thời gian bắt đầu -->
<div class="col-md-6">
    <label class="form-label fw-bold">Bắt đầu <span class="text-danger">*</span></label>
    <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
    @error('start_time')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Thời gian kết thúc -->
<div class="col-md-6">
    <label class="form-label fw-bold">Kết thúc <span class="text-danger">*</span></label>
    <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
    @error('end_time')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Địa điểm -->
<div class="col-12">
    <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" required>
    @error('location')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Số người tối đa -->
<div class="col-md-4">
    <label class="form-label fw-bold">Số người tối đa</label>
    <input type="number" name="max_participants" class="form-control @error('max_participants') is-invalid @enderror" value="{{ old('max_participants') }}" min="1">
    @error('max_participants')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


            <!-- Hiển thị công khai -->
            <div class="col-md-4">
                <label class="form-label fw-bold">Hiển thị sự kiện</label>
                <select name="is_public" class="form-select">
                    <option value="1" {{ old('is_public',1)==1?'selected':'' }}>Công khai toàn trường</option>
                    <option value="0" {{ old('is_public')==0?'selected':'' }}>Chỉ hiển thị cho CLB</option>
                </select>
            </div>

            <!-- Mô tả -->
            <div class="col-12">
                <label class="form-label fw-bold">Mô tả</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <!-- Ngân sách chi tiết -->
            <div class="col-12 mt-4">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-money-bill-wave"></i> Ngân sách chi tiết</h5>

                <div id="budget-items-container">
                    <div class="row g-3 mb-3 align-items-end budget-item">
                        <div class="col-md-5">
                            <input type="text" name="budget_items[0][item_name]" class="form-control" placeholder="VD: Thuê âm thanh" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="budget_items[0][estimated_cost]" class="form-control cost-input" placeholder="0" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <select name="budget_items[0][type]" class="form-select" required>
                                <option value="club_fund">CLB tự chi</option>
                                <option value="school_fund" selected>Xin cấp từ trường</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-budget-item"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    <button type="button" id="add-budget-item" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Thêm đầu mục
                    </button>
                </div>

                <!-- Tổng kết -->
                <div class="row g-4 border-top pt-3">
                    <div class="col-md-4">
                        <div class="bg-light p-2 rounded text-center">
                            <small class="text-muted d-block">Tổng dự kiến</small>
                            <h5 class="text-primary fw-bold mb-0" id="total-estimated">0đ</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-warning bg-opacity-10 p-2 rounded text-center">
                            <small class="text-muted d-block">Xin cấp từ trường</small>
                            <h5 class="text-warning fw-bold mb-0" id="total-school">0đ</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-info bg-opacity-10 p-2 rounded text-center">
                            <small class="text-muted d-block">CLB tự chi</small>
                            <h5 class="text-info fw-bold mb-0" id="total-club">0đ</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút submit -->
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">Tạo sự kiện</button>
                <a href="{{ route('club_manager.events.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </div>
    </form>
</div>

<script>
let budgetIndex = 1;

document.getElementById('add-budget-item').addEventListener('click', function () {
    const html = `
        <div class="row g-3 mb-3 align-items-end budget-item">
            <div class="col-md-5">
                <input type="text" name="budget_items[${budgetIndex}][item_name]" class="form-control" placeholder="VD: In backdrop" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="budget_items[${budgetIndex}][estimated_cost]" class="form-control cost-input" placeholder="0" min="0" required>
            </div>
            <div class="col-md-3">
                <select name="budget_items[${budgetIndex}][type]" class="form-select" required>
                    <option value="club_fund">CLB tự chi</option>
                    <option value="school_fund">Xin cấp từ trường</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-budget-item"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
    document.getElementById('budget-items-container').insertAdjacentHTML('beforeend', html);
    budgetIndex++;
    calculateTotals();
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-budget-item')) {
        e.target.closest('.budget-item').remove();
        calculateTotals();
    }
});

document.addEventListener('input', function(e) {
    if(e.target.matches('.cost-input') || e.target.matches('select')) calculateTotals();
});

function calculateTotals() {
    let total = 0, school = 0, club = 0;
    document.querySelectorAll('.budget-item').forEach(item => {
        const cost = parseInt(item.querySelector('.cost-input').value) || 0;
        const type = item.querySelector('select').value;
        total += cost;
        if(type==='school_fund') school += cost;
        if(type==='club_fund') club += cost;
    });
    document.getElementById('total-estimated').textContent = formatNumber(total)+'đ';
    document.getElementById('total-school').textContent = formatNumber(school)+'đ';
    document.getElementById('total-club').textContent = formatNumber(club)+'đ';
}

function formatNumber(num){
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

calculateTotals();
</script>
@endsection
