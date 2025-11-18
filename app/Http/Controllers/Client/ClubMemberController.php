<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\Post;
use App\Models\ClubJoinRequest;
use App\Models\ClubJoinFormAnswer;
use App\Models\ClubRecruitmentForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Hiển thị form đăng ký tham gia CLB
     */
    public function showJoinForm($club_id)
    {
        $club = Club::findOrFail($club_id);
        $user = Auth::user();

        // Kiểm tra đã là thành viên chưa
        $isMember = \App\Models\ClubMember::where('club_id', $club_id)
            ->whereHas('member', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->exists();

        if ($isMember) {
            return redirect()->route('club.member.view', ['club_id' => $club_id])
                ->with('info', 'Bạn đã là thành viên của CLB này.');
        }

        // Kiểm tra đã có yêu cầu đang chờ chưa
        $pendingRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['approved', 'rejected', 'cancelled'])
            ->first();

        if ($pendingRequest) {
            return redirect()->route('club.member.view', ['club_id' => $club_id])
                ->with('info', 'Bạn đã có yêu cầu tham gia đang chờ xử lý.');
        }

        // Lấy form tuyển thành viên mặc định
        $form = $club->defaultRecruitmentForm ?? $club->recruitmentForms()->where('is_active', true)->first();
        
        if (!$form) {
            return redirect()->back()->with('error', 'CLB này chưa có form tuyển thành viên.');
        }

        $questions = $form->activeQuestions;

        return view('client.pages.club.join_form', compact('club', 'form', 'questions'));
    }

    /**
     * Xử lý đăng ký tham gia CLB
     */
    public function submitJoinRequest(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $user = Auth::user();

        // Kiểm tra đã là thành viên chưa
        $isMember = \App\Models\ClubMember::where('club_id', $club_id)
            ->whereHas('member', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->exists();

        if ($isMember) {
            return back()->with('error', 'Bạn đã là thành viên của CLB này.');
        }

        // Kiểm tra đã có yêu cầu đang chờ chưa
        $pendingRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['approved', 'rejected', 'cancelled'])
            ->first();

        if ($pendingRequest) {
            return back()->with('error', 'Bạn đã có yêu cầu tham gia đang chờ xử lý.');
        }

        // Lấy form tuyển thành viên
        $form = $club->defaultRecruitmentForm ?? $club->recruitmentForms()->where('is_active', true)->first();
        
        if (!$form) {
            return back()->with('error', 'CLB này chưa có form tuyển thành viên.');
        }

        $questions = $form->activeQuestions;
        $answers = $request->input('answers', []);

        // Validate answers
        foreach ($questions as $question) {
            if ($question->is_required && empty($answers[$question->id])) {
                return back()->with('error', "Vui lòng trả lời câu hỏi: {$question->question}");
            }
        }

        DB::transaction(function () use ($club_id, $user, $form, $questions, $answers, $request) {
            // Tạo join request
            $joinRequest = ClubJoinRequest::create([
                'club_id' => $club_id,
                'user_id' => $user->id,
                'status' => 'pending_interview',
                'reason' => $request->input('reason'),
                'requested_at' => now(),
            ]);

            // Lưu answers
            foreach ($questions as $question) {
                if (isset($answers[$question->id]) && !empty($answers[$question->id])) {
                    $answerValue = is_array($answers[$question->id]) 
                        ? json_encode($answers[$question->id]) 
                        : $answers[$question->id];

                    ClubJoinFormAnswer::create([
                        'request_id' => $joinRequest->id,
                        'question_id' => $question->id,
                        'answer' => $answerValue,
                    ]);
                }
            }
        });

        return redirect()->route('club.member.view', ['club_id' => $club_id])
            ->with('success', 'Đã gửi yêu cầu tham gia CLB thành công! Vui lòng chờ CLB xử lý.');
    }
}

