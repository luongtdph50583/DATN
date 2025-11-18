<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\ClubJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class InterviewController extends Controller
{
    /**
     * Hiển thị danh sách phỏng vấn và điểm danh
     */
    public function index(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        // Lấy danh sách yêu cầu tham gia CLB có trạng thái cần phỏng vấn
        $query = ClubJoinRequest::where('club_id', $club_id)
            ->whereIn('status', ['pending_interview', 'waiting_attendance'])
            ->with(['user.member', 'interviewer', 'formAnswers.question']);

        // Filter theo search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('user.member', function($q2) use ($search) {
                    $q2->where('student_code', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }

        // Filter theo status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter theo interviewer
        if ($request->filled('interviewer_id')) {
            $query->where('interviewer_id', $request->input('interviewer_id'));
        }

        $requests = $query->orderBy('requested_at', 'desc')->get();

        // Lấy danh sách người phỏng vấn (có thể là thành viên ban quản lý)
        $interviewers = User::whereHas('member.clubMembers', function($q) use ($club_id) {
                $q->where('club_id', $club_id)
                  ->whereIn('club_members.role', ['club_manager', 'deputy_manager', 'secretary']);
            })
            ->get();

        return view('client.pages.club.interviews', compact('club', 'requests', 'interviewers'));
    }

    /**
     * Lên lịch phỏng vấn
     */
    public function schedule(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $validated = $request->validate([
            'request_id' => 'required|exists:club_join_requests,id',
            'scheduled_at' => 'required|date|after:now',
            'location' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('id', $validated['request_id'])
            ->where('status', 'pending_interview')
            ->firstOrFail();

        $joinRequest->status = 'waiting_attendance';
        $joinRequest->interview_scheduled_at = $validated['scheduled_at'];
        $joinRequest->interview_location = $validated['location'] ?? null;
        $joinRequest->interview_note = $validated['note'] ?? null;
        $joinRequest->interviewer_id = Auth::id();
        $joinRequest->interview_result = 'pending';
        $joinRequest->handled_by = Auth::id();
        $joinRequest->save();

        // Tạo bản ghi lịch phỏng vấn trong bảng riêng (để lưu lịch sử)
        \App\Models\ClubInterviewSchedule::create([
            'request_id' => $joinRequest->id,
            'club_id' => $club_id,
            'interviewer_id' => Auth::id(),
            'scheduled_at' => $validated['scheduled_at'],
            'location' => $validated['location'] ?? null,
            'status' => 'scheduled',
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Đã lên lịch phỏng vấn thành công.');
    }

    /**
     * Điểm danh phỏng vấn (đánh dấu đã hoàn thành)
     */
    public function attendance(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $validated = $request->validate([
            'request_id' => 'required|exists:club_join_requests,id',
            'attendance_status' => 'required|in:completed,no_show',
            'interview_note' => 'nullable|string|max:500',
        ]);

        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('id', $validated['request_id'])
            ->where('status', 'waiting_attendance')
            ->firstOrFail();

        $joinRequest->status = 'waiting_approval';
        if ($validated['attendance_status'] === 'completed') {
            $joinRequest->interview_result = 'pass';
        } else {
            $joinRequest->interview_result = 'no_show';
        }
        $joinRequest->interview_completed_at = now();

        $joinRequest->interview_note = $validated['interview_note'] ?? $joinRequest->interview_note;
        $joinRequest->handled_by = Auth::id();
        $joinRequest->handled_at = now();
        $joinRequest->save();

        // Cập nhật bản ghi lịch phỏng vấn
        $interviewSchedule = \App\Models\ClubInterviewSchedule::where('request_id', $joinRequest->id)
            ->where('status', 'scheduled')
            ->first();
        
        if ($interviewSchedule) {
            $interviewSchedule->status = $validated['attendance_status'] === 'completed' ? 'completed' : 'no_show';
            $interviewSchedule->interview_result = $validated['attendance_status'];
            $interviewSchedule->completed_at = now();
            $interviewSchedule->save();
        }

        return redirect()->back()
            ->with('success', 'Đã cập nhật trạng thái phỏng vấn thành công.');
    }

    /**
     * Đánh dấu yêu cầu đang liên hệ phỏng vấn
     */
    public function contact(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $data = $request->validate([
            'request_id' => 'required|exists:club_join_requests,id',
            'note' => 'nullable|string|max:255',
        ]);

        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('id', $data['request_id'])
            ->where('status', 'pending_interview')
            ->firstOrFail();

        $joinRequest->status = 'pending_interview'; // Giữ nguyên, chỉ thêm note
        $joinRequest->handled_by = Auth::id();
        $joinRequest->note = $data['note'] ?? $joinRequest->note;
        $joinRequest->save();

        return redirect()->back()
            ->with('success', 'Đã đánh dấu đang liên hệ phỏng vấn.');
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

