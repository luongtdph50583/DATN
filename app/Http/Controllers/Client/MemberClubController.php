<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;
class MemberClubController extends Controller
{
    public function index(Club $club)
    {
        // Lấy tất cả thành viên active kèm profile
       $members = ClubMember::with(['member.user']) // member → user
            ->where('club_id', $club->id)
            ->where('status', 'active')
            ->get();

        // Debug
        // dd($members->toArray());

        return view('client.pages.member.showmember', compact('club', 'members'));
    }
    
public function search(Request $request, Club $club)
{
    $query = $request->input('query');

$members = ClubMember::with('member.user')
    ->where('club_id', $club->id)
    ->where('status', 'active')
    ->where(function ($q) use ($query) {
        $q->whereHas('member.user', function ($uq) use ($query) {
            $uq->where('name', 'like', "%{$query}%");
        })->orWhereHas('member', function ($mq) use ($query) {
            $mq->where('student_code', 'like', "%{$query}%");
        });
    })
    ->get();


    // Trả về view partial
    return view('client.pages.member.partials.members_table', compact('members'))->render();
}
public function show(Club $club, $clubMemberId)
{
    // Lấy member theo club_member id
    $clubMember = ClubMember::with(['member.user'])
        ->where('club_id', $club->id)
        ->where('id', $clubMemberId)
        ->firstOrFail();

    return view('client.pages.member.show_member_detail', compact('club', 'clubMember'));
}
}
