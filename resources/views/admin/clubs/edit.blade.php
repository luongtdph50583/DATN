@extends('admin.layouts.app')

@section('title', 'Sửa CLB')
@section('card-title', 'Sửa câu lạc bộ: ' . $club->name)

@section('card-body')
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
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

                            <form action="{{ route('admin.clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    {{-- Cột trái --}}
                                    <div class="col-md-4">
                                        {{-- Thông tin cơ bản --}}
                            <div class="card mb-4 shadow-sm border-primary">
                                <div class="card-header bg-primary text-white fw-bold">Thông tin cơ bản</div>
                                <div class="card-body">
                                    <!-- Logo CLB -->
                                    <div class="text-center mb-3">
                                        <img id="logoPreview"
                                            src="{{ $club->logo ? asset('storage/' . $club->logo) : asset('images/default-club.png') }}"
                                            alt="Logo CLB" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>

                                    <!-- Cập nhật logo -->
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Cập nhật logo</label>
                                        <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                        @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Giới hạn thành viên -->
                                    <div class="mb-3">
                                        <label for="member_limit" class="form-label">Giới hạn thành viên</label>
                                        <input type="number" name="member_limit" id="member_limit" class="form-control"
                                            value="{{ $club->member_limit }}" placeholder="Nhập số lượng tối đa...">
                                        @error('member_limit') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Trạng thái -->


                                    <!-- Ngày thành lập -->
                                    <p><strong>Ngày thành lập:</strong> {{ $club->founded_at?->format('d/m/Y') ?? '—' }}</p>
                                </div>
                            </div>

        <!-- Card Trạng thái CLB -->
        <div class="card mb-4 shadow-sm border-warning">
            <div class="card-header bg-warning text-dark fw-bold">Trạng thái CLB</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select select2-single">
                        <option value="active" {{ $club->status === 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ $club->status === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
                    </select>
                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>



                                        {{-- Ban quản lý --}}
                                        <div class="card mb-4 shadow-sm border-success">
                                            <div class="card-header bg-success text-white fw-bold">Ban quản lý CLB</div>
                                            <div class="card-body">
                                                @php
                                                $roles = [
                                                    'club_manager' => 'Chủ nhiệm',
                                                    'deputy_manager' => 'Phó chủ nhiệm',
                                                    'secretary' => 'Thư ký',
                                                    'treasurer' => 'Thủ quỹ',
                                                    'event_manager' => 'Quản lý sự kiện',
                                                    'communication' => 'Truyền thông'
                                                ];
                                                @endphp

                                                @foreach($roles as $roleKey => $roleLabel)
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ $roleLabel }}</label>
                                                        <select name="managers[{{ $roleKey }}]" class="form-select select2-single">
                                                            <option value="">-- Chọn thành viên --</option>
                                                            @foreach($clubMembers as $member)
                                                                @if($member['role'] === 'member' || $member['role'] === $roleKey)
                                                                    <option value="{{ $member['member']['id'] }}"
                                                                        {{ old('managers.' . $roleKey, $member['role'] === $roleKey ? $member['member']['id'] : null) == $member['member']['id'] ? 'selected' : '' }}>
                                                                        {{ $member['member']['user']['name'] ?? '—' }}
                                                                        @if(!empty($member['member']['student_code']) || !empty($member['member']['user']['email']))
                                                                            ({{ $member['member']['student_code'] ?? '' }} - {{ $member['member']['user']['email'] ?? '' }})
                                                                        @endif
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @error('managers.' . $roleKey) <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Cột phải --}}
                                    <div class="col-md-8">
                                        <div class="card mb-4 shadow-sm border-info">
                                            <div class="card-header bg-info text-white fw-bold">Thông tin chi tiết</div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Tên CLB</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $club->name) }}">
                                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="field" class="form-label">Lĩnh vực</label>
                                                    <input type="text" name="field" id="field" class="form-control" value="{{ old('field', $club->field) }}">
                                                    @error('field') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="location" class="form-label">Địa điểm</label>
                                                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $club->location) }}">
                                                    @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $club->email) }}">
                                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">Điện thoại</label>
                                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $club->phone) }}">
                                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                {{-- Mô tả --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Mô tả</label>
                                                    <div id="descriptionEditor" style="min-height:200px; border:1px solid #ced4da; border-radius:6px; padding:10px; background:#fff;">
                                                        {!! old('description', $club->description) !!}
                                                    </div>
                                                    <input type="hidden" name="description" id="descriptionInput">
                                                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                {{-- Nội quy --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Nội quy CLB</label>
                                                    <div id="rulesEditor" style="min-height:200px; border:1px solid #ced4da; border-radius:6px; padding:10px; background:#fff;">
                                                        {!! old('rules', $club->rules) !!}
                                                    </div>
                                                    <input type="hidden" name="rules" id="rulesInput">
                                                    @error('rules') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Cập nhật CLB</button>
                            </form>
@endsection

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    // Select2 cho ban quản lý
    $('.select2-single').select2({ placeholder: 'Chọn thành viên', allowClear: true, width:'100%' });

    // Quill Editors
    var descriptionQuill = new Quill('#descriptionEditor', {
        theme: 'snow',
        placeholder: 'Nhập mô tả...',
        modules: { toolbar: [['bold','italic','underline'],['link'],[{list:'ordered'},{list:'bullet'}],['clean']] }
    });
    var rulesQuill = new Quill('#rulesEditor', {
        theme: 'snow',
        placeholder: 'Nhập nội quy...',
        modules: { toolbar: [['bold','italic','underline'],['link'],[{list:'ordered'},{list:'bullet'}],['clean']] }
    });

    // Submit form gán nội dung Quill
    $('form').submit(function(){
        $('#descriptionInput').val(descriptionQuill.root.innerHTML);
        $('#rulesInput').val(rulesQuill.root.innerHTML);
    });

    // Preview logo trước khi upload
    $('#logo').change(function(e){
        const [file] = e.target.files;
        if(file){
            $('#logoPreview').attr('src', URL.createObjectURL(file));
        }
    });
});
</script>
@endpush
