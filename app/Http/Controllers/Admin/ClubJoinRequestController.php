<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;

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


public function destroy($id)
{
    $request = ClubJoinRequest::findOrFail($id);
    $request->delete();

    return redirect()->back()->with('success', 'Yêu cầu đã được xóa thành công.');
}


    public function handleRequest(Request $req, $id)
    {
        $request = ClubJoinRequest::with(['user.member', 'club'])->findOrFail($id);

        if ($request->status !== 'pending') {
           
            return back()->with('error', 'Yêu cầu đã được xử lý trước đó.');
        }

        $action = $req->input('action');
        $note = $req->input('note');

        $user = $request->user;
        $club = $request->club;
        $member = $user->member;

        // Kiểm tra trạng thái CLB
        if ($club->status !== 'active') {
            Log::warning("CLB #{$club->id} không hoạt động. Không thể duyệt.");
            return back()->with('error', 'Chỉ có thể tham gia CLB đang hoạt động.');
        }

        // Kiểm tra nếu người dùng chưa có thông tin thành viên
        if (!$member) {
            Log::warning("User #{$user->id} chưa có thông tin thành viên.");
            return back()->with('error', 'Người dùng chưa có thông tin thành viên.');
        }

        // Kiểm tra nếu đã là thành viên CLB
        $alreadyMember = ClubMember::where('club_id', $club->id)
            ->where('member_id', $member->id)
            ->exists();

        if ($alreadyMember) {
            Log::warning("Member #{$member->id} đã là thành viên CLB #{$club->id}");
            return back()->with('error', 'Người dùng đã là thành viên của CLB này.');
        }

        // Kiểm tra số lượng thành viên hiện tại
        $memberCount = ClubMember::where('club_id', $club->id)->count();
        $maxMembers = $club->member_limit ?? 50;

        if ($memberCount >= $maxMembers) {
            Log::warning("CLB #{$club->id} đã đạt giới hạn thành viên ({$memberCount}/{$maxMembers})");
            return back()->with('error', 'CLB đã đạt giới hạn số lượng thành viên.');
        }

        // Kiểm tra xác thực thông tin cá nhân
        $isVerified = $member->citizen_id && $member->issued_date && $member->issued_place;

        if (!$isVerified) {
            return back()->with('error', 'Người dùng chưa xác thực đầy đủ thông tin cá nhân.');
        }

        if ($action === 'approve') {
            ClubMember::create([
                'club_id' => $club->id,
                'member_id' => $member->id,
                'status' => 'active',
                'joined_at' => now(),
                'role' => 'member',
                'note' => $note,
            ]);

            $request->status = 'approved';
            $request->note = $note;
            $request->handled_by = Auth::id();

            $request->save();


            // Gửi thông báo
            $batchId = uniqid();

            SendNotificationJob::dispatch(
                $user->id,
                "Yêu cầu của bạn tham gia CLB '{$club->name}' đã được duyệt.",
                $note,
                'both',
                $batchId,
                false
            );

            return redirect()->route('admin.club_join_requests.index')->with('success', 'Yêu cầu đã được duyệt.');
        }

        if ($action === 'reject') {
            $request->status = 'rejected';
            $request->note = $note;
           $request->handled_by = Auth::id();

            $request->save();


            return redirect()->route('admin.club_join_requests.index')->with('success', 'Yêu cầu đã bị từ chối.');
        }

        return back()->with('error', 'Hành động không hợp lệ.');
    }

    public function show2($id)
    {
        $request = ClubJoinRequest::with([
            'user.member',
            'club.manager',
            'club.members'
        ])->findOrFail($id);

        return view('admin.club_join_requests.show2', compact('request'));
    }
    public function filter(Request $request)
    {
        $query = ClubJoinRequest::with(['user', 'club']);

        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$keyword%"))
                    ->orWhereHas('club', fn($c) => $c->where('name', 'like', "%$keyword%"));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByDesc('requested_at')->get();

        // ✅ Trả JSON thô
        return response()->json([
            'data' => $requests->map(fn($r) => [
                'id' => $r->id,
                'user' => $r->user->name ?? '—',
                'club' => $r->club->name ?? '—',
                'requested_at' => optional($r->requested_at)->format('d/m/Y') ?? '—',
                'status' => $r->status,
                'show_url' => route('admin.club_join_requests.show2', $r->id),
            ])
        ]);
    }


}