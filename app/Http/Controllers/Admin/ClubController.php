<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    // === Danh sách CLB ===
    public function index(Request $request)
    {
        $query = Club::with('leader');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $clubs = $query->orderBy('id', 'desc')->get();

        return view('admin.clubs.index', compact('clubs'));
    }

    // === Form tạo CLB ===
    public function create()
    {
        $users = User::all();
        return view('admin.clubs.create', compact('users'));
    }

    // === Lưu CLB mới ===
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'field' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,pending,inactive',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // Nếu chưa chọn leader → gán mặc định là người đang đăng nhập
        $validated['leader_id'] = $request->input('leader_id') ?? Auth::id();

        Club::create($validated);

        return redirect()->route('admin.clubs.index')->with('success', 'Thêm Câu lạc bộ thành công!');
    }

    // === Xem chi tiết CLB ===
    public function show(Club $club)
    {
        $club->load(['leader', 'members.user']);
        return view('admin.clubs.show', compact('club'));
    }

    // === Form sửa CLB ===
    public function edit(Club $club)
    {
        $users = User::all();
        return view('admin.clubs.edit', compact('club', 'users'));
    }

    // === Cập nhật CLB ===
    public function update(Request $request, Club $club)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'field' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,pending,inactive',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('logo')) {
            if ($club->logo && Storage::disk('public')->exists($club->logo)) {
                Storage::disk('public')->delete($club->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $club->update($validated);

        return redirect()->route('admin.clubs.index')->with('success', 'Cập nhật CLB thành công!');
    }

    // === Xóa CLB ===
    public function destroy(Club $club)
    {
        if ($club->logo && Storage::disk('public')->exists($club->logo)) {
            Storage::disk('public')->delete($club->logo);
        }

        $club->delete();

        return redirect()->route('admin.clubs.index')->with('success', 'Xóa CLB thành công!');
    }

    // === Form gán chủ nhiệm ===
    public function assign(Club $club)
    {
        $users = User::all();
        return view('admin.clubs.assign', compact('club', 'users'));
    }

    // === Lưu chủ nhiệm ===
    public function assignStore(Request $request, Club $club)
    {
        $validated = $request->validate([
            'leader_id' => 'required|exists:users,id',
        ]);

        $club->update(['leader_id' => $validated['leader_id']]);

        return redirect()->route('admin.clubs.index')->with('success', 'Gán chủ nhiệm thành công!');
    }

    public function approve(Club $club)
{
    // Chỉ duyệt nếu đang ở trạng thái pending
    if ($club->status !== 'pending') {
        return redirect()->back()->with('warning', 'CLB này đã được duyệt hoặc bị vô hiệu hóa.');
    }

    $club->update(['status' => 'active']);

    return redirect()
        ->route('admin.clubs.index')
        ->with('success', "CLB '{$club->name}' đã được duyệt thành công!");
}

}
