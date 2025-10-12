@extends('dashboard')

     @section('content')
         <div class="card shadow mb-4">
             <div class="card-header py-3">
                 <h6 class="m-0 font-weight-bold text-primary">Bình luận</h6>
             </div>
             <div class="card-body">
                 <div class="table-responsive">
                     <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                         <thead>
                             <tr>
                                 <th>ID</th>
                                 <th>Nội dung</th>
                                 <th>Người bình luận</th>
                                 <th>Ngày bình luận</th>
                                 <th>Hành động</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>1</td>
                                 <td>Bài viết hay lắm!</td>
                                 <td>Nguyen Van B</td>
                                 <td>12/10/2025</td>
                                 <td>
                                     <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                 </td>
                             </tr>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     @endsection