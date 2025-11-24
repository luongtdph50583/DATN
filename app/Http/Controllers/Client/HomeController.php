<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
class HomeController extends Controller
{

    public function index()
    {
        // Có thể gửi dữ liệu banner, info,... nếu cần
        return view('client.pages.home.index');
    }

    // Hiển thị danh sách CLB
 public function showClubs(Request $request)
{
    $user = $request->user();
    $member = $user->member; // quan hệ 1-1 với bảng members

    $query = Club::query()->where('status', 'active');

    // Lọc theo từ khóa search
    if ($request->has('search') && $request->search) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('field', 'like', "%{$search}%");
        });
    }

    // Lọc những CLB mà member đã tham gia
    if ($member) {
        $joinedClubIds = $member->clubs()->pluck('clubs.id')->toArray();
        if (!empty($joinedClubIds)) {
            $query->whereNotIn('id', $joinedClubIds);
        }
    }

    $clubs = $query->orderBy('name')->paginate(12);

    return view('client.pages.member.index', compact('clubs'));
}




    // Trang chi tiết CLB
 // Controller
public function show(Club $club) // chú ý số ít $club
{
    return view('client.pages.member.showClub', compact('club'));
}

}
