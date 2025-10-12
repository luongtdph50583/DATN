<?php

     namespace App\Http\Controllers\Admin;

     use App\Http\Controllers\Controller;
     use Illuminate\Support\Facades\Auth;

     class DocumentController extends Controller
     {
        protected function checkAdmin()
         {
             if (!Auth::check() || Auth::user()->role !== 'admin') {
                 return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
             }
             return null;
         }

         public function index()
         {
             return view('admin.documents.index');
         }
     }