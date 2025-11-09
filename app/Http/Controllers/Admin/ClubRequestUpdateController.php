<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\User;
use App\Models\Member;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\ClubUpdateLog;
use App\Models\FacultyMember;
use App\Jobs\SendNotificationJob;
use App\Models\ClubRequestUpdate;
use App\Http\Controllers\Controller;
use App\Services\ClubUpdateLogService;

class ClubRequestUpdateController extends Controller
{
    protected ClubUpdateLogService $logService;


    /**
     * Xử lý duyệt/từ chối đề xuất thay đổi CLB
     */
    public function indexRequests()
    {
        $requests = ClubRequestUpdate::with(['club', 'club.clubMembers.user', 'memberUpdates.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.club_update_requests.index', compact('requests'));
    }


    /**
     * Hiển thị chi tiết 1 yêu cầu update
     */
    public function showRequest($id)
    {
        // Lấy đề xuất và club
        $update = ClubRequestUpdate::with([
            'proposer',                    // người đề xuất
            'memberUpdates.user',           // thành viên đề xuất
            'club.clubMembers.user'         // thành viên hiện tại
        ])->findOrFail($id);

        $club = $update->club;

        // Dữ liệu cũ
        $currentData = [
            'club' => $club,
            'members' => $club->clubMembers // collection, giữ tất cả member hiện tại
        ];

        // Dữ liệu đề xuất
        $proposedData = [
            'club' => $update,
            'members' => $update->memberUpdates // collection, giữ tất cả member đề xuất
        ];

        return view('admin.club_update_requests.show', compact('currentData', 'proposedData', 'update', 'club'));
    }


    public function handleUpdateRequest(Request $request, $id)
    {
        $status = $request->input('status'); // 'approved' hoặc 'rejected'
        $rejectedReason = $request->input('rejected_reason');

        $update = ClubRequestUpdate::with(['memberUpdates', 'club.clubMembers', 'club.advisor.facultyMember.user', 'proposer'])->findOrFail($id);
        $club = $update->club;
        $creatorUser = $update->proposer; // người gửi yêu cầu

        if ($status === 'approved') {

            if ($update->advisor_id && $update->advisor_id != $club->advisor_id) {
                $club->advisor_id = $update->advisor_id;
            }

            // 2️⃣ Cập nhật thông tin CLB
            $fields = ['name', 'slogan', 'description', 'field', 'member_limit', 'email', 'phone', 'logo', 'rules', 'location'];
            foreach ($fields as $field) {
                if (!is_null($update->$field)) {
                    $club->$field = $update->$field;
                }
            }
            $club->manager_id = $update->manager_id ?? $club->manager_id;
            $club->save();

            // 3️⃣ Cập nhật ban quản lý
            foreach ($update->memberUpdates as $memberUpdate) {
                $member = Member::where('user_id', $memberUpdate->user_id)->first();
                if (!$member)
                    continue;

                $role = $memberUpdate->role;

                if ($role !== 'member') {
                    $existing = $club->clubMembers->where('role', $role)->first();
                    if ($existing) {
                        $existing->member_id = $member->id;
                        $existing->save();
                    } else {
                        $club->clubMembers()->create([
                            'member_id' => $member->id,
                            'role' => $role,
                        ]);
                    }
                } else {
                    $existing = $club->clubMembers()->where('role', 'member')->where('member_id', $member->id)->first();
                    if (!$existing) {
                        $club->clubMembers()->create([
                            'member_id' => $member->id,
                            'role' => 'member',
                        ]);
                    }
                }
            }

            // 4️⃣ Cập nhật trạng thái yêu cầu
            $update->status = 'approved';
            $update->save();

            // 5️⃣ Gửi thông báo thành công
            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu cập nhật CLB được duyệt",
                "Yêu cầu cập nhật CLB '{$club->name}' của bạn đã được duyệt.",
                'both',
                uniqid(),
                false
            );

        } elseif ($status === 'rejected') {
            // 6️⃣ Từ chối yêu cầu
            $update->status = 'rejected';
            $update->note = $rejectedReason;
            $update->save();

            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu cập nhật CLB bị từ chối",
                "Yêu cầu cập nhật CLB '{$club->name}' của bạn đã bị từ chối. Lý do: {$rejectedReason}",
                'both',
                uniqid(),
                false
            );
        }

        return redirect()->route('admin.club_requests_update.index')
            ->with('success', 'Xử lý yêu cầu cập nhật CLB thành công!');
    }




    /**
     * Xử lý thêm/xóa/sửa ban quản lý từ đề xuất
     */


}
