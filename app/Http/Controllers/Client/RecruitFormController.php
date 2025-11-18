<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubJoinFormQuestion;
use App\Models\ClubJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecruitFormController extends Controller
{
    /**
     * Hiển thị form tạo câu hỏi tuyển thành viên
     * (Tùy chọn - có thể bỏ qua nếu không cần form tùy chỉnh)
     */
    public function create($club_id)
    {
        $club = Club::with('joinFormQuestions')->findOrFail($club_id);
        $this->authorizeClubManager($club);

        $questionTypes = [
            'short_text' => 'Trả lời ngắn',
            'long_text' => 'Trả lời dài',
            'number' => 'Số',
            'select' => 'Lựa chọn 1 đáp án',
            'checkbox' => 'Nhiều đáp án',
        ];

        return view('client.pages.club.recruit_form', compact('club', 'questionTypes'));
    }

    /**
     * Lưu form tuyển thành viên
     * (Tùy chọn - có thể bỏ qua nếu không cần form tùy chỉnh)
     */
    public function store(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:short_text,long_text,number,select,checkbox',
            'options' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'is_required' => 'nullable|boolean',
        ]);

        $options = null;
        if (in_array($validated['type'], ['select', 'checkbox'])) {
            $options = collect(preg_split('/\r\n|\r|\n/', $validated['options'] ?? ''))
                ->filter(fn ($value) => filled(trim($value)))
                ->values()
                ->all();
        }

        $nextOrder = $validated['order']
            ?? (($club->joinFormQuestions()->max('order') ?? 0) + 1);

        $club->joinFormQuestions()->create([
            'question' => $validated['question'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'options' => $options,
            'order' => $nextOrder,
            'is_required' => (bool) ($validated['is_required'] ?? false),
            'is_active' => true,
        ]);

        return redirect()->route('club_manager.recruit_form.create', ['club_id' => $club_id])
            ->with('success', 'Đã thêm câu hỏi mới cho form tuyển thành viên.');
    }

    /**
     * Hiển thị danh sách yêu cầu tham gia CLB (để xử lý)
     */
    public function index($club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        // Lấy tất cả yêu cầu tham gia CLB
        $requests = ClubJoinRequest::where('club_id', $club_id)
            ->with(['user.member', 'formAnswers.question'])
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('client.pages.club.recruit_requests', compact('club', 'requests'));
    }

    /**
     * Duyệt yêu cầu tham gia CLB từ form tuyển
     * (Tương tự ClubMemberRequestController::approve)
     */
    public function approve(Request $request, $club_id, $member_id)
    {
        // Logic tương tự ClubMemberRequestController::approve
        // Có thể refactor để dùng chung method
        return app(ClubMemberRequestController::class)->approve($request, $club_id, $member_id);
    }

    /**
     * Từ chối yêu cầu tuyển thành viên.
     */
    public function reject(Request $request, $club_id, $request_id)
    {
        return app(ClubMemberRequestController::class)->reject($request, $club_id, $request_id);
    }

    /**
     * Bật/tắt câu hỏi trong form.
     */
    public function toggle($club_id, $question_id)
    {
        $club = Club::with('joinFormQuestions')->findOrFail($club_id);
        $this->authorizeClubManager($club);

        $question = $club->joinFormQuestions()->where('id', $question_id)->firstOrFail();
        $question->is_active = !$question->is_active;
        $question->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái câu hỏi.');
    }

    /**
     * Kiểm tra quyền quản lý CLB
     */
    private function authorizeClubManager($club)
    {
        $user = Auth::user();
        $managedClubs = $user->getManagedClubs();

        if (!$managedClubs->contains('id', $club->id)) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }
    }
}

