@extends('admin.layouts.app')

   @section('content')
       <div class="container-fluid">
           <h1 class="h3 mb-4 text-gray-800">Chỉnh sửa Sự kiện</h1>
           @if ($errors->any())
               <div class="alert alert-danger">
                   <ul>
                       @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                       @endforeach
                   </ul>
               </div>
           @endif
           <form action="{{ route('admin.events.update', $event->id) }}" method="POST">
               @csrf
               @method('PUT')
               <div class="card shadow mb-4">
                   <div class="card-body">
                       <div class="form-group">
                           <label for="club_id">CLB</label>
                           <select name="club_id" class="form-control" required>
                               @foreach ($clubs as $club)
                                   <option value="{{ $club->id }}" {{ old('club_id', $event->club_id) == $club->id ? 'selected' : '' }}>
                                       {{ $club->name }}
                                   </option>
                               @endforeach
                           </select>
                       </div>
                       <div class="form-group">
                           <label for="name">Tên sự kiện</label>
                           <input type="text" name="name" class="form-control" value="{{ old('name', $event->name) }}" required>
                       </div>
                       <div class="form-group">
                           <label for="description">Mô tả</label>
                           <textarea name="description" class="form-control" rows="3">{{ old('description', $event->description) }}</textarea>
                       </div>
                       <div class="form-group">
                           <label for="event_date">Ngày diễn ra</label>
                           <input type="datetime-local" name="event_date" class="form-control" value="{{ old('event_date', $event->event_date) }}" required>
                       </div>
                       <div class="form-group">
                           <label for="location">Địa điểm</label>
                           <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}" required>
                       </div>
                       <div class="form-group">
                           <label for="status">Trạng thái</label>
                           <select name="status" class="form-control" required>
                               <option value="pending" {{ old('status', $event->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                               <option value="approved" {{ old('status', $event->status) == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                               <option value="rejected" {{ old('status', $event->status) == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                           </select>
                       </div>
                       <div class="form-group">
                           <label for="created_by">Người tạo</label>
                           <select name="created_by" class="form-control" required>
                               @foreach ($users as $user)
                                   <option value="{{ $user->id }}" {{ old('created_by', $event->created_by) == $user->id ? 'selected' : '' }}>
                                       {{ $user->name }} ({{ $user->email }})
                                   </option>
                               @endforeach
                           </select>
                       </div>
                       <button type="submit" class="btn btn-primary">Cập nhật</button>
                       <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Hủy</a>
                   </div>
               </div>
           </form>
       </div>
   @endsection