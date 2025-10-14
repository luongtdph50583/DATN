@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Sửa Sự kiện</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin sự kiện</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.events.update', $event->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Tên sự kiện</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $event->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $event->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Ngày sự kiện</label>
                        <input type="datetime-local" name="event_date" class="form-control" id="event_date" value="{{ old('event_date', $event->event_date ? $event->event_date->format('Y-m-d\TH:i') : '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Địa điểm</label>
                        <input type="text" name="location" class="form-control" id="location" value="{{ old('location', $event->location) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="club_id">CLB</label>
                        <select name="club_id" class="form-control" id="club_id">
                            <option value="">Sự kiện chung</option>
                            @foreach ($clubs as $club)
                                <option value="{{ $club->id }}" {{ old('club_id', $event->club_id) == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" class="form-control" id="status" required>
                            <option value="pending" {{ old('status', $event->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="approved" {{ old('status', $event->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="rejected" {{ old('status', $event->status) == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật sự kiện</button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection