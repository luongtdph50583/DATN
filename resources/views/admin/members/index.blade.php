@extends('dashboard')

     @section('content')
         <div class="card shadow mb-4">
             <div class="card-header py-3">
                 <h6 class="m-0 font-weight-bold text-primary">Quản lý Thành viên</h6>
                 <div class="d-flex justify-content-end">
                     <form action="{{ route('admin.members.export.excel') }}" method="GET" class="mr-2">
                         @csrf
                         <input type="hidden" name="search_name" value="{{ isset($searchName) ? $searchName : '' }}">
                         <input type="hidden" name="club_id" value="{{ isset($clubId) ? $clubId : '' }}">
                         <input type="hidden" name="role" value="{{ isset($role) ? $role : '' }}">
                         <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Xuất Excel</button>
                     </form>
                     <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm thành viên</a>
                 </div>
             </div>
             <div class="card-body">
                 <div class="mb-4">
                     <form action="{{ route('admin.members.index') }}" method="GET" class="form-row">
                         <input type="hidden" name="activeTab" value="members">
                         <div class="col-md-3 mb-2">
                             <input type="text" name="search_name" value="{{ isset($searchName) ? $searchName : '' }}" class="form-control" placeholder="Tên thành viên...">
                         </div>
                         <div class="col-md-3 mb-2">
                             <select name="club_id" class="form-control">
                                 <option value="">Tất cả CLB</option>
                                 @foreach (\App\Models\Club::all() as $club)
                                     <option value="{{ $club->id }}" {{ (isset($clubId) && $clubId == $club->id) ? 'selected' : '' }}>
                                         {{ $club->name }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                         <div class="col-md-2 mb-2">
                             <select name="role" class="form-control">
                                 <option value="">Tất cả</option>
                                 <option value="admin" {{ (isset($role) && $role == 'admin') ? 'selected' : '' }}>Admin</option>
                                 <option value="member" {{ (isset($role) && $role == 'member') ? 'selected' : '' }}>Member</option>
                             </select>
                         </div>
                         <div class="col-md-2 mb-2">
                             <button type="submit" class="btn btn-primary btn-block">Tìm kiếm</button>
                         </div>
                     </form>
                 </div>
                 <div class="table-responsive">
                     <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                         <thead>
                             <tr>
                                 <th>ID</th>
                                 <th>Tên</th>
                                 <th>Email</th>
                                 <th>Vai trò</th>
                                 <th>CLB</th>
                                 <th>Ngày tham gia</th>
                                 <th>Hành động</th>
                             </tr>
                         </thead>
                         <tbody>
                             @php
                                 $clubs = isset($clubs) ? $clubs : collect();
                                 $hasClubs = $clubs->isNotEmpty();
                             @endphp
                             @if ($hasClubs)
                                 @foreach ($clubs as $club)
                                     @php
                                         $members = isset($club->members) ? $club->members : collect();
                                         \Log::info('Club: ' . $club->name . ', Members: ', $members->toArray());
                                     @endphp
                                     @forelse ($members as $member)
                                         <tr>
                                             <td>{{ $member->user->id }}</td>
                                             <td>{{ $member->user->name }}</td>
                                             <td>{{ $member->user->email }}</td>
                                             <td>
                                                 <span class="badge {{ $member->role == 'admin' ? 'badge-primary' : 'badge-success' }}">
                                                     {{ ucfirst($member->role) }}
                                                 </span>
                                             </td>
                                             <td>{{ $club->name }}</td>
                                             <td>
                                                 @if (isset($member->joined_at) && $member->joined_at instanceof \Carbon\Carbon)
                                                     {{ $member->joined_at->format('d/m/Y') }}
                                                 @else
                                                     {{ $member->joined_at ?: 'Chưa xác định' }}
                                                 @endif
                                             </td>
                                             <td>
                                                 <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                 <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                             </td>
                                         </tr>
                                     @empty
                                         <tr>
                                             <td colspan="7" class="text-center text-muted">Không có thành viên trong CLB {{ $club->name ?? 'này' }}.</td>
                                         </tr>
                                     @endforelse
                                 @endforeach
                             @else
                                 <tr>
                                     <td colspan="7" class="text-center text-muted">Không có câu lạc bộ nào.</td>
                                 </tr>
                             @endif
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     @endsection