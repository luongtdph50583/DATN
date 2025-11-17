<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\Member;
use App\Models\ClubMember;
use App\Models\ClubJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;

class ClubMemberRequestController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu tham gia CLB
     */
    public function index($club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $requests = ClubJoinRequest::where('club_id', $club_id)
            ->with(['user.member'])
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('client.pages.club.member_requests', compact('club', 'requests'));
    }

    /**
     * Duyệt yêu cầu tham gia CLB
     */
    public function approve(Request $request, $club_id, $member_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->whereHas('user.member', function($q) use ($member_id) {
                $q->where('id', $member_id);
            })
            ->where('status', 'pending')
            ->with('user.member')
            ->firstOrFail();

        $user = $joinRequest->user;
        $member = $user->member;

        if (!$member) {
            return redirect()->back()
                ->with('error', 'Người dùng chưa có hồ sơ thành viên.');
        }

        // Kiểm tra đã là thành viên chưa
        $existingMember = ClubMember::where('club_id', $club_id)
            ->where('member_id', $member->id)
            ->first();

        if ($existingMember) {
            return redirect()->back()
                ->with('error', 'Người này đã là thành viên của CLB.');
        }

        DB::transaction(function () use ($club_id, $member, $joinRequest) {
            // Tạo thành viên
            ClubMember::create([
                'club_id' => $club_id,
                'member_id' => $member->id,
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // Cập nhật trạng thái yêu cầu
            $joinRequest->status = 'approved';
            $joinRequest->handled_by = Auth::id();
            $joinRequest->handled_at = now();
            $joinRequest->save();

            // Gửi thông báo
            $batchId = uniqid();
            SendNotificationJob::dispatch(
                $user->id,
                "Yêu cầu tham gia CLB được duyệt",
                "Yêu cầu tham gia CLB '{$joinRequest->club->name}' của bạn đã được duyệt.",
                'both',
                $batchId,
                false
            );
        });

        return redirect()->back()
            ->with('success', 'Đã duyệt yêu cầu tham gia CLB thành công.');
    }

    /**
     * Từ chối yêu cầu tham gia CLB
     */
    public function reject(Request $request, $club_id, $request_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('id', $request_id)
            ->where('status', 'pending')
            ->with('user')
            ->firstOrFail();

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $joinRequest->status = 'rejected';
        $joinRequest->handled_by = Auth::id();
        $joinRequest->handled_at = now();
        $joinRequest->note = $request->input('rejection_reason', 'Không đáp ứng yêu cầu');
        $joinRequest->save();

        // Gửi thông báo
        $batchId = uniqid();
        SendNotificationJob::dispatch(
            $joinRequest->user->id,
            "Yêu cầu tham gia CLB bị từ chối",
            "Yêu cầu tham gia CLB '{$joinRequest->club->name}' của bạn đã bị từ chối. Lý do: {$joinRequest->note}",
            'both',
            $batchId,
            false
        );

        return redirect()->back()
            ->with('success', 'Đã từ chối yêu cầu tham gia CLB.');
    }

    /**
     * Kiểm tra quyền quản lý CLB
     */
    private function authorizeClubManager($club)
    {
        $user = Auth::user();
        $managedClubs = $user->getManagedClubs();

        if (!$managedClubs->contains('id', $club->id)) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }
    }
}

