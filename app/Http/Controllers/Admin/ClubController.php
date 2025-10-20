<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    // === Hiển thị danh sách CLB ===
    public function index(Request $request)
{
    $query = Club::query();

    // Nếu có từ khóa tìm kiếm
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $clubs = $query->orderBy('id', 'desc')->get();

    return view('admin.clubs.index', compact('clubs'));
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
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Club::create($validated);

        return redirect()->route('admin.clubs.index')->with('success', 'Thêm Câu lạc bộ thành công!');
    }
    //show
public function show(Club $club)
{
    $club->load(['manager', 'members.user']); // lấy thêm thông tin người quản lý và thành viên
    return view('admin.clubs.show', compact('club'));
}

    // === Form chỉnh sửa CLB ===
    public function edit(Club $club)
    {
        return view('admin.clubs.edit', compact('club'));
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
            'manager_id' => 'required|exists:users,id',
        ]);

        $club->update(['manager_id' => $validated['manager_id']]);

        return redirect()->route('admin.clubs.index')->with('success', 'Gán chủ nhiệm thành công!');
    }
}
