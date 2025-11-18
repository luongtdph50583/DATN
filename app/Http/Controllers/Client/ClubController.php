<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ClubMember;

class ClubController extends Controller
{
    public function myClubs()
    {
        $user = Auth::user();

        // Lấy profile member của user
        $member = $user->member; // nếu User->member() trả về hasOne(Member::class)

        if (!$member) {
            $memberships = collect(); // user chưa có profile member
        } else {
            // Lấy tất cả membership active của member
            $memberships = ClubMember::with('club')
                ->where('member_id', $member->id)
                ->where('status', 'active')
                ->get();
        }

        return view('client.clubs.my-clubs', compact('memberships'));
    }
}
