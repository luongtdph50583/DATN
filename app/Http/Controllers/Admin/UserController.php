<?php
     namespace App\Http\Controllers\Admin;

     use App\Http\Controllers\Controller;
     use App\Models\User;
     use Illuminate\Http\Request;
     use Illuminate\Support\Facades\Auth;
     use Illuminate\Support\Facades\Hash;
     use Illuminate\Support\Facades\Storage;
   

    class UserController extends Controller
    {
        
        public function index(Request $request)
        {
            
          $query = User::query();
          // Lọc theo tên
          if ($request->filled('name')) {
              $query->where('name', 'like', '%' . $request->name . '%');
          }
          // Lọc theo email
          if ($request->filled('email')) {
              $query->where('email', 'like', '%' . $request->email . '%');
          }
          if ($request->filled('status')) {
              $query->where('status', $request->status);
          }
          if ($request->filled('student_id')) {
              $query->where('student_id', 'like', '%' . $request->student_id . '%');
          }
          // Lọc theo vai trò
          if ($request->filled('role')) {
              $query->where('role', $request->role);
          }

          $users = $query->paginate(10)->withQueryString();

          return view('admin.users.index', compact('users'));
          $users = User::all();
            return view('admin.users.index', compact('users'));

        
        }

          // app/Http/Controllers/Admin/UserController.php

public function toggleStatus(User $user)
{
    $newStatus = $user->status === 'active' ? 'inactive' : 'active';
    $user->update(['status' => $newStatus]);

    $action = $newStatus === 'active' ? 'mở khóa' : 'khóa';
    return redirect()->route('admin.users.index')
        ->with('success', "Tài khoản đã được {$action} thành công!");
}

         public function create()
         {

            

             return view('admin.users.create');
         }

         public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'role' => 'required|in:admin,club_manager,member',
        'status' => 'required|in:active,inactive',
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $data = $request->except(['password_confirmation']);
    $data['password'] = Hash::make($request->password);

    if ($request->hasFile('avatar')) {
        $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
    }

    User::create($data);

    return redirect()->route('admin.users.index')
        ->with('success', 'Thêm người dùng thành công!');
}

         public function edit(User $user)
         {

             

             return view('admin.users.edit', compact('user'));
         }

         public function update(Request $request, User $user)
         {

             

             $validated = $request->validate([
                 'name' => 'required|string|max:255',
                 'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                 'password' => 'nullable|string|min:8|confirmed',
                 'role' => 'required|in:admin,club_manager,member',
                 'status' => 'required|in:active,inactive',
                 'phone' => 'nullable|string|max:15',
                 'student_id' => 'nullable|string|max:10|unique:users,student_id,' . $user->id,
                 'department' => 'nullable|string|max:255',
                 'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
             ]);

             $data = $validated;
             if ($validated['password']) {
                 $data['password'] = Hash::make($validated['password']);
             } else {
                 unset($data['password']);
             }

             if ($request->hasFile('avatar')) {
                 if ($user->avatar) {
                     Storage::disk('public')->delete($user->avatar);
                 }
                 $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
             }

             $user->update($data);

             return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã được cập nhật.');
         }

         public function destroy(User $user)
         {

             

             if ($user->avatar) {
                 Storage::disk('public')->delete($user->avatar);
             }
             $user->delete();
             return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã được xóa.');
         }
         function show(User $user)
        {
             return view('admin.users.show', compact('user'));
        }
        
}
     