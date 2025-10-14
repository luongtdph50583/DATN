@extends('admin.layouts.app')

   @section('content')
       <div class="container-fluid">
           <h1 class="h3 mb-4 text-gray-800">Thêm mới Thành viên</h1>
           @if ($errors->any())
               <div class="alert alert-danger">
                   <ul>
                       @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                       @endforeach
                   </ul>
               </div>
           @endif
           <form action="{{ route('admin.members.store') }}" method="POST">
               @csrf
               <div class="card shadow mb-4">
                   <div class="card-body">
                       <div class="form-group">
                           <label for="name">Tên</label>
                           <input type="text" name="name" class="form-control" required>
                       </div>
                       <div class="form-group">
                           <label for="email">Email</label>
                           <input type="email" name="email" class="form-control" required>
                       </div>
                       <div class="form-group">
                           <label for="phone">Số điện thoại</label>
                           <input type="text" name="phone" class="form-control">
                       </div>
                       <div class="form-group">
                           <label for="address">Địa chỉ</label>
                           <textarea name="address" class="form-control"></textarea>
                       </div>
                       <div class="form-group">
                           <label for="status">Trạng thái</label>
                           <select name="status" class="form-control" required>
                               <option value="active">Active</option>
                               <option value="inactive">Inactive</option>
                           </select>
                       </div>
                       <button type="submit" class="btn btn-primary">Lưu</button>
                       <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Hủy</a>
                   </div>
               </div>
           </form>
       </div>
   @endsection