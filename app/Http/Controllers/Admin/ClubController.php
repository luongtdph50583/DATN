<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    /**
     * Danh sách CLB
     */
    public function index(Request $request)
    {
        $query = Club::with('manager');

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $clubs = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.clubs.index', compact('clubs'));
    }

    /**
     * Form tạo CLB
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email')->get();
        $managers = Club::pluck('manager_id')->toArray(); // danh sách user đã là chủ nhiệm
        return view('admin.clubs.create', compact('users', 'managers'));
    }

    /**
     * Lưu CLB mới
     */
   public function store(Request $request)
{
    $request->validate([
        'name'          => 'required|string|max:255|unique:clubs,name',
        'field'         => 'required|string|max:255',
        'manager_id'    => 'required|exists:users,id',
        'email'         => 'nullable|email',
        'phone'         => 'nullable|string|max:20',
        'member_limit'  => 'nullable|integer|min:1',
        'logo'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'description'   => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {
        // Upload logo nếu có
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('uploads/logos', 'public');
        }

        // Tạo CLB mới
        $club = Club::create([
            'name'          => $request->name,
            'field'         => $request->field,
            'description'   => $request->description,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'member_limit'  => $request->member_limit,
            'logo'          => $logoPath,
            'manager_id'    => $request->manager_id,
            'status'        => 'active',
        ]);

        // Gán chủ nhiệm vào bảng club_members
     ClubMember::create([
    'club_id' => $club->id,
    'member_id' => $club->manager_id,
    'role' => 'leader',
    'joined_at' => now(),
]);

        DB::commit();
        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Thêm CLB mới thành công và đã gán chủ nhiệm!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Lỗi: ' . $e->getMessage());
    }
}

    /**
     * Sửa CLB
     */
    public function edit(Club $club)
{
    // Lấy danh sách thành viên của CLB này (kèm user info)
    $members = \App\Models\ClubMember::with('user')
        ->where('club_id', $club->id)
        ->get();

    // Lấy danh sách ID user đang là chủ nhiệm CLB khác
    $managerIds = \App\Models\Club::whereNotNull('manager_id')
        ->where('id', '!=', $club->id)
        ->pluck('manager_id')
        ->toArray();

    return view('admin.clubs.edit', compact('club', 'members', 'managerIds'));
}

    /**
     * Cập nhật CLB
     */
   public function update(Request $request, Club $club)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:clubs,name,' . $club->id,
        'field' => 'nullable|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'member_limit' => 'nullable|integer|min:1',
        'status' => 'required|in:active,inactive',
        'manager_id' => 'nullable|exists:users,id',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // 🖼️ Upload logo (nếu có)
    if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('clubs', 'public');
        $club->logo = $path;
    }

    // Lưu các thông tin cơ bản
    $club->fill([
        'name' => $request->name,
        'field' => $request->field,
        'email' => $request->email,
        'phone' => $request->phone,
        'member_limit' => $request->member_limit,
        'status' => $request->status,
        'description' => $request->description,
    ]);

    // 🧠 Nếu có chọn chủ nhiệm mới
    if ($request->filled('manager_id')) {
        $newManagerId = $request->manager_id;

        // Kiểm tra người này có trong CLB chưa
        $isMember = \App\Models\ClubMember::where('club_id', $club->id)
            ->where('member_id', $newManagerId)
            ->exists();

        if (!$isMember) {
            return back()->withErrors(['manager_id' => 'Người được chọn chưa là thành viên của CLB này!']);
        }

        // Nếu có chủ nhiệm cũ → đổi vai trò về 'member'
        if ($club->manager_id && $club->manager_id != $newManagerId) {
            \App\Models\ClubMember::where('club_id', $club->id)
                ->where('member_id', $club->manager_id)
                ->update(['role' => 'member']);
        }

        // Cập nhật chủ nhiệm mới trong bảng CLB
        $club->manager_id = $newManagerId;

        // Cập nhật vai trò trong club_members
        \App\Models\ClubMember::where('club_id', $club->id)
            ->where('member_id', $newManagerId)
            ->update(['role' => 'leader']);
    } else {
        // Nếu bỏ trống thì bỏ chủ nhiệm
        $club->manager_id = null;
    }

    $club->save();

     return redirect()->route('admin.clubs.index')->with('success', 'Cập nhật CLB thành công!');    
}

/**
 * Hiển thị chi tiết 1 CLB
 */
public function show(Club $club)
{
    // Lấy chủ nhiệm (manager)
    $manager = \App\Models\User::find($club->manager_id);

    // Lấy toàn bộ thành viên (bao gồm cả leader)
    $members = \App\Models\ClubMember::with('member')
        ->where('club_id', $club->id)
        ->get();

    return view('admin.clubs.show', compact('club', 'manager', 'members'));
}

public function assign($clubId)
{
    $club = Club::with('manager')->findOrFail($clubId);

    // Lấy tất cả user đang active
    $users = User::where('status', 'active')->get();

    // Lấy danh sách user đang làm chủ nhiệm CLB khác
    $managerIds = Club::whereNotNull('manager_id')
        ->where('id', '!=', $clubId) // loại bỏ CLB hiện tại
        ->pluck('manager_id')
        ->toArray();

    return view('admin.clubs.assign', compact('club', 'users', 'managerIds'));
}

public function assignStore(Request $request, $clubId)
{
    $club = Club::findOrFail($clubId);

    $validated = $request->validate([
        'manager_id' => 'required|exists:users,id',
    ]);

    // Kiểm tra nếu người này đã là chủ nhiệm của CLB khác
    $isAlreadyManager = Club::where('manager_id', $validated['manager_id'])
        ->where('id', '!=', $clubId)
        ->exists();

    if ($isAlreadyManager) {
        return back()->withErrors(['manager_id' => 'Người này hiện đang là chủ nhiệm của một CLB khác!']);
    }

    // Nếu CLB đã có chủ nhiệm cũ → đổi role về member
    if ($club->manager_id) {
        \App\Models\ClubMember::where('club_id', $clubId)
            ->where('member_id', $club->manager_id)
            ->update(['role' => 'member']);
    }

    // Cập nhật chủ nhiệm mới
    $club->update(['manager_id' => $validated['manager_id']]);

    // Đảm bảo chủ nhiệm mới nằm trong bảng club_members
    $member = \App\Models\ClubMember::where('club_id', $clubId)
        ->where('member_id', $validated['manager_id'])
        ->first();

    if (!$member) {
        \App\Models\ClubMember::create([
            'club_id' => $clubId,
            'member_id' => $validated['manager_id'],
            'role' => 'leader',
            'joined_at' => now(),
        ]);
    } else {
        $member->update(['role' => 'leader']);
    }

    return redirect()->route('admin.clubs.show', $clubId)
        ->with('success', 'Gán chủ nhiệm CLB thành công!');
}

    /**
     * Xóa CLB
     */
    public function destroy(Club $club)
    {
        if ($club->logo) {
            Storage::disk('public')->delete($club->logo);
        }

        $club->delete();
        return redirect()->route('admin.clubs.index')->with('success', 'Đã xóa CLB!');
    }
}
