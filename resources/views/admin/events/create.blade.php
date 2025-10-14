@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Thêm Sự kiện</h1>
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
                <form action="{{ route('admin.events.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Tên sự kiện</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Ngày sự kiện</label>
                        <input type="datetime-local" name="event_date" class="form-control" id="event_date" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Địa điểm</label>
                        <input type="text" name="location" class="form-control" id="location" required>
                    </div>
                    <div class="form-group">
                        <label for="club_id">CLB</label>
                        <select name="club_id" class="form-control" id="club_id">
                            <option value="">Sự kiện chung</option>
                            @foreach ($clubs as $club)
                                <option value="{{ $club->id }}">{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" class="form-control" id="status" required>
                            <option value="pending">Chờ duyệt</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="rejected">Đã từ chối</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm sự kiện</button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection