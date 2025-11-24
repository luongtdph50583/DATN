@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa ngân sách - ' . $event->name)

@section('card-body')
<div class="container-fluid py-4">
    <h3 class="mb-4"><i class="fas fa-money-bill-wave"></i> Ngân sách chi tiết sự kiện</h3>

    <form action="{{ route('admin.events.update_budget', $event) }}" method="POST">
        @csrf
        <div id="budget-items">
            @forelse($event->budgetItems as $item)
                <div class="row g-3 mb-3 border-bottom pb-3 budget-item">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Đầu mục</label>
                        <input type="text" name="items[{{ $loop->index }}][item_name]" 
                               class="form-control" value="{{ $item->item_name }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Dự kiến (VNĐ)</label>
                        <input type="number" name="items[{{ $loop->index }}][estimated_cost]" 
                               class="form-control" value="{{ $item->estimated_cost }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Nguồn kinh phí</label>
                        <select name="items[{{ $loop->index }}][type]" class="form-select" required>
                            <option value="club_fund" {{ $item->type == 'club_fund' ? 'selected' : '' }}>CLB tự chi</option>
                            <option value="school_fund" {{ $item->type == 'school_fund' ? 'selected' : '' }}>Xin cấp trường</option>
                            <option value="other" {{ $item->type == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="col-12">
                        <textarea name="items[{{ $loop->index }}][description]" 
                                  class="form-control" rows="2" placeholder="Mô tả chi tiết (không bắt buộc)">{{ $item->description }}</textarea>
                    </div>
                </div>
            @empty
                <!-- 1 dòng mặc định -->
                <div class="row g-3 mb-3 border-bottom pb-3 budget-item">
                    <div class="col-md-4"><input type="text" name="items[0][item_name]" class="form-control" placeholder="Ví dụ: Thuê âm thanh" required></div>
                    <div class="col-md-3"><input type="number" name="items[0][estimated_cost]" class="form-control" required></div>
                    <div class="col-md-3">
                        <select name="items[0][type]" class="form-select" required>
                            <option value="club_fund">CLB tự chi</option>
                            <option value="school_fund">Xin cấp trường</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center my-4">
            <button type="button" id="add-item" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm đầu mục
            </button>
        </div>

        <div class="text-end">
            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary btn-lg">Lưu ngân sách</button>
        </div>
    </form>
</div>

<script>
let index = {{ $event->budgetItems->count() ?: 1 }};
document.getElementById('add-item').onclick = function() {
    const html = `
        <div class="row g-3 mb-3 border-bottom pb-3 budget-item">
            <div class="col-md-4"><input type="text" name="items[${index}][item_name]" class="form-control" required></div>
            <div class="col-md-3"><input type="number" name="items[${index}][estimated_cost]" class="form-control" required></div>
            <div class="col-md-3">
                <select name="items[${index}][type]" class="form-select" required>
                    <option value="club_fund">CLB tự chi</option>
                    <option value="school_fund">Xin cấp trường</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
    document.getElementById('budget-items').insertAdjacentHTML('beforeend', html);
    index++;
};

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        e.target.closest('.budget-item').remove();
    }
});
</script>
@endsection