@extends('admin.layouts.app')

     @section('content')
         <div class="card shadow mb-4">
             <div class="card-header py-3">
                 <h6 class="m-0 font-weight-bold text-primary">Lịch sử tham gia</h6>
             </div>
             <div class="card-body">
                 <div class="table-responsive">
                     <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                         <thead>
                             <tr>
                                 <th>ID</th>
                                 <th>Tên thành viên</th>
                                 <th>Sự kiện</th>
                                 <th>Ngày tham gia</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>1</td>
                                 <td>Nguyen Van A</td>
                                 <td>Sự kiện 1</td>
                                 <td>11/10/2025</td>
                             </tr>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     @endsection