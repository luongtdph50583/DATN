<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Member;
use App\Models\ClubRequest;
use App\Models\ClubRequestConfirmation;
use Illuminate\Http\Request;
use App\Models\ClubJoinRequest;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\ClubRequestPdfService;
use App\Jobs\SendNotificationJob;

class ClubFormationRequestController extends Controller
{
    protected $pdfService;

    public function __construct(ClubRequestPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Danh sách tất cả yêu cầu của user
     */
    public function index()
    {
        $user = Auth::user();

        $formationRequests = ClubRequest::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn($r) => $r->setAttribute('type', 'formation'));

        $joinRequests = ClubJoinRequest::where('user_id', $user->id)
            ->with('club')
            ->latest()
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

    /**
     * Xem chi tiết 1 yêu cầu thành lập CLB
     */
    public function show(ClubRequest $clubRequest)
    {
        $user = Auth::user();

        // Kiểm tra quyền xem: người tạo, ban quản lý, hoặc thành viên được mời
        $isCreator = $clubRequest->user_id == $user->id;
        $isManagement = in_array($user->id, [
            $clubRequest->club_manager_id,
            $clubRequest->deputy_manager_id,
            $clubRequest->secretary_id,
            $clubRequest->treasurer_id,
            $clubRequest->event_manager_id,
            $clubRequest->communication_id,
        ]);
        $isMember = $clubRequest->hasMember($user->id);

        if (!$isCreator && !$isManagement && !$isMember) {
            abort(403, 'Bạn không có quyền xem yêu cầu này.');
        }

        // Load relationships
        $clubRequest->load([
            'creator.member',
            'confirmations.user.member',
            'approvals.admin',
            'clubManager.member',
            'deputyManager.member',
            'secretary.member',
            'treasurer.member',
            'eventManager.member',
            'communication.member',
        ]);

        // Tính số xác nhận
        $totalMembers = $this->getTotalMembersCount($clubRequest);
        $confirmedCount = $clubRequest->confirmations()->where('status', true)->count();

        return view('client.pages.member.showClubRequest', compact('clubRequest', 'totalMembers', 'confirmedCount'));
    }

    /**
     * Hiển thị form gửi yêu cầu thành lập CLB
     */
    public function create()
    {
        // Lấy danh sách sinh viên (user + member)
        $students = Member::with('user')
            ->whereNull('deleted_at')
            ->get();

        return view('client.pages.member.formation_requestClub', compact('students'));
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

        // Kiểm tra không được giữ chức vụ quản lý CLB khác
        $managerRoles = ['club_manager', 'deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication'];

        $existingRoles = $member->clubMemberships()
            ->whereIn('role', $managerRoles)
            ->exists();

        if ($existingRoles) {
            return redirect()->back()
                ->with('error', 'Bạn đang giữ một chức vụ quản lý trong CLB khác, không thể gửi yêu cầu thành lập CLB mới.')
                ->withInput();
        }

        // Validate form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slogan' => 'required|string|max:255',
            'description' => 'required|string',
            'purpose' => 'required|string',
            'field' => 'required|string|max:255',
            'plan' => 'nullable|string',
            'plan_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'rule' => 'required|string',
            'member_limit' => 'required|integer|min:1',

            // Ban chủ nhiệm
            'club_manager_id' => 'required|exists:users,id',
            'deputy_manager_id' => 'required|exists:users,id',
            'secretary_id' => 'required|exists:users,id',
            'treasurer_id' => 'required|exists:users,id',
            'event_manager_id' => 'required|exists:users,id',
            'communication_id' => 'required|exists:users,id',

            // Thành viên ban đầu
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id',

            // Giảng viên (nếu có)
            'advisor_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Upload files
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('club_requests/logos', 'public');
            }

            $planFilePath = null;
            if ($request->hasFile('plan_file')) {
                $planFilePath = $request->file('plan_file')->store('club_requests/plans', 'public');
            }

            // Tạo yêu cầu thành lập CLB
            $clubRequest = ClubRequest::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'slogan' => $validated['slogan'],
                'description' => $validated['description'],
                'purpose' => $validated['purpose'],
                'field' => $validated['field'],
                'plan' => $validated['plan'] ?? null,
                'plan_file' => $planFilePath,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'logo' => $logoPath,
                'rule' => $validated['rule'],
                'member_limit' => $validated['member_limit'],

                // Ban chủ nhiệm
                'club_manager_id' => $validated['club_manager_id'],
                'deputy_manager_id' => $validated['deputy_manager_id'],
                'secretary_id' => $validated['secretary_id'],
                'treasurer_id' => $validated['treasurer_id'],
                'event_manager_id' => $validated['event_manager_id'],
                'communication_id' => $validated['communication_id'],

                // Giảng viên (nếu có)
                'advisor_id' => $validated['advisor_id'] ?? null,

                // Trạng thái
                'status' => 'pending_confirmation',
            ]);

            // Thêm thành viên ban đầu vào bảng pivot (many-to-many)
            $clubRequest->members()->attach($validated['members']);

            // ========================================
            // TẠO XÁC NHẬN CHO BAN QUẢN LÝ
            // ========================================
            $managementTeam = [
                'club_manager' => $validated['club_manager_id'],
                'deputy_manager' => $validated['deputy_manager_id'],
                'secretary' => $validated['secretary_id'],
                'treasurer' => $validated['treasurer_id'],
                'event_manager' => $validated['event_manager_id'],
                'communication' => $validated['communication_id'],
            ];

            foreach ($managementTeam as $role => $userId) {
                if ($userId) {
                    // Nếu là chủ nhiệm (người tạo đơn) → tự động xác nhận
                    $isCreator = ($userId == $user->id);

                    ClubRequestConfirmation::create([
                        'club_request_id' => $clubRequest->id,
                        'user_id' => $userId,
                        'status' => $isCreator ? true : false,
                        'confirmed_at' => $isCreator ? now() : null,
                    ]);
                }
            }

            // ========================================
            // TẠO XÁC NHẬN CHO THÀNH VIÊN THƯỜNG
            // ========================================
            foreach ($validated['members'] as $memberId) {
                // Kiểm tra không trùng với ban quản lý
                if (!in_array($memberId, $managementTeam)) {
                    ClubRequestConfirmation::create([
                        'club_request_id' => $clubRequest->id,
                        'user_id' => $memberId,
                        'status' => false,
                    ]);
                }
            }

            // ========================================
            // GỬI THÔNG BÁO CHO CÁC THÀNH VIÊN
            // ========================================
            $batchId = uniqid();

            // Gửi cho ban quản lý (trừ chủ nhiệm)
            foreach ($managementTeam as $role => $userId) {
                if ($userId && $userId != $user->id) {
                    $roleLabel = [
                        'deputy_manager' => 'Phó chủ nhiệm',
                        'secretary' => 'Thư ký',
                        'treasurer' => 'Thủ quỹ',
                        'event_manager' => 'Quản lý sự kiện',
                        'communication' => 'Truyền thông',
                    ][$role] ?? 'Ban quản lý';

                    SendNotificationJob::dispatch(
                        $userId,
                        "📋 Xác nhận tham gia CLB '{$clubRequest->name}'",
                        "Bạn được mời làm <strong>{$roleLabel}</strong> trong CLB '<strong>{$clubRequest->name}</strong>'.<br>Vui lòng xác nhận tham gia để hoàn tất quá trình thành lập CLB.",
                        'both',
                        $batchId,
                        false,
                        null,
                        ['club_request_id' => $clubRequest->id],
                        $user->id
                    );
                }
            }

            // Gửi cho thành viên thường
            foreach ($validated['members'] as $memberId) {
                if (!in_array($memberId, $managementTeam) && $memberId != $user->id) {
                    SendNotificationJob::dispatch(
                        $memberId,
                        "📋 Xác nhận tham gia CLB '{$clubRequest->name}'",
                        "Bạn được mời tham gia CLB '<strong>{$clubRequest->name}</strong>' với vai trò <strong>Thành viên</strong>.<br>Vui lòng xác nhận tham gia để hoàn tất quá trình thành lập CLB.",
                        'both',
                        $batchId,
                        false,
                        null,
                        ['club_request_id' => $clubRequest->id],
                        $user->id
                    );
                }
            }

            DB::commit();

            return redirect()->route('club_requests.show', $clubRequest)
                ->with('success', 'Yêu cầu thành lập CLB đã được gửi thành công! Vui lòng đợi các thành viên xác nhận.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Xóa file đã upload nếu có lỗi
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            if ($planFilePath) {
                Storage::disk('public')->delete($planFilePath);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Danh sách đơn cần xác nhận của user hiện tại
     */
    public function myConfirmations()
    {
        $confirmations = ClubRequestConfirmation::with(['clubRequest.creator'])
            ->where('user_id', Auth::id())
            ->where('status', false)
            ->latest()
            ->get();

        return view('client.pages.member.my_confirmations', compact('confirmations'));
    }

    /**
     * Xác nhận tham gia CLB
     */
    public function confirm(ClubRequest $clubRequest)
    {
        $confirmation = ClubRequestConfirmation::where('club_request_id', $clubRequest->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($confirmation->status) {
            return redirect()->back()->with('info', 'Bạn đã xác nhận đơn này rồi!');
        }

        DB::beginTransaction();
        try {
            // Cập nhật xác nhận
            $confirmation->update([
                'status' => true,
                'confirmed_at' => now(),
            ]);

            // Kiểm tra nếu tất cả thành viên đã xác nhận
            $totalMembers = $this->getTotalMembersCount($clubRequest);
            $confirmedCount = $clubRequest->confirmations()->where('status', true)->count();

            if ($confirmedCount >= $totalMembers) {
                // Đủ xác nhận → chuyển sang pending_approval và sinh PDF
                $clubRequest->update(['status' => 'pending_approval']);

                // Sinh PDF snapshot
                $this->pdfService->generatePdf($clubRequest);

                // TODO: Gửi thông báo cho admin
            }

            DB::commit();

            return redirect()->back()->with('success', 'Xác nhận thành công! Cảm ơn bạn đã tham gia.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hủy xác nhận
     */
    public function cancelConfirmation(ClubRequest $clubRequest)
    {
        $confirmation = ClubRequestConfirmation::where('club_request_id', $clubRequest->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$confirmation->status) {
            return redirect()->back()->with('info', 'Bạn chưa xác nhận đơn này!');
        }

        // Không cho phép hủy nếu là chủ nhiệm (người tạo)
        if ($clubRequest->user_id == Auth::id()) {
            return redirect()->back()->with('error', 'Chủ nhiệm không thể hủy xác nhận!');
        }

        // Hủy xác nhận
        $confirmation->update([
            'status' => false,
            'confirmed_at' => null,
        ]);

        // Chuyển lại trạng thái về pending_confirmation nếu đang chờ duyệt
        if ($clubRequest->status === 'pending_approval') {
            $clubRequest->update(['status' => 'pending_confirmation']);
        }

        return redirect()->back()->with('success', 'Đã hủy xác nhận.');
    }

    /**
     * Download PDF (sau khi admin đã phê duyệt)
     */
    public function downloadPdf(ClubRequest $clubRequest)
    {
        // Kiểm tra đã sinh PDF chưa
        if (!in_array($clubRequest->status, ['approved', 'pending_approval'])) {
            return redirect()->back()->with('error', 'PDF chưa khả dụng.');
        }

        return $this->pdfService->downloadPdf($clubRequest);
    }

    /**
     * Xem PDF trên browser
     */
    public function viewPdf(ClubRequest $clubRequest)
    {
        // Kiểm tra đã sinh PDF chưa
        if (!in_array($clubRequest->status, ['approved', 'pending_approval'])) {
            return redirect()->back()->with('error', 'PDF chưa khả dụng.');
        }

        return $this->pdfService->viewPdf($clubRequest);
    }

    /**
     * Hủy đơn (chỉ khi đang pending)
     */
    public function cancel(ClubRequest $clubRequest)
    {
        // Chỉ người tạo mới được hủy
        if ($clubRequest->user_id != Auth::id()) {
            abort(403, 'Bạn không có quyền hủy đơn này.');
        }

        // Chỉ hủy được khi đang pending_confirmation hoặc pending_approval
        if (!in_array($clubRequest->status, ['pending_confirmation', 'pending_approval'])) {
            return redirect()->back()->with('error', 'Không thể hủy đơn đã xử lý.');
        }

        DB::beginTransaction();
        try {
            // Xóa files
            if ($clubRequest->logo) {
                Storage::disk('public')->delete($clubRequest->logo);
            }
            if ($clubRequest->plan_file) {
                Storage::disk('public')->delete($clubRequest->plan_file);
            }
            if ($clubRequest->pdf_file) {
                Storage::disk('public')->delete($clubRequest->pdf_file);
            }

            // Xóa đơn (cascade sẽ xóa confirmations)
            $clubRequest->delete();

            DB::commit();

            return redirect()->route('formation_request.index')
                ->with('success', 'Đã hủy đơn thành lập CLB.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi khi hủy đơn: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Đếm tổng số thành viên (ban quản lý + thành viên thường)
     */
    private function getTotalMembersCount(ClubRequest $clubRequest)
    {
        $managementCount = collect([
            $clubRequest->club_manager_id,
            $clubRequest->deputy_manager_id,
            $clubRequest->secretary_id,
            $clubRequest->treasurer_id,
            $clubRequest->event_manager_id,
            $clubRequest->communication_id,
        ])->filter()->count();

        $regularMembersCount = \DB::table('club_request_members')
            ->where('club_request_id', $clubRequest->id)
            ->count();

        return $managementCount + $regularMembersCount;
    }
}
