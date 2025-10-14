@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Quản lý Sự kiện</h1>
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
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary mb-3">Thêm mới sự kiện</a>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách sự kiện</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sự kiện</th>
                                <th>Mô tả</th>
                                <th>Ngày</th>
                                <th>Địa điểm</th>
                                <th>CLB</th>
                                <th>Người tạo</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $event)
                                <tr>
                                    <td>{{ $event->id }}</td>
                                    <td>{{ $event->name }}</td>
                                    <td>{{ Str::limit($event->description, 50) }}</td>
                                    <td>{{ $event->event_date ? $event->event_date->format('d/m/Y H:i') : 'Chưa xác định' }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>{{ $event->club ? $event->club->name : 'Sự kiện chung' }}</td>
                                    <td>{{ $event->createdBy->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($event->status) }}</td>
                                    <td>
                                        <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        @if ($event->status === 'pending')
                                            <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn duyệt sự kiện?')">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> Duyệt
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.events.reject', $event->id) }}" method="POST" style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn từ chối sự kiện?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i> Từ chối
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display:inline-block;"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Không có sự kiện nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
@endpush