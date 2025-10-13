@extends('admin.layouts.app')

   @section('content')
       <div class="container-fluid">
           <h1 class="h3 mb-4 text-gray-800">Chi tiết Sự kiện</h1>
           <div class="card shadow mb-4">
               <div class="card-body">
                   <p><strong>ID:</strong> {{ $event->id }}</p>
                   <p><strong>Tên sự kiện:</strong> {{ $event->name }}</p>
                   <p><strong>Mô tả:</strong> {{ $event->description ?? 'N/A' }}</p>
                   <p><strong>Ngày diễn ra:</strong> {{ $event->event_date }}</p>
                   <p><strong>Địa điểm:</strong> {{ $event->location }}</p>
                   <p><strong>Trạng thái:</strong> {{ ucfirst($event->status) }}</p>
                   <p><strong>CLB:</strong> {{ $event->club->name ?? 'N/A' }}</p>
                   <p><strong>Người tạo:</strong> {{ $event->createdBy->name ?? 'N/A' }} ({{ $event->createdBy->email ?? 'N/A' }})</p>
                   <p><strong>Ngày tạo:</strong> {{ $event->created_at }}</p>
                   <p><strong>Ngày cập nhật:</strong> {{ $event->updated_at }}</p>
                   <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Quay lại</a>
               </div>
           </div>
       </div>
   @endsection