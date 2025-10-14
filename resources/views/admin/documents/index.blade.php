@extends('admin.layouts.app')

     @section('content')
         <div class="card shadow mb-4">
             <div class="card-header py-3">
                 <h6 class="m-0 font-weight-bold text-primary">Tài liệu</h6>
                 <a href="#" class="btn btn-primary btn-sm">Thêm mới</a>
             </div>
             <div class="card-body">
                 <div class="table-responsive">
                     <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                         <thead>
                             <tr>
                                 <th>ID</th>
                                 <th>Tên tài liệu</th>
                                 <th>Ngày tải lên</th>
                                 <th>Hành động</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>1</td>
                                 <td>Tài liệu hướng dẫn</td>
                                 <td>10/10/2025</td>
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