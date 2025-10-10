<?php
  namespace App\Http\Controllers;

  use Illuminate\Http\Request;

  class HomeController extends Controller
  {
      public function index()
      {
          return view('home'); // Tạo file resources/views/home.blade.php nếu cần
      }
  }