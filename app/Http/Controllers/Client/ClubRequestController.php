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
            'clubMembers.member.user',
            'advisorFaculty.user'
        ])->findOrFail($club_id);

        $this->authorizeClubManager($club);

        // Lấy yêu cầu đang pending nếu có
        $pendingRequest = ClubRequestUpdate::with(['memberUpdates.user'])
            ->where('club_id', $club_id)
            ->where('status', 'pending')
            ->where('user_id', Auth::id())
            ->first();

        // Lấy danh sách users để chọn manager/advisor
        $users = User::where('role', '!=', 'admin')->get();
        $facultyMembers = FacultyMember::with('user')->get();

        return view('client.pages.club.edit_request', compact('club', 'pendingRequest', 'users', 'facultyMembers'));
    }

    /**
     * Lưu đề xuất sửa thông tin CLB
     */
    public function store(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        // Kiểm tra đã có request pending chưa
        $existingRequest = ClubRequestUpdate::where('club_id', $club_id)
            ->where('status', 'pending')
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRequest) {
            return redirect()->back()
                ->with('error', 'Bạn đã có một đề xuất đang chờ duyệt. Vui lòng chờ admin xử lý.');
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'field' => 'nullable|string|max:255',
            'member_limit' => 'nullable|integer|min:1',
            'manager_id' => 'nullable|exists:users,id',
            'advisor_id' => 'nullable|exists:faculty_members,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rules' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'reason' => 'required|string|max:1000',
            'members' => 'nullable|array',
            'members.*.user_id' => 'nullable|exists:users,id',
            'members.*.role' => 'required_with:members.*.user_id|in:club_manager,deputy_manager,secretary,treasurer,event_manager,communication,member',
        ]);

        $memberUpdatesPayload = collect($validated['members'] ?? [])
            ->filter(fn ($member) => !empty($member['user_id']))
            ->values()
            ->all();

        DB::transaction(function () use ($club_id, $validated, $request, $memberUpdatesPayload) {
            // Tạo yêu cầu cập nhật
            $clubRequestUpdate = new ClubRequestUpdate();
            $clubRequestUpdate->club_id = $club_id;
            $clubRequestUpdate->user_id = Auth::id();
            $clubRequestUpdate->name = $validated['name'] ?? null;
            $clubRequestUpdate->slogan = $validated['slogan'] ?? null;
            $clubRequestUpdate->description = $validated['description'] ?? null;
            $clubRequestUpdate->field = $validated['field'] ?? null;
            $clubRequestUpdate->member_limit = $validated['member_limit'] ?? null;
            $clubRequestUpdate->manager_id = $validated['manager_id'] ?? null;
            $clubRequestUpdate->advisor_id = $validated['advisor_id'] ?? null;
            $clubRequestUpdate->advisor_status = 'pending';
            $clubRequestUpdate->email = $validated['email'] ?? null;
            $clubRequestUpdate->phone = $validated['phone'] ?? null;
            $clubRequestUpdate->rules = $validated['rules'] ?? null;
            $clubRequestUpdate->location = $validated['location'] ?? null;
            $clubRequestUpdate->reason = $validated['reason'];
            $clubRequestUpdate->status = 'pending';

            // Xử lý logo
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $path = $file->store('club_logos', 'public');
                $clubRequestUpdate->logo = $path;
            }

            $clubRequestUpdate->save();

            // Lưu thành viên ban quản lý đề xuất
            if (!empty($memberUpdatesPayload)) {
                foreach ($memberUpdatesPayload as $memberData) {
                    ClubRequestMemberUpdate::create([
                        'club_request_update_id' => $clubRequestUpdate->id,
                        'user_id' => $memberData['user_id'],
                        'role' => $memberData['role'],
                    ]);
                }
            }
        });

        return redirect()->route('club_manager.edit_request.index', ['club_id' => $club_id])
            ->with('success', 'Đã gửi đề xuất sửa thông tin CLB thành công! Vui lòng chờ admin duyệt.');
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

