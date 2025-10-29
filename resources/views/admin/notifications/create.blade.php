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

{{-- @push('scripts')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery -->
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function () {
        const renderSelectAllButton = (targetSelector) => {
            const $target = $(targetSelector);
            if (!$target.length) return;

            const $wrapper = $('<div class="mb-2"><button type="button" class="btn btn-sm btn-outline-primary">Chọn tất cả</button></div>');
            $wrapper.find('button').on('click', function () {
                const allValues = $target.find('option').map(function () {
                    return $(this).val();
                }).get();
                $target.val(allValues).trigger('change');
            });

            $target.before($wrapper);
        };

        $('#target_type').on('change', function () {
            let type = $(this).val();
            let $targetDiv = $('#target_select');
            $targetDiv.empty();

            // 1. Chọn user cụ thể
            if (type === 'user') {
                $targetDiv.html(`
                    <label class="form-label">Chọn người nhận</label>
                    <select id="users_select" name="users[]" class="form-select" multiple></select>
                `);
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
                renderSelectAllButton('#users_select');
            }

            // 2. Chọn CLB + thành viên
            else if (type === 'club') {
                $targetDiv.html(`
                    <label class="form-label">Chọn CLB</label>
                    <select id="club_select" class="form-select"><option value="">-- Chọn CLB --</option></select>
                    <label class="form-label mt-2">Chọn thành viên</label>
                    <select id="club_members" name="users[]" class="form-select" multiple></select>
                `);

                $.get('{{ route("admin.notifications.fetchClubs") }}', function (clubs) {
                    let options = clubs.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                    $('#club_select').append(options);
                });

                $('#club_select').on('change', function () {
                    let clubId = $(this).val();
                    $('#club_members').empty();
                    if (clubId) {
                        $.get('{{ route("admin.notifications.fetchClubMembers") }}', { club_id: clubId }, function (data) {
                            let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                            $('#club_members').append(options);
                            $('#club_members').select2({ placeholder: 'Chọn thành viên...' });
                            renderSelectAllButton('#club_members');
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
                            renderSelectAllButton('#role_users');
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

                $.get('{{ route("admin.notifications.fetchEvents") }}', function (events) {
                    let options = events.map(e => `<option value="${e.id}">${e.name}</option>`).join('');
                    $('#event_select').append(options);
                });

                $('#event_select').on('change', function () {
                    let eventId = $(this).val();
                    $('#event_members').empty();
                    if (eventId) {
                        $.get('{{ route("admin.notifications.fetchEventMembers") }}', { event_id: eventId }, function (data) {
                            let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                            $('#event_members').append(options);
                            $('#event_members').select2({ placeholder: 'Chọn người tham gia...' });
                            renderSelectAllButton('#event_members');
                        });
                    }
                });
            }
        });

        // ✅ Validate khi submit form
        $('form').on('submit', function (e) {
            const $userSelect = $('select[name="users[]"]');
            if ($userSelect.length && (!$userSelect.val() || $userSelect.val().length === 0)) {
                alert('Vui lòng chọn ít nhất một người nhận.');
                e.preventDefault();
            }
        });
    });
    </script>
@endpush --}}
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            const renderSelectButtons = ($select, type = '', extra = {}) => {
                if (!$select.length || $select.prev('.select-actions').length) return;

                let isLoading = true;

                const $wrapper = $(`
                <div class="mb-2 d-flex gap-2 align-items-center select-actions">
                    <span class="text-muted loading-indicator">Đang tải dữ liệu...</span>
                    <button type="button" class="btn btn-sm btn-outline-primary select-all-btn" disabled>Chọn tất cả</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-btn">Bỏ chọn tất cả</button>
                </div>
            `);

                const $loading = $wrapper.find('.loading-indicator');
                const $selectAllBtn = $wrapper.find('.select-all-btn');
                const $deselectAllBtn = $wrapper.find('.deselect-all-btn');

                $select.before($wrapper);

                const checkReady = () => {
                    if ($select.data('select2')) {
                        isLoading = false;
                        $loading.hide();
                        $selectAllBtn.prop('disabled', false);
                    } else {
                        $loading.show();
                        $selectAllBtn.prop('disabled', true);
                    }
                };

                const interval = setInterval(() => {
                    checkReady();
                    if (!isLoading) clearInterval(interval);
                }, 300);

                $selectAllBtn.on('click', function () {
                    if (isLoading) {
                        alert('Dữ liệu chưa tải xong. Vui lòng chờ một chút rồi thử lại.');
                        return;
                    }

                    $loading.show();

                    let url = '';
                    let params = {};

                    switch (type) {
                        case 'user-ajax':
                            url = '{{ route("admin.notifications.fetchUsers") }}';
                            params = { all: true };
                            break;
                        case 'club':
                            url = '{{ route("admin.notifications.fetchClubMembers") }}';
                            params = { club_id: extra.club_id };
                            break;
                        case 'role':
                            url = '{{ route("admin.notifications.fetchUsers") }}';
                            params = { role: extra.role };
                            break;
                        case 'event':
                            url = '{{ route("admin.notifications.fetchEventMembers") }}';
                            params = { event_id: extra.event_id };
                            break;
                        default:
                            $loading.hide();
                            return;
                    }

                    $.get(url, params, function (data) {
                        const allOptions = data.results.map(u => new Option(u.text, u.id, true, true));
                        const allIds = data.results.map(u => u.id);

                        $select.empty().append(allOptions);
                        $select.val(allIds).trigger('change');

                        $loading.hide();
                        $selectAllBtn.prop('disabled', false);
                    });
                });

                $deselectAllBtn.on('click', function () {
                    $select.val(null).trigger('change');
                });
            };

            $('#target_type').on('change', function () {
                let type = $(this).val();
                let $targetDiv = $('#target_select');
                $targetDiv.empty();

                if (type === 'user') {
                    $targetDiv.html(`
                    <label class="form-label">Chọn người nhận</label>
                    <select id="users_select" name="users[]" class="form-select" multiple></select>
                `);

                    const $select = $('#users_select');
                    $select.select2({
                        placeholder: 'Chọn người dùng...',
                        minimumInputLength: 0, // ✅ hiển thị danh sách luôn
                        ajax: {
                            url: '{{ route("admin.notifications.fetchUsers") }}',
                            dataType: 'json',
                            delay: 300,
                            data: params => ({ q: params.term || '', page: params.page || 1 }),
                            processResults: data => ({
                                results: data.results,
                                pagination: { more: data.pagination?.more || false }
                            }),
                            cache: true
                        }
                    });

                    // Khi mở Select2, load danh sách luôn
                    $select.on('select2:opening', function () {
                        if (!$select.data('select2').isOpen()) {
                            $.get('{{ route("admin.notifications.fetchUsers") }}', { q: '' }, function (data) {
                                const allOptions = data.results.map(u => new Option(u.text, u.id, false, false));
                                $select.empty().append(allOptions);
                            });
                        }
                    });

                    renderSelectButtons($select, 'user-ajax');
                }

                else if (type === 'club') {
                    $targetDiv.html(`
                    <label class="form-label">Chọn CLB</label>
                    <select id="club_select" class="form-select"><option value="">-- Chọn CLB --</option></select>
                    <label class="form-label mt-2">Chọn thành viên</label>
                    <select id="club_members" name="users[]" class="form-select" multiple></select>
                `);

                    $.get('{{ route("admin.notifications.fetchClubs") }}', function (clubs) {
                        let options = clubs.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                        $('#club_select').append(options);
                    });

                    $('#club_select').on('change', function () {
                        let clubId = $(this).val();
                        const $members = $('#club_members');
                        $members.empty();
                        if (clubId) {
                            $.get('{{ route("admin.notifications.fetchClubMembers") }}', { club_id: clubId }, function (data) {
                                let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                                $members.append(options);
                                $members.select2({ placeholder: 'Chọn thành viên...' });
                                renderSelectButtons($members, 'club', { club_id: clubId });
                            });
                        }
                    });
                }

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
                        const $users = $('#role_users');
                        $users.empty();
                        if (role) {
                            $.get('{{ route("admin.notifications.fetchUsers") }}', { role: role }, function (data) {
                                let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                                $users.append(options);
                                $users.select2({ placeholder: 'Chọn người...' });
                                renderSelectButtons($users, 'role', { role: role });
                            });
                        }
                    });
                }

                else if (type === 'event') {
                    $targetDiv.html(`
                    <label class="form-label">Chọn sự kiện</label>
                    <select id="event_select" class="form-select"><option value="">-- Chọn sự kiện --</option></select>
                    <label class="form-label mt-2">Chọn người tham gia</label>
                    <select id="event_members" name="users[]" class="form-select" multiple></select>
                `);

                    $.get('{{ route("admin.notifications.fetchEvents") }}', function (events) {
                        let options = events.map(e => `<option value="${e.id}">${e.name}</option>`).join('');
                        $('#event_select').append(options);
                    });

                    $('#event_select').on('change', function () {
                        let eventId = $(this).val();
                        const $members = $('#event_members');
                        $members.empty();
                        if (eventId) {
                            $.get('{{ route("admin.notifications.fetchEventMembers") }}', { event_id: eventId }, function (data) {
                                let options = data.results.map(u => `<option value="${u.id}">${u.text}</option>`).join('');
                                $members.append(options);
                                $members.select2({ placeholder: 'Chọn người tham gia...' });
                                renderSelectButtons($members, 'event', { event_id: eventId });
                            });
                        }
                    });
                }
            });

            $('form').on('submit', function (e) {
                const $userSelect = $('select[name="users[]"]');
                if ($userSelect.length && (!$userSelect.val() || $userSelect.val().length === 0)) {
                    alert('Vui lòng chọn ít nhất một người nhận.');
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush









