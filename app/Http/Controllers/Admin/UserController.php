<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     */
    public function index()
{
    $users = User::orderByRaw("
        CASE
            WHEN role = 'admin' THEN 1
            WHEN role = 'club_manager' THEN 2
            WHEN role = 'member' THEN 3
            ELSE 4
        END
    ")->paginate(10);

    return view('admin.users.index', compact('users'));
}


    /**
     * Hiển thị form thêm người dùng mới
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới vào CSDL
     * (Chỉ thêm được tài khoản admin)
     */
    public function store(Request $request)
    {
        // ✅ Validate dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'status' => 'required|in:active,inactive',
            'avatar' => 'nullable|image|max:2048',
        ]);

        // ✅ Bắt buộc role là admin
        $validated['role'] = 'admin';
        $validated['password'] = bcrypt($validated['password']);

        // ✅ Upload ảnh nếu có
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Tạo tài khoản quản trị viên thành công!');
    }

    /**
     * Hiển thị thông tin chi tiết người dùng
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa người dùng
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'role' => 'required|in:admin,club_manager,member',
        'status' => 'required|in:active,inactive',
    ]);

    // Cập nhật chỉ hai trường được phép
    $user->update([
        'role' => $validated['role'],
        'status' => $validated['status'],
    ]);

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'Cập nhật vai trò và trạng thái người dùng thành công!');
}


    /**
     * Xóa mềm người dùng
     */
    public function softDelete(Request $request, User $user)
{
    $request->validate([
        'delete_reason' => 'required|string|max:1000'
    ]);

    $user->update([
        'deleted_by' => auth()->id(),
        'delete_reason' => $request->delete_reason
    ]);

    $user->delete();

    return redirect()->route('admin.users.index')
        ->with('success', 'Đã xóa tài khoản thành công! Đã lưu lý do.');
}

    /**
     * Danh sách người dùng đã xóa
     */
    public function deleted()
{
    $users = User::onlyTrashed()->paginate(10);

    return view('admin.users.deleted', compact('users'));
}

public function destroy(User $user)
{
    return $this->softDelete(request(), $user);
}

    /**
     * Khôi phục người dùng bị xóa
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Khôi phục người dùng thành công!');
    }

    /**
     * Xóa vĩnh viễn người dùng
     */
    public function forceDelete(User $user)
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->forceDelete();

        return redirect()
            ->route('admin.users.deleted')
            ->with('danger', 'Đã xóa vĩnh viễn người dùng.');
    }

    /**
     * Đổi trạng thái (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'message' => 'Cập nhật trạng thái thành công!'
        ]);
    }
}
