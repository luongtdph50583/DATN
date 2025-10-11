<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;

class MemberController extends Controller
{
    public function index()
    {
        if (!Auth::check() || (Auth::check() && Auth::user()->role !== 'admin')) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }

        // Khởi tạo biến $clubs mặc định là mảng rỗng
        $clubs = [];

        // Thực hiện truy vấn với xử lý ngoại lệ
        try {
            $clubs = Club::with('members.user')->get();
        } catch (\Exception $e) {
            \Log::error('Lỗi truy vấn danh sách CLB: ' . $e->getMessage());
            // Gán mảng rỗng để tránh lỗi trong view
        }

        return view('index', compact('clubs'))->with('activeTab', 'members');
    }
}