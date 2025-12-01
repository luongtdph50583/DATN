@extends('client.layouts.app')
@section('title', 'Quản lý form tuyển thành viên - ' . $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="mb-0">Quản lý form tuyển thành viên - {{ $club->name }}</h2>
            <div class="btn-group">
                <a href="{{ route('club_manager.recruit_forms.create', ['club_id' => $club->id]) }}"
                    class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tạo form mới
                </a>
                <a href="{{ route('club_manager.posts.index', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Danh sách form</h5>
            </div>
            <div class="card-body">
                @if($forms->isEmpty())
                    <div class="text-center py-5">
                        <p class="text-muted mb-4">Chưa có form tuyển thành viên nào.</p>
                        <a href="{{ route('club_manager.recruit_forms.create', ['club_id' => $club->id]) }}"
                            class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tạo form đầu tiên
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên form</th>
                                    <th>Mô tả</th>
                                    <th>Số câu hỏi</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forms as $index => $form)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $form->name }}</strong>
                                            @if($form->is_default)
                                                <span class="badge bg-success ms-2">Mặc định</span>
                                            @endif
                                        </td>
                                        <td>{{ $form->description ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $form->questions_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if($form->is_active)
                                                <span class="badge bg-primary">Đang hoạt động</span>
                                            @else
                                                <span class="badge bg-secondary">Đã tắt</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('club_manager.recruit_form.create', ['club_id' => $club->id, 'form_id' => $form->id]) }}"
                                                    class="btn btn-outline-primary" title="Quản lý câu hỏi">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!$form->is_default)
                                                    <form
                                                        action="{{ route('club_manager.recruit_forms.set_default', ['club_id' => $club->id, 'form_id' => $form->id]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success"
                                                            title="Đặt làm mặc định">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    </form>
                                                    <form
                                                        action="{{ route('club_manager.recruit_forms.destroy', ['club_id' => $club->id, 'form_id' => $form->id]) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa form này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger"
                                                            title="Xóa form">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection




