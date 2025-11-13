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
                {!! old('content_html') !!}
            </div>
            <input type="hidden" name="content_html" id="contentHtmlInput">
            @error('content_html') <div class="text-danger small">{{ $message }}</div> @enderror
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

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        $(function () {
            // ==========================
            // CKEditor 5 - lưu plain text
            // ==========================
            let ckEditorInstance = null;
            const $contentHtmlInput = $('#contentHtmlInput');
            const initialContent = @json(old('content_html', ''));

            ClassicEditor.create(document.querySelector('#editor'), {
                toolbar: [
                    'heading',
                    '|',
                    'bold', 'italic', 'underline', 'strikethrough',
                    '|',
                    'bulletedList', 'numberedList',
                    '|',
                    'link', 'blockQuote', 'undo', 'redo'
                ]
            }).then(editor => {
                ckEditorInstance = editor;
                if (initialContent) {
                    editor.setData(initialContent);
                }
                $contentHtmlInput.val(editor.getData());
            }).catch(error => console.error(error));

            $('form').on('submit', function () {
                if (!ckEditorInstance) return;
                const html = ckEditorInstance.getData();
                $contentHtmlInput.val(html);
            });

            // ==========================
            // Select2 helpers
            // ==========================
            const routes = {
                users: @json(route('admin.notifications.fetchUsers')),
                clubs: @json(route('admin.notifications.fetchClubs')),
                clubMembers: @json(route('admin.notifications.fetchClubMembers')),
                events: @json(route('admin.notifications.fetchEvents')),
                eventMembers: @json(route('admin.notifications.fetchEventMembers')),
            };

            const clubMemberRoles = [
                'club_manager',
                'deputy_manager',
                'secretary',
                'treasurer',
                'event_manager',
                'communication',
                'member',
            ];

            const initialState = {
                type: @json(old('target_type')),
                users: @json(old('users', [])),
                clubId: @json(old('club_id')),
                role: @json(old('role')),
                eventId: @json(old('event_id')),
                applied: false,
            };

            const $targetWrapper = $('#target_select');
            const $targetType = $('#target_type');

            const initSelect2 = ($select, { placeholder = 'Chọn...', url = null, extraParams = {} } = {}) => {
                const config = {
                    width: '100%',
                    placeholder,
                    allowClear: true,
                };

                if (url) {
                    config.ajax = {
                        url,
                        dataType: 'json',
                        delay: 250,
                        cache: true,
                        data: params => ({
                            q: params.term || '',
                            page: params.page || 1,
                            ...extraParams,
                        }),
                        processResults: data => ({ results: data.results || [] }),
                    };
                }

                return $select.select2(config);
            };

            const ensureToolbar = ($select, loadAllFn) => {
                if (!$select.length || $select.prev('.select-actions').length) return;

                const $toolbar = $(`
                    <div class="mb-2 d-flex gap-2 align-items-center select-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary select-all-btn">Chọn tất cả</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-btn">Bỏ chọn tất cả</button>
                    </div>
                `);
                $toolbar.insertBefore($select);

                $toolbar.find('.select-all-btn').on('click', function () {
                    const $btn = $(this);
                    $btn.prop('disabled', true).text('Đang chọn...');
                    loadAllFn().always(() => {
                        $btn.prop('disabled', false).text('Chọn tất cả');
                    });
                });

                $toolbar.find('.deselect-all-btn').on('click', function () {
                    $select.val(null).trigger('change');
                });
            };

            const appendOptions = ($select, items, select = true) => {
                items.forEach(item => {
                    if (!$select.find(`option[value="${item.id}"]`).length) {
                        const option = new Option(item.text, item.id, select, select);
                        $select.append(option);
                    } else if (select) {
                        $select.find(`option[value="${item.id}"]`).prop('selected', true);
                    }
                });
                $select.trigger('change');
            };

            const requestAll = (url, params, $select) => {
                return $.get(url, params).then(data => {
                    const results = data.results || [];
                    appendOptions($select, results, true);
                });
            };

            const preloadSelected = (url, ids, params, $select) => {
                if (!ids || !ids.length) {
                    return $.Deferred().resolve().promise();
                }
                return $.get(url, Object.assign({}, params, { ids })).then(data => {
                    const results = data.results || [];
                    appendOptions($select, results, true);
                });
            };

            // ==========================
            // Cache helpers for clubs/events
            // ==========================
            let clubCache = null;
            let eventCache = null;

            const loadClubs = () => {
                if (clubCache) return $.Deferred().resolve(clubCache).promise();
                return $.get(routes.clubs).then(data => {
                    clubCache = data || [];
                    return clubCache;
                });
            };

            const loadEvents = () => {
                if (eventCache) return $.Deferred().resolve(eventCache).promise();
                return $.get(routes.events).then(data => {
                    eventCache = data || [];
                    return eventCache;
                });
            };

            // ==========================
            // Render target selectors
            // ==========================
            const renderUserTarget = (useOld) => {
                $targetWrapper.html(`
                    <label class="form-label">Chọn người nhận</label>
                    <select id="users_select" name="users[]" class="form-select" multiple></select>
                `);

                const $select = $('#users_select');
                initSelect2($select, {
                    placeholder: 'Nhập tên hoặc email...',
                    url: routes.users,
                });

                ensureToolbar($select, () => requestAll(routes.users, { all: 1 }, $select));

                if (useOld) {
                    const ids = [...initialState.users];
                    preloadSelected(routes.users, ids, {}, $select).then(() => {
                        initialState.users = [];
                    });
                }
            };

            const renderClubTarget = (useOld) => {
                $targetWrapper.html(`
                    <label class="form-label">Chọn CLB</label>
                    <select id="club_select" name="club_id" class="form-select">
                        <option value="">-- Chọn CLB --</option>
                    </select>
                    <label class="form-label mt-3">Chọn thành viên</label>
                    <select id="club_members" name="users[]" class="form-select" multiple disabled></select>
                `);

                const $clubSelect = $('#club_select');
                const $membersSelect = $('#club_members');

                const setupMembersSelect = (clubId, preselectedIds = []) => {
                    $membersSelect.prop('disabled', !clubId);
                    $membersSelect.val(null).trigger('change');
                    $membersSelect.empty();

                    if (!clubId) {
                        if ($membersSelect.data('select2')) {
                            $membersSelect.select2('destroy');
                        }
                        return;
                    }

                    if ($membersSelect.data('select2')) {
                        $membersSelect.select2('destroy');
                    }

                    initSelect2($membersSelect, {
                        placeholder: 'Nhập tên hoặc email...',
                        url: routes.clubMembers,
                        extraParams: { club_id: clubId },
                    });

                    ensureToolbar($membersSelect, () => requestAll(routes.clubMembers, { club_id: clubId, all: 1 }, $membersSelect));

                    if (preselectedIds.length) {
                        preloadSelected(routes.clubMembers, preselectedIds, { club_id: clubId }, $membersSelect).then(() => {
                            initialState.users = [];
                        });
                    }
                };

                loadClubs().then(clubs => {
                    const options = clubs.map(club => `<option value="${club.id}">${club.name}</option>`).join('');
                    $clubSelect.append(options);

                    if (useOld && initialState.clubId) {
                        $clubSelect.val(String(initialState.clubId));
                        setupMembersSelect(String(initialState.clubId), [...initialState.users]);
                        initialState.clubId = null;
                        initialState.users = [];
                    }
                });

                $clubSelect.on('change', function () {
                    const clubId = $(this).val();
                    setupMembersSelect(clubId, []);
                });
            };

            const renderRoleTarget = (useOld) => {
                $targetWrapper.html(`
                        <label class="form-label">Chọn vai trò</label>
                        <select id="role_select" name="role" class="form-select" required>
                            <option value="">-- Chọn vai trò --</option>
                            <option value="club_manager">Chủ nhiệm CLB</option>
                            <option value="deputy_manager">Phó chủ nhiệm</option>
                            <option value="secretary">Thư ký</option>
                            <option value="treasurer">Thủ quỹ</option>
                            <option value="event_manager">Quản lý sự kiện</option>
                            <option value="communication">Truyền thông</option>
                            <option value="member">Thành viên thường</option>
                            <option value="admin">Admin hệ thống</option>
                        </select>
                        <label class="form-label mt-3">Chọn người</label>
                        <select id="role_users" name="users[]" class="form-select" multiple disabled></select>
                    `);

                const $roleSelect = $('#role_select');
                const $usersSelect = $('#role_users');

                const setupRoleUsers = (role, preselectedIds = []) => {
                    $usersSelect.prop('disabled', !role);
                    $usersSelect.val(null).trigger('change');
                    $usersSelect.empty();

                    if (!role) {
                        if ($usersSelect.data('select2')) {
                            $usersSelect.select2('destroy');
                        }
                        return;
                    }

                    if ($usersSelect.data('select2')) {
                        $usersSelect.select2('destroy');
                    }

                    const isClubRole = clubMemberRoles.includes(role);
                    const url = isClubRole ? routes.clubMembers : routes.users;
                    const params = isClubRole ? { role } : { role };

                    initSelect2($usersSelect, {
                        placeholder: 'Nhập tên hoặc email...',
                        url,
                        extraParams: params,
                    });

                    ensureToolbar($usersSelect, () => requestAll(url, { ...params, all: 1 }, $usersSelect));

                    if (preselectedIds.length) {
                        preloadSelected(url, preselectedIds, params, $usersSelect).then(() => {
                            initialState.users = [];
                        });
                    }
                };

                if (useOld && initialState.role) {
                    $roleSelect.val(initialState.role);
                    setupRoleUsers(initialState.role, [...initialState.users]);
                    $usersSelect.prop('disabled', false);
                    initialState.role = null;
                    initialState.users = [];
                }

                $roleSelect.on('change', function () {
                    setupRoleUsers($(this).val(), []);
                });
            };

            const renderEventTarget = (useOld) => {
                $targetWrapper.html(`
                    <label class="form-label">Chọn sự kiện</label>
                    <select id="event_select" name="event_id" class="form-select">
                        <option value="">-- Chọn sự kiện --</option>
                    </select>
                    <label class="form-label mt-3">Chọn người tham gia</label>
                    <select id="event_members" name="users[]" class="form-select" multiple disabled></select>
                `);

                const $eventSelect = $('#event_select');
                const $membersSelect = $('#event_members');

                const setupEventMembers = (eventId, preselectedIds = []) => {
                    $membersSelect.prop('disabled', !eventId);
                    $membersSelect.val(null).trigger('change');
                    $membersSelect.empty();

                    if (!eventId) {
                        if ($membersSelect.data('select2')) {
                            $membersSelect.select2('destroy');
                        }
                        return;
                    }

                    if ($membersSelect.data('select2')) {
                        $membersSelect.select2('destroy');
                    }

                    initSelect2($membersSelect, {
                        placeholder: 'Nhập tên hoặc email...',
                        url: routes.eventMembers,
                        extraParams: { event_id: eventId },
                    });

                    ensureToolbar($membersSelect, () => requestAll(routes.eventMembers, { event_id: eventId, all: 1 }, $membersSelect));

                    if (preselectedIds.length) {
                        preloadSelected(routes.eventMembers, preselectedIds, { event_id: eventId }, $membersSelect).then(() => {
                            initialState.users = [];
                        });
                    }
                };

                loadEvents().then(events => {
                    const options = events.map(event => `<option value="${event.id}">${event.name}</option>`).join('');
                    $eventSelect.append(options);

                    if (useOld && initialState.eventId) {
                        $eventSelect.val(String(initialState.eventId));
                        setupEventMembers(String(initialState.eventId), [...initialState.users]);
                        initialState.eventId = null;
                        initialState.users = [];
                    }
                });

                $eventSelect.on('change', function () {
                    setupEventMembers($(this).val(), []);
                });
            };

            const renderTargetInputs = (type, useOld = false) => {
                $targetWrapper.empty();

                switch (type) {
                    case 'user':
                        renderUserTarget(useOld);
                        break;
                    case 'club':
                        renderClubTarget(useOld);
                        break;
                    case 'role':
                        renderRoleTarget(useOld);
                        break;
                    case 'event':
                        renderEventTarget(useOld);
                        break;
                    default:
                        // no additional fields
                        break;
                }
            };

            $targetType.on('change', function () {
                renderTargetInputs(this.value, false);
            });

            if (initialState.type) {
                $targetType.val(initialState.type);
                renderTargetInputs(initialState.type, true);
                initialState.type = null;
            } else {
                $targetType.val('');
                $targetWrapper.empty();
            }

            // Validate submit: đảm bảo có người nhận khi cần
            $('form').on('submit', function (e) {
                const $userSelect = $('select[name="users[]"]:visible');
                const selectedValues = $userSelect.val();
                const hasSelectedUsers = Array.isArray(selectedValues) && selectedValues.length > 0;
                const hasClub = !!$('[name="club_id"]').val();
                const hasEvent = !!$('[name="event_id"]').val();
                const hasRole = !!$('[name="role"]').val();

                if ($userSelect.length && !hasSelectedUsers && !hasClub && !hasEvent && !hasRole) {
                    alert('Vui lòng chọn ít nhất một người nhận.');
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
