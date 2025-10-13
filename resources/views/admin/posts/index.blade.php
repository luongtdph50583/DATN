@extends('admin.layouts.app')

     @section('content')
         <div class="card shadow mb-4">
             <div class="card-header py-3">
                 <h6 class="m-0 font-weight-bold text-primary">Tin tức & Bài viết</h6>
                 <a href="#" class="btn btn-primary btn-sm">Thêm mới</a>
             </div>
             <div class="card-body">
                 <div class="table-responsive">
                     <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                         <thead>
                             <tr>
                                 <th>ID</th>
                                 <th>Tiêu đề</th>
                                 <th>Ngày đăng</th>
                                 <th>Hành động</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>1</td>
                                 <td>Tin tức tháng 10</td>
                                 <td>12/10/2025</td>
                                 <td>
                                     <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                     <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                 </td>
                             </tr>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     @endsection