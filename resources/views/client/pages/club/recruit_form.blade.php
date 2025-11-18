@extends('client.layouts.app')
@section('title', 'Form tuyển thành viên - ' . $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="mb-0">Form tuyển thành viên - {{ $club->name }}</h2>
            <div class="btn-group">
                <a href="{{ route('club_manager.recruit.index', ['club_id' => $club->id]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-users me-1"></i> Danh sách yêu cầu
                </a>
                <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Các câu hỏi hiện tại</h5>
                <span class="text-muted small">Hiển thị tối đa {{ $club->joinFormQuestions->count() }} câu hỏi.</span>
            </div>
            <div class="card-body">
                @if($club->joinFormQuestions->isEmpty())
                    <p class="text-muted mb-0">Chưa có câu hỏi nào trong form tuyển thành viên.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Câu hỏi</th>
                                    <th>Loại</th>
                                    <th>Bắt buộc</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($club->joinFormQuestions as $index => $question)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $question->question }}</strong>
                                            @if($question->description)
                                                <p class="mb-0 text-muted small">{{ $question->description }}</p>
                                            @endif
                                        </td>
                                        <td>{{ $questionTypes[$question->type] ?? $question->type }}</td>
                                        <td>
                                            @if($question->is_required)
                                                <span class="badge bg-success">Có</span>
                                            @else
                                                <span class="badge bg-secondary">Không</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($question->is_active)
                                                <span class="badge bg-primary">Đang hiển thị</span>
                                            @else
                                                <span class="badge bg-dark">Đã ẩn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('club_manager.recruit_form.toggle', ['club_id' => $club->id, 'question' => $question->id]) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm {{ $question->is_active ? 'btn-outline-dark' : 'btn-outline-success' }}">
                                                    {{ $question->is_active ? 'Ẩn câu hỏi' : 'Hiển thị' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thêm câu hỏi mới</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('club_manager.recruit_form.store', ['club_id' => $club->id]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                        <input type="text" name="question" class="form-control" value="{{ old('question') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Mô tả chi tiết cho câu hỏi (nếu có)">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Loại câu hỏi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                @foreach($questionTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="order" class="form-control" min="1" value="{{ old('order') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" value="1" id="is_required" name="is_required" {{ old('is_required') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">
                                    Bắt buộc trả lời
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Lựa chọn (mỗi dòng một lựa chọn)</label>
                        <textarea name="options" rows="3" class="form-control" placeholder="Chỉ cần nhập khi chọn kiểu câu hỏi có nhiều lựa chọn">{{ old('options') }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Thêm câu hỏi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

