@extends('client.layouts.app')
@section('title', 'Gửi thông báo - ' . $club->name)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="mb-1">Gửi thông báo tới thành viên</h2>
                        <p class="text-muted mb-0">CLB: {{ $club->name }}</p>
                    </div>
                    <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}"
                        class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại quản lý CLB
                    </a>
                </div>

                {{-- Hiển thị thông báo --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('club_manager.notifications.store', ['club_id' => $club->id]) }}"
                            method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nội dung thông báo <span class="text-danger">*</span></label>
                                <textarea name="content_html" id="notificationContentEditor" class="form-control" rows="8"
                                    >{{ old('content_html') }}</textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Kênh gửi <span class="text-danger">*</span></label>
                                    <select name="send_via" class="form-select" required>
                                        <option value="database">In-app</option>
                                        <option value="mail">Email</option>
                                        <option value="both">Cả hai</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Đối tượng nhận <span class="text-danger">*</span></label>
                                    <select name="target" id="targetSelect" class="form-select" required>
                                        <option value="all">Tất cả thành viên CLB</option>
                                        <option value="role">Theo chức vụ</option>
                                        <option value="custom">Chọn thành viên cụ thể</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3" id="roleWrapper" style="display:none;">
                                <label class="form-label">Chức vụ trong CLB</label>
                                <select name="role" id="roleSelect" class="form-select">
                                    <option value="">-- Chọn chức vụ --</option>
                                    @foreach($memberRoles as $roleKey => $label)
                                        <option value="{{ $roleKey }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div id="roleDebug" class="small text-muted mt-2" style="display:none;"></div>
                            </div>

                            <div class="mt-3" id="membersWrapper" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label">Danh sách thành viên</label>
                                    
                                </div>
                                <select id="membersSelect" name="user_ids[]" class="form-select" multiple></select>
                                <div id="membersDebug" class="small text-muted mt-2" style="display:none;"></div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="theme-btn">
                                    <i class="fas fa-paper-plane me-1"></i> Gửi thông báo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endsection

@push('scripts')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    {{-- Select2 CHUẨN --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

  <script>
$(function () {

    /* ------------------ CKEditor ------------------ */
    let notificationEditor;

    ClassicEditor.create(document.querySelector('#notificationContentEditor'))
        .then(editor => {
            notificationEditor = editor;
        })
        .catch(e => console.error("CKEditor error:", e));

    /* Sync CKEditor → textarea trước khi submit */
    $('form').on('submit', function () {
        if (notificationEditor) {
            $('#notificationContentEditor').val(notificationEditor.getData());
        }
    });


    /* ------------------ DOM ------------------ */
    const $targetSelect = $('#targetSelect');
    const $roleWrapper = $('#roleWrapper');
    const $membersWrapper = $('#membersWrapper');
    const $roleSelect = $('#roleSelect');
    const $membersSelect = $('#membersSelect');
    const $roleDebug = $('#roleDebug');
    const $membersDebug = $('#membersDebug');

  


    /* ------------------ INIT SELECT2 ------------------ */
    function initMembersSelect2() {
        if (!$membersSelect.data('select2')) {
            console.log("Init Select2...");
            $membersSelect.select2({
                width: '100%',
                placeholder: 'Nhập tên hoặc email...',
                ajax: {
                    url: "{{ route('club_manager.notifications.fetchClubMembers', ['club_id' => $club->id]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            role: $roleSelect.val() || '',
                            target: $targetSelect.val(),
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        $membersDebug.text(`Tìm thấy ${data.results?.length || 0} thành viên`).show();
                        return { results: data.results || [] };
                    }
                }
            });
        }
    }


    /* ------------------ Toggle UI ------------------ */
    function toggleFields() {
        const val = $targetSelect.val();

        // Toggle hiển thị
        $roleWrapper.toggle(val === 'role');
        $membersWrapper.toggle(val === 'custom');

        // CUSTOM -> bật Select2
        if (val === 'custom') {
            initMembersSelect2();
        } else {
            if ($membersSelect.data('select2')) {
                $membersSelect.select2('destroy');
            }
            $membersSelect.empty();
        }

        // ROLE -> debug
        if (val === 'role') {
            $.get("{{ route('club_manager.notifications.fetchClubMembers', ['club_id' => $club->id]) }}",
                { role: $roleSelect.val(), all: 1 }
            ).done(function (data) {
                $roleDebug.text(`Tìm thấy ${data.results?.length || 0} thành viên`).show();
            });
        }
    }

    $targetSelect.on('change', toggleFields);
    $roleSelect.on('change', toggleFields);

    toggleFields();
});
</script>


@endpush

