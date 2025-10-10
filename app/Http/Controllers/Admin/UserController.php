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
         protected function checkAdmin()
         {
             if (!Auth::check() || Auth::user()->role !== 'admin') {
                 return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
             }
             return null;
         }

         public function index(Request $request)
         {
              if ($redirect = $this->checkAdmin()) {
              return $redirect;
          }

          $query = User::query();

          // Lọc theo tên
          if ($request->filled('name')) {
              $query->where('name', 'like', '%' . $request->name . '%');
          }

          // Lọc theo email
          if ($request->filled('email')) {
              $query->where('email', 'like', '%' . $request->email . '%');
          }

          // Lọc theo vai trò
          if ($request->filled('role')) {
              $query->where('role', $request->role);
          }

          $users = $query->paginate(10)->withQueryString();

          return view('admin.users.index', compact('users'));
         }

          public function toggleStatus(Request $request, User $user)
      {
          if ($redirect = $this->checkAdmin()) {
              return $redirect;
          }

          $newStatus = $user->status === 'active' ? 'inactive' : 'active';
          $user->update(['status' => $newStatus]);

          return response()->json([
              'success' => true,
              'status' => $newStatus,
              'message' => "Tài khoản đã được cập nhật trạng thái thành '$newStatus'.",
          ]);
      }

         public function create()
         {
             if ($redirect = $this->checkAdmin()) {
                 return $redirect;
             }

             return view('admin.users.create');
         }

         public function store(Request $request)
         {
             if ($redirect = $this->checkAdmin()) {
                 return $redirect;
             }

             $validated = $request->validate([
                 'name' => 'required|string|max:255',
                 'email' => 'required|string|email|max:255|unique:users',
                 'password' => 'required|string|min:8|confirmed',
                 'role' => 'required|in:admin,club_manager,member',
                 'status' => 'required|in:active,inactive',
                 'phone' => 'nullable|string|max:15',
                 'student_id' => 'nullable|string|max:10|unique:users',
                 'department' => 'nullable|string|max:255',
                 'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
             ]);

             $data = $validated;
             $data['password'] = Hash::make($validated['password']);
             if ($request->hasFile('avatar')) {
                 $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
             }

             User::create($data);

             return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã được tạo.');
         }

         public function edit(User $user)
         {
             if ($redirect = $this->checkAdmin()) {
                 return $redirect;
             }

             return view('admin.users.edit', compact('user'));
         }

         public function update(Request $request, User $user)
         {
             if ($redirect = $this->checkAdmin()) {
                 return $redirect;
             }

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
             if ($redirect = $this->checkAdmin()) {
                 return $redirect;
             }

             if ($user->avatar) {
                 Storage::disk('public')->delete($user->avatar);
             }
             $user->delete();
             return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã được xóa.');
         }
     }