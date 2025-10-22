<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubJoinRequest;
use App\Models\ClubMember;
use Illuminate\Http\Request;

class ClubJoinRequestController extends Controller
{
    // === Danh sách yêu cầu ===
    public function index(Request $request)
    {
        $query = ClubJoinRequest::with(['user', 'club.leader']);

        // Tìm kiếm theo tên người dùng hoặc tên CLB
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('club', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.club-join-requests.index', compact('requests'));
    }

    // === Xem chi tiết yêu cầu ===
    public function show(ClubJoinRequest $joinRequest)
    {
        $joinRequest->load(['user', 'club.leader']);

        return view('admin.club-join-requests.show', compact('joinRequest'));
    }

    // === Xử lý duyệt hoặc từ chối ===
   public function handle(Request $request, ClubJoinRequest $joinRequest)
{
    $action = $request->input('action');

    if ($action === 'approve') {
        // ✅ 1. Cập nhật trạng thái yêu cầu
        $joinRequest->update(['status' => 'approved']);

        // ✅ 2. Thêm thành viên vào bảng club_members (nếu chưa có)
        ClubMember::firstOrCreate([
            'club_id' => $joinRequest->club_id,
            'user_id' => $joinRequest->user_id,
        ], [
            'role' => 'member', // hoặc bạn muốn đặt là 'thành viên'
        ]);

        return back()->with('success', '✅ Đã duyệt và thêm thành viên vào CLB!');
    } 
    elseif ($action === 'reject') {
        $joinRequest->update(['status' => 'rejected']);
        return back()->with('success', '❌ Đã từ chối yêu cầu!');
    }

    return back()->with('error', 'Hành động không hợp lệ!');
}

}
