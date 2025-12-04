@extends('client.layouts.app')

@section('title', 'Chi tiết sự kiện - ' . $event->name)

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">{{ $event->name }}</h3>
            <p class="text-muted mb-0">CLB: <strong>{{ $club->name }}</strong></p>
        </div>

        <a href="{{ route('club_manager.events.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- TRẠNG THÁI THEO THỜI GIAN --}}
    @php
        $now = now();
        if ($event->start_time > $now) {
            $timeStatus = "Sự kiện chưa diễn ra";
            $badge = "info";
        } elseif ($event->end_time < $now) {
            $timeStatus = "Sự kiện đã kết thúc";
            $badge = "secondary";
        } else {
            $timeStatus = "Sự kiện đang diễn ra";
            $badge = "success";
        }
    @endphp

    <div class="mb-3">
        <span class="badge bg-{{ $badge }} px-3 py-2">{{ $timeStatus }}</span>

        @if($event->status === 'pending')
            <span class="badge bg-warning text-dark px-3 py-2">Chờ duyệt</span>
        @elseif($event->status === 'approved')
            <span class="badge bg-success px-3 py-2">Đã duyệt</span>
        @elseif($event->status === 'rejected')
            <span class="badge bg-danger px-3 py-2">Bị từ chối</span>
        @endif
    </div>

    {{-- THÔNG TIN CƠ BẢN --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">Thông tin sự kiện</h5>
        </div>
        <div class="card-body">
            <p><strong>Thời gian:</strong><br>
                {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }} - 
                {{ \Carbon\Carbon::parse($event->end_time)->format('d/m/Y H:i') }}
            </p>

            <p><strong>Địa điểm:</strong> {{ $event->location }}</p>

            <p><strong>Giới hạn người tham gia:</strong>
                {{ $event->max_participants ?? 'Không giới hạn' }}
            </p>

            <p><strong>Hiển thị:</strong>
                {{ $event->is_public ? 'Công khai toàn trường' : 'Chỉ hiển thị cho CLB' }}
            </p>

            <p><strong>Mô tả:</strong><br>
                {{ $event->description ?: 'Không có mô tả.' }}
            </p>
        </div>
    </div>

    {{-- NẾU BỊ TỪ CHỐI – HIỂN THỊ LÝ DO --}}
    @if($event->status === 'rejected')
        <div class="card shadow-sm mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="fw-bold mb-0">Lý do từ chối</h5>
            </div>
            <div class="card-body">
                <p><strong>Thời gian:</strong> {{ $event->deleted_at }}</p>
                <p class="text-danger"><strong>Nội dung:</strong><br>{{ $event->delete_reason }}</p>
            </div>
        </div>
    @endif

    {{-- BẢNG NGÂN SÁCH --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">Ngân sách chi tiết</h5>
        </div>

        <div class="card-body">
            @if($event->budgetItems->isEmpty())
                <p class="text-muted">Không có mục chi phí nào.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-light">
                            <th>Tên mục</th>
                            <th>Chi phí dự kiến</th>
                            <th>Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->budgetItems as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ number_format($item->estimated_cost, 0, ',', '.') }}đ</td>
                            <td>
                                @if($item->type === 'school_fund') Xin cấp từ trường @endif
                                @if($item->type === 'club_fund') CLB tự chi @endif
                                @if($item->type === 'other') Khác @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
