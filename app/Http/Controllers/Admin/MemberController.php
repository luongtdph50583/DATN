<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use App\Exports\MembersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Member;
use App\Models\User;

class MemberController extends Controller
{
    public function index(Request $request)
{
    $query = Member::with('user')->latest();

    if ($request->filled('search_name')) {
        $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search_name . '%'));
    }
    if ($request->filled('search_email')) {
        $query->whereHas('user', fn($q) => $q->where('email', 'like', '%' . $request->search_email . '%'));
    }
    if ($request->filled('search_major')) {
        $query->where('major', 'like', '%' . $request->search_major . '%');
    }

    $members = $query->paginate(15);
    // TOP 10 THÀNH VIÊN HOẠT ĐỘNG NHIỀU CLB NHẤT TRONG THÁNG NÀY
    $topMembers = Member::with('user') 
        ->whereHas('clubs', fn($q) => $q->where('club_members.created_at', '>=', now()->subDays(30)))
        ->withCount(['clubs as clubs_count' => fn($q) => $q->where('club_members.created_at', '>=', now()->subDays(30))])
        ->orderByDesc('clubs_count')
        ->limit(10)
        ->get();
    

    return view('admin.members.index', compact('members', 'topMembers'));
}

    public function exportExcel(Request $request)
    {
        $query = Member::with('user')->latest();

        if ($request->filled('search_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_name . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        if ($request->filled('club_id')) {
            $query->whereHas('clubs', function ($q) use ($request) {
                $q->where('clubs.id', $request->club_id);
            });
        }

        $members = $query->get();

        return Excel::download(
            new MembersExport($members),
            'danh_sach_thanh_vien_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    // =================== CREATE =====================
    public function create()
    {
        $users = User::whereDoesntHave('member')->orderBy('name')->get();
        return view('admin.members.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:members,user_id',
            'student_code' => 'required|string|max:255|unique:members,student_code',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'course' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:100',
            'citizen_id' => 'nullable|string|max:20|unique:members,citizen_id',
            'issued_date' => 'nullable|date',
            'issued_place' => 'nullable|string|max:100',
            'ethnicity' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        Member::create($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Thêm thành viên thành công!');
    }

    // =================== SHOW =====================
   public function show(Member $member)
    {
        // Load tất cả quan hệ
        $member->load([
            'user',
            'clubs',
         
            'posts',
            'comments'
        ]);

        return view('admin.members.show', compact('member'));
    }

    // =================== EDIT =====================
    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    // =================== UPDATE =====================
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'student_code' => 'required|string|max:255|unique:members,student_code,' . $member->id,
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'course' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:100',
            'citizen_id' => 'nullable|string|max:20|unique:members,citizen_id,' . $member->id,
            'issued_date' => 'nullable|date',
            'issued_place' => 'nullable|string|max:100',
            'ethnicity' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $member->update($validated);

        return redirect()->route('admin.members.index')->with('success', 'Thành viên đã được cập nhật thành công.');
    }

    // =================== DELETE =====================
public function destroy(Member $member, Request $request)
{
    $reason = $request->input('delete_reason', 'Không có lý do');
    $member->delete();

    return redirect()->route('admin.members.index')
                     ->with('success', "Đã xóa thành viên \"{$member->user->name}\" vào thùng rác. Lý do: {$reason}");
}


// THÊM 3 HÀM MỚI
public function trashed()
{
    $members = Member::onlyTrashed()->with('user')->paginate(15);
    return view('admin.members.trashed', compact('members'));
}

public function restore($id)
{
    $member = Member::onlyTrashed()->findOrFail($id);
    $member->restore();

    // Chuyển hướng về index và truyền ID vừa khôi phục qua session
    return redirect()
        ->route('admin.members.index')
        ->with('success', "Đã khôi phục thành viên <strong>{$member->user->name}</strong> thành công!")
        ->with('restored_id', $member->id);
}


public function forceDelete($id)
{
    $member = Member::onlyTrashed()->findOrFail($id);
    $member->forceDelete();
    return back()->with('success', 'Xóa vĩnh viễn thành công!');
}
}
