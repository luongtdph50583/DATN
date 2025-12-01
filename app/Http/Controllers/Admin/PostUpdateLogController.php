<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\PostUpdateLog;
use App\Http\Controllers\Controller;

class PostUpdateLogController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = PostUpdateLog::with([
            'post' => function ($q) {
                $q->withTrashed()->with('club'); // load cả post đã xóa mềm
            },
            'updatedBy'
        ]);

        // ✅ Lọc theo từ khóa (tiêu đề bài viết)
        if ($request->filled('keyword')) {
            $query->whereHas('post', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->keyword}%");
            });
        }

        // ✅ Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->whereHas('post', function ($q) use ($request) {
                $q->where('club_id', $request->club_id);
            });
        }

        // ✅ Lọc theo người cập nhật
        if ($request->filled('author')) {
            $query->whereHas('updatedBy', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->author}%");
            });
        }

        // Phân trang
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $logs->appends($request->query());

        // thu thập user_ids (changed_by) và club_ids (từ post chưa bị xóa)
        $userIds = $logs->pluck('changed_by')->unique()->toArray();
        $clubIds = $logs->pluck('post.club_id')->unique()->filter()->toArray();

        // Lấy club_members chính xác
        $clubMembers = ClubMember::with('member')
            ->whereIn('club_id', $clubIds)
            ->whereHas('member', function ($q) use ($userIds) {
                $q->whereIn('user_id', $userIds);
            })
            ->get();

        // Tạo map: key = user_id|club_id => role
        $roleMap = [];
        foreach ($clubMembers as $cm) {
            if ($cm->relationLoaded('member') && $cm->member) {
                $memberUserId = $cm->member->user_id;
                $roleMap[$memberUserId . '|' . $cm->club_id] = $cm->role;
            }
        }

        // Fallback join nếu không có relation member()
        if (empty($roleMap) && !empty($userIds) && !empty($clubIds)) {
            $rows = \DB::table('club_members')
                ->join('members', 'club_members.member_id', '=', 'members.id')
                ->select('members.user_id', 'club_members.club_id', 'club_members.role')
                ->whereIn('club_members.club_id', $clubIds)
                ->whereIn('members.user_id', $userIds)
                ->get();

            foreach ($rows as $r) {
                $roleMap[$r->user_id . '|' . $r->club_id] = $r->role;
            }
        }

        // Build logRoles map (log_id -> display role)
        $logRoles = [];
        foreach ($logs as $log) {
            $user = $log->updatedBy;

            if (!$user) {
                $logRoles[$log->id] = 'Không rõ';
                continue;
            }

            if ($user->role === 'admin') {
                $logRoles[$log->id] = 'Admin';
                continue;
            }

            $clubId = $log->post?->club_id;
            if ($clubId) {
                $key = $user->id . '|' . $clubId;
                $logRoles[$log->id] = $roleMap[$key] ?? 'Member';
            } else {
                $logRoles[$log->id] = 'Member';
            }
        }

        // ✅ Nạp danh sách CLB để hiển thị dropdown lọc
        $clubs = Club::orderBy('name')->get();

        return view('admin.post_update_logs.index', compact('logs', 'logRoles', 'clubs'));
    }

    public function show($id)
    {
        $log = PostUpdateLog::with(['post.club', 'updatedBy'])->findOrFail($id);

        $user = $log->updatedBy;
        $roleDisplay = 'Không rõ';

        if ($user) {

            // Admin
            if ($user->role === 'admin') {
                $roleDisplay = 'Admin';
            } else {
                // Member → lấy role đúng chuẩn member_id → user_id
                $clubId = $log->post->club_id;

                $clubMember = ClubMember::with('member.user')
                    ->where('club_id', $clubId)
                    ->whereHas('member.user', function ($q) use ($user) {
                        $q->where('id', $user->id);
                    })
                    ->first();

                $roleDisplay = $clubMember->role ?? 'Member';
            }
        }

        return view('admin.post_update_logs.show', compact('log', 'roleDisplay'));
    }





}
