<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\EventFundRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventFundRequestController extends Controller
{
    // 1️⃣ Hiển thị danh sách yêu cầu
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



    // 7️⃣ Form duyệt yêu cầu
    public function approveForm($id)
    {
        $request = EventFundRequest::with('event')->findOrFail($id);
        return view('admin.event_funds.requests.approve', compact('request'));
    }

    // 8️⃣ Duyệt yêu cầu và cộng quỹ
    public function approve(Request $request, $id)
    {
        $fundRequest = EventFundRequest::findOrFail($id);

        // Chỉ duyệt khi đang pending_disbursement
        if ($fundRequest->status !== 'pending_disbursement') {
            return redirect()->back()->with('info', 'Yêu cầu này đã được duyệt trước đó.');
        }

        // Validate số tiền duyệt
        $request->validate([
            'approved_amount' => 'required|numeric|min:0|max:' . $fundRequest->amount_requested,
        ]);

        // Gán thông tin duyệt
        $fundRequest->approved_amount = $request->approved_amount;
        $fundRequest->disbursement_start = $request->disbursement_start;
          $fundRequest->disbursement_end = $request->disbursement_end;

        $fundRequest->approved_by = auth()->id();

        // ❗ Chỉ chuyển sang trạng thái *đang giải ngân* chứ KHÔNG giải ngân ngay
        $fundRequest->status = 'disbursing';

        $fundRequest->save();

        return redirect()->route('admin.event_fund_requests.index')
            ->with('success', 'Yêu cầu đã được duyệt. Trạng thái: ĐANG GIẢI NGÂN.');
    }


    // 9️⃣ Từ chối / hủy yêu cầu
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


    // 10️⃣ Xem chi tiết yêu cầu
    public function show($id)
    {
        $request = EventFundRequest::with(['event', 'user'])->findOrFail($id);
        return view('admin.event_funds.requests.show', compact('request'));
    }
    public function disbursing($id)
    {
        $request = EventFundRequest::findOrFail($id);
        return view('admin.event_funds.requests.disbursing', compact('request'));
    }
  // Cập nhật giải ngân (đang giải ngân)
public function updateDisbursement(Request $request, $id)
{
    $fundRequest = EventFundRequest::findOrFail($id);

    $request->validate([
        'disbursement_amount' => 'required|numeric|min:0|max:' . ($fundRequest->approved_amount - $fundRequest->amount_disbursed),
        'disbursement_proof.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    $amount = $request->disbursement_amount;

    // Upload minh chứng
    $files = $fundRequest->disbursement_proof ? json_decode($fundRequest->disbursement_proof, true) : [];
    if($request->hasFile('disbursement_proof')) {
        foreach($request->file('disbursement_proof') as $file) {
            $files[] = $file->store('disbursement_proofs', 'public');
        }
    }
    $fundRequest->disbursement_proof = json_encode($files);

    // Cập nhật số tiền đã giải ngân
    $fundRequest->amount_disbursed += $amount;

    // Lưu vào lịch sử giải ngân
    $history = $fundRequest->disbursement_history ? json_decode($fundRequest->disbursement_history, true) : [];
    $history[] = [
        'amount' => $amount,
        'date' => now(),
        'disbursed_by_id' => auth()->id(),
        'disbursed_by_name' => auth()->user()->name,
        'proof' => $files,
    ];
    $fundRequest->disbursement_history = json_encode($history);

    // Kiểm tra action
    if($request->action === 'complete' || $fundRequest->amount_disbursed >= $fundRequest->approved_amount) {
        $fundRequest->status = 'disbursed';
        $fundRequest->disbursement_date = now();
    } else {
        $fundRequest->status = 'disbursing';
    }

    $fundRequest->save();

    return redirect()->route('admin.event_fund_requests.index')
                     ->with('success', 'Cập nhật giải ngân thành công.');
}

// Hoàn tất giải ngân (nếu vẫn muốn giữ riêng)
public function completeDisbursement($id)
{
    $fundRequest = EventFundRequest::findOrFail($id);

    if($fundRequest->amount_disbursed < $fundRequest->approved_amount) {
        return redirect()->route('admin.event_fund_requests.index')
                         ->with('error', 'Số tiền giải ngân chưa đủ, không thể hoàn tất.');
    }

    $fundRequest->status = 'disbursed';
    $fundRequest->disbursement_date = now();
    $fundRequest->save();

    return redirect()->route('admin.event_fund_requests.index')
                     ->with('success', 'Hoàn tất giải ngân thành công.');
}


}
