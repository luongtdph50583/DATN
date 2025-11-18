<?php

namespace App\Http\Controllers\Admin;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\ClubLeaveRequest;
use App\Jobs\SendNotificationJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ClubLeaveRequestController extends Controller
{
    //
  public function handleRequest(Request $request, $id)
{
    $leaveRequest = ClubLeaveRequest::findOrFail($id);

    if ($leaveRequest->status !== 'pending') {
        return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý.');
    }

    $request->validate([
        'action' => 'required|in:approve,reject',
        'note' => 'nullable|string|max:255'
    ]);

    // Tìm thành viên trong CLB dựa trên member_id
    $member = ClubMember::where('club_id', $leaveRequest->club_id)
        ->whereHas('member', function($q) use ($leaveRequest) {
            $q->where('user_id', $leaveRequest->user_id);
        })
        ->first();

    if (!$member) {
        return redirect()->back()->with('error', 'Không tìm thấy thành viên trong CLB.');
    }

    // Chủ nhiệm không được rời CLB
    if ($member->role === 'club_manager') {
        return redirect()->back()->with('error', 'Không thể duyệt rời CLB cho Chủ nhiệm. Cần chuyển quyền chủ nhiệm trước.');
    }

    // Cập nhật trạng thái yêu cầu
    $leaveRequest->status = $request->action === 'approve' ? 'approved' : 'rejected';
    $leaveRequest->handled_by = Auth::id();
    $leaveRequest->handled_at = now();
    $leaveRequest->note = $request->note; // lưu note
    $leaveRequest->save();

    // Nếu duyệt → xóa thành viên khỏi CLB
    if ($request->action === 'approve') {
        $member->delete();
    }

    // Gửi thông báo in-app kèm note
    $message = 'Yêu cầu rời CLB của bạn đã được ' 
        . ($request->action === 'approve' ? 'duyệt' : 'từ chối') 
        . '.';

    if (!empty($request->note)) {
        $message .= ' Ghi chú: ' . $request->note;
    }

    SendNotificationJob::dispatch(
        $leaveRequest->user_id,
        'Yêu cầu rời CLB',
        $message,
        'database',
        'leave_request_' . $leaveRequest->id,
        true 
    );

    return redirect()->back()->with('success', $request->action === 'approve'
        ? 'Đã duyệt yêu cầu rời CLB.'
        : 'Đã từ chối yêu cầu rời CLB.');
}

    public function destroy($id)
    {
        $leaveRequest = ClubLeaveRequest::findOrFail($id);
        $leaveRequest->delete();

        return redirect()->back()->with('success', 'Yêu cầu rời CLB đã được xóa.');
    }


    public function index()
    {
        $requests = ClubLeaveRequest::with(['user', 'club'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.club_leave_requests.index', compact('requests'));
    }
    public function showRequest($id)
    {
        $request = ClubLeaveRequest::with(['user', 'club'])->findOrFail($id);

        // ✅ Trả về HTML cho offcanvas
        return view('admin.club_leave_requests.show', compact('request'));
    }
    public function show2($id)
    {
        $request = ClubLeaveRequest::with(['user.member', 'club.manager.user'])->findOrFail($id);

        return view('admin.club_leave_requests.show2', compact('request'));
    }






}
