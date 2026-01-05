<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ContactController extends Controller
{
    public function index()
    {
        return view('client.pages.contact.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|min:3',
            'email'   => 'required|email',
            'message' => 'required|min:10'
        ]);

        // Tạm thời chỉ hiển thị thông báo
        // (sau này có thể lưu DB hoặc gửi email)
        
        return back()->with('success', 'Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn.');
    }
}
