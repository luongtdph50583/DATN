<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;

class ClubFundController extends Controller
{
    /**
     * Hiển thị trang quản lý quỹ CLB
     * Chỉ user có role = treasurer mới truy cập được
     */
   public function show(Club $club)
{
    $user = Auth::user();

    // Lấy profile member của user
    $member = $user->member; // member.id

    if (!$member) {
        abort(403, 'Bạn không phải là thành viên của CLB này.');
    }

    // Lấy membership trong CLB
    $membership = $club->clubMembers()
                       ->where('member_id', $member->id) // dùng member.id
                       ->where('status', 'active')
                       ->first();

    // Nếu không phải treasurer → 403
    if (!$membership || $membership->role !== 'treasurer') {
        abort(403, 'Bạn không có quyền truy cập quản lý quỹ.');
    }

    // Nếu có quyền, lấy dữ liệu quỹ
    $fund = $club->fund ?? null; // nếu dùng quan hệ hasOne Fund

    return view('client.clubs.fund', [
        'club' => $club,
        'fund' => $fund,
        'membership' => $membership,
    ]);
}

}
