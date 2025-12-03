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
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        /* ============================================================
         * DEBUG: raw request từ client
         * ============================================================ */
        \Log::info('RAW REQUEST', ['all' => $request->all()]);

        $status = $request->input('status');
        $rejectedReason = $request->input('rejected_reason');

        $update = ClubRequestUpdate::with([
            'memberUpdates',
            'club.clubMembers',
            'club.advisorFaculty',
            'proposer'
        ])->findOrFail($id);

        $club = $update->club;
        $creatorUser = $update->proposer;

        /* ============================================================
         * DEBUG: update object
         * ============================================================ */
        \Log::info('DEBUG UPDATE OBJECT', [
            'update_id' => $update->id,
            'club_id' => $club->id,
            'manager_id_from_update' => $update->manager_id,
            'memberUpdates' => $update->memberUpdates->map(fn($m) => [
                'role' => $m->role,
                'user_id' => $m->user_id,
                'old_user_id' => $m->old_user_id,
                'action' => $m->action ?? null,
            ])->toArray()
        ]);

        /* ====================================================================
         * APPROVE REQUEST
         * ==================================================================== */
        if ($status === 'approved') {

            // 1) Validate tên CLB không trùng
            if (!is_null($update->name)) {
                $existingClub = Club::where('name', $update->name)
                    ->where('id', '!=', $club->id)
                    ->first();
                if ($existingClub) {
                    return back()->withErrors(['name' => 'Tên CLB đã tồn tại ở CLB khác.']);
                }
            }

            // 2) Validate không trùng thành viên BQL (bỏ qua club_manager)
            $managerIds = [];
            foreach ($update->memberUpdates as $memberUpdate) {
                if ($memberUpdate->role === 'club_manager')
                    continue;

                $uid = $memberUpdate->user_id;

                // ✅ Bỏ qua nếu là remove action hoặc null
                if ($uid === null || $memberUpdate->action === 'remove')
                    continue;

                if (in_array($uid, $managerIds)) {
                    return back()->withErrors(['members' => 'Các thành viên ban quản lý không được trùng nhau.']);
                }

                $managerIds[] = $uid;

                $member = Member::where('user_id', $uid)->first();
                if ($member) {
                    $roleExists = ClubMember::where('role', $memberUpdate->role)
                        ->whereHas('club', fn($q) => $q->where('id', '!=', $club->id))
                        ->where('member_id', $member->id)
                        ->exists();

                    if ($roleExists) {
                        return back()->withErrors([
                            'members' => "Thành viên '{$memberUpdate->user->name}' đã giữ chức vụ '{$memberUpdate->role}' ở CLB khác."
                        ]);
                    }
                }
            }

            // 3) Validate Chủ nhiệm riêng
            $managerUpdate = $update->memberUpdates->firstWhere('role', 'club_manager');

            \Log::info('DEBUG managerUpdate (from memberUpdates)', [
                'exists' => $managerUpdate ? true : false,
                'managerUpdate' => $managerUpdate ? [
                    'user_id' => $managerUpdate->user_id,
                    'old_user_id' => $managerUpdate->old_user_id,
                    'action' => $managerUpdate->action ?? null
                ] : null,
            ]);

            if ($managerUpdate) {
                $managerIncomingUserId = $managerUpdate->user_id;
                $managerAction = $managerUpdate->action ?? null;

                // Validate khi assign chủ nhiệm mới
                if ($managerAction === 'assign' && !is_null($managerIncomingUserId)) {
                    if (in_array($managerIncomingUserId, $managerIds)) {
                        return back()->withErrors(['manager' => 'Chủ nhiệm không được trùng với thành viên ban quản lý khác.']);
                    }

                    $managerMember = Member::where('user_id', $managerIncomingUserId)->first();
                    if ($managerMember) {
                        $managerRoleExists = ClubMember::where('role', 'club_manager')
                            ->whereHas('club', fn($q) => $q->where('id', '!=', $club->id))
                            ->where('member_id', $managerMember->id)
                            ->exists();

                        if ($managerRoleExists) {
                            return back()->withErrors([
                                'manager' => "Người được chỉ định làm Chủ nhiệm đã là Chủ nhiệm ở CLB khác."
                            ]);
                        }
                    }
                }
            }

            // 4) Chuẩn bị log
            $changes = [];

            if (!is_null($update->advisor_id) && $update->advisor_status === 'approved') {
                if ($update->advisor_id != $club->advisor_id) {
                    $changes['advisor_id'] = ['old' => $club->advisor_id, 'new' => $update->advisor_id];
                    $club->advisor_id = $update->advisor_id;
                }
            }

            $fields = ['name', 'slogan', 'description', 'field', 'member_limit', 'email', 'phone', 'logo', 'rules', 'location'];
            foreach ($fields as $field) {
                if (!is_null($update->$field) && $club->$field != $update->$field) {
                    $changes[$field] = ['old' => $club->$field, 'new' => $update->$field];
                    $club->$field = $update->$field;
                }
            }

            /* -----------------------------------------------------------------------
             * 5) XỬ LÝ CHỦ NHIỆM (club_manager)
             * ---------------------------------------------------------------------- */

            \Log::info('DEBUG BEFORE MANAGER_APPLY', [
                'club_current_manager' => $club->manager_id,
            ]);

            if ($managerUpdate) {
                $managerAction = $managerUpdate->action ?? null;

                // ✅ REMOVE case
                if ($managerAction === 'remove') {
                    $oldManagerUserId = $managerUpdate->old_user_id;

                    if ($oldManagerUserId) {
                        $oldManagerMember = Member::where('user_id', $oldManagerUserId)->first();

                        if ($oldManagerMember) {
                            $oldManagerRecord = $club->clubMembers()
                                ->where('member_id', $oldManagerMember->id)
                                ->where('role', 'club_manager')
                                ->first();

                            if ($oldManagerRecord) {
                                $oldManagerRecord->role = 'member';
                                $oldManagerRecord->appointed_at = null;
                                $oldManagerRecord->save();

                                $changes['managers']['club_manager'] = [
                                    'old' => $oldManagerMember->id,
                                    'new' => null
                                ];

                                \Log::info('DEBUG MANAGER REMOVED', [
                                    'old_user_id' => $oldManagerUserId,
                                    'old_member_id' => $oldManagerMember->id
                                ]);
                            }
                        }

                        // Set club.manager_id = null
                        if ($club->manager_id == $oldManagerUserId) {
                            $club->manager_id = null;
                        }
                    }
                }

                // ✅ ASSIGN case
                elseif ($managerAction === 'assign') {
                    $newManagerUserId = $managerUpdate->user_id;
                    $oldManagerUserId = $managerUpdate->old_user_id;

                    if (!is_null($newManagerUserId)) {
                        $newManagerMember = Member::where('user_id', $newManagerUserId)->first();

                        if ($newManagerMember) {
                            $newManagerId = $newManagerMember->id;

                            // Demote người cũ nếu có
                            if ($oldManagerUserId) {
                                $oldManagerMember = Member::where('user_id', $oldManagerUserId)->first();

                                if ($oldManagerMember && $oldManagerMember->id != $newManagerId) {
                                    $oldManagerRecord = $club->clubMembers()
                                        ->where('member_id', $oldManagerMember->id)
                                        ->where('role', 'club_manager')
                                        ->first();

                                    if ($oldManagerRecord) {
                                        $oldManagerRecord->role = 'member';
                                        $oldManagerRecord->appointed_at = null;
                                        $oldManagerRecord->save();
                                    }
                                }
                            }

                            // Promote người mới
                            $existing = $club->clubMembers()->where('member_id', $newManagerId)->first();
                            if ($existing) {
                                $existing->role = 'club_manager';
                                $existing->appointed_at = now();
                                $existing->save();
                            } else {
                                ClubMember::create([
                                    'club_id' => $club->id,
                                    'member_id' => $newManagerId,
                                    'role' => 'club_manager',
                                    'appointed_at' => now(),
                                    'joined_at' => now(),
                                    'status' => 'active',
                                ]);
                            }

                            $changes['managers']['club_manager'] = [
                                'old' => $oldManagerUserId ? Member::where('user_id', $oldManagerUserId)->first()?->id : null,
                                'new' => $newManagerId
                            ];

                            // Update club.manager_id
                            $club->manager_id = $newManagerUserId;

                            \Log::info('DEBUG MANAGER ASSIGNED', [
                                'new_user_id' => $newManagerUserId,
                                'new_member_id' => $newManagerId,
                                'old_user_id' => $oldManagerUserId
                            ]);
                        }
                    }
                }
            } else {
                \Log::info('DEBUG no managerUpdate found — keep existing manager', [
                    'club_manager' => $club->manager_id
                ]);
            }

            // Lưu club
            $club->save();

            /* =======================================================================
             * 6) Xử lý các vai trò khác
             * ======================================================================= */
            foreach ($update->memberUpdates as $memberUpdate) {

                if ($memberUpdate->role === 'club_manager')
                    continue;

                $role = $memberUpdate->role;
                $action = $memberUpdate->action;
                $newUserId = $memberUpdate->user_id;
                $oldUserId = $memberUpdate->old_user_id;

                \Log::info("DEBUG Processing role: {$role}", [
                    'action' => $action,
                    'new_user_id' => $newUserId,
                    'old_user_id' => $oldUserId
                ]);

                // ✅ REMOVE case
                if ($action === 'remove') {
                    if ($oldUserId) {
                        $oldMember = Member::where('user_id', $oldUserId)->first();

                        if ($oldMember) {
                            $record = $club->clubMembers()
                                ->where('member_id', $oldMember->id)
                                ->where('role', $role)
                                ->first();

                            if ($record) {
                                $record->role = 'member';
                                $record->appointed_at = null;
                                $record->save();

                                $changes['managers'][$role] = [
                                    'old' => $oldMember->id,
                                    'new' => null
                                ];

                                \Log::info("DEBUG REMOVED role {$role}", [
                                    'old_user_id' => $oldUserId,
                                    'old_member_id' => $oldMember->id
                                ]);
                            }
                        }
                    }
                    continue;
                }

                // ✅ ASSIGN case
                if ($action === 'assign' && !is_null($newUserId)) {
                    $newMember = Member::where('user_id', $newUserId)->first();

                    if ($newMember) {
                        // Demote người cũ nếu khác người mới
                        if ($oldUserId && $oldUserId != $newUserId) {
                            $oldMember = Member::where('user_id', $oldUserId)->first();

                            if ($oldMember && $oldMember->id != $newMember->id) {
                                $oldRoleHolder = $club->clubMembers()
                                    ->where('member_id', $oldMember->id)
                                    ->where('role', $role)
                                    ->first();

                                if ($oldRoleHolder) {
                                    $oldRoleHolder->role = 'member';
                                    $oldRoleHolder->appointed_at = null;
                                    $oldRoleHolder->save();
                                }
                            }
                        }

                        // Promote người mới
                        $existing = $club->clubMembers()->where('member_id', $newMember->id)->first();
                        if ($existing) {
                            $existing->role = $role;
                            $existing->appointed_at = now();
                            $existing->save();
                        } else {
                            ClubMember::create([
                                'club_id' => $club->id,
                                'member_id' => $newMember->id,
                                'role' => $role,
                                'appointed_at' => now(),
                                'joined_at' => now(),
                                'status' => 'active',
                            ]);
                        }

                        $changes['managers'][$role] = [
                            'old' => $oldUserId ? Member::where('user_id', $oldUserId)->first()?->id : null,
                            'new' => $newMember->id
                        ];

                        \Log::info("DEBUG ASSIGNED role {$role}", [
                            'new_user_id' => $newUserId,
                            'new_member_id' => $newMember->id,
                            'old_user_id' => $oldUserId
                        ]);
                    }
                }
            }

            /* =======================================================================
             * 7) Lưu log thay đổi
             * ======================================================================= */
            if (!empty($changes)) {
                \Log::info('DEBUG CHANGES TO LOG', $changes);

                $this->logService = app(ClubUpdateLogService::class);
                $this->logService->logAdminUpdate(
                    $club,
                    $changes,
                    auth()->id(),
                    $creatorUser?->id
                );
            }

            /* =======================================================================
             * 8) Hoàn tất
             * ======================================================================= */
            $update->status = 'approved';
            $update->save();

            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu cập nhật CLB được duyệt",
                "Yêu cầu cập nhật CLB '{$club->name}' được duyệt.",
                'both',
                uniqid(),
                false
            );
        }

        /* ====================================================================
         * REJECT REQUEST
         * ==================================================================== */ elseif ($status === 'rejected') {
            $update->status = 'rejected';
            $update->note = $rejectedReason;
            $update->save();

            SendNotificationJob::dispatch(
                $creatorUser->id,
                "Yêu cầu cập nhật CLB bị từ chối",
                "Lý do: {$rejectedReason}",
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
