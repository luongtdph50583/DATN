<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\User;
use App\Models\Member;
use App\Models\ClubMember;
use App\Models\FacultyMember;
use App\Models\ClubRequestUpdate;
use App\Models\ClubRequestMemberUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ClubRequestController extends Controller
{
    /**
     * Hiển thị form đề xuất sửa thông tin CLB
     */
    public function index($club_id)
    {
        $club = Club::with([
            'clubMembers.member.user',  // ← Đã có
            'advisorFaculty.user'
        ])->findOrFail($club_id);

        $this->authorizeClubManager($club);

        // Lấy yêu cầu đang pending
        $pendingRequest = ClubRequestUpdate::with([
            'memberUpdates.user',
            'memberUpdates.oldUser'
        ])
            ->where('club_id', $club_id)
            ->where('status', 'pending')
            ->where('user_id', Auth::id())
            ->first();

        // Lấy danh sách thành viên CLB với đầy đủ thông tin
        $clubMembers = $club->clubMembers()
            ->where('status', 'active')
            ->with('member.user')
            ->get()
            ->map(function ($clubMember) {
                $user = $clubMember->member->user;
                $member = $clubMember->member;

                return [
                    'id' => $user->id,
                    'text' => sprintf(
                        '%s (%s) - %s',
                        $user->name,
                        $member->student_code ?? 'N/A',
                        $user->email
                    ),
                    'current_role' => $clubMember->role
                ];
            });

        // Lấy BQL hiện tại (eager load đầy đủ member và user)
        $currentManagers = $club->clubMembers()
            ->whereIn('role', [
                'club_manager',
                'deputy_manager',
                'secretary',
                'treasurer',
                'event_manager',
                'communication'
            ])
            ->with('member.user')  // ← Eager load member và user
            ->get()
            ->keyBy('role');

        $managementRoles = [
            'club_manager' => 'Chủ nhiệm',
            'deputy_manager' => 'Phó Chủ nhiệm',
            'secretary' => 'Thư ký',
            'treasurer' => 'Thủ quỹ',
            'event_manager' => 'Quản lý sự kiện',
            'communication' => 'Truyền thông'
        ];

        $users = User::where('role', '!=', 'admin')->get();

        return view('client.pages.club.edit_request', compact(
            'club',
            'pendingRequest',
            'clubMembers',
            'currentManagers',
            'managementRoles',
            'users'
        ));
    }


    /**
     * Lưu đề xuất sửa thông tin CLB
     */
    public function store(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        // CHỐNG DOUBLE REQUEST
        $existingRequest = ClubRequestUpdate::where('club_id', $club_id)
            ->where('status', 'pending')
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'Bạn đã có một đề xuất đang chờ duyệt.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
            'members' => 'nullable|array',
        ]);

        $memberUpdatesPayload = [];
        $debug = [];

        // Tránh 1 user bị assign nhiều chức vụ trong 1 đề xuất
        $incomingAssignedIds = [];

        // Các role BQL trong bảng club_members
        $officerRoles = [
            'deputy_manager',
            'secretary',
            'treasurer',
            'event_manager',
            'communication'
        ];

        foreach (($request->members ?? []) as $roleKey => $item) {

            $role = $item['role'] ?? null;
            $incoming = $item['user_id'] ?? null;       // user_id gửi từ FE
            $old = $item['old_user_id'] ?? null;

            $debug[$role] = [
                'incoming' => $incoming,
                'old' => $old
            ];

            // Không thay đổi
            if ($incoming === "" || $incoming === null) {
                continue;
            }

            // REMOVE
            if ($incoming === "__remove__") {
                if ($old === null)
                    continue;

                $memberUpdatesPayload[] = [
                    'role' => $role,
                    'user_id' => null,
                    'old_user_id' => $old,
                    'action' => 'remove',
                ];
                continue;
            }

            // Lấy tên user để hiển thị trong thông báo
            $user = User::find($incoming);
            $userName = $user ? $user->name : "Người dùng";

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ CHECK TRÙNG VAI TRÒ TRONG CHÍNH ĐỀ XUẤT NÀY
            |--------------------------------------------------------------------------
            */
            if (in_array($incoming, $incomingAssignedIds)) {
                return back()->with('error', "Thành viên '{$userName}' đang được đề xuất vào nhiều chức vụ khác nhau.");
            }
            $incomingAssignedIds[] = $incoming;

            /*
            |--------------------------------------------------------------------------
            | 1️⃣b CHECK TRÙNG VAI TRÒ TRONG CLB HIỆN TẠI
            |--------------------------------------------------------------------------
            */
            $alreadyOfficerInClub = DB::table('club_members AS cm')
                ->join('members AS m', 'm.id', '=', 'cm.member_id')
                ->where('cm.club_id', $club_id)
                ->where('m.user_id', $incoming)
                ->whereIn('cm.role', $officerRoles)
                ->exists();

            if ($alreadyOfficerInClub) {
                return back()->with('error', "Thành viên '{$userName}' hiện đang giữ chức vụ quản lý trong CLB này.");
            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ CHECK NGƯỜI NÀY CÓ ĐANG LÀ QUẢN LÝ Ở CLB KHÁC KHÔNG?
            |--------------------------------------------------------------------------
            */
            if ($role === 'club_manager') {
                $isManagerElsewhere = Club::where('id', '!=', $club_id)
                    ->where('manager_id', $incoming)
                    ->exists();

                if ($isManagerElsewhere) {
                    return back()->with('error', "Thành viên '{$userName}' đang là Chủ nhiệm của CLB khác.");
                }
            } else {
                $isOfficerElsewhere = DB::table('club_members AS cm')
                    ->join('members AS m', 'm.id', '=', 'cm.member_id')
                    ->where('cm.club_id', '!=', $club_id)
                    ->where('m.user_id', '=', $incoming)
                    ->whereIn('cm.role', $officerRoles)
                    ->exists();

                if ($isOfficerElsewhere) {
                    return back()->with('error', "Thành viên '{$userName}' đang giữ chức vụ quản lý ở CLB khác.");
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ LƯU PAYLOAD
            |--------------------------------------------------------------------------
            */
            if ($incoming != $old) {
                $memberUpdatesPayload[] = [
                    'role' => $role,
                    'user_id' => $incoming,
                    'old_user_id' => $old,
                    'action' => 'assign',
                ];
            }
        }

        logger()->info("DEBUG MEMBER INPUT", $debug);
        logger()->info("DEBUG MEMBER UPDATES", $memberUpdatesPayload);

        if (empty($memberUpdatesPayload)) {
            return back()->with('info', 'Không có thay đổi nào.');
        }

        DB::transaction(function () use ($club_id, $request, $memberUpdatesPayload) {
            $update = ClubRequestUpdate::create([
                'club_id' => $club_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'reason' => $request->reason,
            ]);

            foreach ($memberUpdatesPayload as $m) {
                ClubRequestMemberUpdate::create([
                    'club_request_update_id' => $update->id,
                    'role' => $m['role'],
                    'user_id' => $m['user_id'],
                    'old_user_id' => $m['old_user_id'],
                    'action' => $m['action'],
                ]);
            }
        });

        return redirect()->route('club_manager.edit_request.index', ['club_id' => $club_id])
            ->with('success', 'Đã gửi đề xuất thay đổi thành công!');
    }









    /**
     * Kiểm tra quyền quản lý CLB
     */
    private function authorizeClubManager($club)
    {
        $user = Auth::user();
        $managedClubs = $user->getManagedClubs();

        if (!$managedClubs->contains('id', $club->id)) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }
    }
}

