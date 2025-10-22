<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubJoinRequest;
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
            $joinRequest->update(['status' => 'approved']);
            return back()->with('success', '✅ Đã duyệt yêu cầu tham gia!');
        }

        if ($action === 'reject') {
            $joinRequest->update(['status' => 'rejected']);
            return back()->with('success', '❌ Đã từ chối yêu cầu!');
        }

        return back()->with('error', 'Hành động không hợp lệ!');
    }
}
