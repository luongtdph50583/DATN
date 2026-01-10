<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Models\ClubInterviewSchedule;
use App\Models\ClubJoinRequest;
use App\Models\ClubMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClubJoinRequestController extends Controller
{
    public function index()
    {
        $requests = ClubJoinRequest::with(['club', 'user'])
            ->orderByDesc('requested_at')
            ->get();

        return view('admin.club_join_requests.index', compact('requests'));
    }
    public function showRequest($id)
    {
        $data = $this->loadRequestDetail($id);

        return view('admin.club_join_requests.partials.detail', $data);
    }



    public function destroy($id)
{
    $request = ClubJoinRequest::findOrFail($id);
    $request->delete();

    return redirect()->back()->with('success', 'Yêu cầu đã được xóa thành công.');
}


    public function handle(Request $req, $id)
    {
        $isAjax = $req->ajax() || $req->wantsJson() || $req->header('X-Requested-With') === 'XMLHttpRequest';
        
        $requestModel = ClubJoinRequest::with(['user.member', 'club'])->findOrFail($id);
        $action = $req->input('action');

        if (!$action) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
            }
            return back()->with('error', 'Hành động không hợp lệ.');
        }

        if (in_array($requestModel->status, ['approved', 'rejected', 'cancelled'])) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Yêu cầu này đã được xử lý.'], 400);
            }
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        try {
            DB::transaction(function () use ($action, $requestModel, $req) {
                switch ($action) {
                    case 'schedule':
                        $this->scheduleInterview($requestModel, $req);
                        break;
                    case 'complete_interview':
                        $this->completeInterview($requestModel, $req);
                        break;
                    case 'approve':
                        $this->approveRequest($requestModel, $req);
                        break;
                    case 'reject':
                        $this->rejectRequest($requestModel, $req);
                        break;
                    case 'cancel':
                        $this->cancelRequest($requestModel, $req);
                        break;
                    default:
                        throw new \InvalidArgumentException('Hành động không hợp lệ.');
                }
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ.',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            Log::error('Handle club join request failed', [
                'request_id' => $requestModel->id,
                'action' => $action,
                'error' => $th->getMessage(),
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xử lý yêu cầu: ' . $th->getMessage()
                ], 500);
            }
            return back()->with('error', 'Không thể xử lý yêu cầu: ' . $th->getMessage());
        }

        // Nếu là AJAX request, trả về JSON
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => $this->getSuccessMessage($action),
                'status' => $requestModel->fresh()->status
            ]);
        }

        return back()->with('success', $this->getSuccessMessage($action));
    }


    public function show2($id)
    {
        $data = $this->loadRequestDetail($id);

        return view('admin.club_join_requests.show2', $data);
    }
    public function filter(Request $request)
    {
        $query = ClubJoinRequest::with(['user', 'club']);

        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$keyword%"))
                    ->orWhereHas('club', fn($c) => $c->where('name', 'like', "%$keyword%"));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByDesc('requested_at')->get();

        // ✅ Trả JSON thô
        return response()->json([
            'data' => $requests->map(fn($r) => [
                'id' => $r->id,
                'user' => $r->user->name ?? '—',
                'club' => $r->club->name ?? '—',
                'requested_at' => optional($r->requested_at)->format('d/m/Y') ?? '—',
                'status' => $r->status,
                'show_url' => route('admin.club_join_requests.show2', $r->id),
            ])
        ]);
    }

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

        $request->status = 'interview';
        $request->interviewer_id = $data['interviewer_id'];
        $request->interview_scheduled_at = $scheduledAt;
        $request->interview_location = $data['location'];
        $request->interview_note = $data['interview_note'] ?? $request->interview_note;
        $request->note = $req->input('note');
        $request->handled_by = Auth::id();
        $request->save();

        $message = "CLB {$request->club->name} đã lên lịch phỏng vấn cho bạn vào {$scheduledAt->format('d/m/Y H:i')} tại {$data['location']}.";
        if (!empty($data['interview_note'])) {
            $message .= ' Ghi chú: ' . $data['interview_note'];
        }

        $this->notifyCandidate(
            $request,
            'Thông báo lịch phỏng vấn',
            $message,
            'schedule',
            [
                'scheduled_at' => $scheduledAt->toIso8601String(),
                'interviewer_id' => $data['interviewer_id'],
            ]
        );
    }

    private function completeInterview(ClubJoinRequest $request, Request $req): void
    {
        $data = $req->validate([
            'interview_result' => 'required|in:completed,no_show,cancelled',
            'interview_feedback' => 'nullable|string|max:1000',
        ]);

        $request->status = 'interview_completed';
        $request->interview_result = $data['interview_result'];
        $request->interview_note = $data['interview_feedback'] ?? $request->interview_note;
        $request->interview_completed_at = now();
        $request->note = $req->input('note');
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

        $resultLabel = match ($data['interview_result']) {
            'completed' => 'Hoàn thành phỏng vấn',
            'no_show' => 'Không tham dự phỏng vấn',
            'cancelled' => 'Buổi phỏng vấn đã bị hủy',
            default => 'Phỏng vấn'
        };

        $message = "{$resultLabel}.";
        
        if (!empty($data['interview_feedback'])) {
            $message .= ' Nhận xét: ' . $data['interview_feedback'];
        }

        $this->notifyCandidate(
            $request,
            'Cập nhật kết quả phỏng vấn',
            $message,
            'interview_result',
            [
                'interview_result' => $data['interview_result'],
            ]
        );
    }

    private function approveRequest(ClubJoinRequest $request, Request $req): void
    {
        $memberProfile = $request->user->member;
        if (!$memberProfile) {
            throw new \InvalidArgumentException('Người dùng chưa có hồ sơ thành viên.');
        }

        $exists = ClubMember::where('club_id', $request->club_id)
            ->where('member_id', $memberProfile->id)
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException('Người này đã là thành viên của CLB.');
        }

        ClubMember::create([
            'club_id' => $request->club_id,
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
        $this->notifyCandidate(
            $request,
            'Yêu cầu tham gia được duyệt',
            $message,
            'approved',
            ['membership_created' => true]
        );
    }

    private function rejectRequest(ClubJoinRequest $request, Request $req): void
    {
        $request->status = 'rejected';
        $request->handled_by = Auth::id();
        $request->handled_at = now();
        $request->note = $req->input('note');
        $request->save();

        $message = "Rất tiếc, yêu cầu tham gia CLB {$request->club->name} của bạn đã bị từ chối.";
        if ($request->note) {
            $message .= ' Lý do: ' . $request->note;
        }

        $this->notifyCandidate(
            $request,
            'Yêu cầu tham gia bị từ chối',
            $message,
            'rejected'
        );
    }

    private function cancelRequest(ClubJoinRequest $request, Request $req): void
    {
        $request->status = 'cancelled';
        $request->handled_by = Auth::id();
        $request->handled_at = now();
        $request->note = $req->input('note');
        $request->save();

        $message = "Yêu cầu tham gia CLB {$request->club->name} đã bị hủy.";

        $this->notifyCandidate(
            $request,
            'Yêu cầu tham gia bị hủy',
            $message,
            'cancelled'
        );
    }

    private function getSuccessMessage(string $action): string
    {
        return match ($action) {
            'schedule' => 'Đã cập nhật lịch phỏng vấn.',
            'complete_interview' => 'Đã lưu kết quả phỏng vấn.',
            'approve' => 'Đã duyệt yêu cầu và thêm thành viên vào CLB.',
            'reject' => 'Đã từ chối yêu cầu tham gia.',
            'cancel' => 'Đã hủy yêu cầu tham gia.',
            default => 'Đã xử lý yêu cầu.',
        };
    }

    private function notifyCandidate(ClubJoinRequest $request, string $title, string $message, string $batchSuffix, array $context = []): void
    {
        $html = '<p>' . nl2br(e($message)) . '</p>';

        SendNotificationJob::dispatch(
            $request->user_id,
            $title,
            $html,
            'database',
            'club_join_request_' . $request->id . '_' . $batchSuffix . '_' . now()->timestamp,
            false,
            $message,
            $this->buildNotificationContext($request, $context)
        );
    }

    private function buildNotificationContext(ClubJoinRequest $request, array $extra = []): array
    {
        $sender = Auth::user();

        return array_merge([
            'sender' => [
                'id' => $sender->id ?? null,
                'name' => $sender->name ?? 'Hệ thống',
                'email' => $sender->email ?? null,
            ],
            'club' => [
                'id' => $request->club->id,
                'name' => $request->club->name,
            ],
            'request_id' => $request->id,
            'type' => 'club_join_request',
            'link' => route('admin.club_join_requests.show2', $request->id),
            'status' => $request->status,
        ], $extra);
    }

    private function loadRequestDetail(int $id): array
    {
        $request = ClubJoinRequest::with([
            'user.member',
            'club.manager',
            'club.clubMembers.member.user',
            'interviewSchedules.interviewer',
            'formAnswers.question',
        ])->findOrFail($id);

        $interviewers = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        $memberProfile = $request->user->member;
        $membership = null;

        if ($memberProfile) {
            $membership = ClubMember::with(['member.user'])
                ->where('club_id', $request->club_id)
                ->where('member_id', $memberProfile->id)
                ->first();
        }

        return [
            'request' => $request,
            'interviewers' => $interviewers,
            'membership' => $membership,
            'timeline' => $this->buildTimeline($request, $membership),
        ];
    }

    private function buildTimeline(ClubJoinRequest $request, ?ClubMember $membership): array
    {
        $hasManagerAction = !is_null($request->handled_by) || in_array($request->status, [
            'scheduling_interview',
            'interview',
            'interview_completed',
            'approved',
            'rejected',
        ]);
        $hasSchedule = !is_null($request->interview_scheduled_at);
        $hasInterviewResult = $request->interview_result && $request->interview_result !== 'pending';
        $hasDecision = in_array($request->status, ['approved', 'rejected']);
        $hasMembership = !is_null($membership);

        return [
            [
                'step' => 1,
                'title' => 'Thành viên gửi yêu cầu',
                'completed' => true,
                'timestamp' => $request->requested_at,
                'description' => 'Sinh viên đã hoàn tất biểu mẫu đăng ký tham gia.',
            ],
            [
                'step' => 2,
                'title' => 'Quản lý xử lý yêu cầu',
                'completed' => $hasManagerAction,
                'timestamp' => $hasManagerAction ? ($request->handled_at ?? $request->updated_at) : null,
                'description' => $hasManagerAction
                    ? 'Ban quản lý đã tiếp nhận yêu cầu.'
                    : 'Chờ ban quản lý phản hồi.',
            ],
            [
                'step' => 3,
                'title' => 'Sắp lịch phỏng vấn',
                'completed' => $hasSchedule,
                'timestamp' => $request->interview_scheduled_at,
                'description' => $hasSchedule
                    ? 'Đã có lịch phỏng vấn cụ thể.'
                    : 'Chưa lên lịch phỏng vấn.',
            ],
            [
                'step' => 4,
                'title' => 'Điểm danh / Đánh giá phỏng vấn',
                'completed' => $hasInterviewResult,
                'timestamp' => $request->interview_completed_at,
                'description' => $hasInterviewResult
                    ? 'Kết quả phỏng vấn đã được ghi nhận.'
                    : 'Chờ cập nhật kết quả phỏng vấn.',
            ],
            [
                'step' => 5,
                'title' => 'Ra quyết định duyệt',
                'completed' => $hasDecision,
                'timestamp' => $request->handled_at,
                'description' => $hasDecision
                    ? 'Yêu cầu đã được duyệt.'
                    : 'Chờ quyết định duyệt.',
            ],
            [
                'step' => 6,
                'title' => 'Thêm vào CLB',
                'completed' => $hasMembership,
                'timestamp' => $membership?->joined_at,
                'description' => $hasMembership
                    ? 'Thành viên đã có trong danh sách CLB.'
                    : 'Chưa được thêm vào danh sách thành viên.',
            ],
        ];
    }
}
