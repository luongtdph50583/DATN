<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Fund;
use App\Models\Post;
use App\Models\Event;
use App\Models\Media;
use App\Models\Member;
use App\Models\ClubPlan;
use App\Models\Document;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\ClubUpdateLog;
use App\Models\FundTransaction;
use Illuminate\Validation\Rule;
use App\Jobs\SendNotificationJob;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ClubUpdateLogService;
use App\Notifications\CustomNotification;
use Illuminate\Validation\ValidationException;


class ClubController extends Controller
{
    /**
     * Hiển thị danh sách CLB
     */
    public function index()
    {
        $clubs = Club::with(['manager.member.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.clubs.index', compact('clubs'));

    }
    public function show($id)
    { {
            // Lấy thông tin CLB, kèm số lượng bài viết & sự kiện đã duyệt
            $club = Club::with([
                'manager',
                // 'advisorFaculty.user' // Giảng viên đỡ đầu
            ])->withCount([
                        'posts as total_posts' => function ($query) {
                            $query->where('status', 'approved')
                                ->where('is_visible', true);
                        },
                        'events as total_events' => function ($query) {
                            $query->where('status', 'approved');
                        }
                    ])->findOrFail($id);

            // Lấy danh sách thành viên CLB, phân trang 10 bản ghi/trang
            $clubMembers = ClubMember::with(['member.user'])
                ->where('club_id', $id)
                ->paginate(10);

            // 3 bài viết nổi bật gần đây
            $featuredPosts = Post::where('club_id', $id)
                ->where('status', 'approved')
                ->where('is_visible', true)
                ->orderBy('is_featured', 'desc') // ưu tiên bài nổi bật
                ->orderBy('created_at', 'desc')  // mới tạo gần đây trước
                ->take(3)
                ->get();

            // 3 sự kiện sắp diễn ra
            $upcomingEvents = Event::where('club_id', $id)
                ->where('status', 'approved')
                ->where('start_time', '>=', now()) // sự kiện chưa bắt đầu
                ->orderBy('start_time', 'asc')     // sự kiện sớm nhất trước
                ->take(3)
                ->get();

            // Trả về view chi tiết CLB
            return view('admin.clubs.show', compact(
                'club',
                'clubMembers',
                'featuredPosts',
                'upcomingEvents'
            ));
        }
    }

    public function filterMembers(Request $request, Club $club)
    {
        $status = $request->get('status');
        $keyword = $request->get('keyword');

        $members = ClubMember::with(['member.user'])
            ->where('club_id', $club->id)
            ->where('role', 'member')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->whereHas('member.user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    })->orWhereHas('member', function ($memberQuery) use ($keyword) {
                        $memberQuery->where('student_code', 'like', "%{$keyword}%");
                    });
                });
            })
            ->orderByDesc('joined_at')
            ->get();

        $html = view('admin.clubs.partials.members_rows', [
            'members' => $members,
            'startIndex' => 1,
        ])->render();

        return response()->json([
            'html' => $html,
            'count' => $members->count(),
        ]);
    }
    public function edit($id)
    {
        $club = Club::findOrFail($id);

        // Lấy danh sách thành viên trong CLB hiện tại
        $clubMembers = ClubMember::with('member.user')
            ->where('club_id', $id)
            ->get()
            ->filter(function ($item) {
                if (!$item->member || !$item->member->user) {
                    return false;
                }

                // Kiểm tra xem member này có từng giữ chức vụ khác ngoài 'member' ở CLB khác không
                $hasOtherRole = ClubMember::where('member_id', $item->member_id)
                    ->where('role', '!=', 'member')
                    ->where('club_id', '!=', $item->club_id)
                    ->exists();

                // Giữ lại nếu KHÔNG có chức vụ nào khác ngoài 'member'
                return !$hasOtherRole;
            })
            ->map(function ($item) {
                return [
                    'role' => $item->role,
                    'member' => [
                        'id' => $item->member->id,
                        'student_code' => $item->member->student_code,
                        'user' => [
                            'name' => $item->member->user->name,
                            'email' => $item->member->user->email,
                        ],
                    ],
                ];
            })
            ->values()
            ->toArray();

        return view('admin.clubs.edit', compact('club', 'clubMembers'));
    }

    public function searchJson(Request $request)
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status');

        $clubs = Club::with(['manager.member.user'])
            ->when($keyword, function ($query, $keyword) {
                $query->where('name', 'like', "%$keyword%")
                    ->orWhere('field', 'like', "%$keyword%")
                    ->orWhereHas('manager.member.user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->get();

        return response()->json($clubs);
    }


    public function searchMembers(Request $request)
    {
        try {
            $keyword = $request->get('q');

            $members = Member::with('user')
                ->whereHas('user')
                ->whereDoesntHave('clubs', function ($q) {
                    $q->whereIn('club_members.role', [
                        'club_manager',
                        'deputy_manager',
                        'secretary',
                        'treasurer',
                        'event_manager',
                        'communication',
                    ]);
                })
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($sub) use ($keyword) {
                        $sub->whereHas('user', function ($query) use ($keyword) {
                            $query->where('name', 'like', "%$keyword%")
                                ->orWhere('email', 'like', "%$keyword%");
                        })->orWhere('student_code', 'like', "%$keyword%");
                    });
                })
                ->get();

            $results = $members->map(function ($m) {
                $name = $m->user->name ?? 'Không rõ';
                $email = $m->user->email ?? '—';
                $code = $m->student_code ?? '—';
                return [
                    'id' => $m->id,
                    'text' => "{$name} ({$code}) - {$email}",
                ];
            });

            return response()->json($results);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi tìm kiếm thành viên:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['error' => 'Đã xảy ra lỗi server'], 500);
        }
    }
    public function destroy(Request $request, $id)
    {
        $club = Club::with(['posts', 'documents'])->findOrFail($id);
        $reason = $request->input('delete_reason', 'Vi phạm nội quy');

        if ($club->status === 'active') {
            return redirect()->back()->withErrors(['error' => 'Chỉ có thể xóa CLB không hoạt động.']);
        }

        DB::beginTransaction();

        try {
            // ✅ Xóa bài viết và media liên quan
            foreach ($club->posts as $post) {
                $mediaList = Media::withTrashed()
                    ->where('related_type', 'post')
                    ->where('related_id', $post->id)
                    ->get();

                foreach ($mediaList as $media) {
                    $filePath = storage_path('app/public/' . $media->file_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $media->forceDelete();
                }

                if ($post->thumbnail && file_exists(storage_path('app/public/' . $post->thumbnail))) {
                    unlink(storage_path('app/public/' . $post->thumbnail));
                }

                $post->forceDelete();
            }

            // ✅ Xóa tài liệu liên quan
            foreach ($club->documents as $doc) {
                $filePath = storage_path('app/public/' . $doc->file_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $doc->forceDelete();
            }

            // ✅ Lấy thông tin chủ nhiệm
            $manager = DB::table('club_members as cm')
                ->join('members as m', 'cm.member_id', '=', 'm.id')
                ->join('users as u', 'm.user_id', '=', 'u.id')
                ->where('cm.club_id', $club->id)
                ->where('cm.role', 'club_manager')
                ->select('u.id as user_id', 'u.name', 'u.email')
                ->first();

            if ($manager) {
                Log::info("Debug manager before delete: ID={$manager->user_id}, Name={$manager->name}, Email={$manager->email}");

                $batchId = uniqid('club_deleted_');

                SendNotificationJob::dispatch(
                    $manager->user_id,
                    "CLB bị xóa",
                    "Câu lạc bộ '{$club->name}' đã bị xóa. Lý do: {$reason}",
                    "both",
                    $batchId,
                    true
                );
            } else {
                Log::warning("Club ID {$club->id} không có chủ nhiệm khi xóa!");
            }

            // ✅ Xóa thành viên CLB
            DB::table('club_members')->where('club_id', $club->id)->delete();

            // ✅ Xóa chính CLB
            $club->delete();

            DB::commit();

            return redirect()->route('admin.clubs.index')->with('success', 'CLB đã được xóa thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Lỗi khi xóa CLB", [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return redirect()->back()->withErrors(['error' => 'Đã xảy ra lỗi khi xóa CLB: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Club $club, ClubUpdateLogService $logService)
    {
        // ✅ Validate dữ liệu chỉ cho phép sửa ban quản lý và trạng thái
        $request->validate([
            'status' => 'required|in:active,inactive',
            'managers' => 'nullable|array',
            'managers.*' => 'nullable|exists:members,id',
        ]);

        $changes = []; // mảng lưu các thay đổi

        // ✅ Kiểm tra và lưu thay đổi trạng thái
        if ($club->status !== $request->status) {
            $changes['status'] = [
                'old' => $club->status,
                'new' => $request->status
            ];
            $club->status = $request->status;
            $club->save();
        }

        // ✅ Cập nhật ban quản lý
        $roles = [
            'club_manager',
            'deputy_manager',
            'secretary',
            'treasurer',
            'event_manager',
            'communication'
        ];

        foreach ($roles as $role) {
            $newMemberId = $request->managers[$role] ?? null;
            $newMemberId = $newMemberId ? intval($newMemberId) : null;

            $current = $club->members()->wherePivot('role', $role)->first();

            if ($newMemberId) {
                $newMember = Member::find($newMemberId);
                if (!$newMember || !$newMember->user) {
                    return redirect()->back()
                        ->withErrors(['managers.' . $role => 'Thành viên chưa hợp lệ hoặc chưa có user liên kết.'])
                        ->withInput();
                }

                $currentMemberId = $current?->id;
                if ($currentMemberId !== $newMemberId) {
                    // ✅ Ghi thay đổi
                    $changes['managers'][$role] = [
                        'old' => $currentMemberId,
                        'new' => $newMemberId
                    ];
                }

                // ✅ Hạ người cũ nếu khác người mới
                if ($current && $current->id != $newMemberId) {
                    $club->members()->updateExistingPivot($current->id, [
                        'role' => 'member',
                        'appointed_at' => null,
                        'updated_at' => now(),
                    ]);
                }

                // ✅ Gán role mới
                $club->members()->syncWithoutDetaching([
                    $newMemberId => [
                        'role' => $role,
                        'appointed_at' => now(),
                        'joined_at' => now(),
                        'status' => 'active',
                    ]
                ]);

                // ✅ Nếu là chủ nhiệm → cập nhật manager_id
                if ($role === 'club_manager') {
                    $club->manager_id = $newMember->user_id;
                    $club->save();
                }

            } else {
                // ✅ Nếu bỏ trống → hạ người cũ
                if ($current) {
                    $changes['managers'][$role] = [
                        'old' => $current->id,
                        'new' => null
                    ];

                    $club->members()->updateExistingPivot($current->id, [
                        'role' => 'member',
                        'appointed_at' => null,
                        'updated_at' => now(),
                    ]);

                    if ($role === 'club_manager') {
                        $club->manager_id = null;
                        $club->save();
                    }
                }
            }
        }

        // ✅ Lưu log nếu có thay đổi
        if (!empty($changes)) {
            ClubUpdateLog::create([
                'club_id' => $club->id,
                'admin_id' => auth()->id(),
                'proposer_id' => null,
                'changed_fields' => json_encode($changes, JSON_UNESCAPED_UNICODE),
                'type' => 'admin',
                'status' => 'approved',
            ]);
        }

        return redirect()->route('admin.clubs.index')->with('success', 'Cập nhật thành công');
    }





    // public function update(Request $request, Club $club)
    // {
    //     // ✅ Validate dữ liệu
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255', Rule::unique('clubs', 'name')->ignore($club->id)],
    //         'field' => 'nullable|string|max:255',
    //         'location' => 'nullable|string|max:255',
    //         'email' => 'nullable|email|max:255',
    //         'phone' => 'nullable|string|max:20',
    //         'member_limit' => 'nullable|integer|min:1',
    //         'status' => 'required|in:active,inactive',
    //         'description' => 'nullable|string',
    //         'rules' => 'nullable|string',
    //         'managers' => 'nullable|array',
    //         'managers.*' => 'nullable|exists:members,id',
    //         'logo' => 'nullable|image|max:2048',
    //     ]);

    //     // ✅ Check trùng người trong ban quản lý
    //     if ($request->filled('managers')) {
    //         $managerValues = array_filter($request->managers);
    //         if (count($managerValues) !== count(array_unique($managerValues))) {
    //             return redirect()->back()
    //                 ->withErrors(['managers' => 'Ban quản lý không thể có cùng một người ở nhiều chức vụ.'])
    //                 ->withInput();
    //         }
    //     }

    //     // ✅ Upload logo nếu có
    //     if ($request->hasFile('logo')) {
    //         $club->logo = $request->file('logo')->store('logos', 'public');
    //     }

    //     // ✅ Cập nhật thông tin cơ bản CLB
    //     $club->update([
    //         'name' => $request->name,
    //         'field' => $request->field,
    //         'location' => $request->location,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'member_limit' => $request->member_limit,
    //         'status' => $request->status,
    //         'description' => $request->description,
    //         'rules' => $request->rules,
    //     ]);

    //     // ✅ Cập nhật ban quản lý
    //     $roles = [
    //         'club_manager',
    //         'deputy_manager',
    //         'secretary',
    //         'treasurer',
    //         'event_manager',
    //         'communication'
    //     ];

    //     foreach ($roles as $role) {
    //         $newMemberId = $request->managers[$role] ?? null;
    //         $newMemberId = $newMemberId ? intval($newMemberId) : null;

    //         $current = $club->members()->wherePivot('role', $role)->first();

    //         if ($newMemberId) {
    //             $newMember = Member::find($newMemberId);

    //             if (!$newMember || !$newMember->user) {
    //                 return redirect()->back()
    //                     ->withErrors(['managers.' . $role => 'Thành viên chưa hợp lệ hoặc chưa có user liên kết.'])
    //                     ->withInput();
    //             }

    //             // Kiểm tra giữ vai trò ở CLB khác
    //             $conflict = ClubMember::where('member_id', $newMemberId)
    //                 ->where('club_id', '!=', $club->id)
    //                 ->where('role', '!=', 'member')
    //                 ->exists();

    //             if ($conflict) {
    //                 return redirect()->back()
    //                     ->withErrors(['managers.' . $role => 'Thành viên đang giữ chức vụ khác ở CLB khác.'])
    //                     ->withInput();
    //             }

    //             // Hạ người cũ nếu khác người mới
    //             if ($current && $current->id != $newMemberId) {
    //                 $club->members()->updateExistingPivot($current->id, [
    //                     'role' => 'member',
    //                     'appointed_at' => null,
    //                     'updated_at' => now(),
    //                 ]);
    //             }

    //             // Gán role mới
    //             $club->members()->syncWithoutDetaching([
    //                 $newMemberId => [
    //                     'role' => $role,
    //                     'appointed_at' => now(),
    //                     'joined_at' => now(),
    //                     'status' => 'active',
    //                 ]
    //             ]);

    //             // Nếu là chủ nhiệm → cập nhật manager_id trong bảng clubs
    //             if ($role === 'club_manager') {
    //                 $club->manager_id = $newMember->user_id;
    //                 $club->save();
    //             }

    //         } else {
    //             // Nếu bỏ trống → hạ người cũ
    //             if ($current) {
    //                 $club->members()->updateExistingPivot($current->id, [
    //                     'role' => 'member',
    //                     'appointed_at' => null,
    //                     'updated_at' => now(),
    //                 ]);

    //                 if ($role === 'club_manager') {
    //                     $club->manager_id = null;
    //                     $club->save();
    //                 }
    //             }
    //         }
    //     }

    //     return redirect()->route('admin.clubs.index')->with('success', 'Cập nhật CLB thành công!');
    // }

    public function create()
    {
        $clubMembers = Member::with('user')
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->get();

        return view('admin.clubs.create', compact('clubMembers'));
    }

    // ✅ Xử lý thêm CLB
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // ✅ Validate dữ liệu
            $request->validate([
                'name' => 'required|string|max:255|unique:clubs,name',
                'slogan' => 'nullable|string|max:255',
                'field' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'description' => 'nullable|string',
                'rules' => 'nullable|string',
                'status' => 'required|in:active,inactive',
                'member_limit' => 'nullable|integer|min:1',
                'logo' => 'nullable|image|max:2048',
                'managers' => 'nullable|array',
                'managers.*' => 'nullable|exists:members,id',
            ]);

            // ✅ Upload logo nếu có
            $logoPath = $request->hasFile('logo')
                ? $request->file('logo')->store('logos', 'public')
                : null;

            // ✅ Kiểm tra trùng người trong ban quản lý
            if ($request->filled('managers')) {
                $managerValues = array_filter($request->managers);
                if (count($managerValues) !== count(array_unique($managerValues))) {
                    return redirect()->back()
                        ->withErrors(['managers' => 'Ban quản lý không thể có cùng một người ở nhiều chức vụ.'])
                        ->withInput();
                }
            }

            // ✅ Tạo CLB
            $club = Club::create([
                'name' => $request->name,
                'slogan' => $request->slogan, // 👈 thêm dòng này
                'field' => $request->field,
                'location' => $request->location,
                'email' => $request->email,
                'phone' => $request->phone,
                'description' => $request->description,
                'rules' => $request->rules,
                'status' => $request->status,
                'member_limit' => $request->member_limit,
                'logo' => $logoPath,
                'founded_at' => now(),
            ]);

            // ✅ Lưu ban quản lý
            $roles = [
                'club_manager',
                'deputy_manager',
                'secretary',
                'treasurer',
                'event_manager',
                'communication'
            ];

            foreach ($roles as $role) {
                $memberId = $request->managers[$role] ?? null;
                if ($memberId) {
                    $member = Member::with('user')->find($memberId);
                    if (!$member || !$member->user) {
                        DB::rollBack();
                        return redirect()->back()
                            ->withErrors(['managers' => "Thành viên ID {$memberId} không hợp lệ hoặc chưa liên kết user."])
                            ->withInput();
                    }

                    // Kiểm tra giữ vai trò ở CLB khác
                    $conflict = ClubMember::where('member_id', $memberId)
                        ->whereIn('role', $roles)
                        ->exists();

                    if ($conflict) {
                        DB::rollBack();
                        return redirect()->back()
                            ->withErrors(['managers' => "Thành viên {$member->user->name} đang giữ chức vụ quản lý ở CLB khác."])
                            ->withInput();
                    }

                    // Nếu là Chủ nhiệm → cập nhật manager_id
                    if ($role === 'club_manager') {
                        $club->manager_id = $member->user_id;
                        $club->save();
                    }

                    // Lưu vào bảng pivot
                    ClubMember::create([
                        'club_id' => $club->id,
                        'member_id' => $memberId,
                        'role' => $role,
                        'joined_at' => now(),
                        'appointed_at' => now(),
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.clubs.index')
                ->with('success', 'Thêm câu lạc bộ thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Lỗi khi thêm CLB', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()])
                ->withInput();
        }
    }








    public function searchAllMembers(Request $request)
    {
        $query = $request->get('q', '');

        $members = Member::with('user')
            ->when($query, function ($q) use ($query) {
                $q->whereHas('user', function ($sub) use ($query) {
                    $sub->where('name', 'like', "%$query%")
                        ->orWhere('email', 'like', "%$query%");
                })->orWhere('student_code', 'like', "%$query%");
            })
            ->select('id', 'student_code', 'user_id')
            ->limit(10)
            ->get();

        return response()->json(
            $members->map(function ($m) {
                return [
                    'id' => $m->id,
                    'text' => $m->user->name . ' (' . $m->student_code . ')',
                ];
            })
        );
    }

    public function removeMember(Request $request, Club $club, Member $member)
    {
        // Kiểm tra xem member có trong club không
        if (!$club->members()->where('member_id', $member->id)->exists()) {
            return redirect()->back()->with('error', 'Thành viên không thuộc CLB này.');
        }

        // Không cho xóa chủ nhiệm
        $pivot = $club->members()->where('member_id', $member->id)->first()->pivot;
        if ($pivot->role === 'club_manager') {
            return redirect()->back()->with('error', 'Không thể xóa Chủ nhiệm khỏi CLB.');
        }

        // Xóa khỏi pivot table
        $club->members()->detach($member->id);

        return redirect()->back()->with('success', 'Đã xóa thành viên khỏi CLB.');
    }




}
