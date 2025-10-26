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

        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea name="content" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Đối tượng nhận</label>
            <select name="target_type" id="target_type" class="form-select" required>
                <option value="">-- Chọn kiểu gửi --</option>
                <option value="user">Người dùng cụ thể</option>
                <option value="club">Thành viên CLB</option>
                <option value="role">Theo vai trò</option>
                <option value="event">Người tham gia sự kiện</option>
            </select>
        </div>

        <div id="target_select" class="mb-3"></div>

        <div class="mb-3">
            <label class="form-label">Hình thức gửi</label>
            <select name="send_via" class="form-select" required>
                <option value="database">Chỉ lưu thông báo</option>
                <option value="mail">Chỉ gửi mail</option>
                <option value="both">Gửi cả hai</option>
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

    <script>
        $(document).ready(function () {
            $('#target_type').on('change', function () {
                let type = $(this).val();
                let $targetDiv = $('#target_select');
                $targetDiv.empty();

                // 1. Chọn user cụ thể
                if (type === 'user') {
                    $targetDiv.html('<label class="form-label">Chọn người nhận</label><select id="users_select" name="users[]" class="form-select" multiple></select>');
                    $('#users_select').select2({
                        placeholder: 'Chọn người dùng...',
                        ajax: {
                            url: '{{ route("admin.notifications.fetchUsers") }}',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) { return { q: params.term }; },
                            processResults: function (data) { return { results: data.results }; },
                            cache: true
                        }
                    });
                }

                // 2. Chọn CLB + thành viên
                else if (type === 'club') {
                    $targetDiv.html(`
                    <label class="form-label">Chọn CLB</label>
                    <select id="club_select" class="form-select"><option value="">-- Chọn CLB --</option></select>
                    <label class="form-label mt-2">Chọn thành viên</label>
                    <select id="club_members" name="users[]" class="form-select" multiple></select>
                `);

                    // Load CLB
                    $.get('{{ route("admin.notifications.fetchClubs") }}', function (clubs) {
                        let options = clubs.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                        $('#club_select').append(options);
                    });

                    // Khi chọn CLB, load thành viên
                    $('#club_select').on('change', function () {
                        let clubId = $(this).val();
                        $('#club_members').empty();
                        if (clubId) {
                            $.get('{{ route("admin.notifications.fetchClubMembers") }}', { club_id: clubId }, function (data) {
                                let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                                $('#club_members').append(options);
                                $('#club_members').select2({ placeholder: 'Chọn thành viên...' });
                            });
                        }
                    });
                }

                // 3. Chọn vai trò
                else if (type === 'role') {
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

                    $('#role_select').on('change', function () {
                        let role = $(this).val();
                        $('#role_users').empty();
                        if (role) {
                            $.get('{{ route("admin.notifications.fetchUsers") }}', { role: role }, function (data) {
                                let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                                $('#role_users').append(options);
                                $('#role_users').select2({ placeholder: 'Chọn người...' });
                            });
                        }
                    });
                }

                // 4. Chọn sự kiện + người tham gia
                else if (type === 'event') {
                    $targetDiv.html(`
                    <label class="form-label">Chọn sự kiện</label>
                    <select id="event_select" class="form-select"><option value="">-- Chọn sự kiện --</option></select>
                    <label class="form-label mt-2">Chọn người tham gia</label>
                    <select id="event_members" name="users[]" class="form-select" multiple></select>
                `);

                    // Load events
                    $.get('{{ route("admin.notifications.fetchEvents") }}', function (events) {
                        let options = events.map(e => `<option value="${e.id}">${e.name}</option>`).join('');
                        $('#event_select').append(options);
                    });

                    // Khi chọn event, load người tham gia
                    $('#event_select').on('change', function () {
                        let eventId = $(this).val();
                        $('#event_members').empty();
                        if (eventId) {
                            $.get('{{ route("admin.notifications.fetchEventMembers") }}', { event_id: eventId }, function (data) {
                                let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                                $('#event_members').append(options);
                                $('#event_members').select2({ placeholder: 'Chọn người tham gia...' });
                            });
                        }
                    });
                }

            });
        });
    </script>
@endpush
