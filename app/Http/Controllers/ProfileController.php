<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
       public function show(Request $request)
    {
        $user = $request->user();
        $member = $user->member; // quan hệ 1-1 với bảng members

        return view('profile.show', compact('user', 'member'));
    }
    /**
     * Hiển thị form chỉnh sửa profile.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $member = $user->member; // Lấy profile member

        return view('profile.edit', [
            'user' => $user,
            'member' => $member,
        ]);
    }

    /**
     * Cập nhật thông tin profile.
     */
public function update(Request $request)
{
    $user = $request->user();
    $member = $user->member;

    $data = $request->validate([
        'student_code' => 'nullable|string|max:50',
        'gender' => 'nullable|in:male,female,other',
        'date_of_birth' => [
            'nullable',
            'date',
            'before_or_equal:' . now()->subYears(18)->format('Y-m-d'), // đủ 18 tuổi
        ],
        'address' => 'nullable|string|max:255',
        'course' => 'nullable|string|max:100',
        'major' => 'nullable|string|max:100',
        'citizen_id' => [
            'nullable',
            'digits:12',                  // phải 12 số
            'regex:/^0\d{11}$/',          // bắt đầu bằng số 0
        ],
        'issued_date' => [
            'nullable',
            'date',
            'before:today',               // trước ngày hiện tại
        ],
        'issued_place' => 'nullable|string|max:100',
        'ethnicity' => 'nullable|string|max:50',
        'phone' => [
            'nullable',
            'digits:10',                  // 10 số
            'regex:/^0\d{9}$/',           // bắt đầu bằng 0
        ],
    ]);

    // Nếu chưa có profile member, tạo mới
    if (!$member) {
        $member = $user->member()->create($data);
    } else {
        $member->update($data);
    }

    return Redirect::route('profile.show')->with('status', 'Cập nhật thành công thông tin cá nhân.');
}


    /**
     * Xóa tài khoản người dùng (vẫn thao tác trên users).
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
      public function editAvatar()
    {
        $user = auth()->user();
        return view('profile.avatar', compact('user'));
    }

    // Xử lý upload avatar
    public function updateAvatar(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Xóa avatar cũ nếu có
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        // Lưu file mới
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Cập nhật avatar thành công!');
    }
}
