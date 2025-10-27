<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    // =================== Danh sách CLB + Tìm kiếm ===================
    public function index(Request $request)
    {
        $query = Club::with('manager'); // load manager để tránh N+1

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('field')) {
            $query->where('field', 'like', '%' . $request->field . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clubs = $query->orderBy('id', 'desc')->get();
        $users = User::all(); // dùng để gán chủ nhiệm

        return view('admin.clubs.index', compact('clubs', 'users'));
    }

    // =================== Hiển thị chi tiết CLB ===================
    public function show(Club $club)
    {
        $club->load('manager', 'members'); // load manager + members
        return view('admin.clubs.show', compact('club'));
    }

    // =================== Form tạo CLB ===================
    public function create()
    {
        $users = User::all();
        return view('admin.clubs.create', compact('users'));
    }

    // =================== Lưu CLB mới ===================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'field' => 'required',
            'status' => 'nullable|in:active,pending,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        // Upload logo nếu có
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public'); // storage/app/public/logos
            $data['logo'] = $path;
        }

        Club::create($data);

        return redirect()->route('admin.clubs.index')
            ->with('success', 'Tạo CLB thành công');
    }

    // =================== Form sửa CLB ===================
    public function edit(Club $club)
    {
        $users = User::all();
        return view('admin.clubs.edit', compact('club', 'users'));
    }

    // =================== Cập nhật CLB ===================
    public function update(Request $request, Club $club)
    {
        $request->validate([
            'name' => 'required',
            'field' => 'required',
            'status' => 'nullable|in:active,pending,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        // Upload logo mới, xóa logo cũ nếu có
        if ($request->hasFile('logo')) {
            if ($club->logo && Storage::disk('public')->exists($club->logo)) {
                Storage::disk('public')->delete($club->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $club->update($data);

        return redirect()->route('admin.clubs.index')
            ->with('success', 'Cập nhật CLB thành công');
    }

    // =================== Xóa CLB ===================
    public function destroy(Club $club)
    {
        if ($club->logo && Storage::disk('public')->exists($club->logo)) {
            Storage::disk('public')->delete($club->logo);
        }

        $club->delete();

        return back()->with('success', 'Xóa CLB thành công');
    }

    // =================== Form gán chủ nhiệm ===================
    public function assign(Club $club)
    {
        $users = User::all();
        return view('admin.clubs.assign', compact('club', 'users'));
    }

    // =================== Lưu chủ nhiệm ===================
    public function assignManager(Request $request, Club $club)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id'
        ]);

        $club->manager_id = $request->manager_id;
        $club->save();

        return redirect()->route('admin.clubs.index')
            ->with('success', 'Gán chủ nhiệm thành công!');
    }
}
