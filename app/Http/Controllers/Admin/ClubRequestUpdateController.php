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
        $requests = ClubRequestUpdate::with([
            'club',
            'club.clubMembers.user',
            'memberUpdates.user'
        ])
            ->where('status', 'pending') // ✅ chỉ lấy những đề xuất đang chờ duyệt
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.club_update_requests.index', compact('requests'));
    }


    /**
     * Hiển thị chi tiết 1 yêu cầu update
     */
    public function showRequest($id)
    {
        $update = ClubRequestUpdate::with([
            'proposer',
            'memberUpdates.user',
            'memberUpdates.memberInfo',
            'club.clubMembers.member.user',   // Ban quản lý hiện tại → user
            'club.clubMembers.member',        // Ban quản lý hiện tại → member
            'club.manager',                   // Chủ nhiệm hiện tại → user
            'club.advisorFaculty',            // Giảng viên đỡ đầu hiện tại
            'advisorFacultyProposed',         // Giảng viên đề xuất
            'managerProposed'                 // Chủ nhiệm đề xuất → user
        ])->findOrFail($id);

        $club = $update->club;

        // 🔹 Dữ liệu cũ (hiện tại)
        $currentData = [
            'club' => $club,
            'members' => $club->clubMembers,      // Ban quản lý hiện tại
            'manager' => $club->manager,          // Chủ nhiệm hiện tại
            'advisor' => $club->advisorFaculty,   // Giảng viên hiện tại
        ];

        // 🔹 Dữ liệu đề xuất (cập nhật)
        $proposedData = [
            'club' => $update,
            'members' => $update->memberUpdates,          // Thành viên đề xuất
            'manager' => $update->managerProposed,       // Chủ nhiệm đề xuất
            'advisor' => $update->advisorFacultyProposed // Giảng viên đề xuất (nếu có)
        ];

        return view('admin.club_update_requests.show', compact(
            'currentData',
            'proposedData',
            'update',
            'club'
        ));
    }




    public function handleUpdateRequest(Request $request, $id)
    {
        $status = $request->input('status'); // 'approved' hoặc 'rejected'
        $rejectedReason = $request->input('rejected_reason');

        $update = ClubRequestUpdate::with([
            'memberUpdates',
            'club.clubMembers',
            'club.advisorFaculty',
            'proposer'
        ])->findOrFail($id);

        $club = $update->club;
        $creatorUser = $update->proposer;

        if ($status === 'approved') {

            // 🔹 Validate tên CLB không trùng
            if (!is_null($update->name)) {
                $existingClub = Club::where('name', $update->name)
                    ->where('id', '!=', $club->id)
                    ->first();
                if ($existingClub) {
                    return back()->withErrors(['name' => 'Tên CLB đã tồn tại ở CLB khác.']);
                }
            }

            // 🔹 Validate ban quản lý không trùng user_id trong cùng CLB
            $managerIds = [];
            foreach ($update->memberUpdates as $memberUpdate) {
                if ($memberUpdate->role !== 'member') {
                    $uid = $memberUpdate->user_id;
                    if (in_array($uid, $managerIds)) {
                        return back()->withErrors(['members' => 'Các thành viên ban quản lý không được trùng nhau.']);
                    }
                    $managerIds[] = $uid;

                    // 🔹 Kiểm tra role này đã có ở CLB khác chưa
                    $roleExists = ClubMember::where('role', $memberUpdate->role)
                        ->whereHas('club', fn($q) => $q->where('id', '!=', $club->id))
                        ->where('member_id', $memberUpdate->user_id)
                        ->exists();

                    if ($roleExists) {
                        return back()->withErrors([
                            'members' => "Thành viên '{$memberUpdate->user->name}' đã giữ chức vụ '{$memberUpdate->role}' ở CLB khác."
                        ]);
                    }
                }
            }

            // Nếu có manager_id riêng trong update, kiểm tra tương tự
            if (!is_null($update->manager_id)) {
                if (in_array($update->manager_id, $managerIds)) {
                    return back()->withErrors(['manager' => 'Chủ nhiệm không được trùng với thành viên ban quản lý khác.']);
                }
                $managerIds[] = $update->manager_id;

                $managerRoleExists = ClubMember::where('role', 'club_manager')
                    ->whereHas('club', fn($q) => $q->where('id', '!=', $club->id))
                    ->where('member_id', $update->manager_id)
                    ->exists();

                if ($managerRoleExists) {
                    return back()->withErrors([
                        'manager' => "Người được chỉ định làm Chủ nhiệm đã là Chủ nhiệm ở CLB khác."
                    ]);
                }
            }

            // 🔹 1️⃣ Cập nhật giảng viên đỡ đầu nếu có
            if (!is_null($update->advisor_id) && $update->advisor_status === 'approved') {
                if ($update->advisor_id != $club->advisor_id) {
                    $club->advisor_id = $update->advisor_id;
                }
            }

            // 🔹 2️⃣ Cập nhật thông tin CLB
            $fields = ['name', 'slogan', 'description', 'field', 'member_limit', 'email', 'phone', 'logo', 'rules', 'location'];
            foreach ($fields as $field) {
                if (!is_null($update->$field)) {
                    $club->$field = $update->$field;
                }
            }

            // 🔹 3️⃣ Cập nhật manager (chủ nhiệm)
            if (!is_null($update->manager_id) && $update->manager_id != $club->manager_id) {
                $club->manager_id = $update->manager_id;
                $managerMember = Member::where('user_id', $update->manager_id)->first();
                if ($managerMember) {
                    $oldManager = $club->clubMembers()->where('role', 'club_manager')->where('member_id', '!=', $managerMember->id)->first();
                    if ($oldManager) {
                        $oldManager->role = 'member';
                        $oldManager->save();
                    }

                    $club->clubMembers()->updateOrCreate(
                        ['role' => 'club_manager'],
                        [
                            'member_id' => $managerMember->id,
                            'appointed_at' => now(),
                        ]
                    );
                }
            }

            $club->save();

            // 🔹 4️⃣ Cập nhật ban quản lý các vai trò khác
            foreach ($update->memberUpdates as $memberUpdate) {
                $member = Member::where('user_id', $memberUpdate->user_id)->first();
                if (!$member)
                    continue;

                $role = $memberUpdate->role;

                if ($role !== 'member') {
                    $oldRoleHolder = $club->clubMembers()
                        ->where('role', $role)
                        ->where('member_id', '!=', $member->id)
                        ->first();
                    if ($oldRoleHolder) {
                        $oldRoleHolder->role = 'member';
                        $oldRoleHolder->save();
                    }

                    $club->clubMembers()->updateOrCreate(
                        ['role' => $role],
                        [
                            'member_id' => $member->id,
                            'appointed_at' => now(),
                        ]
                    );
                } else {
                    $club->clubMembers()->firstOrCreate(
                        ['role' => 'member', 'member_id' => $member->id],
                        ['appointed_at' => now()]
                    );
                }
            }

            // 🔹 5️⃣ Cập nhật trạng thái yêu cầu
            $update->status = 'approved';
            $update->save();

            // 🔹 6️⃣ Gửi thông báo
            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu cập nhật CLB được duyệt",
                "Yêu cầu cập nhật CLB '{$club->name}' của bạn đã được duyệt.",
                'both',
                uniqid(),
                false
            );

        } elseif ($status === 'rejected') {
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
