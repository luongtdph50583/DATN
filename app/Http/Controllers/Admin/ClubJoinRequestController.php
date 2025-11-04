<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubJoinRequest;
use App\Models\ClubMember;
use Illuminate\Http\Request;

class ClubJoinRequestController extends Controller
{
    public function index()
    {
        $requests = ClubJoinRequest::with(['club', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.club-join-requests.index', compact('requests'));
    }
    public function show($id)
{
    $requestJoin = ClubJoinRequest::with(['club', 'user'])->findOrFail($id);

    return view('admin.club-join-requests.show', compact('requestJoin'));
}


    public function approve($id)
    {
        $requestJoin = ClubJoinRequest::findOrFail($id);

        // Kiểm tra người này đã trong CLB chưa
        $exists = ClubMember::where('club_id', $requestJoin->club_id)
            ->where('member_id', $requestJoin->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Thành viên đã ở trong CLB này!');
        }

        // Thêm vào bảng club_members
        ClubMember::create([
            'club_id' => $requestJoin->club_id,
            'member_id' => $requestJoin->user_id,
            'role' => 'member',
            'joined_at' => now()
        ]);

        // Cập nhật trạng thái yêu cầu
        $requestJoin->status = 'approved';
        $requestJoin->save();

        return back()->with('success', 'Đã chấp nhận thành viên vào CLB!');
    }

    public function reject($id)
    {
        $requestJoin = ClubJoinRequest::findOrFail($id);
        $requestJoin->status = 'rejected';
        $requestJoin->save();

        return back()->with('success', 'Đã từ chối yêu cầu!');
    }
}
