@extends('admin.layouts.app')

@section('title', 'Chi tiết Câu lạc bộ')

@section('card-body')
<div class="container py-4">
    <h1 class="h4 mb-4 text-gray-800">Chi tiết CLB: {{ $club->name }}</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Chủ nhiệm:</strong> {{ $club->leader->name ?? 'Không rõ' }}</p>
            <p><strong>Lĩnh vực:</strong> {{ $club->field ?? '—' }}</p>
            <p><strong>Mô tả:</strong></p>
            <div class="border p-3 bg-light rounded">
                {{ $club->description ?? 'Không có mô tả' }}
            </div>
            <p class="mt-3"><strong>Trạng thái:</strong> {{ ucfirst($club->status) }}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thành viên CLB</h5>
        </div>
        <div class="card-body">
            @if($club->members->isEmpty())
                <p class="text-muted">Chưa có thành viên nào.</p>
            @else
                <ul class="list-group">
                    @foreach($club->members as $member)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $member->user->name }}
                            <small class="text-muted">{{ $member->created_at->format('d/m/Y') }}</small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>
</div>
@endsection
    