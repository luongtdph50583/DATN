@extends('admin.layouts.app')

@section('title', 'Chi tiết CLB')

@section('card-body')
<div class="container-fluid">
    <h1 class="mb-4">Chi tiết CLB</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Card thông tin CLB --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if($club->logo)
                        <img src="{{ asset('storage/' . $club->logo) }}" class="img-fluid border p-2" style="max-height:200px;">
                    @else
                        <div class="border p-4 text-muted">Chưa có logo</div>
                    @endif
                </div>
                <div class="col-md-8">
                    <p><strong>ID:</strong> {{ $club->id }}</p>
                    <p><strong>Tên:</strong> {{ $club->name }}</p>
                    <p><strong>Lĩnh vực:</strong> {{ $club->field ?? 'Chưa cập nhật' }}</p>
                    <p><strong>Trạng thái:</strong> {{ ucfirst($club->status ?? 'Chưa cập nhật') }}</p>
                    <p><strong>Chủ nhiệm:</strong> 
                        {{ $club->manager->name ?? 'Chưa có' }}
                        @if(!$club->manager)
                            <a href="{{ route('admin.clubs.assign', $club->id) }}" class="btn btn-success btn-sm">Gán</a>
                        @endif
                    </p>
                    <p><strong>Số lượng thành viên:</strong> {{ $club->members->count() }}</p>
                    <p><strong>Ngày tạo:</strong> {{ optional($club->created_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Ngày cập nhật:</strong> {{ optional($club->updated_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-3 border p-3 bg-light">
                <strong>Mô tả:</strong>
                <p>{{ $club->description ?? 'Chưa có mô tả' }}</p>
            </div>
        </div>
    </div>

    {{-- Danh sách thành viên --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Thành viên CLB ({{ $club->members->count() }})</h5>
            @if($club->members->isEmpty())
                <p class="text-muted">Chưa có thành viên nào</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($club->members as $member)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $member->name }}
                            <span class="badge bg-primary rounded-pill">{{ $member->email }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
@endsection
