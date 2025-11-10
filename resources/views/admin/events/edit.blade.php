{{-- resources/views/admin/events/edit.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa Sự kiện')

@section('card-body')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1 text-primary fw-bold">Chỉnh sửa Sự kiện</h1>
            <p class="text-muted small mb-0">Cập nhật thông tin • ID: #{{ $event->id }}</p>
        </div>
        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-info shadow-sm">
            Xem chi tiết
        </a>
    </div>

    <!-- Form -->
    <div class="card border-0 shadow-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <!-- Alert lỗi -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4">
                        <strong>Có lỗi xảy ra:</strong>
                        <ul class="mt-2 mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-5">

                    <!-- Cột trái: Thông tin cơ bản + Ngân sách -->
                    <div class="col-lg-7">

                        <!-- Thông tin cơ bản -->
                        <div class="bg-white rounded-4 shadow-sm p-4 mb-4 border">
                            <h5 class="fw-bold text-primary mb-4">Thông tin cơ bản</h5>

                            <div class="row g-4">
                                <!-- CLB -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Câu lạc bộ</label>
                                    <div class="form-control bg-light border-0">{{ $event->club?->name ?? '—' }}</div>
                                    <input type="hidden" name="club_id" value="{{ $event->club_id }}">
                                    <input type="hidden" name="created_by" value="{{ $event->created_by }}">
                                </div>

                                <!-- Tên sự kiện -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Tên sự kiện</label>
                                    <div class="form-control bg-light border-0">{{ $event->name }}</div>
                                    <input type="hidden" name="name" value="{{ $event->name }}">
                                </div>

                                <!-- Mô tả -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Mô tả</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả sự kiện...">{{ old('description', $event->description) }}</textarea>
                                </div>

                                <!-- Thời gian -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_time" class="form-control" required
                                           value="{{ old('start_time', $event->start_time?->format('Y-m-d\TH:i')) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_time" class="form-control" required
                                           value="{{ old('end_time', $event->end_time?->format('Y-m-d\TH:i')) }}">
                                </div>

                                <!-- Địa điểm -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                                    <input type="text" name="location" class="form-control" required
                                           value="{{ old('location', $event->location) }}">
                                </div>

                                <!-- Giới hạn người tham gia -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Giới hạn người tham gia</label>
                                    <input type="number" name="max_participants" class="form-control"
                                           value="{{ old('max_participants', $event->max_participants) }}" min="1" placeholder="Không giới hạn">
                                </div>

                                <!-- Hiển thị -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Hiển thị</label>
                                    <select name="is_public" class="form-select">
                                        <option value="1" {{ $event->is_public ? 'selected' : '' }}>Công khai toàn trường</option>
                                        <option value="0" {{ !$event->is_public ? 'selected' : '' }}>Chỉ CLB</option>
                                    </select>
                                </div>

                                <!-- Trạng thái -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="pending" {{ $event->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                        <option value="approved" {{ $event->status == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                        <option value="rejected" {{ $event->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Ngân sách -->
                        <div class="bg-gradient-primary text-black rounded-4 shadow-sm p-4">
                            <h5 class="fw-bold mb-4">Quản lý ngân sách (VNĐ)</h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label text-black opacity-90">Dự kiến</label>
                                    <input type="number" name="budget_estimated" class="form-control form-control-lg text-primary fw-bold"
                                           value="{{ old('budget_estimated', $event->budget_estimated) }}" step="1000" placeholder="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-black opacity-90">Quỹ cần cấp</label>
                                    <input type="number" name="budget_requested" class="form-control form-control-lg text-success fw-bold"
                                           value="{{ old('budget_requested', $event->budget_requested) }}" step="1000" placeholder="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-black opacity-90">Quỹ CLB tự chi</label>
                                    <input type="number" name="budget_club" class="form-control form-control-lg text-danger fw-bold"
                                           value="{{ old('budget_club', $event->budget_club) }}" step="1000" placeholder="0">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Cột phải: Ảnh + Người tạo + Duyệt -->
                    <div class="col-lg-5">

                        <!-- Ảnh bìa -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">Ảnh bìa sự kiện</label>
                            <div class="poster-zone border-3 border-dashed border-primary rounded-4 bg-light position-relative overflow-hidden"
                                 style="height: 380px; cursor: pointer;"
                                 onclick="document.getElementById('poster_input').click()">

                                @if($event->media_id)
                                    <img src="{{ $event->media?->url }}" id="poster_preview"
                                         class="w-100 h-100 object-fit-cover rounded-4">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-3 rounded-circle shadow-lg"
                                            onclick="event.stopPropagation(); removePoster()">
                                        Xóa
                                    </button>
                                @else
                                    <div id="poster_placeholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                        <i class="fas fa-image fa-5x mb-4 opacity-50"></i>
                                        <p class="fw-bold fs-5 mb-1">Click hoặc kéo thả ảnh</p>
                                        <small class="opacity-75">JPG, PNG, WEBP • Tối đa 5MB</small>
                                    </div>
                                    <img id="poster_preview" class="w-100 h-100 object-fit-cover rounded-4 d-none">
                                @endif
                            </div>

                            <input type="file" name="poster" id="poster_input" class="d-none" accept="image/*" onchange="previewPoster(this)">
                            <input type="hidden" name="remove_poster" id="remove_poster" value="0">
                        </div>

                        <!-- Người tạo -->
                        <div class="bg-white rounded-4 shadow-sm p-4 border">
                            <h6 class="fw-bold text-primary mb-3">Người tạo</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:56px; height:56px; font-size:20px; font-weight:bold;">
                                    {{ substr($event->createdBy?->name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">{{ $event->createdBy?->name ?? 'Hệ thống' }}</div>
                                    <small class="text-muted">{{ $event->createdBy?->email ?? '' }}</small>
                                    <div class="text-success small mt-1">
                                        {{ $event->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Người duyệt -->
                        @if($event->approval_by)
                        <div class="bg-success text-white rounded-4 shadow-sm p-4 mt-4">
                            <h6 class="fw-bold mb-3">Đã duyệt bởi</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:56px; height:56px; font-weight:bold;">
                                    ✓
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">{{ $event->approvalBy?->name }}</div>
                                    <small>{{ $event->approvalBy?->email }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="text-end mt-5 pt-4 border-top">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                        Cập nhật sự kiện
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-lg px-5 ms-3">
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
