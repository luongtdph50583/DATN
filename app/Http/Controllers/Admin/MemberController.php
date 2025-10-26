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

    // Tìm theo tên
    if ($request->filled('search_name')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search_name . '%');
        });
    }

    // Tìm theo email
    if ($request->filled('search_email')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('email', 'like', '%' . $request->search_email . '%');
        });
    }

    // Tìm theo chuyên ngành
    if ($request->filled('search_major')) {
        $query->where('major', 'like', '%' . $request->search_major . '%');
    }

    $members = $query->paginate(15);

    return view('admin.members.index', compact('members'));
}

public function exportExcel(Request $request)
{
    // DÙNG CÙNG QUERY VỚI index()
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



         public function create()
    {
        $users = User::whereDoesntHave('member')->orderBy('name')->get();

        return view('admin.members.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:members,user_id',
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
             $validated = $request->validate([
       
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

             $member->update($request->all());

             return redirect()->route('admin.members.index')->with('success', 'Thành viên đã được cập nhật thành công.');
         }


         
         public function destroy(Member $member)
         {
             $member->delete();

             return redirect()->route('admin.members.index')->with('success', 'Thành viên đã được xóa thành công.');
         }
}
