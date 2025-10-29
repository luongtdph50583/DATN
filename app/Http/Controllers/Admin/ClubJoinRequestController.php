<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\ClubJoinRequest;
use App\Models\ClubMember;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class ClubJoinRequestController extends Controller
{
    /**
     * 📨 Người dùng gửi yêu cầu tham gia CLB
     */
    public function store($club_id)
    {
        $userId = Auth::id();

        // Kiểm tra trùng
        $existingRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            return back()->with('error', '⚠️ Bạn đã gửi yêu cầu hoặc đã là thành viên của CLB này.');
        }

        ClubJoinRequest::create([
            'club_id' => $club_id,
            'user_id' => $userId,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return back()->with('success', '✅ Đã gửi yêu cầu tham gia CLB thành công!');
    }

    /**
     * 📋 Danh sách yêu cầu cho người quản lý CLB
     */
    public function index()
    {
        $managerId = Auth::id();

        // CLB mà user này quản lý
        $clubs = Club::where('manager_id', $managerId)->pluck('id');

        // Lấy yêu cầu "pending"
        $requests = ClubJoinRequest::whereIn('club_id', $clubs)
            ->where('status', 'pending')
            ->with(['club', 'user'])
            ->latest()
            ->get();

        return view('admin.club-join-requests.index', compact('requests'));
    }

    /**
     * 🔍 Hiển thị chi tiết một yêu cầu
     */
    public function show($id)
    {
        $joinRequest = ClubJoinRequest::with(['club', 'user'])->findOrFail($id);
        return view('admin.club-join-requests.show', compact('joinRequest'));
    }

    /**
     * ✅ Duyệt yêu cầu
     */
    public function approve($id)
    {
        $request = ClubJoinRequest::findOrFail($id);

        // Tìm hoặc tạo member tương ứng với user_id
        $member = Member::firstOrCreate([
            'user_id' => $request->user_id
        ]);

        // Kiểm tra trùng thành viên trong CLB
        $exists = ClubMember::where('club_id', $request->club_id)
            ->where('member_id', $member->id)
            ->exists();

        if (!$exists) {
            ClubMember::create([
                'club_id' => $request->club_id,
                'member_id' => $member->id,
                'role' => 'member',
                'joined_at' => now(),
            ]);
        }

        $request->update(['status' => 'approved']);

        return back()->with('success', '✅ Đã duyệt yêu cầu tham gia CLB.');
    }

    /**
     * ❌ Từ chối yêu cầu
     */
    public function reject($id)
    {
        $request = ClubJoinRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);

        return back()->with('error', '🚫 Đã từ chối yêu cầu tham gia CLB.');
    }
}
