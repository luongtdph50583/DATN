<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClubJoinRequest;
use Illuminate\Http\Request;
use App\Models\ClubRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
 use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\User;

class ClubFormationRequestController extends Controller
{
   
public function index()
{
    $user = Auth::user();

    $formationRequests = ClubRequest::where('user_id', $user->id)
        ->get()
        ->map(fn($r) => $r->setAttribute('type', 'formation'));

    $joinRequests = ClubJoinRequest::where('user_id', $user->id)
        ->with('club')
        ->get()
        ->map(fn($r) => $r->setAttribute('type', 'join'));

    $allRequests = $formationRequests->concat($joinRequests)
        ->sortByDesc('created_at');

    // Phân trang thủ công
    $page = request()->get('page', 1);
    $perPage = 10;
    $paginated = new LengthAwarePaginator(
        $allRequests->forPage($page, $perPage)->values(),
        $allRequests->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('client.pages.member.MyRequests', [
        'requests' => $paginated
    ]);
}




    // Xem chi tiết 1 yêu cầu thành lập CLB
    public function show(ClubRequest $request)
    {
        // Chỉ user gửi mới xem được
        if ($request->user_id != Auth::id()) {
            abort(403, 'Bạn không có quyền xem yêu cầu này.');
        }
        return view('client.pages.member.showClubRequest', compact('request'));
    }
    /**
     * Hiển thị form gửi yêu cầu thành lập CLB
     */
    public function create()
    {
         $advisors = User::whereHas('facultyMember', function ($q) {
        $q->where('verified', true)
          ->where('status', 'active');
    })->get();
        return view('client.pages.member.formation_requestClub', compact('advisors'));
    }

    /**
     * Lưu yêu cầu thành lập CLB
     */
  public function store(Request $request)
{
    $user = Auth::user();
    $member = $user->member;

    if (!$member) {
        return redirect()->back()->with('error', 'Bạn chưa có hồ sơ thành viên.');
    }

    // Các role quản lý không được gửi yêu cầu thành lập CLB mới
    $managerRoles = ['club_manager', 'deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication'];

    $existingRoles = $member->clubMemberships() // Quan hệ ClubMember
        ->whereIn('role', $managerRoles)
        ->exists();

    if ($existingRoles) {
        return redirect()->back()->with('error', 'Bạn đang giữ một chức vụ quản lý trong CLB khác, không thể gửi yêu cầu thành lập CLB mới.');
    }

    // Validate form
    $request->validate([
        'name' => 'required|string|max:255',
        'slogan' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'purpose' => 'nullable|string',
        'field' => 'nullable|string|max:255',
        'plan' => 'nullable|string',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'logo' => 'nullable|image|max:2048',
        'advisor_id' => 'nullable|exists:users,id',
         'approval_document' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        'rule' => 'nullable|string',
        'member_limit' => 'nullable|integer|min:1',
    ]);

    // Tạo yêu cầu thành lập CLB
    $clubRequest = new ClubRequest();
    $clubRequest->user_id = $user->id;
    $clubRequest->name = $request->name;
    $clubRequest->slogan = $request->slogan;
    $clubRequest->description = $request->description;
    $clubRequest->purpose = $request->purpose;
    $clubRequest->field = $request->field;
    $clubRequest->plan = $request->plan;
    $clubRequest->email = $request->email;
    $clubRequest->phone = $request->phone;
     $clubRequest->approval_document = $request->approval_document;
    $clubRequest->advisor_id = $request->advisor_id;
    $clubRequest->rule = $request->rule;
    $clubRequest->member_limit = $request->member_limit;

    if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('club_logos', 'public');
        $clubRequest->logo = $path;
    }

    $clubRequest->save();

    return redirect()->route('formation_request.create')
        ->with('success', 'Yêu cầu thành lập CLB đã được gửi thành công! Chúng tôi sẽ xem xét và liên hệ với bạn.');
}

}
