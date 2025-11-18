<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ClubMemberController extends Controller
{
    /**
     * Hiển thị thông tin CLB cho member (không phải manager)
     */
    public function view($club_id)
    {
        $club = Club::with([
            'members.user',
            'clubMembers.member.user',
            'posts' => function($q) {
                $q->where('status', 'approved')
                  ->where('is_visible', true)
                  ->latest()
                  ->limit(10);
            },
            'events' => function($q) {
                $q->where('status', 'approved')
                  ->latest()
                  ->limit(5);
            }
        ])->findOrFail($club_id);

        // Kiểm tra user có phải là thành viên của CLB này không
        $user = Auth::user();
        $isMember = \App\Models\ClubMember::where('club_id', $club_id)
            ->whereHas('member', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->exists();

        if (!$isMember && $user->role !== 'admin') {
            abort(403, 'Bạn không phải thành viên của CLB này.');
        }

        return view('client.pages.club.view', compact('club'));
    }
}

