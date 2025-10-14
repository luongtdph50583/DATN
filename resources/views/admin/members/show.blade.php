@extends('admin.layouts.app')

   @section('content')
       <div class="container-fluid">
           <h1 class="h3 mb-4 text-gray-800">Chi tiết Thành viên</h1>
           <div class="card shadow mb-4">
               <div class="card-body">
                   <p><strong>ID:</strong> {{ $member->id }}</p>
                   <p><strong>Tên:</strong> {{ $member->name }}</p>
                   <p><strong>Email:</strong> {{ $member->email }}</p>
                   <p><strong>Số điện thoại:</strong> {{ $member->phone ?? 'N/A' }}</p>
                   <p><strong>Địa chỉ:</strong> {{ $member->address ?? 'N/A' }}</p>
                   <p><strong>Trạng thái:</strong> {{ ucfirst($member->status) }}</p>
                   <p><strong>Ngày tạo:</strong> {{ $member->created_at }}</p>
                   <p><strong>Ngày cập nhật:</strong> {{ $member->updated_at }}</p>
                   <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Quay lại</a>
               </div>
           </div>
       </div>
   @endsection