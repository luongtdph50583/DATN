@extends('admin.layouts.app')
@section('title', 'Thêm CLB')
@section('card-title', 'Thêm câu lạc bộ mới')
@section('card-body')

        {{-- ⚠️ Hiển thị lỗi tổng quát (nếu có) --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($errors->has('error'))
            <div class="alert alert-danger py-2">
                <i class="bi bi-x-circle"></i> {{ $errors->first('error') }}
            </div>
        @endif

        <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-4">
                {{-- Cột trái --}}
                <div class="col-md-4">
                    {{-- Thông tin cơ bản --}}
                    <div class="card shadow-sm border-primary mb-4">
                        <div class="card-header bg-primary text-white">Thông tin cơ bản</div>
                        <div class="card-body text-center">
                            <img id="logoPreview" src="{{ asset('images/default-club.png') }}" class="img-fluid rounded mb-3"
                                style="max-height: 150px;">
                            <input type="file" name="logo" id="logo" class="form-control mb-3" accept="image/*">

                            <div class="mb-3 text-start">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngưng hoạt động
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3 text-start">
                                <label class="form-label">Giới hạn thành viên</label>
                                <input type="number" name="member_limit" class="form-control"
                                    value="{{ old('member_limit', 50) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Ban quản lý --}}
                    <div class="card shadow-sm border-success">
                        <div class="card-header bg-success text-white">Ban quản lý CLB</div>
                        <div class="card-body">

                            {{-- ⚠️ Hiển thị lỗi riêng của ban quản lý --}}
                            @if ($errors->has('managers'))
                                <div class="alert alert-danger py-2 mb-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    {{ $errors->first('managers') }}
                                </div>
                            @endif

                            @php
$roles = [
    'club_manager' => 'Chủ nhiệm',
    'deputy_manager' => 'Phó chủ nhiệm',
    'secretary' => 'Thư ký',
    'treasurer' => 'Thủ quỹ',
    'event_manager' => 'Quản lý sự kiện',
    'communication' => 'Truyền thông',
];
                            @endphp

                            @foreach ($roles as $key => $label)
                                <div class="mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <select name="managers[{{ $key }}]" class="form-select select2-member"
                                        data-placeholder="Chọn {{ strtolower($label) }}">
                                        @if (old("managers.$key"))
                                            <option value="{{ old("managers.$key") }}" selected>
                                                Thành viên đã chọn
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Cột phải --}}
                <div class="col-md-8">
                    <div class="card border-info shadow-sm">
                        <div class="card-header bg-info text-white">Thông tin chi tiết</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tên CLB</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lĩnh vực</label>
                                <input type="text" name="field" class="form-control" value="{{ old('field') }}">
                            </div>
                            <div class="mb-3">
                                    <label class="form-label">Slogan</label>
                                    <input type="text" name="slogan" class="form-control" placeholder="Nhập slogan của CLB"
                                        value="{{ old('slogan', $club->slogan ?? '') }}">
                                </div>
                            <div class="mb-3">
                                <label class="form-label">Địa điểm</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <div id="description-editor" style="height: 200px;">{!! old('description') !!}</div>
                                <input type="hidden" name="description" id="description-input">
                            </div>

                            {{-- Nội quy --}}
                            <div class="mb-3">
                                <label class="form-label">Nội quy</label>
                                <div id="rules-editor" style="height: 200px;">{!! old('rules') !!}</div>
                                <input type="hidden" name="rules" id="rules-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary">Thêm CLB</button>
        </form>
@endsection




@push('scripts')
        {{-- Select2 --}}
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
           $(document).ready(function () {
                $('#logo').change(function (e) {
                    const [file] = e.target.files;
                    if (file) $('#logoPreview').attr('src', URL.createObjectURL(file));
                });

                $('.select2-member').select2({
                    placeholder: function () {
                        return $(this).data('placeholder');
                    },
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route("admin.clubs.members.search") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(item => ({
                                    id: item.id,
                                    text: item.text || '—'
                                }))
                            };
                        },
                        cache: true
                    },
                    templateResult: function (data) {
                        if (!data.id) return data.text;
                        return $('<span>' + data.text + '</span>');
                    },
                    templateSelection: function (data) {
                        return data.text || '—';
                    }
                });

                $('.select2-member').on('select2:select select2:unselect', function (e) {
                    const val = $(this).val();
                    if (val) {
                        if (!$(this).find('option[value="' + val + '"]').length) {
                            $(this).append(new Option(e.params.data.text, val, true, true)).trigger('change');
                        }
                    } else {
                        $(this).find('option').prop('selected', false);
                    }
                });

                const quillDesc = new Quill('#description-editor', { theme: 'snow' });
                const quillRules = new Quill('#rules-editor', { theme: 'snow' });

                $('form').on('submit', function () {
                    $('#description-input').val(quillDesc.root.innerHTML);
                    $('#rules-input').val(quillRules.root.innerHTML);
                });
            });

        </script>
@endpush
