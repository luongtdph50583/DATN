<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\SendGeneralNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Exception;

class NotificationController extends Controller
{
    /**
     * Hiển thị form gửi thông báo
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Gửi thông báo cho tất cả người dùng
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            // Lấy tất cả người dùng có status active
            $users = User::where('status', 'active')->get();
            $title = $request->input('title');
            $message = $request->input('message');

            // Gửi thông báo
            Notification::send($users, new SendGeneralNotification($title, $message));

            return redirect()->route('admin.notifications.create')->with('success', 'Thông báo đã được gửi thành công cho ' . $users->count() . ' người dùng!');
        } catch (Exception $e) {
            \Log::error('Lỗi khi gửi thông báo: ' . $e->getMessage());
            return redirect()->route('admin.notifications.create')->with('error', 'Đã xảy ra lỗi khi gửi thông báo. Vui lòng kiểm tra log hoặc liên hệ admin.');
        }
    }
}