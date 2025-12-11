<?php

namespace App\Http\Controllers\Client;

use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\ClubMemberLog;
use App\Models\ClubLeaveRequest;
use App\Models\ClubMemberViolation;
use App\Http\Controllers\Controller;

class ClubLeaveRequestController extends Controller
{
    //
    public function index()
    {
        $requests = ClubLeaveRequest::with(['user', 'club'])->latest()->paginate(10);

        return view('client.pages.club_leave_requests.index', compact('requests'));
    }

    /**
     * Admin xử lý yêu cầu rời CLB
     */
    public function handle(Request $request, $id)
    {
        $req = ClubLeaveRequest::findOrFail($id);

        if ($req->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        // Cập nhật yêu cầu
        $req->update([
            'status' => 'approved',
            'handled_by' => auth()->id(),
            'handled_at' => now(),
            'note' => $request->input('note'),
        ]);

        // Cập nhật trạng thái thành viên
        ClubMember::where('club_id', $req->club_id)
            ->where('member_id', $req->user_id)
            ->update([
                'status' => 'left',
                'left_at' => now(),
            ]);

        // Ghi log hành động rời CLB
        ClubMemberLog::create([
            'club_id' => $req->club_id,
            'member_id' => $req->user_id,
            'action' => 'left',
            'performed_by' => auth()->id(), // admin xử lý
            'reason' => $request->input('note'),
        ]);

        // Nếu có vi phạm/nghĩa vụ chưa hoàn tất
        if ($request->has('violation_type')) {
            ClubMemberViolation::create([
                'club_id' => $req->club_id,
                'member_id' => $req->user_id,
                'reported_by' => auth()->id(),
                'type' => $request->input('violation_type'),
                'description' => $request->input('violation_desc'),
                'issued_at' => now(),
            ]);

            ClubMemberLog::create([
                'club_id' => $req->club_id,
                'member_id' => $req->user_id,
                'action' => 'violation_recorded',
                'performed_by' => auth()->id(),
                'reason' => $request->input('violation_desc'),
            ]);
        }

        return back()->with('success', 'Yêu cầu rời CLB đã được xử lý.');
    }

    /**
     * Hệ thống tự động xử lý yêu cầu quá hạn (có thể gọi từ cron job)
     */
    public function autoExpire()
    {
        $requests = ClubLeaveRequest::where('status', 'pending')
            ->where('requested_at', '<', now()->subDays(7))
            ->get();

        foreach ($requests as $req) {
            $req->update([
                'status' => 'expired',
                'expired_at' => now(),
            ]);

            ClubMember::where('club_id', $req->club_id)
                ->where('member_id', $req->user_id)
                ->update([
                    'status' => 'left',
                    'left_at' => now(),
                ]);

            ClubMemberLog::create([
                'club_id' => $req->club_id,
                'member_id' => $req->user_id,
                'action' => 'left',
                'performed_by' => null, // hệ thống tự động
                'reason' => 'Auto expired after 7 days',
            ]);
        }
    }
}
