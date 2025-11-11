@extends('admin.layouts.app')

@section('title', 'Tạo thông báo')
@section('card-title', 'Quản lý thông báo')
@section('card-header', 'Tạo thông báo mới')

@section('card-body')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.notifications.store') }}" method="POST">
    @csrf

    {{-- Tiêu đề --}}
    <div class="mb-3">
        <label class="form-label">Tiêu đề</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    {{-- Quill Editor --}}
    <div class="mb-3">
        <label for="editor" class="form-label">Nội dung</label>
        <div id="editor"
            style="min-height: 300px; max-height: 600px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 6px; padding: 10px; background-color: #fff;">
            {!! old('content') !!}
        </div>
        <input type="hidden" name="content" id="contentInput">
        @error('content') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    {{-- Chọn đối tượng nhận --}}
    <div class="mb-3">
        <label class="form-label">Đối tượng nhận</label>
        <select name="target_type" id="target_type" class="form-select" required>
            <option value="">-- Chọn kiểu gửi --</option>
            <option value="user" {{ old('target_type') == 'user' ? 'selected' : '' }}>Người dùng cụ thể</option>
            <option value="club" {{ old('target_type') == 'club' ? 'selected' : '' }}>Thành viên CLB</option>
            <option value="role" {{ old('target_type') == 'role' ? 'selected' : '' }}>Theo vai trò</option>
            <option value="event" {{ old('target_type') == 'event' ? 'selected' : '' }}>Người tham gia sự kiện</option>
        </select>
    </div>

    <div id="target_select" class="mb-3"></div>

    {{-- Hình thức gửi --}}
    <div class="mb-3">
        <label class="form-label">Hình thức gửi</label>
        <select name="send_via" class="form-select" required>
            <option value="database" {{ old('send_via') == 'database' ? 'selected' : '' }}>Chỉ lưu thông báo</option>
            <option value="mail" {{ old('send_via') == 'mail' ? 'selected' : '' }}>Chỉ gửi mail</option>
            <option value="both" {{ old('send_via') == 'both' ? 'selected' : '' }}>Gửi cả hai</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Gửi thông báo</button>
</form>
@endsection

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Quill -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==========================
    // Quill Editor
    // ==========================
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Nhập nội dung...',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });
    quill.root.innerHTML = {!! json_encode(old('content', $content ?? '')) !!};

    $('form').on('submit', function() {
        $('#contentInput').val(quill.root.innerHTML);
    });

    // ==========================
    // Dynamic target selects
    // ==========================
    const initSelect2 = ($select, placeholder = 'Chọn...', ajaxUrl = null, extraParams = {}) => {
        $select.select2({
            placeholder,
            minimumInputLength: 0,
            ajax: ajaxUrl ? {
                url: ajaxUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) { return Object.assign({ q: params.term }, extraParams); },
                processResults: function(data) { return { results: data.results }; },
                cache: true
            } : null
        });
    };

    const addSelectButtons = ($select) => {
        if (!$select.length || $select.prev('.select-actions').length) return;
        const $wrapper = $(`
            <div class="mb-2 d-flex gap-2 align-items-center select-actions">
                <button type="button" class="btn btn-sm btn-outline-primary select-all-btn">Chọn tất cả</button>
                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-btn">Bỏ chọn tất cả</button>
            </div>
        `);
        $wrapper.insertBefore($select);

        $wrapper.find('.select-all-btn').on('click', function() {
            const allVals = $select.find('option').map((i,o) => $(o).val()).get();
            $select.val(allVals).trigger('change');
        });
        $wrapper.find('.deselect-all-btn').on('click', function() {
            $select.val(null).trigger('change');
        });
    };

    $('#target_type').on('change', function () {
        const type = $(this).val();
        const $targetDiv = $('#target_select');
        $targetDiv.empty();

        if(type === 'user') {
            $targetDiv.html('<label class="form-label">Chọn người nhận</label><select id="users_select" name="users[]" class="form-select" multiple></select>');
            const $sel = $('#users_select');
            initSelect2($sel, 'Chọn người dùng...', '{{ route("admin.notifications.fetchUsers") }}');
            addSelectButtons($sel);
        }
        else if(type === 'club') {
            $targetDiv.html(`
                <label class="form-label">Chọn CLB</label>
                <select id="club_select" class="form-select"><option value="">-- Chọn CLB --</option></select>
                <label class="form-label mt-2">Chọn thành viên</label>
                <select id="club_members" name="users[]" class="form-select" multiple></select>
            `);
            $.get('{{ route("admin.notifications.fetchClubs") }}', function(clubs) {
                const options = clubs.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                $('#club_select').append(options);
            });
            $('#club_select').on('change', function() {
                const clubId = $(this).val();
                const $members = $('#club_members');
                $members.empty();
                if(clubId) {
                    initSelect2($members, 'Chọn thành viên...', '{{ route("admin.notifications.fetchClubMembers") }}', { club_id: clubId });
                    addSelectButtons($members);
                }
            });
        }
        else if(type === 'role') {
            $targetDiv.html(`
                <label class="form-label">Chọn vai trò</label>
                <select id="role_select" class="form-select">
                    <option value="">-- Chọn vai trò --</option>
                    <option value="club_manager">Chủ nhiệm CLB</option>
                    <option value="member">Thành viên</option>
                    <option value="admin">Admin</option>
                </select>
                <label class="form-label mt-2">Chọn người</label>
                <select id="role_users" name="users[]" class="form-select" multiple></select>
            `);
            $('#role_select').on('change', function() {
                const role = $(this).val();
                const $users = $('#role_users');
                $users.empty();
                if(role) {
                    initSelect2($users, 'Chọn người...', '{{ route("admin.notifications.fetchUsers") }}', { role });
                    addSelectButtons($users);
                }
            });
        }
        else if(type === 'event') {
            $targetDiv.html(`
                <label class="form-label">Chọn sự kiện</label>
                <select id="event_select" class="form-select"><option value="">-- Chọn sự kiện --</option></select>
                <label class="form-label mt-2">Chọn người tham gia</label>
                <select id="event_members" name="users[]" class="form-select" multiple></select>
            `);
            $.get('{{ route("admin.notifications.fetchEvents") }}', function(events) {
                const options = events.map(e => `<option value="${e.id}">${e.name}</option>`).join('');
                $('#event_select').append(options);
            });
            $('#event_select').on('change', function() {
                const eventId = $(this).val();
                const $members = $('#event_members');
                $members.empty();
                if(eventId) {
                    initSelect2($members, 'Chọn người tham gia...', '{{ route("admin.notifications.fetchEventMembers") }}', { event_id: eventId });
                    addSelectButtons($members);
                }
            });
        }
    });

    // Validate submit
    $('form').on('submit', function(e) {
        const $userSelect = $('select[name="users[]"]');
        if($userSelect.length && (!$userSelect.val() || !$userSelect.val().length)) {
            alert('Vui lòng chọn ít nhất một người nhận.');
            e.preventDefault();
        }
    });
});
</script>
@endpush
