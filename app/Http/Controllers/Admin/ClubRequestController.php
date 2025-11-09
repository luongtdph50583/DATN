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

        return view('admin.club_requests.show2', compact('request'));
    }



    public function handleRequest(Request $request, $id)
    {
        $clubRequest = ClubRequest::with('clubRequestMembers.user.member')->findOrFail($id);
        $creatorUser = $clubRequest->user;

        if (!$creatorUser) {
            return redirect()->back()->withErrors(['error' => 'Người gửi không tồn tại'])->withInput();
        }

        $creatorMember = $creatorUser->member ?? null;
        if (!$creatorMember) {
            return redirect()->back()->withErrors(['error' => 'Người gửi chưa có hồ sơ thành viên'])->withInput();
        }

        // Validate dữ liệu
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'plan' => $clubRequest->type === 'create' ? 'required|string|max:2000' : 'nullable|string|max:2000',
            'note' => 'nullable|string|max:500',
        ]);

        $status = $request->input('status');
        $note = $request->input('note');

        if ($status === 'approved') {

            // --- TẠO CLB MỚI ---
            if ($clubRequest->advisor_status !== 'approved') {
                return redirect()->back()->withErrors(['error' => 'Giảng viên đỡ đầu chưa chấp thuận'])->withInput();
            }

            if (Club::where('name', $clubRequest->name)->exists()) {
                return redirect()->back()->withErrors(['error' => 'Tên CLB đã tồn tại'])->withInput();
            }

            if (ClubMember::where('member_id', $creatorMember->id)->where('role', 'club_manager')->exists()) {
                return redirect()->back()->withErrors(['error' => 'Người tạo đang giữ vai trò chủ nhiệm ở CLB khác'])->withInput();
            }

            // Check trùng vai trò trong CLB khác
            foreach ($clubRequest->clubRequestMembers as $memberReq) {
                $user = $memberReq->user;
                $member = $user->member ?? null;
                if (!$member || $memberReq->role === 'member')
                    continue;

                if (ClubMember::where('member_id', $member->id)->where('role', $memberReq->role)->exists()) {
                    return redirect()->back()->withErrors([
                        'error' => "Thành viên {$user->name} đã có vai trò {$memberReq->role} trong CLB khác"
                    ])->withInput();
                }
            }

            $club = Club::create([
                'name' => $clubRequest->name,
                'slogan' => $clubRequest->slogan,
                'description' => $clubRequest->description,
                'purpose' => $clubRequest->purpose,
                'field' => $clubRequest->field,
                'plan' => $clubRequest->plan,
                'email' => $clubRequest->email,
                'phone' => $clubRequest->phone,
                'logo' => $clubRequest->logo,
                'status' => 'active',
                'founded_at' => now(),
                'manager_id' => $creatorUser->id,
                'advisor_id' => $clubRequest->advisor_id,
                'rule' => $clubRequest->rule,
                'member_limit' => $clubRequest->member_limit,
            ]);

            // Chủ nhiệm
            ClubMember::create([
                'club_id' => $club->id,
                'member_id' => $creatorMember->id,
                'role' => 'club_manager',
                'status' => 'active',
                'joined_at' => now(),
                'appointed_at' => now(),
            ]);

            // Các thành viên khác
            foreach ($clubRequest->clubRequestMembers as $memberReq) {
                $user = $memberReq->user;
                $member = $user->member ?? null;
                if (!$member)
                    continue;

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

            // Gửi thông báo duyệt
            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu thành lập CLB được duyệt",
                "Yêu cầu của bạn về CLB '{$club->name}' đã được duyệt.",
                'both',
                uniqid(),
                false
            );

        } else {
            // Từ chối
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
