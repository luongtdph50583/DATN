<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Member;
use App\Models\ClubMember;
use App\Models\ClubRequest;
use App\Models\ClubRequestApproval;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ClubRequestPdfService;

class ClubRequestController extends Controller
{
    protected $pdfService;

    public function __construct(ClubRequestPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * 📋 Danh sách yêu cầu tạo CLB
     */
    public function indexRequests()
    {
        $requests = ClubRequest::with([
            'creator.member',
            'confirmations',
            'approvals'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.club_requests.index', compact('requests'));
    }

    /**
     * 👁️ Xem chi tiết yêu cầu
     */
    public function showRequest($id)
    {
        $request = ClubRequest::with([
            'creator.member',
            'confirmations',
            'approvals.admin',
            'clubManager.member',
            'deputyManager.member',
            'secretary.member',
            'treasurer.member',
            'eventManager.member',
            'communication.member',
        ])->findOrFail($id);

        // Đếm tổng số thành viên (ban quản lý + thành viên thường)
        $managementCount = collect([
            $request->club_manager_id,
            $request->deputy_manager_id,
            $request->secretary_id,
            $request->treasurer_id,
            $request->event_manager_id,
            $request->communication_id,
        ])->filter()->count();

        $regularMembersCount = \DB::table('club_request_members')
            ->where('club_request_id', $id)
            ->count();

        $totalMembers = $managementCount + $regularMembersCount;

        $confirmedCount = $request->confirmations()
            ->where('status', true)
            ->count();

        $isAllConfirmed = $confirmedCount >= $totalMembers && $totalMembers > 0;

        return view('admin.club_requests.show', compact('request', 'totalMembers', 'confirmedCount', 'isAllConfirmed'));
    }

    /**
     * ✅ PHÊ DUYỆT đơn thành lập CLB
     */
    public function approve(Request $request, $id)
    {
        $clubRequest = ClubRequest::with(['creator.student', 'members'])->findOrFail($id);

        // Validate
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        // Kiểm tra trạng thái
        if ($clubRequest->status !== 'pending_approval') {
            return redirect()->back()->withErrors(['error' => 'Đơn chưa đủ điều kiện phê duyệt (chưa có đủ xác nhận)']);
        }

        // Kiểm tra admin đã phê duyệt chưa
        $existingApproval = ClubRequestApproval::where('club_request_id', $clubRequest->id)
            ->where('admin_id', Auth::id())
            ->first();

        if ($existingApproval) {
            return redirect()->back()->withErrors(['error' => 'Bạn đã phê duyệt đơn này rồi']);
        }

        $creatorUser = $clubRequest->creator;
        if (!$creatorUser) {
            return redirect()->back()->withErrors(['error' => 'Người tạo không tồn tại']);
        }

        $creatorMember = Member::where('user_id', $creatorUser->id)->first();
        if (!$creatorMember) {
            return redirect()->back()->withErrors(['error' => 'Người tạo chưa có hồ sơ thành viên']);
        }

        // Kiểm tra tên CLB trùng
        if (Club::where('name', $clubRequest->name)->exists()) {
            return redirect()->back()->withErrors(['name' => 'Tên CLB đã tồn tại']);
        }

        // Kiểm tra người tạo chưa là quản lý CLB khác
        if (
            ClubMember::where('member_id', $creatorMember->id)
                ->where('role', 'club_manager')->exists()
        ) {
            return redirect()->back()->withErrors(['error' => 'Người tạo đang giữ vai trò quản lý ở CLB khác']);
        }

        DB::transaction(function () use ($clubRequest, $request, $creatorUser, $creatorMember) {

            // 1. Ghi phê duyệt
            ClubRequestApproval::create([
                'club_request_id' => $clubRequest->id,
                'admin_id' => Auth::id(),
                'status' => 'approved',
                'note' => $request->input('note'),
                'approved_at' => now(),
            ]);

            // 2. Sinh PDF snapshot đầy đủ
            $this->pdfService->generatePdf($clubRequest);

            // 3. Tạo CLB
            $club = Club::create([
                'name' => $clubRequest->name,
                'field' => $clubRequest->field ?? 'Chưa cập nhật',
                'description' => $clubRequest->description ?? '',
                'email' => $clubRequest->email ?? null,
                'phone' => $clubRequest->phone ?? null,
                'logo' => $clubRequest->logo ?? null,
                'status' => 'active',
                'founded_at' => now(),
                'manager_id' => $creatorUser->id,
            ]);

            // 4. Chuyển thành viên sang club_members
            // Lấy từ bảng club_request_members (pivot table)
            $memberRequests = \DB::table('club_request_members')
                ->where('club_request_id', $clubRequest->id)
                ->get();

            foreach ($memberRequests as $memberRequest) {
                $member = Member::where('user_id', $memberRequest->user_id)->first();

                if ($member) {
                    ClubMember::create([
                        'club_id' => $club->id,
                        'member_id' => $member->id,
                        'role' => $memberRequest->role ?? 'member',
                        'status' => 'active',
                        'joined_at' => now(),
                        'appointed_at' => $memberRequest->role !== 'member' ? now() : null,
                    ]);
                }
            }

            // 5. Cập nhật trạng thái đơn
            $clubRequest->update([
                'status' => 'approved',
                'handled_at' => now(),
            ]);

            // 6. Gửi thông báo cho người tạo
            $batchId = uniqid();
            SendNotificationJob::dispatch(
                $creatorUser->id,
                "✅ Yêu cầu thành lập CLB được duyệt",
                "Chúc mừng! Yêu cầu thành lập CLB '<strong>{$club->name}</strong>' đã được phê duyệt.<br>CLB của bạn đã chính thức hoạt động.",
                'both',
                $batchId,
                false,
                null,
                [],
                auth()->id()
            );
        });

        return redirect()->route('admin.club_requests.index')
            ->with('success', 'Đơn đã được phê duyệt và CLB đã được tạo thành công! ✅');
    }

    /**
     * ❌ TỪ CHỐI đơn thành lập CLB
     */
    public function reject(Request $request, $id)
    {
        $clubRequest = ClubRequest::with('creator')->findOrFail($id);

        $request->validate([
            'note' => 'required|string|max:500',
        ]);

        // Kiểm tra trạng thái
        if (!in_array($clubRequest->status, ['pending_approval', 'pending_confirmation'])) {
            return redirect()->back()->withErrors(['error' => 'Không thể từ chối đơn này']);
        }

        DB::transaction(function () use ($clubRequest, $request) {

            // Ghi phê duyệt
            ClubRequestApproval::create([
                'club_request_id' => $clubRequest->id,
                'admin_id' => Auth::id(),
                'status' => 'rejected',
                'note' => $request->input('note'),
                'approved_at' => now(),
            ]);

            // Cập nhật trạng thái
            $clubRequest->update([
                'status' => 'rejected',
                'handled_at' => now(),
            ]);

            // Gửi thông báo
            $batchId = uniqid();
            SendNotificationJob::dispatch(
                $clubRequest->creator->id,
                "❌ Yêu cầu thành lập CLB bị từ chối",
                "Yêu cầu thành lập CLB '<strong>{$clubRequest->name}</strong>' đã bị từ chối.<br><strong>Lý do:</strong> {$request->input('note')}",
                'both',
                $batchId,
                false,
                null,
                [],
                auth()->id()
            );
        });

        return redirect()->route('admin.club_requests.index')
            ->with('success', 'Đơn đã bị từ chối.');
    }

    /**
     * 🔍 Lọc yêu cầu (AJAX)
     */
    public function filterRequests(Request $request)
    {
        $query = ClubRequest::query()->with(['creator.member', 'confirmations']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('creator', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByDesc('created_at')->get();

        return response()->json($requests);
    }

    /**
     * 🗑️ Xóa yêu cầu
     */
    public function destroy($id)
    {
        $request = ClubRequest::findOrFail($id);

        // Chỉ cho phép xóa nếu là rejected hoặc draft
        if (!in_array($request->status, ['rejected', 'draft'])) {
            return redirect()->back()->withErrors(['error' => 'Chỉ có thể xóa đơn đã bị từ chối hoặc nháp']);
        }

        $request->delete();

        return redirect()->back()->with('success', 'Yêu cầu đã được xóa thành công.');
    }

    /**
     * 📄 Xem PDF
     */
    public function viewPdf($id)
    {
        $clubRequest = ClubRequest::findOrFail($id);

        // Chỉ cho phép xem PDF khi đã được phê duyệt hoặc có đủ xác nhận
        if (!in_array($clubRequest->status, ['approved', 'pending_approval'])) {
            return redirect()->back()->withErrors(['error' => 'PDF chưa khả dụng']);
        }

        return $this->pdfService->viewPdf($clubRequest);
    }

    /**
     * 💾 Tải PDF
     */
    public function downloadPdf($id)
    {
        $clubRequest = ClubRequest::findOrFail($id);

        if (!in_array($clubRequest->status, ['approved', 'pending_approval'])) {
            return redirect()->back()->withErrors(['error' => 'PDF chưa khả dụng']);
        }

        return $this->pdfService->downloadPdf($clubRequest);
    }
}
