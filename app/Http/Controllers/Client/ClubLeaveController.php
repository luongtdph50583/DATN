<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\ClubLeaveRequest;
use App\Models\ClubMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ClubLeaveRequestNotification;

class ClubLeaveController extends Controller
{
    public function store(Request $request, Club $club)
    {
        // Validate lý do
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

      $userId = Auth::id();

$member = ClubMember::where('club_id', $club->id)
    ->whereHas('member', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
    ->first();

if (!$member) {
    return back()->with('error', 'Bạn không phải là thành viên CLB.');
}

        // Lưu lịch sử rời CLB (coi như đã duyệt)
        $leaveRequest = ClubLeaveRequest::create([
            'club_id'      => $club->id,
            'user_id'      => $userId,
            'reason'       => $request->reason,
            'status'       => 'approved',
            'handled_by'   => $userId,
            'handled_at'   => now(),
            'requested_at' => now(),
        ]);

        // Xóa khỏi bảng club_members
        $member->delete();

        // Gửi thông báo cho chủ nhiệm (nếu có)
        if ($club->manager) {
            Notification::send(
                $club->manager,
                new ClubLeaveRequestNotification($leaveRequest)
            );
        }

        // Redirect về trang chủ
        return redirect()
            ->route('client.home')
            ->with('success', 'Bạn đã rời khỏi CLB thành công.');
    }
}
