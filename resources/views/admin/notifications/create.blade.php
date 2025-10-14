@extends('admin.layouts.app')

     @section('content')
         <div class="container-fluid">
             <div class="d-sm-flex align-items-center justify-content-between mb-4">
                 <h1 class="h3 mb-0 text-gray-800">Gửi Thông báo Chung</h1>
             </div>

             @if (session('success'))
                 <div class="alert alert-success">
                     {{ session('success') }}
                 </div>
             @endif

             <div class="card shadow mb-4">
                 <div class="card-header py-3">
                     <h6 class="m-0 font-weight-bold text-primary">Nhập thông tin thông báo</h6>
                 </div>
                 <div class="card-body">
                     <form action="{{ route('admin.notifications.store') }}" method="POST">
                         @csrf
                         <div class="form-group">
                             <label for="title">Tiêu đề</label>
                             <input type="text" name="title" id="title" class="form-control" required>
                         </div>
                         <div class="form-group">
                             <label for="message">Nội dung</label>
                             <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                         </div>
                         <button type="submit" class="btn btn-primary">Gửi Thông báo</button>
                         <a href="{{ route('dashboard') }}" class="btn btn-secondary">Quay lại</a>
                     </form>
                 </div>
             </div>
         </div>
     @endsection