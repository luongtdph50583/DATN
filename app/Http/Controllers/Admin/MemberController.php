<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use App\Exports\MembersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Member;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $clubs = collect();
        $searchName = $request->input('search_name', '');
        $clubId = $request->input('club_id', '');
        $role = $request->input('role', '');

        try {
            $query = Club::with(['members' => function ($q) {
                $q->with('user');
            }]);

            if ($searchName) {
                $query->whereHas('members.user', function ($q) use ($searchName) {
                    $q->where('name', 'like', "%{$searchName}%");
                });
            }

            if ($clubId) {
                $query->where('id', $clubId);
            }

            if ($role) {
                $query->whereHas('members', function ($q) use ($role) {
                    $q->where('role', $role);
                });
            }

            $clubs = $query->get();
            \Log::info('Danh sách clubs: ', $clubs->toArray());
        } catch (\Exception $e) {
            \Log::error('Lỗi truy vấn danh sách CLB: ' . $e->getMessage());
            $clubs = collect();
        }

        $activeTab = $request->input('activeTab', 'members');

         $members = Member::all();
             return view('admin.members.index', compact('members'));
    }

    public function exportExcel(Request $request)
    {
        $searchName = $request->input('search_name', '');
        $clubId = $request->input('club_id', '');
        $role = $request->input('role', '');

        $query = Club::with(['members' => function ($q) {
            $q->with('user');
        }]);

        if ($searchName) {
            $query->whereHas('members.user', function ($q) use ($searchName) {
                $q->where('name', 'like', "%{$searchName}%");
            });
        }

        if ($clubId) {
            $query->where('id', $clubId);
        }

        if ($role) {
            $query->whereHas('members', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $clubs = $query->get();

        return Excel::download(new MembersExport($clubs), 'danh_sach_thanh_vien_' . now()->format('YmdHis') . '.xlsx');
    }
    
        
        
         public function create()
         {
             return view('admin.members.create');
         }

         
         public function store(Request $request)
         {
             $request->validate([
                 'name' => 'required|string|max:255',
                 'email' => 'required|email|unique:members,email',
                 'phone' => 'nullable|string|max:15',
                 'address' => 'nullable|string|max:500',
                 'status' => 'required|in:active,inactive',
             ]);

             Member::create($request->all());

             return redirect()->route('admin.members.index')->with('success', 'Thành viên đã được tạo thành công.');
         }

        
         public function show(Member $member)
         {
             return view('admin.members.show', compact('member'));
         }

         
         public function edit(Member $member)
         {
             return view('admin.members.edit', compact('member'));
         }

       
         public function update(Request $request, Member $member)
         {
             $request->validate([
                 'name' => 'required|string|max:255',
                 'email' => 'required|email|unique:members,email,' . $member->id,
                 'phone' => 'nullable|string|max:15',
                 'address' => 'nullable|string|max:500',
                 'status' => 'required|in:active,inactive',
             ]);

             $member->update($request->all());

             return redirect()->route('admin.members.index')->with('success', 'Thành viên đã được cập nhật thành công.');
         }

         
         public function destroy(Member $member)
         {
             $member->delete();

             return redirect()->route('admin.members.index')->with('success', 'Thành viên đã được xóa thành công.');
         }
}