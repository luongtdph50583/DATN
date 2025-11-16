<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use App\Models\Club;
use App\Http\Requests\FundTransactionRequest;
use App\Models\Event;
use App\Models\EventFundRequest;
use App\Models\Fund;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
{
    // Query chính, load quan hệ
    $query = EventFundRequest::with(['event.club', 'requestedBy', 'approvedBy', 'disbursedBy'])
        ->orderBy('created_at', 'desc');

    // Tổng chi: tổng approved_amount của các giao dịch đã duyệt hoặc đang giải ngân
    $totalExpense = EventFundRequest::whereIn('status', ['disbursing', 'disbursed'])
        ->sum('approved_amount');

    // Số lượng đang chờ giải ngân
    $pendingCount = EventFundRequest::where('status', 'pending_disbursement')->count();

    // Số lượng đang giải ngân
    $disbursingCount = EventFundRequest::where('status', 'disbursing')->count();

    // Lọc theo CLB
    if ($request->filled('club_id')) {
        $query->whereHas('event', function ($q) use ($request) {
            $q->where('club_id', $request->club_id);
        });
    }

    // Lọc theo trạng thái
    if ($request->filled('status')) {
        if (in_array($request->status, ['pending_disbursement', 'disbursing', 'disbursed'])) {
            $query->where('status', $request->status);
        }
    }

    // Lọc theo ngày tạo
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }
$totalDisbursedAmount = EventFundRequest::whereIn('status', ['disbursing', 'disbursed'])
    ->sum('amount_disbursed');

    $transactions = $query->paginate(20);

    $clubs = Club::all(); // dùng cho filter

    return view('admin.funds.index', [
        'transactions' => $transactions,
        'clubs' => $clubs,
        'totalExpense' => $totalExpense,
          'totalDisbursedAmount' => $totalDisbursedAmount, // tổng số tiền đã giải ngân
        'pendingCount' => $pendingCount,
        'disbursingCount' => $disbursingCount, // thêm biến này cho card "Đang giải ngân"
    ]);
}

  public function approve(Request $request, $id)
    {
        $fundRequest = EventFundRequest::findOrFail($id);

        // Chỉ duyệt nếu chưa được duyệt
        if ($fundRequest->status === 'disbursed') {
            return redirect()->back()->with('info', 'Yêu cầu này đã được duyệt.');
        }

        $request->validate([
            'approved_amount' => 'required|numeric|min:0|max:' . $fundRequest->amount_requested,
        ]);

        $approvedAmount = $request->approved_amount;

        // Cập nhật approved_amount và status
        $fundRequest->approved_amount = $approvedAmount;
        $fundRequest->status = 'disbursed';
        $fundRequest->approved_by = auth()->id();
        $fundRequest->save();

        // Cập nhật quỹ event
        $event = $fundRequest->event;
        $event->budget_estimated += $approvedAmount;
        $event->budget_current   += $approvedAmount;
        $event->save();

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Đã duyệt yêu cầu và cập nhật quỹ event thành công.');
    }
   
    // Hiển thị form từ chối
public function showRejectForm($id)
{
    $request = EventFundRequest::findOrFail($id);
    return view('admin.event_funds.requests.reject', compact('request'));
}

// Xử lý POST từ chối
public function reject(Request $request, $id)
{
    $requestFund = EventFundRequest::findOrFail($id);

    $request->validate([
        'rejection_reason' => 'required|string|max:500',
    ]);

    $requestFund->update([
        'status' => 'rejected',
        'rejection_reason' => $request->rejection_reason,
        'approved_by' => auth()->id(),
    ]);

    return redirect()->route('admin.funds.index')->with('success', 'Yêu cầu đã bị từ chối thành công.');
}


}
