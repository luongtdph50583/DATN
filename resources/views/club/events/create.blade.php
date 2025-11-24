@extends('client.layouts.app')
@section('title', 'Tạo sự kiện mới')

@section('content')
<div class="container py-5">
    <h2>Tạo sự kiện mới</h2>
    <form action="{{ route('club.events.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Tên sự kiện</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Loại</label>
                <select name="type" class="form-select" required>
                    <option value="offline">Offline</option>
                    <option value="lien_hoan">Liên hoan</option>
                    <option value="hop">Họp</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Bắt đầu</label>
                <input type="datetime-local" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kết thúc</label>
                <input type="datetime-local" name="end_time" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Địa điểm</label>
                <input type="text" name="location" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Số người tối đa</label>
                <input type="number" name="max_participants" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Tạo sự kiện</button>
                <a href="{{ route('club.events.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </div>
    </form>
</div>
@endsection