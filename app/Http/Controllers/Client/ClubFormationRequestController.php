<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClubRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClubFormationRequestController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $requests = ClubRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.pages.member.MyRequests', compact('requests'));
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
        return view('client.pages.member.formation_requestClub');
    }

    /**
     * Lưu yêu cầu thành lập CLB
     */
    public function store(Request $request)
    {
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
            'rule' => 'nullable|string',
            'member_limit' => 'nullable|integer|min:1',
        ]);

        $clubRequest = new ClubRequest();
        $clubRequest->user_id = Auth::id();
        $clubRequest->name = $request->name;
        $clubRequest->slogan = $request->slogan;
        $clubRequest->description = $request->description;
        $clubRequest->purpose = $request->purpose;
        $clubRequest->field = $request->field;
        $clubRequest->plan = $request->plan;
        $clubRequest->email = $request->email;
        $clubRequest->phone = $request->phone;
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
