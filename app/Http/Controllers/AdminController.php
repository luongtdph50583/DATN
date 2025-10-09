<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
     public function index()
    {
        // Trả về view trang dashboard (ví dụ)
        return view('admin.dashboard');
    }
}

