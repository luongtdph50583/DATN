<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Đánh dấu thông báo đã đọc.
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $notificationId = $request->input('notification_id');

        if ($notificationId) {
            $notification = $user->unreadNotifications()
                ->where('id', $notificationId)
                ->first();

            if ($notification) {
                $notification->markAsRead();
            }
        } else {
            $user->unreadNotifications->each->markAsRead();
        }

        return redirect()->back()->with('success', 'Đã cập nhật thông báo.');
    }
}

