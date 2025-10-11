<!DOCTYPE html>
<html>
<head>
    <title>Sửa Sự kiện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Sửa Sự kiện</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.events.update', $event->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $event->title) }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" id="description">{{ old('description', $event->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="event_date" class="form-label">Ngày diễn ra</label>
            <input type="date" name="event_date" class="form-control" id="event_date" value="{{ old('event_date', $event->event_date) }}" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Địa điểm</label>
            <input type="text" name="location" class="form-control" id="location" value="{{ old('location', $event->location) }}">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>