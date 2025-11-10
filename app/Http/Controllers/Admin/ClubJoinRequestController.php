<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\ClubJoinRequest;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use App\Models\ClubInterviewSchedule;

class ClubJoinRequestController extends Controller
{
    public function index()
    {
        $requests = ClubJoinRequest::with(['club', 'user'])
            ->orderByDesc('requested_at')
            ->get();

        return view('admin.club_join_requests.show2', compact('requests'));
    }
    public function showRequest($id)
    {
        $request = ClubJoinRequest::with(['user.member', 'club.manager'])->findOrFail($id);
        $interviewers = User::select('id', 'name')->get(); // hoặc where('role', 'manager')

        return view('admin.club_join_requests.show', compact('request', 'interviewers'));
    }



    public function destroy($id)
{
    $request = ClubJoinRequest::findOrFail($id);
    $request->delete();

    return redirect()->back()->with('success', 'Yêu cầu đã được xóa thành công.');
}


    public function handle(Request $req, $id)
    {
        // $request = ClubJoinRequest::with(['user.member', 'club'])->findOrFail($id);

        // // ✅ Nếu đã xử lý rồi thì không cho xử lý lại
        // if (in_array($request->status, ['approved', 'rejected', 'cancelled'])) {
        //     return back()->with('error', 'Yêu cầu này đã được xử lý.');
        // }

        // $action = $req->input('action');
        // $note = $req->input('note');
        // $user = $request->user;
        // $club = $request->club;
        // $member = $user->member;

        // // 🔒 Kiểm tra CLB và người dùng
        // if ($club->status !== 'active') {
        //     return back()->with('error', 'Chỉ có thể xử lý yêu cầu của CLB đang hoạt động.');
        // }
        // if (!$member) {
        //     return back()->with('error', 'Người dùng chưa có thông tin thành viên.');
        // }

        // switch ($action) {

        //     // 🔹 Lên lịch phỏng vấn
        //     case 'schedule':
        //         $interviewer_id = $req->input('interviewer_id');
        //         $scheduled_at = $req->input('scheduled_at');
        //         $location = $req->input('location');
        //         $status = $req->input('status') ?? 'scheduled';
        //         $interview_note = $req->input('interview_note');

        //         // Validate đơn giản
        //         if (!$interviewer_id || !$scheduled_at) {
        //             return back()->with('error', 'Vui lòng chọn người phỏng vấn và thời gian.');
        //         }

        //         // Tạo bản ghi lịch phỏng vấn
        //         ClubInterviewSchedule::create([
        //             'request_id' => $request->id,
        //             'interviewer_id' => $interviewer_id,
        //             'scheduled_at' => $scheduled_at,
        //             'location' => $location,
        //             'status' => $status,
        //             'note' => $interview_note,
        //         ]);

        //         // Cập nhật trạng thái yêu cầu
        //         $request->update([
        //             'status' => 'scheduling_interview',
        //             'note' => $note,
        //             'handled_by' => auth()->id(),
        //         ]);

        //         return back()->with('success', 'Yêu cầu đã được lên lịch phỏng vấn.');

        //     // 🔹 Đánh dấu phỏng vấn hoàn tất
        //     case 'complete_interview':
        //         $request->update([
        //             'status' => 'interview_completed',
        //             'note' => $note,
        //             'handled_by' => auth()->id(),
        //         ]);
        //         return back()->with('success', 'Đã đánh dấu phỏng vấn hoàn tất, chờ duyệt.');

        //     // 🔹 Duyệt đơn
        //     case 'approve':
        //         $alreadyMember = ClubMember::where('club_id', $club->id)
        //             ->where('member_id', $member->id)
        //             ->exists();
        //         if ($alreadyMember) {
        //             return back()->with('error', 'Người này đã là thành viên của CLB.');
        //         }

        //         ClubMember::create([
        //             'club_id' => $club->id,
        //             'member_id' => $member->id,
        //             'status' => 'active',
        //             'joined_at' => now(),
        //             'role' => 'member',
        //             'note' => $note,
        //         ]);

        //         $request->update([
        //             'status' => 'approved',
        //             'note' => $note,
        //             'handled_by' => auth()->id(),
        //             'handled_at' => now(),
        //         ]);

        //         return redirect()->route('admin.club_join_requests.index')
        //             ->with('success', 'Yêu cầu đã được duyệt.');

        //     // 🔹 Từ chối đơn
        //     case 'reject':
        //         $request->update([
        //             'status' => 'rejected',
        //             'note' => $note,
        //             'handled_by' => auth()->id(),
        //             'handled_at' => now(),
        //         ]);

        //         return redirect()->route('admin.club_join_requests.index')
        //             ->with('success', 'Yêu cầu đã bị từ chối.');

        //     // 🔹 Hủy đơn
        //     case 'cancel':
        //         $request->update([
        //             'status' => 'cancelled',
        //             'note' => $note,
        //             'handled_by' => auth()->id(),
        //             'handled_at' => now(),
        //         ]);
        //         return back()->with('success', 'Yêu cầu đã được hủy.');

        //     default:
        //         return back()->with('error', 'Hành động không hợp lệ.');
        // }
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
