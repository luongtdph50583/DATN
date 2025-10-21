<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubJoinRequest;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;

class ClubJoinRequestController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu tham gia CLB.
     */
    public function index()
    {
        $requests = ClubJoinRequest::with(['user', 'club'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.club-join-requests.index', compact('requests'));
    }

    /**
     * Hiển thị chi tiết 1 yêu cầu.
     */
    public function show($id)
    {
        $request = ClubJoinRequest::with(['user', 'club'])->findOrFail($id);
        return view('admin.club-join-requests.show', compact('request'));
    }

    /**
     * Cập nhật trạng thái yêu cầu (duyệt hoặc từ chối).
     */
    public function update(Request $request, $id)
    {
        $joinRequest = ClubJoinRequest::findOrFail($id);

        $action = $request->input('action');
        if ($action === 'approve') {
            $joinRequest->status = 'approved';
            $joinRequest->save();

            // Nếu cần thêm người dùng vào CLB (nếu có bảng trung gian)
            if (method_exists($joinRequest->club, 'members')) {
                $joinRequest->club->members()->syncWithoutDetaching([$joinRequest->user_id]);
            }

            return redirect()
                ->route('admin.club-join-requests.show', $id)
                ->with('success', 'Đã duyệt yêu cầu tham gia CLB thành công.');
        }

        if ($action === 'reject') {
            $joinRequest->status = 'rejected';
            $joinRequest->save();

            return redirect()
                ->route('admin.club-join-requests.show', $id)
                ->with('success', 'Đã từ chối yêu cầu tham gia CLB.');
        }

        return redirect()
            ->route('admin.club-join-requests.show', $id)
            ->with('error', 'Hành động không hợp lệ.');
    }

    /**
     * Xóa yêu cầu tham gia CLB.
     */
    public function destroy($id)
    {
        $request = ClubJoinRequest::findOrFail($id);
        $request->delete();

        return redirect()
            ->route('admin.club-join-requests.index')
            ->with('success', 'Đã xóa yêu cầu tham gia CLB.');
    }
}
