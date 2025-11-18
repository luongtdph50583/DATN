<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Trả về view home.blade.php
        return view('client.pages.post.index');
    }
}
