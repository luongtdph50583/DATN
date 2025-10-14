@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Chi tiết Sự kiện</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ $event->name }}</h6>
            </div>
            <div class="card-body">
                <p><strong>Mô tả:</strong> {{ $event->description ?? 'Chưa có mô tả' }}</p>
                <p><strong>Ngày:</strong> {{ $event->event_date ? $event->event_date->format('d/m/Y H:i') : 'Chưa xác định' }}</p>
                <p><strong>Địa điểm:</strong> {{ $event->location }}</p>
                <p><strong>CLB:</strong> {{ $event->club ? $event->club->name : 'Sự kiện chung' }}</p>
                <p><strong>Người tạo:</strong> {{ $event->createdBy->name ?? 'N/A' }}</p>
                <p><strong>Trạng thái:</strong> {{ ucfirst($event->status) }}</p>
                <p><strong>Số lượng đăng ký:</strong> {{ $totalRegistrations }}</p>

                <h5 class="mt-4">Danh sách đăng ký</h5>
                @if ($registrations->isEmpty())
                    <p>Chưa có ai đăng ký cho sự kiện này.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered" id="registrationTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên người dùng</th>
                                    <th>Thời gian đăng ký</th>
                                    <th>Trạng thái</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registrations as $registration)
                                    <tr>
                                        <td>{{ $registration->id }}</td>
                                        <td>{{ $registration->user->name ?? 'N/A' }}</td>
                                        <td>{{ $registration->registered_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ ucfirst($registration->status) }}</td>
                                        <td>{{ $registration->notes ?? 'Không có ghi chú' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#registrationTable').DataTable();
        });
    </script>
@endpush