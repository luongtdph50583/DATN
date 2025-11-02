<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\ClubJoinRequest;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ClubJoinRequestController extends Controller
{
    public function index()
    {
        $requests = ClubJoinRequest::with(['club', 'user'])
            ->orderByDesc('requested_at')
            ->get();

        return view('admin.club_join_requests.index', compact('requests'));
    }
    public function showRequest($id)
    {
        $request = ClubJoinRequest::with(['user.member', 'club.manager'])->findOrFail($id);
        return view('admin.club_join_requests.show', compact('request'));
    }




    public function handleRequest(Request $req, $id)
    {
        $request = ClubJoinRequest::with(['user.member', 'club'])->findOrFail($id);

        Log::info("Bắt đầu xử lý yêu cầu #{$request->id} từ user #{$request->user_id}");

        if ($request->status !== 'pending') {
            Log::warning("Yêu cầu #{$request->id} đã được xử lý trước đó.");
            return back()->with('error', 'Yêu cầu đã được xử lý trước đó.');
        }

        $action = $req->input('action');
        $note = $req->input('note');

        $user = $request->user;
        $club = $request->club;

        // Kiểm tra trạng thái CLB
        if ($club->status !== 'active') {
            Log::warning("CLB #{$club->id} không hoạt động. Không thể duyệt.");
            return back()->with('error', 'Chỉ có thể tham gia CLB đang hoạt động.');
        }

        // Kiểm tra nếu đã là thành viên CLB
        $alreadyMember = ClubMember::where('club_id', $club->id)
            ->where('member_id', $user->id)
            ->exists();

        if ($alreadyMember) {
            Log::warning("User #{$user->id} đã là thành viên CLB #{$club->id}");
            return back()->with('error', 'Người dùng đã là thành viên của CLB này.');
        }

        // Kiểm tra số lượng thành viên hiện tại
        $memberCount = ClubMember::where('club_id', $club->id)->count();
        $maxMembers = $club->limit ?? 50;

        if ($memberCount >= $maxMembers) {
            Log::warning("CLB #{$club->id} đã đạt giới hạn thành viên ({$memberCount}/{$maxMembers})");
            return back()->with('error', 'CLB đã đạt giới hạn số lượng thành viên.');
        }

        // Kiểm tra xác thực thông tin cá nhân
        $memberInfo = $user->member;
        $isVerified = $memberInfo &&
            $memberInfo->citizen_id &&
            $memberInfo->issued_date &&
            $memberInfo->issued_place;

        if (!$isVerified) {
            Log::warning("User #{$user->id} chưa xác thực đầy đủ thông tin cá nhân.");
            return back()->with('error', 'Người dùng chưa xác thực đầy đủ thông tin cá nhân.');
        }

        if ($action === 'approve') {
            // Tạo thành viên mới
            ClubMember::create([
                'club_id' => $club->id,
                'member_id' => $user->id,
                'status' => 'active',
                'joined_at' => now(),
                'role' => 'member',
                'note' => $note,
            ]);

            $request->status = 'approved';
            $request->note = $note;
            $request->save();

            Log::info("Yêu cầu #{$request->id} đã được duyệt. Tạo thành viên thành công.");

            // Gửi thông báo
            $batchId = uniqid();
            Log::info("Gửi thông báo đến user #{$user->id} với batchId {$batchId}");

            SendNotificationJob::dispatch(
                $user->id,
                "Yêu cầu tham gia CLB được duyệt",
                "Yêu cầu của bạn tham gia CLB '{$club->name}' đã được duyệt.",
                'both',
                $batchId,
                false
            );

            return redirect()->route('admin.club_join_requests.index')->with('success', 'Yêu cầu đã được duyệt.');
        }

        if ($action === 'reject') {
            $request->status = 'rejected';
            $request->note = $note;
            $request->save();

            Log::info("Yêu cầu #{$request->id} đã bị từ chối.");

            return redirect()->route('admin.club_join_requests.index')->with('success', 'Yêu cầu đã bị từ chối.');
        }

        Log::error("Hành động không hợp lệ: {$action}");
        return back()->with('error', 'Hành động không hợp lệ.');
    }
}