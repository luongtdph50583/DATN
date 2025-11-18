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
use Illuminate\Support\Facades\Auth;

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

    public function create()
    {
        $clubs = Club::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $facultyMembers = FacultyMember::with('user')
            ->orderBy('employee_code')
            ->get();
        $roles = [
            'club_manager' => 'Chủ nhiệm CLB',
            'deputy_manager' => 'Phó chủ nhiệm',
            'event_manager' => 'Quản lý sự kiện',
            'communication' => 'Truyền thông',
            'secretary' => 'Thư ký',
            'treasurer' => 'Thủ quỹ',
            'member' => 'Thành viên thường',
        ];

        return view('admin.club_update_requests.create', compact('clubs', 'users', 'facultyMembers', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'name' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'field' => 'nullable|string|max:255',
            'member_limit' => 'nullable|integer|min:1',
            'manager_id' => 'nullable|exists:users,id',
            'advisor_id' => 'nullable|exists:faculty_members,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'rules' => 'nullable|string',
            'reason' => 'required|string|max:1000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'management_updates' => 'array',
            'management_updates.*.user_id' => 'nullable|exists:users,id',
            'management_updates.*.role' => 'nullable|in:club_manager,deputy_manager,event_manager,communication,secretary,treasurer,member',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('club-update-logos', 'public');
        }

        $update = ClubRequestUpdate::create([
            'club_id' => $validated['club_id'],
            'user_id' => Auth::id(),
            'name' => $validated['name'] ?? null,
            'slogan' => $validated['slogan'] ?? null,
            'description' => $validated['description'] ?? null,
            'field' => $validated['field'] ?? null,
            'member_limit' => $validated['member_limit'] ?? null,
            'manager_id' => $validated['manager_id'] ?? null,
            'advisor_id' => $validated['advisor_id'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'logo' => $validated['logo'] ?? null,
            'rules' => $validated['rules'] ?? null,
            'location' => $validated['location'] ?? null,
            'reason' => $validated['reason'],
            'status' => 'pending',
            'advisor_status' => 'pending',
        ]);

        $managementUpdates = collect($request->input('management_updates', []))
            ->filter(fn ($item) => !empty($item['user_id']) && !empty($item['role']));

        foreach ($managementUpdates as $entry) {
            $update->memberUpdates()->create([
                'user_id' => $entry['user_id'],
                'role' => $entry['role'],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('admin.club_requests_update.index')
            ->with('success', 'Đã tạo đề xuất cập nhật CLB. Yêu cầu đang chờ duyệt.');
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

            // 🔹 Chuẩn bị mảng thay đổi để lưu log
            $changes = [];

            // 🔹 1️⃣ Cập nhật giảng viên đỡ đầu nếu có
            if (!is_null($update->advisor_id) && $update->advisor_status === 'approved') {
                if ($update->advisor_id != $club->advisor_id) {
                    $changes['advisor_id'] = [
                        'old' => $club->advisor_id,
                        'new' => $update->advisor_id
                    ];
                    $club->advisor_id = $update->advisor_id;
                }
            }

            // 🔹 2️⃣ Cập nhật thông tin CLB và lưu thay đổi
            $fields = ['name', 'slogan', 'description', 'field', 'member_limit', 'email', 'phone', 'logo', 'rules', 'location'];
            foreach ($fields as $field) {
                if (!is_null($update->$field)) {
                    $oldValue = $club->$field;
                    $newValue = $update->$field;
                    if ($oldValue != $newValue) {
                        $changes[$field] = [
                            'old' => $oldValue,
                            'new' => $newValue
                        ];
                        $club->$field = $newValue;
                    }
                }
            }

            // 🔹 3️⃣ Cập nhật manager (chủ nhiệm)
            if (!is_null($update->manager_id) && $update->manager_id != $club->manager_id) {
                $oldManagerMember = $club->clubMembers()->where('role', 'club_manager')->first();
                $oldManagerId = $oldManagerMember ? $oldManagerMember->member_id : null;

                $club->manager_id = $update->manager_id;
                $managerMember = Member::where('user_id', $update->manager_id)->first();
                if ($managerMember) {
                    $newManagerId = $managerMember->id;

                    // Lưu thay đổi chủ nhiệm
                    if (!isset($changes['managers'])) {
                        $changes['managers'] = [];
                    }
                    $changes['managers']['club_manager'] = [
                        'old' => $oldManagerId,
                        'new' => $newManagerId
                    ];

                    if ($oldManagerMember && $oldManagerMember->member_id != $newManagerId) {
                        $oldManagerMember->role = 'member';
                        $oldManagerMember->appointed_at = null;
                        $oldManagerMember->save();
                    }

                    // Kiểm tra xem member này đã có trong club chưa
                    $existingManager = $club->clubMembers()
                        ->where('member_id', $newManagerId)
                        ->first();

                    if ($existingManager) {
                        // Nếu member đã tồn tại, chỉ cập nhật role
                        $existingManager->role = 'club_manager';
                        $existingManager->appointed_at = now();
                        $existingManager->save();
                    } else {
                        // Nếu chưa tồn tại, tạo mới
                        ClubMember::create([
                            'club_id' => $club->id,
                            'member_id' => $newManagerId,
                            'role' => 'club_manager',
                            'appointed_at' => now(),
                            'joined_at' => now(),
                            'status' => 'active',
                        ]);
                    }
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
                    // Tìm người đang giữ role này (khác member hiện tại)
                    $oldRoleHolder = $club->clubMembers()
                        ->where('role', $role)
                        ->where('member_id', '!=', $member->id)
                        ->first();

                    // Kiểm tra xem member này đã có trong club chưa
                    $existingMember = $club->clubMembers()
                        ->where('member_id', $member->id)
                        ->first();

                    $oldMemberId = $oldRoleHolder ? $oldRoleHolder->member_id : null;
                    $newMemberId = $member->id;

                    // Lưu thay đổi nếu có
                    if ($oldMemberId != $newMemberId) {
                        if (!isset($changes['managers'])) {
                            $changes['managers'] = [];
                        }
                        $changes['managers'][$role] = [
                            'old' => $oldMemberId,
                            'new' => $newMemberId
                        ];
                    }

                    // Hạ người cũ xuống member nếu có
                    if ($oldRoleHolder) {
                        $oldRoleHolder->role = 'member';
                        $oldRoleHolder->appointed_at = null;
                        $oldRoleHolder->save();
                    }

                    // Cập nhật hoặc tạo mới với điều kiện đúng (club_id + member_id)
                    if ($existingMember) {
                        // Nếu member đã tồn tại, chỉ cập nhật role
                        $existingMember->role = $role;
                        $existingMember->appointed_at = now();
                        $existingMember->save();
                    } else {
                        // Nếu chưa tồn tại, tạo mới
                        ClubMember::create([
                            'club_id' => $club->id,
                            'member_id' => $member->id,
                            'role' => $role,
                            'appointed_at' => now(),
                            'joined_at' => now(),
                            'status' => 'active',
                        ]);
                    }
                } else {
                    // Đối với role 'member', chỉ tạo nếu chưa tồn tại
                    $existingMember = $club->clubMembers()
                        ->where('member_id', $member->id)
                        ->first();

                    if (!$existingMember) {
                        ClubMember::create([
                            'club_id' => $club->id,
                            'member_id' => $member->id,
                            'role' => 'member',
                            'joined_at' => now(),
                            'status' => 'active',
                        ]);
                    }
                }
            }

            // 🔹 5️⃣ Lưu log vào club_update_logs nếu có thay đổi
            if (!empty($changes)) {
                $this->logService = app(ClubUpdateLogService::class);
                // Lưu proposer_id là người đề xuất thay đổi
                $proposerId = $creatorUser ? $creatorUser->id : null;
                $this->logService->logAdminUpdate($club, $changes, auth()->id(), $proposerId);
            }

            // 🔹 6️⃣ Cập nhật trạng thái yêu cầu
            $update->status = 'approved';
            $update->save();

            // 🔹 7️⃣ Gửi thông báo
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
