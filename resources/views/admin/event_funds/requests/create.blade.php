@extends('admin.layouts.app')

@section('title', 'Tạo yêu cầu cấp kinh phí')
@section('card-title', 'Tạo yêu cầu cấp kinh phí mới')

@section('card-body')
<div class="container">
    <form action="{{ route('admin.event_fund_requests.store') }}" method="POST">
        @csrf

    <!-- Chọn câu lạc bộ -->
<div class="form-group mb-3">
    <label for="club_id" class="form-label">Chọn câu lạc bộ</label>
    <select id="club_id" class="form-select">
        <option value="">-- Chọn câu lạc bộ --</option>
        @foreach($clubs as $club)
            <option value="{{ $club->id }}">{{ $club->name }}</option>
        @endforeach
    </select>
</div>

<!-- Chọn sự kiện -->
<div class="form-group mb-3">
    <label for="event_id" class="form-label">Chọn sự kiện</label>
    <select name="event_id" id="event_id" class="form-select" required>
        <option value="">-- Chọn sự kiện --</option>
        @foreach($events as $event)
            <option value="{{ $event->id }}" data-club="{{ $event->club_id }}">
                {{ $event->name }}
            </option>
        @endforeach
    </select>
    @error('event_id') <small class="text-danger">{{ $message }}</small> @enderror
</div>


        <!-- Nguồn quỹ -->
        <div class="form-group mb-3">
            <label for="source_type" class="form-label">Nguồn cấp</label>
            <select name="source_type" id="source_type" class="form-select" required>
                <option value="">-- Chọn nguồn cấp --</option>
                @foreach($sourceTypes as $key => $label)
                    <option value="{{ $key }}" {{ old('source_type') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('source_type') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Số tiền yêu cầu -->
        <div class="form-group mb-3">
            <label for="amount_requested" class="form-label">Số tiền yêu cầu (VNĐ)</label>
            <input type="number" name="amount_requested" id="amount_requested" class="form-control" 
                   value="{{ old('amount_requested') }}" placeholder="Nhập số tiền yêu cầu" required>
            @error('amount_requested') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Ghi chú / lý do -->
        <div class="form-group mb-3">
            <label for="note" class="form-label">Ghi chú / Lý do yêu cầu</label>
            <textarea name="note" id="note" rows="4" class="form-control">{{ old('note') }}</textarea>
            @error('note') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary me-2">Hủy</a>
            <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const clubSelect = document.getElementById('club_id');
    const eventSelect = document.getElementById('event_id');

    clubSelect.addEventListener('change', function () {
        const clubId = this.value;

        Array.from(eventSelect.options).forEach(option => {
            if (!option.value) return; // option mặc định
            if (clubId === '' || option.dataset.club === clubId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });

        // Reset chọn event
        eventSelect.value = '';
    });
});
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const clubSelect = $('#club_id');
    const eventSelect = $('#event_id');

    // ✅ Lưu danh sách event gốc để lọc lại sau
    const allEvents = $('#event_id option').clone();

    // Khi chọn CLB
    clubSelect.on('change', function () {
        const clubId = $(this).val();

        // Xóa toàn bộ event hiện tại
        eventSelect.empty();

        // Thêm lại option mặc định
        eventSelect.append('<option value="">-- Chọn sự kiện --</option>');

        // Lọc event theo club_id
        allEvents.each(function () {
            const eventClubId = $(this).data('club');
            if (!clubId || eventClubId == clubId) {
                eventSelect.append($(this));
            }
        });

        // Refresh lại select2
        eventSelect.val('').trigger('change');
    });

    // ✅ Khởi tạo Select2
    $('#club_id, #event_id').select2({
        placeholder: '-- Chọn --',
        allowClear: true,
        width: '100%'
    });
});
</script>

@endsection
