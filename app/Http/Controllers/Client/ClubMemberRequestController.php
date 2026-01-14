<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\Member;
use App\Models\ClubMember;
use App\Models\ClubJoinRequest;
use App\Models\ClubInterviewSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJobClient
;
use App\Notifications\InterviewScheduledNotification;
use Carbon\Carbon;

class ClubMemberRequestController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu tham gia CLB
     */
    public function index(Request $req, $club_id)   
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $query = ClubJoinRequest::where('club_id', $club_id)
            ->with(['user.member', 'interviewer', 'handler', 'formAnswers.question']);

        // Filter theo search
        if ($req->filled('search')) {
            $search = $req->input('search');
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
        if ($req->filled('status')) {
            $query->where('status', $req->input('status'));
        }

        // Filter theo ngày
        if ($req->filled('from_date')) {
            $query->whereDate('requested_at', '>=', $req->input('from_date'));
        }
        if ($req->filled('to_date')) {
            $query->whereDate('requested_at', '<=', $req->input('to_date'));
        }

        $requests = $query->orderBy('requested_at', 'desc')->get();

        // Lấy danh sách người phỏng vấn (quản lý CLB)
        $interviewers = User::whereHas('member', function($q) use ($club_id) {
            $q->whereHas('clubMembers', function($q2) use ($club_id) {
                $q2->where('club_id', $club_id)
                  ->whereIn('role', ['club_manager', 'deputy_manager']);
            });
        })->get();

        return view('client.pages.club.member_requests', compact('club', 'requests', 'interviewers'));
    }

    /**
     * Hiển thị chi tiết yêu cầu (cho offcanvas)
     */
    public function show($club_id, $request_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $request = ClubJoinRequest::where('club_id', $club_id)
            ->with(['user.member', 'club', 'interviewer', 'handler', 'interviewSchedules', 'formAnswers.question'])
            ->findOrFail($request_id);

        // Lấy danh sách người phỏng vấn
        $interviewers = User::whereHas('member.clubMembers', function($q) use ($club_id) {
            $q->where('club_id', $club_id)
              ->whereIn('role', ['club_manager', 'deputy_manager']);
        })->get();

        $memberProfile = $request->user->member;
        $membership = null;
        if ($memberProfile) {
            $membership = ClubMember::with(['member.user'])
                ->where('club_id', $club_id)
                ->where('member_id', $memberProfile->id)
                ->first();
        }

        $timeline = $this->buildTimeline($request, $membership);

        return view('client.pages.club.partials.request_detail', compact('request', 'club', 'interviewers', 'membership', 'timeline'));
    }

    /**
     * Xây dựng timeline cho yêu cầu
     */
    private function buildTimeline(ClubJoinRequest $request, ?ClubMember $membership): array
    {
        $hasSchedule = !is_null($request->interview_scheduled_at);
        $hasInterviewResult = $request->interview_result && in_array($request->interview_result, ['pass', 'fail']);
        $hasDecision = in_array($request->status, ['approved', 'rejected']);
        $hasMembership = !is_null($membership);

        return [
            [
                'step' => 1,
                'title' => 'Form câu hỏi → User trả lời',
                'completed' => true,
                'timestamp' => $request->requested_at,
                'description' => 'Sinh viên đã hoàn tất biểu mẫu đăng ký tham gia.',
            ],
            [
                'step' => 2,
                'title' => 'Chủ nhiệm tạo lịch phỏng vấn',
                'completed' => $hasSchedule,
                'timestamp' => $request->interview_scheduled_at,
                'description' => $hasSchedule
                    ? 'Đã có lịch phỏng vấn cụ thể.'
                    : 'Chờ chủ nhiệm lên lịch phỏng vấn.',
            ],
            [
                'step' => 3,
                'title' => 'Điểm danh → pass/fail',
                'completed' => $hasInterviewResult,
                'timestamp' => $request->interview_completed_at,
                'description' => $hasInterviewResult
                    ? 'Đã điểm danh và đánh giá phỏng vấn (Kết quả: ' . ($request->interview_result === 'pass' ? 'Đạt' : 'Không đạt') . ').'
                    : 'Chờ điểm danh và đánh giá phỏng vấn.',
            ],
            [
                'step' => 4,
                'title' => 'Duyệt → approved/rejected',
                'completed' => $hasDecision,
                'timestamp' => $hasDecision ? $request->handled_at : null,
                'description' => $hasDecision
                    ? ($request->status === 'approved' ? 'Đã duyệt yêu cầu.' : 'Đã từ chối yêu cầu.')
                    : 'Chờ quyết định duyệt.',
            ],
            [
                'step' => 5,
                'title' => 'Thành viên CLB',
                'completed' => $hasMembership,
                'timestamp' => $hasMembership ? $membership->joined_at : null,
                'description' => $hasMembership
                    ? 'Đã thêm vào danh sách thành viên CLB.'
                    : 'Chưa thêm vào CLB.',
            ],
        ];
    }

    /**
     * Xử lý yêu cầu tham gia CLB (tích hợp tất cả logic)
     */
    public function handle(Request $req, $club_id, $request_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $isAjax = $req->ajax() || $req->wantsJson() || $req->header('X-Requested-With') === 'XMLHttpRequest';

        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->with(['user.member', 'club'])
            ->findOrFail($request_id);

        $action = $req->input('action');
        if (!$action) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
            }
            return back()->with('error', 'Hành động không hợp lệ.');
        }

        if (in_array($joinRequest->status, ['approved', 'rejected', 'cancelled'])) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Yêu cầu này đã được xử lý.'], 400);
            }
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        // Validate status transitions
        $validTransitions = [
            'schedule' => ['pending_interview'],
            'complete_interview' => ['waiting_attendance'],
            'approve' => ['waiting_approval'],
            'reject' => ['pending_interview', 'waiting_attendance', 'waiting_approval'],
            'cancel' => ['pending_interview', 'waiting_attendance', 'waiting_approval'],
        ];

        if (!in_array($joinRequest->status, $validTransitions[$action] ?? [])) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Không thể thực hiện hành động này ở trạng thái hiện tại.'], 400);
            }
            return back()->with('error', 'Không thể thực hiện hành động này ở trạng thái hiện tại.');
        }

        try {
            DB::transaction(function () use ($action, $joinRequest, $req, $club_id) {
                switch ($action) {
                    case 'schedule':
                        $this->scheduleInterview($joinRequest, $req);
                        break;
                    case 'complete_interview':
                        $this->completeInterview($joinRequest, $req);
                        break;
                    case 'approve':
                        $this->approveRequest($joinRequest, $req, $club_id);
                        break;
                    case 'reject':
                        $this->rejectRequest($joinRequest, $req);
                        break;
                    case 'cancel':
                        $this->cancelRequest($joinRequest, $req);
                        break;
                    default:
                        throw new \InvalidArgumentException('Hành động không hợp lệ.');
                }
            });
        } catch (\Throwable $th) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Không thể xử lý: ' . $th->getMessage()], 500);
            }
            return back()->with('error', 'Không thể xử lý: ' . $th->getMessage());
        }

        if ($isAjax) {
            return response()->json(['success' => true, 'message' => 'Đã xử lý yêu cầu thành công.']);
        }
        return back()->with('success', 'Đã xử lý yêu cầu thành công.');
    }

    /**
     * Duyệt yêu cầu tham gia CLB
     */
    public function approve(Request $request, $club_id, $request_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        // Lấy đúng yêu cầu người dùng đã gửi
        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('id', $request_id)
            ->whereIn('status', ['pending', 'interview_completed'])
            ->with('user.member')
            ->firstOrFail();

        $user = $joinRequest->user;
        $member = $user->member;

        if (!$member) {
            return back()->with('error', 'Người dùng chưa có hồ sơ thành viên.');
        }

        // Kiểm tra đã là thành viên chưa
        $existingMember = ClubMember::where('club_id', $club_id)
            ->where('member_id', $member->id)
            ->first();

        if ($existingMember) {
            return back()->with('error', 'Người này đã là thành viên của CLB.');
        }

        DB::transaction(function () use ($club_id, $member, $joinRequest, $user) {

            ClubMember::create([
                'club_id' => $club_id,
                'member_id' => $member->id,
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]);

            $joinRequest->update([
                'status' => 'approved',
                'handled_by' => Auth::id(),
                'handled_at' => now(),
            ]);

            // Gửi thông báo
            SendNotificationJobClient::dispatch(

                $user->id,
                "Yêu cầu tham gia CLB được duyệt",
                "Yêu cầu tham gia CLB '{$joinRequest->club->name}' của bạn đã được duyệt.",
                'both',                              // gửi cả email + database
                uniqid(),                            // key duy nhất
                false,                               // có gửi email hay không
                auth()->id()                         // 👉 thêm sender_id
            );

        });

        return back()->with('success', 'Duyệt yêu cầu thành công!');
    }


    /**
     * Từ chối yêu cầu tham gia CLB
     */
    public function reject(Request $request, $club_id, $request_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $joinRequest = ClubJoinRequest::where('club_id', $club_id)
            ->where('id', $request_id)
            ->whereIn('status', ['pending_interview', 'waiting_attendance', 'waiting_approval'])
            ->with('user')
            ->firstOrFail();

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $joinRequest->status = 'rejected';
        $joinRequest->handled_by = Auth::id();
        $joinRequest->handled_at = now();
        $joinRequest->note = $request->input('rejection_reason', 'Không đáp ứng yêu cầu');
        $joinRequest->save();

        // Gửi thông báo
        $batchId = uniqid();
        SendNotificationJobClient::dispatch(

            $joinRequest->user->id,
            "Yêu cầu tham gia CLB bị từ chối",
            "Yêu cầu tham gia CLB '{$joinRequest->club->name}' của bạn đã bị từ chối. Lý do: {$joinRequest->note}",
            'both',
            $batchId,
            false,
            auth()->id() // 👉 thêm sender_id
        );

        return redirect()->back()
            ->with('success', 'Đã từ chối yêu cầu tham gia CLB.');
    }

    /**
     * Sắp lịch phỏng vấn
     */
    private function scheduleInterview(ClubJoinRequest $request, Request $req): void
    {
        $data = $req->validate([
            'interviewer_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'location' => 'required|string|max:255',
            'interview_note' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:1000',
        ]);

        $scheduledAt = Carbon::parse($data['scheduled_at']);

        ClubInterviewSchedule::updateOrCreate(
            ['request_id' => $request->id],
            [
                'club_id' => $request->club_id,
                'interviewer_id' => $data['interviewer_id'],
                'scheduled_at' => $scheduledAt,
                'location' => $data['location'],
                'status' => 'scheduled',
                'note' => $data['interview_note'] ?? null,
            ]
        );

        // Chuyển từ pending_interview → waiting_attendance
        $request->status = 'waiting_attendance';
        $request->interviewer_id = $data['interviewer_id'];
        $request->interview_scheduled_at = $scheduledAt;
        $request->interview_location = $data['location'];
        $request->interview_note = $data['interview_note'] ?? $request->interview_note;
        $request->note = $req->input('note');
        $request->handled_by = Auth::id();
        $request->save();
        $request->user->notify(
    new InterviewScheduledNotification(
        $request->load(['club', 'interviewer'])
    )
);
        $message = "CLB {$request->club->name} đã lên lịch phỏng vấn cho bạn vào {$scheduledAt->format('d/m/Y H:i')} tại {$data['location']}.";
        if (!empty($data['interview_note'])) {
            $message .= ' Ghi chú: ' . $data['interview_note'];
        }

        SendNotificationJobClient::dispatch(

            $request->user_id,
            'Thông báo lịch phỏng vấn',
            $message,
            'database',
            'club_join_request_' . $request->id . '_schedule_' . now()->timestamp,
            false,
            null,
            [
                'sender_id' => auth()->id()
            ]
        );

    }

    /**
     * Hoàn thành phỏng vấn
     */
    private function completeInterview(ClubJoinRequest $request, Request $req): void
    {
        $data = $req->validate([
            'interview_result' => 'required|in:pass,fail,completed,no_show,cancelled',
            'interview_feedback' => 'nullable|string|max:1000',
        ]);

        // Chuyển request sang giai đoạn duyệt
        $request->status = 'waiting_approval';
        $request->interview_result = $data['interview_result']; // pass | fail
        $request->interview_note = $data['interview_feedback'] ?? $request->interview_note;
        $request->interview_completed_at = now();
        $request->note = $req->input('note');
        $request->handled_by = Auth::id();
        $request->save();

        // Mapping status hợp lệ cho schedule
        $scheduleStatusMap = [
            'pass' => 'completed',
            'fail' => 'completed',
            'completed' => 'completed',
            'no_show' => 'no_show',
            'cancelled' => 'cancelled',
        ];

        $scheduleStatus = $scheduleStatusMap[$data['interview_result']] ?? 'completed';

        // Update lịch phỏng vấn
        $schedule = $request->interviewSchedules()->latest('scheduled_at')->first();
        if ($schedule) {
            $schedule->update([
                'status' => $scheduleStatus, // giá trị hợp lệ cho ENUM
                'note' => $data['interview_feedback'] ?? $schedule->note,
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Duyệt yêu cầu
     */
    private function approveRequest(ClubJoinRequest $request, Request $req, $club_id): void
    {
        $memberProfile = $request->user->member;
        if (!$memberProfile) {
            throw new \InvalidArgumentException('Người dùng chưa có hồ sơ thành viên.');
        }

        $exists = ClubMember::where('club_id', $club_id)
            ->where('member_id', $memberProfile->id)
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException('Người này đã là thành viên của CLB.');
        }

        ClubMember::create([
            'club_id' => $club_id,
            'member_id' => $memberProfile->id,
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now(),
            'note' => $req->input('note'),
        ]);

        $request->status = 'approved';
        $request->handled_by = Auth::id();
        $request->handled_at = now();
        $request->note = $req->input('note');
        $request->save();

        $message = "Chúc mừng! Bạn đã trở thành thành viên của CLB {$request->club->name}.";
        SendNotificationJobClient::dispatch(

            $request->user_id,
            'Yêu cầu tham gia được duyệt',
            $message,
            'both',
            md5('approved_' . $request->id . '_' . now()->timestamp),
            false,
            null, // contentText (giữ null để job tự tạo)
            [
                'sender_id' => auth()->id()
            ]
        );

    }

    /**
     * Từ chối yêu cầu
     */
    private function rejectRequest(ClubJoinRequest $request, Request $req): void
    {
        $request->status = 'rejected';
        $request->handled_by = Auth::id();
        $request->handled_at = now();
        $request->note = $req->input('note') ?? $req->input('rejection_reason', 'Không đáp ứng yêu cầu');
        $request->save();

        $message = "Rất tiếc, yêu cầu tham gia CLB {$request->club->name} của bạn đã bị từ chối.";
        if ($request->note) {
            $message .= ' Lý do: ' . $request->note;
        }

        SendNotificationJobClient::dispatch(

            $request->user_id,
            'Yêu cầu tham gia bị từ chối',
            $message,
            'both',
            md5('rejected_' . $request->id . '_' . now()->timestamp),
            false,        // force
            null,         // contentText -> để job tự convert từ HTML
            [
                'sender_id' => auth()->id()
            ]
        );

    }

    /**
     * Hủy yêu cầu
     */
    private function cancelRequest(ClubJoinRequest $request, Request $req): void
    {
        $request->status = 'cancelled';
        $request->handled_by = Auth::id();
        $request->handled_at = now();
        $request->note = $req->input('note');
        $request->save();

        $message = "Yêu cầu tham gia CLB {$request->club->name} đã bị hủy.";

        SendNotificationJobClient::dispatch(

            $request->user_id,
            'Yêu cầu tham gia bị hủy',
            $message,
            'database',
            md5('cancelled_' . $request->id . '_' . now()->timestamp),
            false,        // force
            null,         // contentText -> job sẽ tự generate từ HTML
            [
                'sender_id' => auth()->id()
            ]
        );

    }

    /**
     * Xử lý hàng loạt - Lên lịch phỏng vấn
     */
    public function batchSchedule(Request $req, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $requestIdsJson = $req->input('request_ids');
        $requestIds = json_decode($requestIdsJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($requestIds) || empty($requestIds)) {
            \Log::error('Batch schedule error', [
                'request_ids' => $requestIdsJson,
                'decoded' => $requestIds,
                'json_error' => json_last_error_msg()
            ]);
            return back()->with('error', 'Dữ liệu không hợp lệ. Vui lòng chọn ít nhất một yêu cầu.');
        }

        $data = $req->validate([
            'interviewer_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'location' => 'required|string|max:255',
            'interview_note' => 'nullable|string|max:1000',
        ]);

        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $count = 0;

        DB::transaction(function () use ($club_id, $data, $scheduledAt, $requestIds, &$count) {
            foreach ($requestIds as $requestId) {
                $request = ClubJoinRequest::where('club_id', $club_id)
                    ->where('status', 'pending_interview')
                    ->with('club')
                    ->find($requestId);

                if (!$request) continue;

                ClubInterviewSchedule::updateOrCreate(
                    ['request_id' => $request->id],
                    [
                        'club_id' => $club_id,
                        'interviewer_id' => $data['interviewer_id'],
                        'scheduled_at' => $scheduledAt,
                        'location' => $data['location'],
                        'status' => 'scheduled',
                        'note' => $data['interview_note'] ?? null,
                    ]
                );

                $request->status = 'waiting_attendance';
                $request->interviewer_id = $data['interviewer_id'];
                $request->interview_scheduled_at = $scheduledAt;
                $request->interview_location = $data['location'];
                $request->interview_note = $data['interview_note'] ?? $request->interview_note;
                $request->handled_by = Auth::id();
                $request->save();

                $message = "CLB {$request->club->name} đã lên lịch phỏng vấn cho bạn vào {$scheduledAt->format('d/m/Y H:i')} tại {$data['location']}.";

                SendNotificationJobClient::dispatch(

                    $request->user_id,                                   // người nhận
                    'Thông báo lịch phỏng vấn',                          // tiêu đề
                    $message,                                            // nội dung html/text
                    'database',                                          // gửi qua database notification
                    md5('schedule_' . $request->id . '_' . now()->timestamp), // batch_id
                    false,                                               // force
                    null,                                                // contentText -> để job tự chuyển html => plain text
                    [
                        'sender_id' => auth()->id()                      // <-- người gửi đúng vị trí
                    ]
                );


                $count++;
            }
        });

        return back()->with('success', "Đã lên lịch phỏng vấn cho {$count} ứng viên.");
    }

    /**
     * Xử lý hàng loạt - Hoàn thành phỏng vấn
     */
    public function batchComplete(Request $req, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $requestIdsJson = $req->input('request_ids');
        $requestIds = json_decode($requestIdsJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($requestIds) || empty($requestIds)) {
            \Log::error('Batch complete error', [
                'request_ids' => $requestIdsJson,
                'decoded' => $requestIds,
                'json_error' => json_last_error_msg()
            ]);
            return back()->with('error', 'Dữ liệu không hợp lệ. Vui lòng chọn ít nhất một yêu cầu.');
        }

        $data = $req->validate([
            'interview_result' => 'required|in:pass,fail,completed,no_show,cancelled',
            'interview_feedback' => 'nullable|string|max:1000',
        ]);

        $count = 0;

        DB::transaction(function () use ($club_id, $data, $requestIds, &$count) {
            foreach ($requestIds as $requestId) {
                $request = ClubJoinRequest::where('club_id', $club_id)
                    ->where('status', 'waiting_attendance')
                    ->find($requestId);

                if (!$request) continue;

                $request->status = 'waiting_approval';
                $request->interview_result = $data['interview_result'];
                $request->interview_note = $data['interview_feedback'] ?? $request->interview_note;
                $request->interview_completed_at = now();
                $request->handled_by = Auth::id();
                $request->save();

                $schedule = $request->interviewSchedules()->latest('scheduled_at')->first();
                if ($schedule) {
                    $schedule->update([
                        'status' => $data['interview_result'],
                        'note' => $data['interview_feedback'] ?? $schedule->note,
                        'completed_at' => now(),
                    ]);
                }

                $count++;
            }
        });

        return back()->with('success', "Đã cập nhật kết quả phỏng vấn cho {$count} ứng viên.");
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

