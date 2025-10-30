@extends('admin.layouts.app')

@section('title', 'Chi tiết CLB')

@section('card-body')
    <h1 class="mb-4">Chi tiết Câu lạc bộ</h1>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Thông tin cơ bản --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Thông tin CLB</div>
        <div class="card-body">
            <div class="row">
                {{-- Logo --}}
                <div class="col-md-3 text-center">
                    @if($club->logo)
                        <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo CLB" class="img-fluid rounded mb-2" style="max-height: 150px;">
                    @else
                        <img src="https://via.placeholder.com/150?text=No+Logo" alt="No logo" class="img-fluid rounded mb-2">
                    @endif
                </div>

                {{-- Thông tin --}}
                <div class="col-md-9">
                    <p><strong>Tên CLB:</strong> {{ $club->name }}</p>
                    <p><strong>Lĩnh vực:</strong> {{ $club->field }}</p>
                    <p><strong>Email CLB:</strong> {{ $club->email ?? '—' }}</p>
                    <p><strong>Điện thoại CLB:</strong> {{ $club->phone ?? '—' }}</p>
                    <p><strong>Giới hạn thành viên:</strong> {{ $club->member_limit ?? 'Không giới hạn' }}</p>
                    <p><strong>Trạng thái:</strong>
                        @if($club->status === 'active')
                            <span class="badge bg-success">Đang hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Ngừng hoạt động</span>
                        @endif
                    </p>
                    <p><strong>Mô tả:</strong><br> {!! nl2br(e($club->description)) !!}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chủ nhiệm --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Chủ nhiệm CLB</div>
        <div class="card-body">
            @if($manager)
                <p><strong>Họ tên:</strong> {{ $manager->name }}</p>
                <p><strong>Email:</strong> {{ $manager->email }}</p>
            @else
                <p class="text-muted">Không tìm thấy thông tin chủ nhiệm.</p>
            @endif
        </div>
    </div>

    {{-- Danh sách thành viên --}}
    <div class="card">
        <div class="card-header fw-bold">Danh sách thành viên ({{ $members->count() }})</div>
        <div class="card-body">
            @if($members->isEmpty())
                <p class="text-muted">Chưa có thành viên nào trong CLB này.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Ngày tham gia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $index => $member)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $member->user->name ?? 'Không xác định' }}</td>
                                    <td>{{ $member->user->email ?? '—' }}</td>
                                    <td>
                                        @if($member->role === 'leader')
                                            <span class="badge bg-primary">Chủ nhiệm</span>
                                        @else
                                            <span class="badge bg-secondary">Thành viên</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($member->joined_at)->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
       
    </div>
@endsection
