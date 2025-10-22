@extends('admin.layouts.app')

@section('title', 'Chi tiết Câu lạc bộ')

@section('card-body')
<div class="container py-4">

    {{-- Tiêu đề --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-gray-800 mb-0">📘 Chi tiết Câu lạc bộ: <strong>{{ $club->name }}</strong></h1>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
    </div>

    {{-- Thông tin CLB --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">🏛️ Thông tin Câu lạc bộ</div>
        <div class="card-body">
            <p><strong>Chủ nhiệm:</strong>
                {{ $club->leader->name ?? '— Chưa gán —' }}
            </p>

            <p><strong>Lĩnh vực:</strong> {{ $club->field ?? '—' }}</p>

            <p><strong>Trạng thái:</strong>
                @switch($club->status)
                    @case('active')
                        <span class="badge bg-success">Đang hoạt động</span>
                        @break
                    @case('pending')
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @break
                    @case('inactive')
                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                        @break
                    @default
                        <span class="badge bg-light text-dark">Không xác định</span>
                @endswitch
            </p>

            <p><strong>Mô tả:</strong></p>
            <div class="border p-3 bg-light rounded">
                {{ $club->description ?? 'Không có mô tả.' }}
            </div>

            <p class="mt-3 text-muted"><strong>Ngày tạo:</strong> {{ $club->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Danh sách thành viên --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
            👥 Danh sách thành viên CLB
        </div>
        <div class="card-body">
            @if($club->members->isEmpty())
                <p class="text-muted fst-italic">Chưa có thành viên nào được duyệt tham gia.</p>
            @else
                <table class="table table-striped align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tên thành viên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày tham gia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($club->members as $index => $member)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $member->user->name ?? 'Không rõ' }}</td>
                                <td>{{ $member->user->email ?? '—' }}</td>
                                <td>{{ ucfirst($member->role ?? 'member') }}</td>
                                <td>{{ $member->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
