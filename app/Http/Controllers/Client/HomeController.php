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
        $query = Club::query()->where('status', 'active');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('field', 'like', "%{$search}%");
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
