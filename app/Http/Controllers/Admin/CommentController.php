<?php

   namespace App\Http\Controllers\Admin;

   use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Auth;
   

   class CommentController extends Controller
   {
       public function index()
       {
           return view('admin.comments.index');
       }
       protected function checkAdmin()
         {
             if (!Auth::check() || Auth::user()->role !== 'admin') {
                 return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
             }
             return null;
         }
   }