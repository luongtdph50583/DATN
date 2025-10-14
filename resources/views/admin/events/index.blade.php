@extends('admin.layouts.app')

   @section('content')
       <div class="container-fluid">
           <h1 class="h3 mb-4 text-gray-800">Quản lý Sự kiện</h1>
           @if (session('success'))
               <div class="alert alert-success">{{ session('success') }}</div>
           @endif
           <a href="{{ route('admin.events.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
           <div class="card shadow mb-4">
               <div class="card-body">
                   <div class="table-responsive">
                       <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                           <thead>
                               <tr>
                                   <th>ID</th>
                                   <th>Tên sự kiện</th>
                                   <th>Mô tả</th>
                                   <th>Ngày diễn ra</th>
                                   <th>Địa điểm</th>
                                   <th>Trạng thái</th>
                                   <th>Người tạo</th>
                                   <th>CLB</th>
                                   <th>Hành động</th>
                               </tr>
                           </thead>
                           <tbody>
                               @foreach ($events as $event)
                                   <tr>
                                       <td>{{ $event->id }}</td>
                                       <td>{{ $event->name }}</td>
                                       <td>{{ Str::limit($event->description, 50) }}</td>
                                       <td>{{ $event->event_date }}</td>
                                       <td>{{ $event->location }}</td>
                                       <td>{{ ucfirst($event->status) }}</td>
                                       <td>{{ $event->createdBy->name ?? 'N/A' }}</td>
                                       <td>{{ $event->club->name ?? 'N/A' }}</td>
                                       <td>
                                           <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-info btn-sm">
                                               <i class="fas fa-eye"></i> Xem
                                           </a>
                                           <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning btn-sm">
                                               <i class="fas fa-edit"></i> Sửa
                                           </a>
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
                               @endforeach
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>
       </div>
   @endsection