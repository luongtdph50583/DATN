<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() || (Auth::check() && Auth::user()->role !== 'admin')) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }

        $clubs = [];
        $searchName = $request->input('search_name', '');
        $clubId = $request->input('club_id', '');
        $role = $request->input('role', '');

        try {
            $query = Club::with('members.user');

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
        } catch (\Exception $e) {
            \Log::error('Lỗi truy vấn danh sách CLB: ' . $e->getMessage());
        }

        $activeTab = $request->input('activeTab', 'members');

        return view('index', compact('clubs', 'searchName', 'clubId', 'role'))->with('activeTab', $activeTab);
    }
}