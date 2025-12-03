<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Member;
use App\Models\ClubMember;
use App\Models\ClubRequest;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Auth;

class ClubRequestController extends Controller
{
    /**
     * 📋 Danh sách yêu cầu tạo CLB
     */
    public function indexRequests()
    {
        $requests = ClubRequest::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.club_requests.index', compact('requests'));
    }

    public function showRequest($id)
    {
        $request = ClubRequest::with([
            'user',                        // Người đề xuất
            'clubRequestMembers.user.member', // Thành viên/ban quản lý + thông tin member
            // 'advisorFaculty.user',            // Giảng viên đỡ đầu + thông tin user
        ])->findOrFail($id);

        return view('admin.club_requests.show', compact('request'));
    }

    public function show2($id)
    {
        // Lấy yêu cầu CLB theo id, kèm thông tin user tạo
        $request = ClubRequest::with('user')->findOrFail($id);

        return view('admin.club_requests.show2', compact('request'));
    }



public function handleRequest(Request $request, $id)
{
    $clubRequest = ClubRequest::findOrFail($id);

    // Validate request
    $request->validate([
        'status' => 'required|in:approved,rejected',
        'note' => 'nullable|string|max:500',
    ]);

    $status = $request->input('status');
    $note = $request->input('note');

    $creatorUser = $clubRequest->user;
    if (!$creatorUser) {
        return redirect()->back()->withErrors(['error' => 'Người tạo không tồn tại'])->withInput();
    }

    $creatorMember = Member::where('user_id', $creatorUser->id)->first();
    if (!$creatorMember) {
        return redirect()->back()->withErrors(['error' => 'Người tạo chưa có hồ sơ thành viên'])->withInput();
    }

    if ($status === 'approved') {

        // Kiểm tra tên CLB hợp lệ
        if (!$clubRequest->name) {
            return redirect()->back()->withErrors(['name' => 'Tên CLB không hợp lệ'])->withInput();
        }

        // Kiểm tra tên CLB trùng
        if (Club::where('name', $clubRequest->name)->exists()) {
            return redirect()->back()->withErrors(['name' => 'Tên CLB đã tồn tại'])->withInput();
        }

        // Kiểm tra người tạo chưa là quản lý CLB khác
        if (ClubMember::where('member_id', $creatorMember->id)
            ->where('role', 'club_manager')->exists()) {
            return redirect()->back()->withErrors(['error' => 'Người tạo đang giữ vai trò quản lý ở CLB khác'])->withInput();
        }

        // Dùng transaction để an toàn
        DB::transaction(function () use ($clubRequest, $creatorUser, $creatorMember) {

            // Tạo CLB
            $club = Club::create([
                'name' => $clubRequest->name,
                'field' => $clubRequest->field ?? 'Chưa cập nhật',
                'description' => $clubRequest->description ?? '',
                'email' => $clubRequest->email ?? null,
                'phone' => $clubRequest->phone ?? null,
                'logo' => $clubRequest->logo ?? null,
                'status' => 'active',
                'founded_at' => now(),
                'manager_id' => $creatorUser->id,
            ]);

            // Tạo ClubMember
            ClubMember::create([
                'club_id' => $club->id,
                'member_id' => $creatorMember->id,
                'role' => 'club_manager',
                'status' => 'active',
                'joined_at' => now(),
                'appointed_at' => now(),
            ]);

            // Xóa mềm yêu cầu
            $clubRequest->delete();

            // Gửi thông báo/email
            $batchId = uniqid();
            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu thành lập CLB được duyệt",
                "Yêu cầu của bạn về CLB '{$club->name}' đã được duyệt.",
                'both',
                $batchId,
                false
            );
        });

        return redirect()->route('admin.club_requests.index')
            ->with('success', 'Yêu cầu đã được duyệt .');

    } else {
        // rejected
        $clubRequest->status = 'rejected';
        $clubRequest->handled_by = Auth::id();
        $clubRequest->note = $note;
        $clubRequest->save();

        $batchId = uniqid();
        SendNotificationJob::dispatch(
            $creatorUser->id,
            "Yêu cầu thành lập CLB bị từ chối",
            "Yêu cầu của bạn về CLB '{$clubRequest->name}' đã bị từ chối. Lý do: {$note}",
            'both',
            $batchId,
            false
        );

        return redirect()->route('admin.club_requests.index')
            ->with('success', 'Yêu cầu đã bị từ chối.');
    }
}


    public function filterRequests(Request $request)
    {
        $query = ClubRequest::query()->with('user');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByDesc('created_at')->get();

        // Trả về JSON để JS render lại tbody
        return response()->json($requests);
    }
    public function destroy($id)
    {
        $request = ClubRequest::findOrFail($id); // dùng ClubRequest
        $request->delete();

        return redirect()->back()->with('success', 'Yêu cầu đã được xóa thành công.');
    }














}
