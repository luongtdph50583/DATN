{{-- resources/views/admin/events/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Sự kiện')

@section('card-body')
<div class="container py-4">
    <h3 class="mb-4">Chỉnh sửa Sự kiện #{{ $event->id }}</h3>

    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Hiển thị lỗi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">

                {{-- Câu lạc bộ --}}
                <div class="mb-3">
                    <label class="form-label">Câu lạc bộ</label>
                    <input type="text" class="form-control" value="{{ $event->club?->name ?? '—' }}" readonly>
                </div>

                {{-- Tên sự kiện --}}
                <div class="mb-3">
                    <label class="form-label">Tên sự kiện</label>
                    <input type="text" class="form-control" value="{{ $event->name }}" readonly>
                </div>

                {{-- Mô tả --}}
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control" rows="3" readonly>{{ $event->description }}</textarea>
                </div>

                {{-- Địa điểm --}}
                <div class="mb-3">
                    <label class="form-label">Địa điểm</label>
                    <input type="text" class="form-control" value="{{ $event->location }}" readonly>
                </div>

                {{-- Giới hạn người tham gia --}}
                <div class="mb-3">
                    <label class="form-label">Giới hạn người tham gia</label>
                    <input type="text" class="form-control" value="{{ $event->max_participants ?? 'Không giới hạn' }}" readonly>
                </div>

                {{-- Hiển thị --}}
                <div class="mb-3">
                    <label class="form-label">Hiển thị</label>
                    <input type="text" class="form-control" value="{{ $event->is_public ? 'Công khai toàn trường' : 'Chỉ CLB' }}" readonly>
                </div>

                {{-- Thời gian bắt đầu --}}
                <div class="mb-3">
                    <label class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="start_time" class="form-control"
                           value="{{ old('start_time', $event->start_time?->format('Y-m-d\TH:i')) }}" required>
                </div>

                {{-- Thời gian kết thúc --}}
                <div class="mb-3">
                    <label class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="end_time" class="form-control"
                           value="{{ old('end_time', $event->end_time?->format('Y-m-d\TH:i')) }}" required>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="pending" {{ $event->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ $event->status == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ $event->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>

              <div class="col-12">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-images text-secondary"></i> Hình ảnh / Video sự kiện
            </h5>
        </div>
        <div class="card-body">

            @if($event->media->isNotEmpty())
                <div class="row g-3">
                    @foreach($event->media as $media)
                        <div class="col-md-3 col-6">
                            <div class="card border-0 shadow-sm">
                                @if(Str::startsWith($media->file_type, 'image'))
                                    <img src="{{ asset('storage/' . $media->file_path) }}" 
                                         class="img-fluid rounded" 
                                         alt="{{ $media->file_name }}" 
                                         style="height: 120px; object-fit: cover; width: 100%;">
                                @elseif(Str::startsWith($media->file_type, 'video'))
                                    <video controls 
                                           class="w-100 rounded" 
                                           style="height: 120px; object-fit: cover;">
                                        <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ $media->file_type }}">
                                        Trình duyệt không hỗ trợ video.
                                    </video>
                                @endif
                                <div class="card-body py-1 px-2 text-center">
                                    <small class="text-muted text-truncate d-block" title="{{ $media->file_name }}">
                                        {{ $media->file_name }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-image fa-4x mb-3"></i>
                    <p>Chưa có hình ảnh / video nào</p>
                </div>
            @endif

            {{-- Upload media mới --}}
            <div class="mt-3">
                <label class="form-label fw-bold">Thêm media mới</label>
                <input type="file" name="media[]" class="form-control" multiple>
                <small class="text-muted">Hỗ trợ: jpeg, png, jpg, gif, mp4, mov, avi (tối đa 10MB mỗi file)</small>
            </div>

        </div>
    </div>
</div>


            </div>

            <div class="col-md-4">
                {{-- Thông tin người tạo --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold">Người tạo</h6>
                        <p class="mb-1">{{ $event->createdBy?->name ?? 'Hệ thống' }}</p>
                        <small class="text-muted">{{ $event->createdBy?->email ?? '' }}</small><br>
                        <small class="text-muted">Ngày tạo: {{ $event->created_at?->format('d/m/Y H:i') }}</small>
                    </div>
                </div>

                {{-- Người duyệt --}}
                @if($event->approval_by)
                <div class="card border-success mb-3">
                    <div class="card-body text-success">
                        <h6 class="fw-bold mb-1">Đã duyệt bởi</h6>
                        <p class="mb-1">{{ $event->approvalBy?->name }}</p>
                        <small>{{ $event->approvalBy?->email }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Nút hành động --}}
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary ms-2">Hủy</a>
        </div>
    </form>
</div>
@endsection
