@extends('admin.layouts.app')

   @section('content')
       <div class="container-fluid">
           <h1 class="h3 mb-4 text-gray-800">Thêm mới Sự kiện</h1>
           @if ($errors->any())
               <div class="alert alert-danger">
                   <ul>
                       @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                       @endforeach
                   </ul>
               </div>
           @endif
           <form action="{{ route('admin.events.store') }}" method="POST">
               @csrf
               <div class="card shadow mb-4">
                   <div class="card-body">
                       <div class="form-group">
                           <label for="club_id">CLB</label>
                           <select name="club_id" class="form-control" required>
                               @foreach ($clubs as $club)
                                   <option value="{{ $club->id }}">{{ $club->name }}</option>
                               @endforeach
                           </select>
                       </div>
                       <div class="form-group">
                           <label for="name">Tên sự kiện</label>
                           <input type="text" name="name" class="form-control" required>
                       </div>
                       <div class="form-group">
                           <label for="description">Mô tả</label>
                           <textarea name="description" class="form-control" rows="3"></textarea>
                       </div>
                       <div class="form-group">
                           <label for="event_date">Ngày diễn ra</label>
                           <input type="datetime-local" name="event_date" class="form-control" required>
                       </div>
                       <div class="form-group">
                           <label for="location">Địa điểm</label>
                           <input type="text" name="location" class="form-control" required>
                       </div>
                       <div class="form-group">
                           <label for="status">Trạng thái</label>
                           <select name="status" class="form-control" required>
                               <option value="pending">Chờ duyệt</option>
                               <option value="approved">Đã duyệt</option>
                               <option value="rejected">Bị từ chối</option>
                           </select>
                       </div>
                       <div class="form-group">
                           <label for="created_by">Người tạo</label>
                           <select name="created_by" class="form-control" required>
                               @foreach ($users as $user)
                                   <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                               @endforeach
                           </select>
                       </div>
                       <button type="submit" class="btn btn-primary">Lưu</button>
                       <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Hủy</a>
                   </div>
               </div>
           </form>
       </div>
   @endsection