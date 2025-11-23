@extends('admin.layouts.app')

@section('title', 'Thêm mới Sự kiện')

@section('card-body')
    {{-- Thêm CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 mb-1">Thêm mới Sự kiện</h1>
                <p class="text-muted small mb-0">Tạo sự kiện mới cho các câu lạc bộ</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.events.store') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Cột 1 -->
                        <div class="col-md-6">
                            <!-- Select CLB -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Câu lạc bộ <span class="text-danger">*</span></label>
                                <select name="club_id" id="club_id" class="form-select select2 @error('club_id') is-invalid @enderror" style="width: 100%;" required>
                                    <option value="">-- Chọn CLB --</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                            {{ $club->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('club_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên sự kiện <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="VD: Hội thảo AI 2025" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Mô tả chi tiết về sự kiện...">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                            </div>
                        </div>

                        <!-- Cột 2 -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="VD: Hội trường A, ĐH Bách Khoa" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Giới hạn số người tham gia</label>
                                <input type="number" name="max_participants" class="form-control" value="{{ old('max_participants') }}" min="1" placeholder="VD: 200">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Hiển thị sự kiện</label>
                                <select name="is_public" class="form-select" required>
                                    <option value="1" {{ old('is_public', 1) == 1 ? 'selected' : '' }}>Công khai toàn trường</option>
                                    <option value="0" {{ old('is_public') == 0 ? 'selected' : '' }}>Chỉ hiển thị cho CLB</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="">-- Chọn trạng thái --</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                                </select>
                            </div>

                            <!-- Select Người tạo -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Người tạo <span class="text-danger">*</span></label>
                                <select name="created_by" id="created_by" class="form-select" required>
                                    <option value="">-- Chọn CLB trước --</option>
                                </select>
                            </div>

                            <!-- ==================== NGÂN SÁCH CHI TIẾT (MỚI) ==================== -->
<div class="col-12 mt-5">
    <h5 class="fw-bold text-primary mb-4">
        <i class="fas fa-money-bill-wave"></i> Ngân sách chi tiết (tự động tính tổng)
    </h5>

    <div id="budget-items-container">
        <!-- Mẫu 1 dòng mặc định -->
        <div class="row g-3 mb-3 align-items-end budget-item">
            <div class="col-md-5">
                <input type="text" name="budget_items[0][item_name]" class="form-control" placeholder="VD: Thuê âm thanh + ánh sáng" required>
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
                <button type="button" class="btn btn-danger btn-sm remove-budget-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="text-center mb-3">
        <button type="button" id="add-budget-item" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Thêm đầu mục
        </button>
    </div>

    <!-- Tổng kết tự động -->
    <div class="row g-4 border-top pt-4">
        <div class="col-md-4">
            <div class="bg-light p-3 rounded text-center">
                <small class="text-muted d-block">Tổng dự kiến</small>
                <h4 class="text-primary fw-bold mb-0" id="total-estimated">0đ</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-warning bg-opacity-10 p-3 rounded text-center">
                <small class="text-muted d-block">Xin cấp từ trường</small>
                <h4 class="text-warning fw-bold mb-0" id="total-school">0đ</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-info bg-opacity-10 p-3 rounded text-center">
                <small class="text-muted d-block">CLB tự chi</small>
                <h4 class="text-info fw-bold mb-0" id="total-club">0đ</h4>
            </div>
        </div>
    </div>
</div>
<!-- ==================== END NGÂN SÁCH CHI TIẾT (MỚI) ==================== -->
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-plus me-2"></i>Tạo sự kiện
                        </button>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary px-5">
                            <i class="fas fa-times me-2"></i>Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Kích hoạt Select2 cho CLB
            $('#club_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Tìm và chọn câu lạc bộ...',
                allowClear: true
            });

            // Khi chọn CLB → tải danh sách thành viên
            $('#club_id').on('change', function () {
                const clubId = $(this).val();
                const createdBySelect = $('#created_by');

                createdBySelect.html('<option value="">-- Đang tải... --</option>');

                if (!clubId) {
                    createdBySelect.html('<option value="">-- Chọn CLB trước --</option>');
                    return;
                }

                fetch(`/admin/events/club-members/${clubId}`)
                    .then(response => response.json())
                    .then(data => {
                        createdBySelect.html('<option value="">-- Chọn người tạo --</option>');
                        data.forEach(user => {
                            const selected = (user.id == '{{ old('created_by') }}') ? 'selected' : '';
                            createdBySelect.append(`<option value="${user.id}" ${selected}>${user.name} (${user.email})</option>`);
                        });
                    })
                    .catch(() => {
                        createdBySelect.html('<option value="">Lỗi tải dữ liệu</option>');
                    });
            });

            // Nếu có old('club_id') thì tự động load danh sách
            @if(old('club_id'))
                $('#club_id').trigger('change');
            @endif
    });
    </script>
    <script>
// Ngân sách chi tiết - Tự động thêm dòng + tính tổng
let budgetIndex = 1;

document.getElementById('add-budget-item').addEventListener('click', function () {
    const html = `
        <div class="row g-3 mb-3 align-items-end budget-item">
            <div class="col-md-5">
                <input type="text" name="budget_items[${budgetIndex}][item_name]" class="form-control" placeholder="VD: In backdrop, banner" required>
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

// Xóa dòng
document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-budget-item')) {
        e.target.closest('.budget-item').remove();
        calculateTotals();
    }
});

// Tính tổng khi nhập
document.addEventListener('input', function (e) {
    if (e.target.matches('.cost-input') || e.target.matches('select[name*="type"]')) {
        calculateTotals();
    }
});

function calculateTotals() {
    let total = 0;
    let school = 0;
    let club = 0;

    document.querySelectorAll('.budget-item').forEach(item => {
        const cost = parseInt(item.querySelector('.cost-input').value) || 0;
        const type = item.querySelector('select').value;

        total += cost;
        if (type === 'school_fund') school += cost;
        if (type === 'club_fund') club += cost;
    });

    document.getElementById('total-estimated').textContent = formatNumber(total) + 'đ';
    document.getElementById('total-school').textContent = formatNumber(school) + 'đ';
    document.getElementById('total-club').textContent = formatNumber(club) + 'đ';
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Tính lần đầu khi load trang (nếu có old input)
calculateTotals();
</script>

@endsection
