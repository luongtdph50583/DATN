@extends('admin.layouts.app')

@section('title', 'Tạo đề xuất cập nhật CLB')
@section('card-title', 'Đề xuất cập nhật CLB')
@section('card-header', 'Nhập thông tin cập nhật')

@section('card-body')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.club_requests_update.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Chọn CLB <span class="text-danger">*</span></label>
                    <select name="club_id" class="form-select" data-select2="true" data-placeholder="Chọn CLB" required>
                        <option value="">-- Chọn CLB --</option>
                        @foreach($clubs as $clubItem)
                            <option value="{{ $clubItem->id }}" @selected(old('club_id') == $clubItem->id)>
                                {{ $clubItem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên CLB mới</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Khẩu hiệu</label>
                    <input type="text" name="slogan" class="form-control" value="{{ old('slogan') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Lĩnh vực hoạt động</label>
                    <input type="text" name="field" class="form-control" value="{{ old('field') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Giới hạn thành viên</label>
                    <input type="number" name="member_limit" class="form-control" min="1" value="{{ old('member_limit') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Chủ nhiệm đề xuất</label>
                    <select name="manager_id" class="form-select" data-select2="true" data-placeholder="Chọn chủ nhiệm">
                        <option value="">-- Chọn thành viên --</option>
                        @foreach($users as $userOption)
                            <option value="{{ $userOption->id }}" @selected(old('manager_id') == $userOption->id)>
                                {{ $userOption->name }} ({{ $userOption->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giảng viên đỡ đầu</label>
                    <select name="advisor_id" class="form-select" data-select2="true" data-placeholder="Chọn giảng viên">
                        <option value="">-- Chọn giảng viên --</option>
                        @foreach($facultyMembers as $advisor)
                            <option value="{{ $advisor->id }}" @selected(old('advisor_id') == $advisor->id)>
                                {{ $advisor->user->name ?? $advisor->employee_code }} - {{ $advisor->department }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email liên hệ</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa điểm hoạt động</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Logo mới</label>
                    <input type="file" name="logo" class="form-control">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Nội quy</label>
            <textarea name="rules" rows="3" class="form-control">{{ old('rules') }}</textarea>
        </div>
        <div class="mb-4">
            <label class="form-label">Lý do đề xuất <span class="text-danger">*</span></label>
            <textarea name="reason" rows="3" class="form-control" required>{{ old('reason') }}</textarea>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light fw-semibold">Ban quản lý đề xuất</div>
            <div class="card-body">
                @php
                    $oldManagement = old('management_updates', [['user_id' => null, 'role' => null]]);
                @endphp
                <div id="managementUpdateList">
                    @foreach($oldManagement as $index => $entry)
                        <div class="row g-3 align-items-end management-row" data-index="{{ $index }}">
                            <div class="col-md-7">
                                <label class="form-label">Thành viên</label>
                                <select name="management_updates[{{ $index }}][user_id]" class="form-select"
                                        data-select2="true" data-placeholder="Chọn thành viên">
                                    <option value="">-- Chọn thành viên --</option>
                                    @foreach($users as $userOption)
                                        <option value="{{ $userOption->id }}"
                                            @selected(($entry['user_id'] ?? null) == $userOption->id)>
                                            {{ $userOption->name }} ({{ $userOption->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Chức vụ</label>
                                <select name="management_updates[{{ $index }}][role]" class="form-select"
                                        data-select2="true" data-placeholder="Chọn chức vụ">
                                    <option value="">-- Chọn chức vụ --</option>
                                    @foreach($roles as $roleKey => $roleLabel)
                                        <option value="{{ $roleKey }}" @selected(($entry['role'] ?? null) === $roleKey)>
                                            {{ $roleLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 text-end">
                                @if($index > 0)
                                    <button type="button" class="btn btn-outline-danger remove-management-row">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="addManagementRow">
                    <i class="fa-solid fa-plus me-1"></i> Thêm thành viên
                </button>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane me-1"></i> Gửi đề xuất
            </button>
        </div>
    </form>
@endsection

<template id="managementRowTemplate">
    <div class="row g-3 align-items-end management-row" data-index="__INDEX__">
        <div class="col-md-7">
            <label class="form-label">Thành viên</label>
            <select name="management_updates[__INDEX__][user_id]" class="form-select" data-select2="true" data-placeholder="Chọn thành viên">
                <option value="">-- Chọn thành viên --</option>
                @foreach($users as $userOption)
                    <option value="{{ $userOption->id }}">
                        {{ $userOption->name }} ({{ $userOption->email }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Chức vụ</label>
            <select name="management_updates[__INDEX__][role]" class="form-select" data-select2="true" data-placeholder="Chọn chức vụ">
                <option value="">-- Chọn chức vụ --</option>
                @foreach($roles as $roleKey => $roleLabel)
                    <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-outline-danger remove-management-row">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</template>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('managementUpdateList');
            const addBtn = document.getElementById('addManagementRow');
            const template = document.getElementById('managementRowTemplate').innerHTML;
            let currentIndex = {{ count($oldManagement) }};

            addBtn.addEventListener('click', function () {
                const html = template.replaceAll('__INDEX__', currentIndex);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                const row = wrapper.firstElementChild;
                container.appendChild(row);
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(row).find('select[data-select2="true"]').select2({ width: '100%' });
                }
                currentIndex++;
            });

            container.addEventListener('click', function (event) {
                if (event.target.closest('.remove-management-row')) {
                    const row = event.target.closest('.management-row');
                    if (row) {
                        row.remove();
                    }
                }
            });
        });
    </script>
@endpush

