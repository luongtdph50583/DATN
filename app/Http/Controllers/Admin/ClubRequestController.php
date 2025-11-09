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
            'advisorFaculty.user',            // Giảng viên đỡ đầu + thông tin user
        ])->findOrFail($id);

        return view('admin.club_requests.show', compact('request'));
    }

    public function show2($id)
    {
        // Lấy yêu cầu CLB theo id, kèm thông tin user tạo
        $request = ClubRequest::with('user')->findOrFail($id);

        // Nếu muốn thêm thông tin liên quan khác, ví dụ các comment, file đính kèm
        // $request->load(['attachments', 'comments']);

        return view('admin.club_requests.show2', compact('request'));
    }



    public function handleRequest(Request $request, $id)
    {
        $clubRequest = ClubRequest::with('clubRequestMembers.user.member')->findOrFail($id);

        // Validate dữ liệu
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'note' => 'nullable|string|max:500',
        ]);

        $status = $request->input('status');
        $note = $request->input('note');

        // Lấy user tạo CLB
        $creatorUser = $clubRequest->user;
        if (!$creatorUser) {
            return redirect()->back()->withErrors(['error' => 'Người tạo không tồn tại'])->withInput();
        }

        // Lấy member tương ứng với user tạo
        $creatorMember = $creatorUser->member ?? null;
        if (!$creatorMember) {
            return redirect()->back()->withErrors(['error' => 'Người tạo chưa có hồ sơ thành viên'])->withInput();
        }

        // Kiểm tra trạng thái giảng viên đỡ đầu
        if ($clubRequest->advisor_status !== 'approved') {
            return redirect()->back()->withErrors(['error' => 'Giảng viên đỡ đầu chưa chấp thuận'])->withInput();
        }

        if ($status === 'approved') {
            // Kiểm tra tên CLB trùng
            if (Club::where('name', $clubRequest->name)->exists()) {
                return redirect()->back()->withErrors(['error' => 'Tên CLB đã tồn tại'])->withInput();
            }

            // Kiểm tra chủ nhiệm chưa là quản lý CLB khác
            if (
                ClubMember::where('member_id', $creatorMember->id)
                    ->where('role', 'club_manager')
                    ->exists()
            ) {
                return redirect()->back()->withErrors(['error' => 'Người tạo đang giữ vai trò chủ nhiệm ở CLB khác'])->withInput();
            }

            // Kiểm tra trùng các vai trò khác trong ban quản lý
            foreach ($clubRequest->clubRequestMembers as $memberReq) {
                $user = $memberReq->user;
                $member = $user->member ?? null;
                if (!$member || $memberReq->role === 'member')
                    continue;

                $exists = ClubMember::where('member_id', $member->id)
                    ->where('role', $memberReq->role)
                    ->exists();

                if ($exists) {
                    return redirect()->back()->withErrors([
                        'error' => "Thành viên {$user->name} đã có vai trò {$memberReq->role} trong CLB khác"
                    ])->withInput();
                }
            }

            // Tạo CLB mới
            // Tạo CLB mới
            $club = Club::create([
                'name' => $clubRequest->name,
                'field' => $clubRequest->field,
                'description' => $clubRequest->description,
                'email' => $clubRequest->email,
                'phone' => $clubRequest->phone,
                'logo' => $clubRequest->logo,
                'status' => 'active',
                'founded_at' => now(),
                'manager_id' => $creatorUser->id, // lưu id của user
                'advisor_id' => $clubRequest->advisor_id, // lưu giảng viên đỡ đầu
                'rule' => $clubRequest->rule,              // 🔹 thêm rule
                'member_limit' => $clubRequest->member_limit, // 🔹 thêm member_limit
            ]);


            // Lưu chủ nhiệm CLB
            ClubMember::create([
                'club_id' => $club->id,
                'member_id' => $creatorMember->id,
                'role' => 'club_manager',
                'status' => 'active',
                'joined_at' => now(),
                'appointed_at' => now(),
            ]);

            // Lưu các thành viên khác từ ban quản lý đề xuất
            foreach ($clubRequest->clubRequestMembers as $memberReq) {
                $user = $memberReq->user;
                $member = $user->member ?? null;
                if (!$member)
                    continue;

                // role != member đã được check trùng trước, thêm trực tiếp
                ClubMember::create([
                    'club_id' => $club->id,
                    'member_id' => $member->id,
                    'role' => $memberReq->role,
                    'status' => 'active',
                    'joined_at' => now(),
                    'appointed_at' => now(),
                ]);
            }

            // Cập nhật trạng thái request
            $clubRequest->status = 'approved';
            $clubRequest->handled_by = auth()->id();
            $clubRequest->note = $note;
            $clubRequest->save();

            // Gửi thông báo/email
            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu thành lập CLB được duyệt",
                "Yêu cầu của bạn về CLB '{$club->name}' đã được duyệt.",
                'both',
                uniqid(),
                false
            );

        } else { // rejected
            $clubRequest->status = 'rejected';
            $clubRequest->handled_by = auth()->id();
            $clubRequest->note = $note;
            $clubRequest->save();

            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu thành lập CLB bị từ chối",
                "Yêu cầu của bạn về CLB '{$clubRequest->name}' đã bị từ chối. Lý do: {$note}",
                'both',
                uniqid(),
                false
            );
        }

        return redirect()->route('admin.club_requests.index')
            ->with('success', 'Đã xử lý yêu cầu thành lập CLB thành công.');
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
