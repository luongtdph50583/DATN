@extends('admin.layouts.app')

@section('title', 'Lịch sử thay đổi CLB')
@section('card-title', 'Lịch sử thay đổi CLB')

@section('card-header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <span class="fw-bold">Lịch sử các thay đổi thông tin câu lạc bộ</span>
        </div>
    </div>
@endsection

@section('card-body')
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

    @if($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tên CLB</th>
                        <th>Người thực hiện</th>
                        <th>Người đề xuất</th>
                        <th>Loại</th>
                        <th>Ngày thay đổi</th>

                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>

                            {{-- CLB --}}
                            <td>
                                <a href="{{ route('admin.clubs.show', $log->club_id) }}" class="text-decoration-none">
                                    {{ $log->club->name ?? '—' }}
                                </a>
                            </td>

                            {{-- Người thực hiện --}}
                            <td>
                                @if($log->admin)
                                    <span class="badge bg-primary">{{ $log->admin->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Người đề xuất --}}
                            <td>
                                @if($log->type === 'proposer' && $log->proposer)
                                    <span class="badge bg-info text-dark">{{ $log->proposer->name }}</span>
                                @else
                                    <span class="text-muted">không có</span>
                                @endif
                            </td>

                            {{-- Loại thay đổi --}}
                            <td>
                                @if($log->type === 'admin')
                                    <span class="badge bg-success">Admin thực hiện</span>
                                @elseif($log->type === 'proposer')
                                    <span class="badge bg-warning text-dark">Đề xuất từ CLB</span>
                                @else
                                    <span class="badge bg-secondary">Không xác định</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}


                            {{-- Ngày --}}
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>

                            {{-- Số trường thay đổi --}}


                            {{-- Xem chi tiết --}}
                            {{-- Xem chi tiết --}}
                            <td>
                                <a href="{{ route('admin.club_update_logs.show', $log->id) }}" class="btn btn-warning btn-sm me-1"
                                    title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center text-muted py-4">
            Không có bản ghi nào.
        </div>
    @endif



@endsection


